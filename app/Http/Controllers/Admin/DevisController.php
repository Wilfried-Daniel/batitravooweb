<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Devis;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DevisController extends Controller
{
    public function index(Request $request): View
    {
        $q = Devis::query()->with('user');
        if ($request->filled('status')) {
            $q->where('status', $request->string('status'));
        }
        if ($search = $request->string('q')->trim()) {
            $q->where(function ($sub) use ($search) {
                $sub->where('title', 'like', "%{$search}%")
                    ->orWhere('client_name', 'like', "%{$search}%")
                    ->orWhere('order_reference', 'like', "%{$search}%");
            });
        }
        $devis = $q->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.devis.index', ['devis' => $devis]);
    }

    public function show(Devis $devis): View
    {
        $devis->load('user');

        return view('admin.devis.show', ['devis' => $devis]);
    }

    public function update(Request $request, Devis $devis): RedirectResponse
    {
        $data = $request->validate([
            'status' => [
                'required',
                Rule::in(['non_traite', 'en_cours', 'envoye', 'valide', 'rejete']),
            ],
            'processed_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);
        $devis->update($data);

        return redirect()->route('admin.devis.show', $devis)->with('ok', 'Devis mis à jour.');
    }
}
