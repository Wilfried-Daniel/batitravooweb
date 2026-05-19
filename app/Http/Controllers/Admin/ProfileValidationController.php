<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileValidationController extends Controller
{
    public function index(Request $request): View
    {
        $allowedStatuses = [
            User::VALIDATION_PENDING,
            User::VALIDATION_APPROVED,
            User::VALIDATION_REJECTED,
            User::VALIDATION_CHANGES_REQUESTED,
        ];

        $status = $request->query('status');
        
        if (! in_array($status, $allowedStatuses, true)) {
            $status = User::VALIDATION_PENDING;
        }

        $q = User::query()
            ->where('role', User::ROLE_USER)
            ->whereNotNull('profile_completed_at')
            ->where('profile_validation_status', $status);

        if ($request->filled('profile_type')) {
            $q->where('profile_type', $request->string('profile_type'));
        }

        if ($search = $request->string('q')->trim()) {
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $q->orderBy('profile_completed_at', 'desc')->paginate(20)->withQueryString();

        $counts = [
            'pending' => User::query()->where('role', User::ROLE_USER)->whereNotNull('profile_completed_at')->where('profile_validation_status', User::VALIDATION_PENDING)->count(),
            'approved' => User::query()->where('role', User::ROLE_USER)->whereNotNull('profile_completed_at')->where('profile_validation_status', User::VALIDATION_APPROVED)->count(),
            'rejected' => User::query()->where('role', User::ROLE_USER)->whereNotNull('profile_completed_at')->where('profile_validation_status', User::VALIDATION_REJECTED)->count(),
            'changes' => User::query()->where('role', User::ROLE_USER)->whereNotNull('profile_completed_at')->where('profile_validation_status', User::VALIDATION_CHANGES_REQUESTED)->count(),
        ];

        return view('admin.profile-validation.index', [
            'users' => $users,
            'currentStatus' => $status,
            'counts' => $counts,
        ]);
    }

    public function show(User $user): View|RedirectResponse
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.profile-validation.index')->with('error', 'Ce compte est un administrateur.');
        }

        $user->load([
            'documents',
            'artisanBusinessCard',
            'products' => fn ($q) => $q->latest()->limit(10),
            'services' => fn ($q) => $q->latest()->limit(10),
        ]);
        $user->loadCount(['products', 'services']);

        $kindLabels = [
            UserDocument::KIND_CNI => 'CNI',
            UserDocument::KIND_OTHER => 'Autre document',
            UserDocument::KIND_CERTIFICATE => 'Certificat',
            UserDocument::KIND_COMMERCE_REGISTER => 'RCCM',
            UserDocument::KIND_MANAGER_CNI => 'CNI dirigeant',
            UserDocument::KIND_DFE => 'DFE',
        ];

        $documentRows = $user->documents->map(function (UserDocument $doc) use ($kindLabels) {
            return [
                'kind' => $doc->kind,
                'label' => $kindLabels[$doc->kind] ?? $doc->kind,
                'url' => $doc->storage_path ? Storage::disk('public')->url($doc->storage_path) : null,
                'original_filename' => $doc->original_filename,
            ];
        });

        return view('admin.profile-validation.show', [
            'user' => $user,
            'documentRows' => $documentRows,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.profile-validation.index');
        }

        $data = $request->validate([
            'action' => [
                'required',
                'string',
                Rule::in(['approve', 'reject', 'changes_requested']),
            ],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($data['action'] === 'changes_requested' && empty(trim((string) ($data['note'] ?? '')))) {
            return back()->withErrors(['note' => 'Indiquez ce que l’utilisateur doit corriger.'])->withInput();
        }

        if ($data['action'] === 'approve') {
            $user->profile_validation_status = User::VALIDATION_APPROVED;
            $user->profile_validation_note = null;
            $user->profile_validated_at = now();
            $user->is_active = true;
        } elseif ($data['action'] === 'reject') {
            $user->profile_validation_status = User::VALIDATION_REJECTED;
            $user->profile_validation_note = $data['note'];
            $user->profile_validated_at = now();
        } else {
            $user->profile_validation_status = User::VALIDATION_CHANGES_REQUESTED;
            $user->profile_validation_note = $data['note'];
            $user->profile_validated_at = null;
        }

        $user->save();

        return redirect()
            ->route('admin.profile-validation.show', $user)
            ->with('ok', 'Décision enregistrée.');
    }
}
