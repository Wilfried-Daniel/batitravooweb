<?php

namespace App\Http\Controllers\Api\Me;

use App\Http\Controllers\Controller;
use App\Models\ArtisanBusinessCard;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArtisanBusinessCardController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $u = $request->user();
        abort_unless($u->profile_type === User::PROFILE_ARTISAN, 403);

        $c = ArtisanBusinessCard::query()->where('user_id', $u->id)->first();

        return response()->json([
            'data' => $c ? $this->row($c) : null,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $u = $request->user();
        abort_unless($u->profile_type === User::PROFILE_ARTISAN, 403);

        $validated = $request->validate([
            'display_name' => ['nullable', 'string', 'max:255'],
            'profession' => ['nullable', 'string', 'max:255'],
            'experience_text' => ['nullable', 'string', 'max:255'],
            'price_on_request' => ['sometimes', 'boolean'],
            'price_on_quote' => ['sometimes', 'boolean'],
            'price_text' => ['nullable', 'string', 'max:255'],
            'services' => ['nullable', 'array', 'max:30'],
            'services.*' => ['string', 'max:500'],
            'avail_immediate' => ['sometimes', 'boolean'],
            'avail_appointment' => ['sometimes', 'boolean'],
            'avail_unavailable' => ['sometimes', 'boolean'],
            'location_text' => ['nullable', 'string', 'max:500'],
            'portfolio' => ['nullable', 'file', 'max:15360', 'mimes:jpg,jpeg,png,webp,pdf'],
        ]);

        $card = ArtisanBusinessCard::query()->firstOrNew(['user_id' => $u->id]);
        unset($validated['portfolio']);
        $card->fill($validated);
        if (array_key_exists('services', $validated)) {
            $card->services = $validated['services'] ?? [];
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

        return response()->json(['data' => $this->row($card->fresh())]);
    }

    public function destroy(Request $request): JsonResponse
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

        return response()->json(['ok' => true]);
    }

    /**
     * @return array<string, mixed>
     */
    private function row(ArtisanBusinessCard $c): array
    {
        $portfolioUrl = $c->portfolio_path
            ? storage_public_url($c->portfolio_path)
            : null;

        return [
            'id' => $c->id,
            'display_name' => $c->display_name,
            'profession' => $c->profession,
            'experience_text' => $c->experience_text,
            'price_on_request' => (bool) $c->price_on_request,
            'price_on_quote' => (bool) $c->price_on_quote,
            'price_text' => $c->price_text,
            'services' => $c->services ?? [],
            'avail_immediate' => (bool) $c->avail_immediate,
            'avail_appointment' => (bool) $c->avail_appointment,
            'avail_unavailable' => (bool) $c->avail_unavailable,
            'location_text' => $c->location_text,
            'portfolio_path' => $c->portfolio_path,
            'portfolio_url' => $portfolioUrl,
            'has_portfolio' => $portfolioUrl !== null,
            'updated_at' => $c->updated_at?->toIso8601String(),
        ];
    }
}
