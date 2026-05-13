<?php

namespace App\Http\Controllers\Api\Me;

use App\Http\Controllers\Controller;
use App\Models\Besoin;
use App\Models\Devis;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Graphiques onglet Dashboard entrepreneur bâtiment (répartition services, séries devis / besoins).
 */
class BatimentDashboardAnalyticsController extends Controller
{
    private const MONTH_LABELS_FR = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];

    public function __invoke(Request $request): JsonResponse
    {
        $u = $request->user();
        abort_unless($u->profile_type === User::PROFILE_ENTREPRENEUR_BATIMENT, 403);

        $period = $request->query('period', 'month');
        if (! in_array($period, ['month', 'year'], true)) {
            $period = 'month';
        }

        $uid = $u->id;
        $now = Carbon::now();

        $servicesSplit = $this->servicesSplit($uid, $period, $now);

        if ($period === 'year') {
            $devisSeries = $this->monthlySeriesYear(
                fn (Carbon $start, Carbon $end) => $this->countDevisBetween($uid, $start, $end),
                $now
            );
            $besoinsSeries = $this->monthlySeriesYear(
                fn (Carbon $start, Carbon $end) => $this->countBesoinsBetween($uid, $start, $end),
                $now
            );
            $servicesActifsSeries = $this->monthlySeriesYearApprovedServicesCreated($uid, $now);
        } else {
            $devisSeries = $this->dailySeriesLast7Days(
                fn (Carbon $day) => $this->countDevisOnDay($uid, $day)
            );
            $besoinsSeries = $this->dailySeriesLast7Days(
                fn (Carbon $day) => $this->countBesoinsOnDay($uid, $day)
            );
            $servicesActifsSeries = $this->dailySeriesLast7Days(
                fn (Carbon $day) => $this->countApprovedServicesOnDay($uid, $day)
            );
        }

        return response()->json([
            'period' => $period,
            'services_split' => $servicesSplit,
            'devis_series' => $devisSeries,
            'besoins_series' => $besoinsSeries,
            'services_actifs_series' => $servicesActifsSeries,
        ]);
    }

    /**
     * Services créés sur la période — « actifs » = approuvés, le reste = non disponibles (pending/rejected).
     *
     * @return array{total: int, actifs: int, non_disponibles: int}
     */
    private function servicesSplit(int $uid, string $period, Carbon $now): array
    {
        $start = $period === 'year'
            ? $now->copy()->startOfYear()
            : $now->copy()->startOfMonth();
        $end = $now->copy()->endOfDay();

        $base = Service::query()
            ->where('user_id', $uid)
            ->whereBetween('created_at', [$start, $end]);

        $total = (int) (clone $base)->count();
        $approved = (int) (clone $base)->where('status', 'approved')->count();
        $non = max(0, $total - $approved);

        return [
            'total' => $total,
            'actifs' => $approved,
            'non_disponibles' => $non,
        ];
    }

    private function countDevisBetween(int $uid, Carbon $start, Carbon $end): int
    {
        return (int) Devis::query()
            ->where('user_id', $uid)
            ->whereBetween('created_at', [$start, $end])
            ->count();
    }

    private function countBesoinsBetween(int $uid, Carbon $start, Carbon $end): int
    {
        return (int) Besoin::query()
            ->where('user_id', $uid)
            ->whereBetween('created_at', [$start, $end])
            ->count();
    }

    private function countDevisOnDay(int $uid, Carbon $day): int
    {
        return (int) Devis::query()
            ->where('user_id', $uid)
            ->whereDate('created_at', $day->toDateString())
            ->count();
    }

    private function countBesoinsOnDay(int $uid, Carbon $day): int
    {
        return (int) Besoin::query()
            ->where('user_id', $uid)
            ->whereDate('created_at', $day->toDateString())
            ->count();
    }

    private function countApprovedServicesOnDay(int $uid, Carbon $day): int
    {
        return (int) Service::query()
            ->where('user_id', $uid)
            ->where('status', 'approved')
            ->whereDate('created_at', $day->toDateString())
            ->count();
    }

    /**
     * @return array{labels: array<int, string>, values: array<int, float>}
     */
    private function dailySeriesLast7Days(callable $counter): array
    {
        $labels = [];
        $values = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i)->startOfDay();
            $labels[] = $this->shortWeekdayFr($day);
            $values[] = (float) $counter($day);
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    /**
     * @return array{labels: array<int, string>, values: array<int, float>}
     */
    private function monthlySeriesYear(callable $counter, Carbon $now): array
    {
        $yearStart = $now->copy()->startOfYear();
        $labels = [];
        $values = [];
        $endToday = $now->copy()->endOfDay();

        for ($m = 0; $m < 12; $m++) {
            $monthStart = $yearStart->copy()->addMonths($m)->startOfMonth();
            $monthEnd = $yearStart->copy()->addMonths($m)->endOfMonth();
            $labels[] = self::MONTH_LABELS_FR[$m];

            if ($monthStart->gt($endToday)) {
                $values[] = 0.0;

                continue;
            }
            if ($monthEnd->gt($endToday)) {
                $monthEnd = $endToday->copy();
            }

            $values[] = (float) $counter($monthStart, $monthEnd);
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    /**
     * @return array{labels: array<int, string>, values: array<int, float>}
     */
    private function monthlySeriesYearApprovedServicesCreated(int $uid, Carbon $now): array
    {
        $yearStart = $now->copy()->startOfYear();
        $labels = [];
        $values = [];
        $endToday = $now->copy()->endOfDay();

        for ($m = 0; $m < 12; $m++) {
            $monthStart = $yearStart->copy()->addMonths($m)->startOfMonth();
            $monthEnd = $yearStart->copy()->addMonths($m)->endOfMonth();
            $labels[] = self::MONTH_LABELS_FR[$m];

            if ($monthStart->gt($endToday)) {
                $values[] = 0.0;

                continue;
            }
            if ($monthEnd->gt($endToday)) {
                $monthEnd = $endToday->copy();
            }

            $values[] = (float) Service::query()
                ->where('user_id', $uid)
                ->where('status', 'approved')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    private function shortWeekdayFr(Carbon $d): string
    {
        $map = [
            0 => 'Dim',
            1 => 'Lun',
            2 => 'Mar',
            3 => 'Mer',
            4 => 'Jeu',
            5 => 'Ven',
            6 => 'Sam',
        ];

        return $map[(int) $d->dayOfWeek] ?? $d->format('D');
    }
}
