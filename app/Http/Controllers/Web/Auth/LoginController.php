<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user instanceof User && $user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('app.home');
        }

        return view('app.auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $user = User::query()->where('email', $data['email'])->first();

        if (! $user || $user->isAdmin() || ! Hash::check($data['password'], $user->password)) {
            return back()->withErrors(['email' => __('auth.failed')])->onlyInput('email');
        }

        Auth::login($user, (bool) ($data['remember'] ?? false));
        $request->session()->regenerate();

        return redirect()->intended(route('app.home'));
    }
}
