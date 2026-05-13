<?php

namespace App\Http\Controllers\Api\Me;

use App\Http\Controllers\Controller;
use App\Models\Besoin;
use App\Models\Candidature;
use App\Models\Devis;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * KPI tableau de bord entrepreneur bâtiment (besoins, candidatures, services, devis).
 */
class BatimentDashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $u = $request->user();
        abort_unless($u->profile_type === User::PROFILE_ENTREPRENEUR_BATIMENT, 403);

        $period = $request->query('period', 'month');
        if (! in_array($period, ['month', 'year'], true)) {
            $period = 'month';
        }

        $now = Carbon::now();

        $rangeStart = $period === 'year'
            ? $now->copy()->startOfYear()
            : $now->copy()->startOfMonth();
        $rangeEnd = $now->copy()->endOfDay();

        $prevAnchor = $period === 'year'
            ? $now->copy()->subYear()
            : $now->copy()->subMonth();
        $prevRangeStart = $period === 'year'
            ? $prevAnchor->copy()->startOfYear()
            : $prevAnchor->copy()->startOfMonth();
        $prevRangeEnd = $prevAnchor->copy()->endOfDay();

        $uid = $u->id;

        $besoinsCur = $this->countBesoins($uid, $rangeStart, $rangeEnd);
        $besoinsPrev = $this->countBesoins($uid, $prevRangeStart, $prevRangeEnd);

        $candCur = $this->countCandidaturesRecues($uid, $rangeStart, $rangeEnd);
        $candPrev = $this->countCandidaturesRecues($uid, $prevRangeStart, $prevRangeEnd);

        $srvCur = $this->countServices($uid, $rangeStart, $rangeEnd);
        $srvPrev = $this->countServices($uid, $prevRangeStart, $prevRangeEnd);

        $devisCur = $this->countDevisPrestataire($uid, $rangeStart, $rangeEnd);
        $devisPrev = $this->countDevisPrestataire($uid, $prevRangeStart, $prevRangeEnd);

        return response()->json([
            'period' => $period,
            'kpi_labels' => [
                'besoins_publies' => 'Besoins publiés',
                'candidatures_recues' => 'Candidatures reçues',
                'services_disponibles' => 'Services disponibles',
                'demandes_devis' => 'Demandes de devis',
            ],
            'kpis' => [
                'besoins_publies' => $this->kpiPayload($besoinsCur, $besoinsPrev),
                'candidatures_recues' => $this->kpiPayload($candCur, $candPrev),
                'services_disponibles' => $this->kpiPayload($srvCur, $srvPrev),
                'demandes_devis' => $this->kpiPayload($devisCur, $devisPrev),
            ],
        ]);
    }

    /**
     * @return array{value: int, trend_pct: int, trend_up: bool}
     */
    private function kpiPayload(int $current, int $previous): array
    {
        if ($previous === 0) {
            return [
                'value' => $current,
                'trend_pct' => $current > 0 ? 100 : 0,
                'trend_up' => $current >= $previous,
            ];
        }

        $pct = (int) round(abs($current - $previous) / $previous * 100);

        return [
            'value' => $current,
            'trend_pct' => min(max($pct, 0), 999),
            'trend_up' => $current >= $previous,
        ];
    }

    private function countBesoins(int $userId, Carbon $start, Carbon $end): int
    {
        return (int) Besoin::query()
            ->where('user_id', $userId)
            ->whereBetween('created_at', [$start, $end])
            ->count();
    }

    private function countCandidaturesRecues(int $ownerUserId, Carbon $start, Carbon $end): int
    {
        return (int) Candidature::query()
            ->whereHas('besoin', fn ($q) => $q->where('user_id', $ownerUserId))
            ->whereBetween('created_at', [$start, $end])
            ->count();
    }

    private function countServices(int $userId, Carbon $start, Carbon $end): int
    {
        return (int) Service::query()
            ->where('user_id', $userId)
            ->whereBetween('created_at', [$start, $end])
            ->count();
    }

    /** Demandes de devis reçues en tant que prestataire ([Devis.user_id]). */
    private function countDevisPrestataire(int $userId, Carbon $start, Carbon $end): int
    {
        return (int) Devis::query()
            ->where('user_id', $userId)
            ->whereBetween('created_at', [$start, $end])
            ->count();
    }
}
