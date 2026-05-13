<?php

namespace App\Http\Controllers\Web\App;

use App\Models\ArtisanBusinessCard;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ArtisanBusinessCardWebController extends ShellController
{
    public function edit(Request $request): View
    {
        abort_unless($request->user()->profile_type === User::PROFILE_ARTISAN, 403);

        $card = ArtisanBusinessCard::query()->where('user_id', $request->user()->id)->first();

        return $this->render($request, 'artisan_carte_visite', [
            'businessCard' => $card,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $u = $request->user();
        abort_unless($u->profile_type === User::PROFILE_ARTISAN, 403);

        $validated = $request->validate([
            'display_name' => ['nullable', 'string', 'max:255'],
            'profession' => ['nullable', 'string', 'max:255'],
            'experience_text' => ['nullable', 'string', 'max:255'],
            'price_text' => ['nullable', 'string', 'max:255'],
            'services' => ['nullable', 'array', 'max:30'],
            'services.*' => ['string', 'max:500'],
            'location_text' => ['nullable', 'string', 'max:500'],
            'portfolio' => ['nullable', 'file', 'max:15360', 'mimes:jpg,jpeg,png,webp,pdf'],
        ]);

        $card = ArtisanBusinessCard::query()->firstOrNew(['user_id' => $u->id]);

        unset($validated['portfolio']);
        $card->fill($validated);
        $card->price_on_request = $request->boolean('price_on_request');
        $card->price_on_quote = $request->boolean('price_on_quote');
        $card->avail_immediate = $request->boolean('avail_immediate');
        $card->avail_appointment = $request->boolean('avail_appointment');
        $card->avail_unavailable = $request->boolean('avail_unavailable');

        if ($request->has('services')) {
            $card->services = array_values(array_filter(
                $request->input('services', []),
                fn ($s) => is_string($s) && trim($s) !== ''
            ));
        }

        if ($request->hasFile('portfolio')) {
            $pf = $request->file('portfolio');
            if ($pf !== null && $pf->isValid()) {
                if ($card->portfolio_path) {
                    Storage::disk('public')->delete($card->portfolio_path);
                }
                $card->portfolio_path = $pf->store('artisan_portfolio/'.$u->id, 'public');
            }
        }

        $card->user_id = $u->id;
        $card->save();

        return redirect()->route('app.artisan.business_card')->with('status', 'Carte de visite enregistrée.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $u = $request->user();
        abort_unless($u->profile_type === User::PROFILE_ARTISAN, 403);

        $c = ArtisanBusinessCard::query()->where('user_id', $u->id)->first();
        if ($c) {
            if ($c->portfolio_path) {
                Storage::disk('public')->delete($c->portfolio_path);
            }
            $c->delete();
        }

        return redirect()->route('app.artisan.business_card')->with('status', 'Carte de visite supprimée.');
    }
}
