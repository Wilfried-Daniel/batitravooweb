<?php

namespace App\Http\Controllers\Web\App;

use App\Http\Controllers\Api\Me\BesoinController as ApiMeBesoinController;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BesoinManageController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $slug = (string) $request->segment(2);
        $response = app(ApiMeBesoinController::class)->store($request);
        if ($response->getStatusCode() !== 201) {
            abort($response->getStatusCode());
        }

        return redirect()->route('app.'.$slug.'.besoins')->with('status', 'Votre besoin a été publié.');
    }
}
