<?php

namespace App\Http\Controllers\Web\App;

use App\Http\Controllers\Api\PublicBesoinController;
use App\Http\Controllers\Api\PublicProductController;
use App\Http\Controllers\Api\PublicServiceController;
use App\Http\Controllers\Controller;
use App\Models\Besoin;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use App\Services\Web\MeApiBridge;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketplaceShowController extends Controller
{
    /** @var array<int, string> */
    private const PROFILE_SEGMENTS = ['particulier', 'artisan', 'batiment', 'fournisseur'];

    public function product(Request $request, Product $product): View
    {
        $slug = $this->profileSlugFromRequest($request);

        $response = app(PublicProductController::class)->show($product);
        if ($response->getStatusCode() >= 400) {
            abort(404);
        }
        $payload = json_decode($response->getContent(), true);
        $item = is_array($payload) ? ($payload['data'] ?? null) : null;
        abort_if(! is_array($item), 404);

        return $this->shellView($request, $slug, $item['title'] ?? 'Produit', 'product', $item);
    }

    public function service(Request $request, Service $service): View
    {
        $slug = $this->profileSlugFromRequest($request);

        $response = app(PublicServiceController::class)->show($service);
        if ($response->getStatusCode() >= 400) {
            abort(404);
        }
        $payload = json_decode($response->getContent(), true);
        $item = is_array($payload) ? ($payload['data'] ?? null) : null;
        abort_if(! is_array($item), 404);

        return $this->shellView($request, $slug, $item['title'] ?? 'Service', 'service', $item);
    }

    public function besoin(Request $request, Besoin $besoin): View
    {
        $slug = $this->profileSlugFromRequest($request);

        $response = app(PublicBesoinController::class)->show($besoin);
        if ($response->getStatusCode() >= 400) {
            abort(404);
        }
        $payload = json_decode($response->getContent(), true);
        $item = is_array($payload) ? ($payload['data'] ?? null) : null;
        abort_if(! is_array($item), 404);

        /** @var MeApiBridge $bridge */
        $bridge = app(MeApiBridge::class);

        $ownerId = isset($item['owner']['id']) ? (int) $item['owner']['id'] : (isset($item['user_id']) ? (int) $item['user_id'] : null);
        $me = auth()->id();
        $isMine = $ownerId !== null && $ownerId === (int) $me;
        $besoinStatusOpen = ($item['status'] ?? '') === 'open';

        $eligibleApplicant = false;
        $alreadyApplied = false;
        $user = $request->user();
        if ($user instanceof User) {
            $eligibleApplicant = $besoinStatusOpen && ! $isMine && in_array($user->profile_type, [
                User::PROFILE_ARTISAN,
                User::PROFILE_ENTREPRENEUR_BATIMENT,
                User::PROFILE_ENTREPRISE_FOURNISSEUR,
            ], true);

            if ($eligibleApplicant) {
                try {
                    foreach ($bridge->candidaturesAsApplicant($request) as $c) {
                        if ((int) ($c['besoin_id'] ?? 0) === (int) ($item['id'] ?? 0)) {
                            $alreadyApplied = true;
                            break;
                        }
                    }
                } catch (\Throwable) {
                    // ignore
                }
            }
        }

        $showApplicantForms = $eligibleApplicant && ! $alreadyApplied;
        $isArtisanApplicant = $showApplicantForms && $user instanceof User && $user->profile_type === User::PROFILE_ARTISAN;

        return view('app.shell.marketplace-show', [
            'profileSlug' => $slug,
            'page' => 'marketplace',
            'title' => $item['title'] ?? 'Besoin',
            'marketplaceDetailKind' => 'besoin',
            'marketplaceItem' => $item,
            'unreadNotifications' => $bridge->unreadNotificationsCount($request),
            'besoinShowApplicantForms' => $showApplicantForms,
            'besoinIsArtisanApplicant' => $isArtisanApplicant,
            'besoinAlreadyApplied' => $alreadyApplied,
        ]);
    }

    private function profileSlugFromRequest(Request $request): string
    {
        $seg = (string) $request->segment(2);
        abort_if(! in_array($seg, self::PROFILE_SEGMENTS, true), 404);

        return $seg;
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function shellView(Request $request, string $slug, string $title, string $kind, array $item): View
    {
        /** @var MeApiBridge $bridge */
        $bridge = app(MeApiBridge::class);

        return view('app.shell.marketplace-show', [
            'profileSlug' => $slug,
            'page' => 'marketplace',
            'title' => $title,
            'marketplaceDetailKind' => $kind,
            'marketplaceItem' => $item,
            'unreadNotifications' => $bridge->unreadNotificationsCount($request),
            'besoinShowApplicantForms' => false,
            'besoinIsArtisanApplicant' => false,
            'besoinAlreadyApplied' => false,
        ]);
    }
}
