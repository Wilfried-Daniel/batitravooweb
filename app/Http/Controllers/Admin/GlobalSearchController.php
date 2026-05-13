<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Besoin;
use App\Models\Devis;
use App\Models\Product;
use App\Models\Service;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GlobalSearchController extends Controller
{
    /**
     * Recherche minimaliste sur plusieurs entités (nom / titre / e-mail).
     */
    public function __invoke(Request $request): View
    {
        $qRaw = $request->string('q')->trim();
        $q = mb_substr($qRaw, 0, 120);

        $users = collect();
        $products = collect();
        $services = collect();
        $devis = collect();
        $besoins = collect();
        $tickets = collect();

        if (mb_strlen($q) >= 2) {
            $like = '%'.$q.'%';

            $users = User::query()
                ->where('role', User::ROLE_USER)
                ->where(function ($sub) use ($like) {
                    $sub->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('phone', 'like', $like);
                })
                ->orderBy('name')
                ->limit(20)
                ->get();

            $products = Product::query()
                ->where('title', 'like', $like)
                ->with('user')
                ->orderByDesc('id')
                ->limit(15)
                ->get();

            $services = Service::query()
                ->where('title', 'like', $like)
                ->with('user')
                ->orderByDesc('id')
                ->limit(15)
                ->get();

            $devis = Devis::query()
                ->where(function ($sub) use ($like) {
                    $sub->where('title', 'like', $like)
                        ->orWhere('client_name', 'like', $like)
                        ->orWhere('order_reference', 'like', $like);
                })
                ->with('user')
                ->orderByDesc('id')
                ->limit(15)
                ->get();

            $besoins = Besoin::query()
                ->where('title', 'like', $like)
                ->with('user')
                ->orderByDesc('id')
                ->limit(15)
                ->get();

            $tickets = SupportTicket::query()
                ->where('subject', 'like', $like)
                ->with('user')
                ->orderByDesc('id')
                ->limit(10)
                ->get();
        }

        $hasQuery = mb_strlen($q) >= 2;
        $total = $users->count() + $products->count() + $services->count()
            + $devis->count() + $besoins->count() + $tickets->count();

        return view('admin.search', [
            'q' => $q,
            'hasQuery' => $hasQuery,
            'total' => $total,
            'users' => $users,
            'products' => $products,
            'services' => $services,
            'devis' => $devis,
            'besoins' => $besoins,
            'tickets' => $tickets,
        ]);
    }
}
