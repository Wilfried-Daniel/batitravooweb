<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Besoin;
use App\Models\Candidature;
use App\Models\Devis;
use App\Models\Product;
use App\Models\Service;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $usersByProfile = User::query()
            ->where('role', User::ROLE_USER)
            ->selectRaw('profile_type, count(*) as c')
            ->groupBy('profile_type')
            ->pluck('c', 'profile_type');

        return view('admin.dashboard', [
            'counts' => [
                'users' => User::where('role', User::ROLE_USER)->count(),
                'products' => Product::count(),
                'services' => Service::count(),
                'devis' => Devis::count(),
                'besoins' => Besoin::count(),
                'candidatures' => Candidature::count(),
                'pending_products' => Product::where('status', 'pending')->count(),
                'pending_services' => Service::where('status', 'pending')->count(),
                'pending_profile_validation' => User::query()
                    ->where('role', User::ROLE_USER)
                    ->whereNotNull('profile_completed_at')
                    ->where('profile_validation_status', User::VALIDATION_PENDING)
                    ->count(),
                'support_tickets_active' => SupportTicket::query()
                    ->whereIn('status', [
                        SupportTicket::STATUS_OPEN,
                        SupportTicket::STATUS_IN_PROGRESS,
                    ])
                    ->count(),
            ],
            'usersByProfile' => $usersByProfile,
            'recentProducts' => Product::query()->with('user')->latest()->limit(5)->get(),
            'recentServices' => Service::query()->with('user')->latest()->limit(5)->get(),
        ]);
    }
}
