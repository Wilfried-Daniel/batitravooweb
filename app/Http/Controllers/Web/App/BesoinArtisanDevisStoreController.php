<?php

namespace App\Http\Controllers\Web\App;

use App\Http\Controllers\Api\Me\DevisController as ApiMeDevisController;
use App\Http\Controllers\Controller;
use App\Models\Besoin;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BesoinArtisanDevisStoreController extends Controller
{
    public function __invoke(Request $request, Besoin $besoin): RedirectResponse
    {
        $slug = (string) $request->segment(2);

        abort_unless($request->user()?->profile_type === User::PROFILE_ARTISAN, 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'client_name' => ['required', 'string', 'max:255'],
            'place' => ['nullable', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:10000'],
            'amount_fcfa' => ['nullable', 'integer', 'min:0'],
            'line_label' => ['nullable', 'string', 'max:255'],
        ]);

        $lineItems = null;
        $amount = (int) ($validated['amount_fcfa'] ?? 0);
        if ($amount > 0) {
            $label = trim((string) ($validated['line_label'] ?? '')) ?: 'Prestation';
            $lineItems = [
                'lignes' => [
                    [
                        'label' => $label,
                        'qty' => 1,
                        'line_total_fcfa' => $amount,
                    ],
                ],
                'totals' => ['subtotal_fcfa' => $amount],
            ];
        }

        $request->merge([
            'besoin_id' => $besoin->id,
            'title' => $validated['title'],
            'client_name' => $validated['client_name'],
            'place' => $validated['place'] ?? $besoin->place,
            'notes' => $validated['notes'] ?? null,
            'line_items' => $lineItems,
            'order_reference' => null,
        ]);

        $contact = trim((string) ($validated['contact'] ?? ''));
        if ($contact !== '') {
            $request->merge(['contact' => $contact]);
        }

        $response = app(ApiMeDevisController::class)->store($request);

        if ($response->getStatusCode() >= 400) {
            $payload = json_decode($response->getContent(), true);

            return redirect()->back()->withInput()->withErrors([
                'besoin_devis' => is_array($payload) && isset($payload['message'])
                    ? (string) $payload['message']
                    : 'Impossible d’enregistrer le devis.',
            ]);
        }

        return redirect()->route('app.'.$slug.'.devis')->with('status', 'Devis transmis au client.');
    }
}
