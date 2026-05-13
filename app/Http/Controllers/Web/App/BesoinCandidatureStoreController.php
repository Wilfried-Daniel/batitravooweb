<?php

namespace App\Http\Controllers\Web\App;

use App\Http\Controllers\Api\Me\CandidatureController as ApiMeCandidatureController;
use App\Http\Controllers\Controller;
use App\Models\Besoin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BesoinCandidatureStoreController extends Controller
{
    public function __invoke(Request $request, Besoin $besoin): RedirectResponse
    {
        $slug = (string) $request->segment(2);

        $request->merge(['besoin_id' => $besoin->id]);

        $request->validate([
            'besoin_id' => ['required', 'integer'],
            'message' => ['nullable', 'string', 'max:10000'],
            'display_name' => ['nullable', 'string', 'max:255'],
            'profession' => ['nullable', 'string', 'max:255'],
        ]);

        $response = app(ApiMeCandidatureController::class)->store($request);

        if ($response->getStatusCode() >= 400) {
            $payload = json_decode($response->getContent(), true);

            return redirect()->back()->withInput()->withErrors([
                'besoin_apply' => is_array($payload) && isset($payload['message'])
                    ? (string) $payload['message']
                    : 'Impossible d’envoyer la candidature.',
            ]);
        }

        return redirect()->back()->with('status', 'Candidature envoyée.');
    }
}
