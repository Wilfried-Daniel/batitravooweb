<?php

namespace App\Http\Controllers\Web\App;

use App\Http\Controllers\Api\Me\ProfileController;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ProfileWebController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $slug = $request->segment(2);

        try {
            $response = app(ProfileController::class)->update($request);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (HttpExceptionInterface $e) {
            return back()->withErrors(['general' => $e->getMessage() ?: 'Erreur'])->withInput();
        }

        if ($response->getStatusCode() >= 400) {
            return $this->backWithJsonOrGenericError($response);
        }

        $afterUpdate = $this->redirectAfterProfileUpdate($request, $slug);
        if ($afterUpdate !== null) {
            return $afterUpdate;
        }

        return redirect()
            ->route('app.'.$slug.'.profile')
            ->with('status', 'Profil enregistré.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $slug = $request->segment(2);

        try {
            $response = app(ProfileController::class)->updatePassword($request);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (HttpExceptionInterface $e) {
            return back()->withErrors(['general' => $e->getMessage() ?: 'Erreur'])->withInput();
        }

        if ($response->getStatusCode() >= 400) {
            $payload = json_decode($response->getContent(), true);
            $msg = is_array($payload) && ! empty($payload['message'])
                ? (string) $payload['message']
                : 'Impossible de mettre à jour le mot de passe.';

            return back()->withErrors(['current_password' => $msg])->withInput();
        }

        if ((string) $request->input('redirect_to', '') === 'password') {
            return redirect()
                ->route('app.'.$slug.'.profile.password.page')
                ->with('status', 'Mot de passe mis à jour.');
        }

        return redirect()
            ->route('app.'.$slug.'.profile')
            ->with('status', 'Mot de passe mis à jour.');
    }

    public function uploadAvatar(Request $request): RedirectResponse
    {
        $slug = $request->segment(2);

        try {
            $response = app(ProfileController::class)->uploadAvatar($request);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (HttpExceptionInterface $e) {
            return back()->withErrors(['avatar' => $e->getMessage() ?: 'Erreur'])->withInput();
        }

        if ($response->getStatusCode() >= 400) {
            $payload = json_decode($response->getContent(), true);
            $msg = is_array($payload) && ! empty($payload['message'])
                ? (string) $payload['message']
                : 'Envoi de la photo impossible.';

            return back()->withErrors(['avatar' => $msg]);
        }

        return redirect()
            ->route('app.'.$slug.'.profile')
            ->with('status', 'Photo de profil mise à jour.');
    }

    /**
     * Retour optionnel après mise à jour profil (ex. formulaire « Localisation » seul).
     */
    private function redirectAfterProfileUpdate(Request $request, string $slug): ?RedirectResponse
    {
        if ((string) $request->input('redirect_to', '') !== 'location') {
            return null;
        }
        if (! in_array($slug, ['batiment', 'fournisseur'], true)) {
            return null;
        }

        return redirect()
            ->route('app.'.$slug.'.profile.location.page')
            ->with('status', 'Localisation enregistrée.');
    }

    private function backWithJsonOrGenericError(JsonResponse $response): RedirectResponse
    {
        $payload = json_decode($response->getContent(), true);
        if (is_array($payload) && isset($payload['errors']) && is_array($payload['errors'])) {
            return back()->withErrors($payload['errors'])->withInput();
        }
        $msg = is_array($payload) && isset($payload['message'])
            ? (string) $payload['message']
            : 'Erreur lors de l’enregistrement.';

        return back()->withErrors(['general' => $msg])->withInput();
    }
}
