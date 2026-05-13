<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidature;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CandidatureController extends Controller
{
    public function index(Request $request): View
    {
        $q = Candidature::query()->with(['besoin', 'applicant']);
        if ($request->filled('status')) {
            $q->where('status', $request->string('status'));
        }
        $candidatures = $q->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        return view('admin.candidatures.index', ['candidatures' => $candidatures]);
    }

    public function update(Request $request, Candidature $candidature): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['recu', 'accepte', 'rejete'])],
        ]);
        $candidature->update($data);

        $candidature->load('besoin');

        return redirect()->route('admin.besoins.show', $candidature->besoin)
            ->with('ok', 'Candidature mise à jour.');
    }
}
