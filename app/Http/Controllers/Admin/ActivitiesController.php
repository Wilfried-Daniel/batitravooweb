<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Besoin;
use App\Models\Product;
use App\Models\Service;
use Illuminate\View\View;

class ActivitiesController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.activities', [
            'recentBesoins' => Besoin::query()->with('user')->latest()->limit(8)->get(),
            'recentServices' => Service::query()->with('user')->latest()->limit(8)->get(),
            'recentProducts' => Product::query()->with('user')->latest()->limit(8)->get(),
        ]);
    }
}
