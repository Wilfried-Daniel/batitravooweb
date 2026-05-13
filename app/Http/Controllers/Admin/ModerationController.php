<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Service;
use Illuminate\View\View;

class ModerationController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.moderation', [
            'pendingProducts' => Product::query()->with('user')->where('status', 'pending')->latest()->limit(15)->get(),
            'pendingServices' => Service::query()->with('user')->where('status', 'pending')->latest()->limit(15)->get(),
            'counts' => [
                'products_pending' => Product::where('status', 'pending')->count(),
                'services_pending' => Service::where('status', 'pending')->count(),
            ],
        ]);
    }
}
