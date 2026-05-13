<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Besoin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BesoinController extends Controller
{
    public function index(Request $request): View
    {
        $q = Besoin::query()->with('user');
        if ($request->filled('status')) {
            $q->where('status', $request->string('status'));
        }
        if ($search = $request->string('q')->trim()) {
            $q->where('title', 'like', "%{$search}%");
        }
        $besoins = $q->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.besoins.index', ['besoins' => $besoins]);
    }

    public function show(Besoin $besoin): View
    {
        $besoin->load(['user', 'candidatures.applicant']);

        return view('admin.besoins.show', ['besoin' => $besoin]);
    }

    public function update(Request $request, Besoin $besoin): RedirectResponse
    {
        $data = $request->validate([
            'status' => [
                'required',
                Rule::in(['open', 'in_progress', 'closed', 'cancelled']),
            ],
        ]);
        $besoin->update($data);

        return redirect()->route('admin.besoins.show', $besoin)->with('ok', 'Besoin mis à jour.');
    }
}
