<?php

namespace App\Http\Controllers\Web\App;

use App\Http\Controllers\Api\Me\MessageController;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MessageSendController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $slug = $request->segment(2);

        try {
            $response = app(MessageController::class)->store($request);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        if ($response->getStatusCode() >= 400) {
            $payload = json_decode($response->getContent(), true);
            $msg = is_array($payload) && isset($payload['message']) ? (string) $payload['message'] : 'Envoi impossible.';

            return back()->withErrors(['body' => $msg])->withInput();
        }

        $receiverId = (int) $request->input('receiver_id');

        return redirect()
            ->route('app.'.$slug.'.messages', ['peer_id' => $receiverId])
            ->with('status', 'Message envoyé.');
    }
}
