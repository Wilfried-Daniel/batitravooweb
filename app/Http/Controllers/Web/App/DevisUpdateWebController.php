<?php

namespace App\Http\Controllers\Web\App;

use App\Http\Controllers\Api\Me\DevisController as ApiMeDevisController;
use App\Http\Controllers\Controller;
use App\Models\Devis;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DevisUpdateWebController extends Controller
{
    public function __invoke(Request $request, Devis $devis): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', 'string', Rule::in(['non_traite', 'en_cours', 'envoye', 'valide', 'rejete'])],
            'notes' => ['nullable', 'string', 'max:10000'],
            'order_reference' => ['nullable', 'string', 'max:64'],
        ]);

        $data = $validated;
        if (($data['status'] ?? '') === '') {
            unset($data['status']);
        }

        $request->merge($data);

        $response = app(ApiMeDevisController::class)->update($request, $devis);

        if ($response->getStatusCode() >= 400) {
            $payload = json_decode($response->getContent(), true);

            return redirect()->back()->withInput()->withErrors([
                'devis_update' => is_array($payload) && isset($payload['message'])
                    ? (string) $payload['message']
                    : 'Impossible de mettre à jour le devis.',
            ]);
        }

        return redirect()->back()->with('status', 'Devis mis à jour.');
    }
}
