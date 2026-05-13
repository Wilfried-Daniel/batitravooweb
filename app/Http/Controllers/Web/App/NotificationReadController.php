<?php

namespace App\Http\Controllers\Web\App;

use App\Http\Controllers\Api\Me\NotificationController;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationReadController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $slug = $request->segment(2);

        $response = app(NotificationController::class)->markAllRead($request);
        if ($response->getStatusCode() >= 400) {
            return redirect()
                ->route('app.'.$slug.'.notifications')
                ->with('status', 'Action impossible.');
        }

        return redirect()
            ->route('app.'.$slug.'.notifications')
            ->with('status', 'Toutes les notifications sont marquées comme lues.');
    }
}
