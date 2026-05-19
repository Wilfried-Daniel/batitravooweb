<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Service;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\View\View;

class PendingController extends Controller
{
    /**
     * Synthèse des files d’attente (lien depuis la cloche du header).
     */
    public function __invoke(): View
    {
        $pendingProfiles = User::query()
            ->where('role', User::ROLE_USER)
            ->whereNotNull('profile_completed_at')
            ->where('profile_validation_status', User::VALIDATION_PENDING)
            ->count();

        $pendingProducts = Product::query()->where('status', 'pending')->count();

        $supportActive = SupportTicket::query()
            ->whereIn('status', [
                SupportTicket::STATUS_OPEN,
                SupportTicket::STATUS_IN_PROGRESS,
            ])
            ->count();

        return view('admin.pending', [
            'pendingProfiles' => $pendingProfiles,
            'pendingProducts' => $pendingProducts,
            'supportActive' => $supportActive,
        ]);
    }
}
