<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdministratorsController extends Controller
{
    public function index(): View
    {
        $admins = User::query()
            ->where('role', User::ROLE_ADMIN)
            ->orderBy('name')
            ->get();

        return view('admin.administrators', ['admins' => $admins]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => User::ROLE_ADMIN,
            'profile_type' => null,
            'phone' => null,
            'is_active' => true,
        ]);

        return redirect()->route('admin.administrators.index')->with('ok', 'Administrateur créé.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if (! $user->isAdmin()) {
            return redirect()->route('admin.administrators.index')->with('error', 'Cet utilisateur n’est pas un administrateur.');
        }

        if ($user->id === $request->user()->id) {
            return redirect()->route('admin.administrators.index')->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $remaining = User::query()->where('role', User::ROLE_ADMIN)->where('id', '!=', $user->id)->count();
        if ($remaining < 1) {
            return redirect()->route('admin.administrators.index')->with('error', 'Impossible de supprimer le dernier administrateur.');
        }

        $user->delete();

        return redirect()->route('admin.administrators.index')->with('ok', 'Administrateur supprimé.');
    }
}
