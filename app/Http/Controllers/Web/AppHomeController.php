<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AppHomeController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user instanceof User) {
            return redirect()->route('login');
        }

        return redirect()->to(match ($user->profile_type) {
            User::PROFILE_PARTICULIER => route('app.particulier.home'),
            User::PROFILE_ARTISAN => route('app.artisan.home'),
            User::PROFILE_ENTREPRENEUR_BATIMENT => route('app.batiment.home'),
            User::PROFILE_ENTREPRISE_FOURNISSEUR => route('app.fournisseur.home'),
            default => route('app.particulier.home'),
        });
    }
}
