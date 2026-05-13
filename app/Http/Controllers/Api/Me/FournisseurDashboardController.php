<?php

namespace App\Http\Controllers\Api\Me;

use App\Http\Controllers\Controller;
use App\Models\Devis;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Tableau de bord entreprise fournisseur — commandes (devis dont le prestataire est le fournisseur).
 */
class FournisseurDashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $u = $request->user();
        abort_unless($u->profile_type === User::PROFILE_ENTREPRISE_FOURNISSEUR, 403);

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

        $base = Devis::query()->where('user_id', $uid);

        $totalCur = (clone $base)->whereBetween('created_at', [$rangeStart, $rangeEnd])->count();
        $totalPrev = (clone $base)->whereBetween('created_at', [$prevRangeStart, $prevRangeEnd])->count();

        $traiteCur = (clone $base)->whereBetween('created_at', [$rangeStart, $rangeEnd])
            ->whereIn('status', ['valide', 'envoye'])
            ->count();
        $traitePrev = (clone $base)->whereBetween('created_at', [$prevRangeStart, $prevRangeEnd])
            ->whereIn('status', ['valide', 'envoye'])
            ->count();

        $enCoursCur = (clone $base)->whereBetween('created_at', [$rangeStart, $rangeEnd])
            ->where('status', 'en_cours')
            ->count();
        $enCoursPrev = (clone $base)->whereBetween('created_at', [$prevRangeStart, $prevRangeEnd])
            ->where('status', 'en_cours')
            ->count();

        $caCur = $this->sumCaInRange($uid, $rangeStart, $rangeEnd);
        $caPrev = $this->sumCaInRange($uid, $prevRangeStart, $prevRangeEnd);

        $pieBase = (clone $base)->whereBetween('created_at', [$rangeStart, $rangeEnd]);
        $pTraite = (clone $pieBase)->whereIn('status', ['valide', 'envoye'])->count();
        $pEncours = (clone $pieBase)->where('status', 'en_cours')->count();
        $pAttente = (clone $pieBase)->whereIn('status', ['non_traite', 'rejete'])->count();
        $pieTotal = $pTraite + $pEncours + $pAttente;

        $pie = [
            [
                'key' => 'traite',
                'label' => 'Traitées',
                'count' => $pTraite,
                'percent' => $pieTotal > 0 ? round($pTraite / $pieTotal * 100, 1) : 0.0,
            ],
            [
                'key' => 'en_cours',
                'label' => 'En cours',
                'count' => $pEncours,
                'percent' => $pieTotal > 0 ? round($pEncours / $pieTotal * 100, 1) : 0.0,
            ],
            [
                'key' => 'attente',
                'label' => 'Non traitées',
                'count' => $pAttente,
                'percent' => $pieTotal > 0 ? round($pAttente / $pieTotal * 100, 1) : 0.0,
            ],
        ];

        $salesByMonth = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = $now->copy()->subMonths($i)->startOfMonth();
            $mEnd = $m->copy()->endOfMonth();
            $cnt = (clone $base)->whereBetween('created_at', [$m, $mEnd])->count();
            $salesByMonth[] = [
                'year' => $m->year,
                'month' => $m->month,
                'label' => $this->monthShortFr($m->month),
                'count' => $cnt,
            ];
        }

        return response()->json([
            'period' => $period,
            'kpi_labels' => [
                'total_commandes' => 'Total commandes',
                'commandes_traitees' => 'Commandes traitées',
                'commandes_en_cours' => 'Commandes en cours',
                'ca_fcfa' => 'Chiffre d’affaires (FCFA)',
            ],
            'kpis' => [
                'total_commandes' => $this->kpiPayload($totalCur, $totalPrev),
                'commandes_traitees' => $this->kpiPayload($traiteCur, $traitePrev),
                'commandes_en_cours' => $this->kpiPayload($enCoursCur, $enCoursPrev),
                'ca_fcfa' => $this->kpiPayloadMoney($caCur, $caPrev),
            ],
            'pie' => $pie,
            'sales_by_month' => $salesByMonth,
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

    /**
     * @return array{value: int, trend_pct: int, trend_up: bool}
     */
    private function kpiPayloadMoney(int $current, int $previous): array
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

    private function sumCaInRange(int $userId, Carbon $start, Carbon $end): int
    {
        $rows = Devis::query()
            ->where('user_id', $userId)
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'valide')
            ->get(['line_items']);

        $sum = 0;
        foreach ($rows as $d) {
            $sum += $this->sumLineItemsTotal($d->line_items);
        }

        return $sum;
    }

    /**
     * @param  array<string, mixed>|null  $items
     */
    private function sumLineItemsTotal(?array $items): int
    {
        if ($items === null || $items === []) {
            return 0;
        }
        if (! empty($items['lignes']) && is_array($items['lignes'])) {
            $t = 0;
            foreach ($items['lignes'] as $line) {
                if (! is_array($line)) {
                    continue;
                }
                $t += (int) ($line['line_total_fcfa'] ?? $line['total'] ?? 0);
            }

            return $t;
        }
        $t = 0;
        foreach ($items as $line) {
            if (! is_array($line)) {
                continue;
            }
            $t += (int) ($line['total'] ?? $line['line_total_fcfa'] ?? 0);
        }

        return $t;
    }

    private function monthShortFr(int $month): string
    {
        return match ($month) {
            1 => 'Jan',
            2 => 'Fév',
            3 => 'Mar',
            4 => 'Avr',
            5 => 'Mai',
            6 => 'Juin',
            7 => 'Juil',
            8 => 'Août',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Déc',
            default => '?',
        };
    }
}
