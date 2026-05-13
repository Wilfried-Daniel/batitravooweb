<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $q = Message::query()->with(['sender', 'receiver']);

        if ($search = $request->string('q')->trim()) {
            $q->where('body', 'like', "%{$search}%");
        }

        $messages = $q->orderByDesc('created_at')->paginate(30)->withQueryString();

        return view('admin.messages.index', ['messages' => $messages]);
    }
}
