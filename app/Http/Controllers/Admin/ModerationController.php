<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\View\View;

class ModerationController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.moderation', [
            'pendingProducts' => Product::query()->with('user')->where('status', 'pending')->latest()->limit(15)->get(),
            'counts' => [
                'products_pending' => Product::where('status', 'pending')->count(),
            ],
        ]);
    }
}
