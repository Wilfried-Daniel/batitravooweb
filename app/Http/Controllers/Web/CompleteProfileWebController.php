<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Api\Me\CompleteProfileController as ApiCompleteProfileController;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Web\MeApiBridge;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class CompleteProfileWebController extends Controller
{
    public function edit(Request $request): View|RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->profile_completed_at !== null) {
            return redirect()->route('app.home');
        }

        $profileSlug = match ($user->profile_type) {
            User::PROFILE_PARTICULIER => 'particulier',
            User::PROFILE_ARTISAN => 'artisan',
            User::PROFILE_ENTREPRENEUR_BATIMENT => 'batiment',
            User::PROFILE_ENTREPRISE_FOURNISSEUR => 'fournisseur',
            default => 'particulier',
        };

        return view('app.complete-profile.edit', [
            'user' => $user,
            'unreadNotifications' => app(MeApiBridge::class)->unreadNotificationsCount($request),
            'title' => 'Compléter le profil',
            'profileSlug' => $profileSlug,
            'page' => 'profile',
            'hideIncompleteProfileBanner' => true,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->profile_completed_at !== null) {
            return redirect()->route('app.home');
        }

        try {
            $response = app(ApiCompleteProfileController::class)->store($request);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (HttpExceptionInterface $e) {
            return redirect()->back()->withErrors(['general' => $e->getMessage() ?: 'Erreur'])->withInput();
        }

        if ($response->getStatusCode() >= 400) {
            $payload = json_decode($response->getContent(), true);
            $msg = is_array($payload) && isset($payload['message']) ? (string) $payload['message'] : 'Erreur lors de l’enregistrement.';

            return redirect()->back()->withErrors(['general' => $msg])->withInput();
        }

        return redirect()->route('app.home')->with('status', 'Profil complété. Validation par l’équipe en cours.');
    }
}
