<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        if (! Auth::attempt(['email' => $data['email'], 'password' => $data['password']], (bool) ($data['remember'] ?? false))) {
            return back()->withErrors(['email' => 'Identifiants invalides.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();
        if (! $user instanceof User) {
            Auth::logout();

            return back()->withErrors(['email' => 'Erreur de session.']);
        }

        if (! $user->isAdmin()) {
            Auth::logout();

            return back()->withErrors(['email' => 'Ce compte n\'est pas un administrateur.']);
        }

        if (! $user->is_active) {
            Auth::logout();

            return back()->withErrors(['email' => 'Compte désactivé.']);
        }

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
