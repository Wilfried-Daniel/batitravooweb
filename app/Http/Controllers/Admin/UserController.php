<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $q = User::query()->where('role', User::ROLE_USER);

        if ($request->filled('profile_type')) {
            $q->where('profile_type', $request->string('profile_type'));
        }

        if ($request->filled('validation_status')) {
            $q->where('profile_validation_status', $request->string('validation_status'));
        }

        if ($request->boolean('inactive_only')) {
            $q->where('is_active', false);
        }

        if ($search = $request->string('q')->trim()) {
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $q->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }

    public function edit(User $user): View|RedirectResponse
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.users.index')->with('error', 'Modifiez les administrateurs en base de données pour ce compte.');
        }

        return view('admin.users.edit', [
            'user' => $user,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.users.index');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:32'],
            'profile_type' => [
                'required',
                'string',
                Rule::in([
                    User::PROFILE_ENTREPRENEUR_BATIMENT,
                    User::PROFILE_ENTREPRISE_FOURNISSEUR,
                    User::PROFILE_ARTISAN,
                    User::PROFILE_PARTICULIER,
                ]),
            ],
            'is_active' => ['required', Rule::in([0, 1, '0', '1', true, false])],
            'company_name' => ['nullable', 'string', 'max:255'],
            'company_address' => ['nullable', 'string', 'max:2000'],
        ]);
        $data['is_active'] = (bool) (int) $data['is_active'];

        $user->fill($data);
        $user->save();

        return redirect()->route('admin.users.index')->with('ok', 'Utilisateur mis à jour.');
    }
}
