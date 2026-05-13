<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Besoin;
use App\Models\Devis;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __invoke(): View
    {
        $devisByStatus = Devis::query()
            ->selectRaw('status, count(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        $productsByStatus = Product::query()
            ->selectRaw('status, count(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        $servicesByStatus = Service::query()
            ->selectRaw('status, count(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        $besoinsByStatus = Besoin::query()
            ->selectRaw('status, count(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        $monthExpr = match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m', created_at)",
            default => "DATE_FORMAT(created_at, '%Y-%m')",
        };
        $userGrowth = User::query()
            ->where('role', User::ROLE_USER)
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw($monthExpr.' as m, count(*) as c')
            ->groupByRaw($monthExpr)
            ->orderBy('m')
            ->get();

        return view('admin.reports', compact(
            'devisByStatus',
            'productsByStatus',
            'servicesByStatus',
            'besoinsByStatus',
            'userGrowth'
        ));
    }
}
