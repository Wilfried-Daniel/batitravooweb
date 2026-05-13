<?php

namespace App\Http\Controllers\Web\App;

use App\Http\Controllers\Api\Me\CandidatureController as ApiMeCandidatureController;
use App\Http\Controllers\Controller;
use App\Models\Candidature;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CandidatureManageController extends Controller
{
    public function update(Request $request, Candidature $candidature): RedirectResponse
    {
        $response = app(ApiMeCandidatureController::class)->update($request, $candidature);
        if ($response->getStatusCode() >= 400) {
            abort($response->getStatusCode());
        }

        return redirect()->back()->with('status', 'Statut de la candidature mis à jour.');
    }
}
