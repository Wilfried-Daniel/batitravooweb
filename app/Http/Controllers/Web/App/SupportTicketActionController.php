<?php

namespace App\Http\Controllers\Web\App;

use App\Http\Controllers\Api\Me\SupportTicketController as ApiMeSupportTicketController;
use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SupportTicketActionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $slug = $request->segment(2);

        try {
            $response = app(ApiMeSupportTicketController::class)->store($request);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        if ($response->getStatusCode() >= 400) {
            return $this->failBack($response);
        }

        $payload = json_decode($response->getContent(), true);
        $id = is_array($payload) && isset($payload['data']['id']) ? (int) $payload['data']['id'] : null;
        if ($id === null) {
            return back()->withErrors(['subject' => 'Réponse API invalide.'])->withInput();
        }

        $ticket = SupportTicket::query()->findOrFail($id);

        return redirect()
            ->route('app.'.$slug.'.support.show', ['ticket' => $ticket->id])
            ->with('status', 'Ticket créé.');
    }

    public function reply(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $slug = $request->segment(2);

        abort_unless($ticket->user_id === $request->user()->id, 404);

        try {
            $response = app(ApiMeSupportTicketController::class)->storeMessage($request, $ticket);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        if ($response->getStatusCode() >= 400) {
            return $this->failBack($response);
        }

        return back()->with('status', 'Message ajouté.');
    }

    private function failBack(JsonResponse $response): RedirectResponse
    {
        $payload = json_decode($response->getContent(), true);
        $msg = is_array($payload) && isset($payload['message']) ? (string) $payload['message'] : 'Erreur.';

        return back()->withErrors(['body' => $msg])->withInput();
    }
}
