<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InAppNotification;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SupportTicketController extends Controller
{
    public function index(Request $request): View
    {
        $q = SupportTicket::query()->with(['user', 'assignedTo']);

        if ($status = $request->string('status')->trim()) {
            if (in_array($status, [
                SupportTicket::STATUS_OPEN,
                SupportTicket::STATUS_IN_PROGRESS,
                SupportTicket::STATUS_RESOLVED,
                SupportTicket::STATUS_CLOSED,
            ], true)) {
                $q->where('status', $status);
            }
        }

        if ($search = $request->string('q')->trim()) {
            $q->where(function ($sub) use ($search) {
                $sub->where('subject', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $tickets = $q->orderByDesc('id')->paginate(25)->withQueryString();

        return view('admin.support.tickets.index', [
            'tickets' => $tickets,
        ]);
    }

    public function show(SupportTicket $ticket): View
    {
        $ticket->load(['messages.user', 'user', 'assignedTo']);

        $admins = User::query()->where('role', User::ROLE_ADMIN)->orderBy('name')->get(['id', 'name', 'email']);

        return view('admin.support.tickets.show', [
            'ticket' => $ticket,
            'admins' => $admins,
        ]);
    }

    public function update(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', Rule::in([
                SupportTicket::STATUS_OPEN,
                SupportTicket::STATUS_IN_PROGRESS,
                SupportTicket::STATUS_RESOLVED,
                SupportTicket::STATUS_CLOSED,
            ])],
            'priority' => ['required', 'string', Rule::in([
                SupportTicket::PRIORITY_LOW,
                SupportTicket::PRIORITY_NORMAL,
                SupportTicket::PRIORITY_HIGH,
            ])],
            'assigned_to_user_id' => ['nullable', 'exists:users,id'],
        ]);

        if ($data['assigned_to_user_id'] !== null) {
            $assignee = User::query()->find($data['assigned_to_user_id']);
            if ($assignee === null || ! $assignee->isAdmin()) {
                return back()->with('error', 'L’assignation doit être un compte administrateur.');
            }
        }

        $prevStatus = $ticket->status;

        $ticket->status = $data['status'];
        $ticket->priority = $data['priority'];
        $ticket->assigned_to_user_id = $data['assigned_to_user_id'];
        if (in_array($data['status'], [SupportTicket::STATUS_RESOLVED, SupportTicket::STATUS_CLOSED], true)) {
            $ticket->closed_at = $ticket->closed_at ?? now();
        } else {
            $ticket->closed_at = null;
        }
        $ticket->save();

        if ($prevStatus !== $ticket->status) {
            $this->notifyTicketOwner($ticket, 'Statut du ticket mis à jour', 'Votre demande « '.$ticket->subject.' » est passée en : '.$ticket->status.'.');
        }

        return redirect()->route('admin.support.tickets.show', $ticket)->with('ok', 'Ticket mis à jour.');
    }

    public function storeMessage(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:20000'],
            'attachment' => ['nullable', 'file', 'max:12288'],
        ]);

        $admin = $request->user();
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('support-tickets', 'public');
        }

        SupportTicketMessage::query()->create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $admin->id,
            'body' => $data['body'],
            'is_staff' => true,
            'attachment_path' => $attachmentPath,
        ]);

        if ($ticket->status === SupportTicket::STATUS_OPEN) {
            $ticket->update(['status' => SupportTicket::STATUS_IN_PROGRESS]);
        }

        $preview = mb_strlen($data['body']) > 140 ? mb_substr($data['body'], 0, 137).'…' : $data['body'];
        $this->notifyTicketOwner($ticket, 'Réponse du support', $preview, [
            'support_ticket_id' => $ticket->id,
        ]);

        return redirect()->route('admin.support.tickets.show', $ticket)->with('ok', 'Réponse envoyée.');
    }

    private function notifyTicketOwner(SupportTicket $ticket, string $title, string $body, array $extraData = []): void
    {
        InAppNotification::query()->create([
            'user_id' => $ticket->user_id,
            'type' => InAppNotification::TYPE_SUPPORT_TICKET,
            'title' => $title,
            'body' => $body,
            'data' => array_merge(['support_ticket_id' => $ticket->id], $extraData),
        ]);
    }
}
