<?php

namespace App\Http\Controllers\Api\Me;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SupportTicketController extends Controller
{
    public function formOptions(Request $request): JsonResponse
    {
        return response()->json([
            'meta' => [
                'priority_options' => SupportTicket::prioritySelectOptions(),
            ],
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $items = SupportTicket::query()
            ->where('user_id', $request->user()->id)
            ->with(['assignedTo:id,name'])
            ->orderByDesc('id')
            ->get()
            ->map(fn (SupportTicket $t) => $this->ticketSummary($t));

        return response()->json(['data' => $items]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:20000'],
            'priority' => ['nullable', 'string', Rule::in(SupportTicket::priorityRuleValues())],
            'attachment' => ['nullable', 'file', 'max:12288'],
        ]);

        $user = $request->user();
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('support-tickets', 'public');
        }

        $ticket = DB::transaction(function () use ($user, $data, $attachmentPath): SupportTicket {
            $ticket = SupportTicket::query()->create([
                'user_id' => $user->id,
                'subject' => $data['subject'],
                'status' => SupportTicket::STATUS_OPEN,
                'priority' => $data['priority'] ?? SupportTicket::PRIORITY_NORMAL,
                'assigned_to_user_id' => null,
                'closed_at' => null,
            ]);

            SupportTicketMessage::query()->create([
                'support_ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'body' => $data['body'],
                'is_staff' => false,
                'attachment_path' => $attachmentPath,
            ]);

            return $ticket;
        });

        $ticket->load(['messages.user', 'assignedTo']);

        return response()->json(['data' => $this->ticketDetail($ticket)], 201);
    }

    public function show(Request $request, SupportTicket $ticket): JsonResponse
    {
        abort_unless($ticket->user_id === $request->user()->id, 404);
        $ticket->load(['messages.user', 'assignedTo']);

        return response()->json(['data' => $this->ticketDetail($ticket)]);
    }

    public function storeMessage(Request $request, SupportTicket $ticket): JsonResponse
    {
        abort_unless($ticket->user_id === $request->user()->id, 404);

        if (in_array($ticket->status, [SupportTicket::STATUS_CLOSED, SupportTicket::STATUS_RESOLVED], true)) {
            return response()->json(['message' => 'Ce ticket est clos ; vous ne pouvez plus répondre.'], 422);
        }

        $data = $request->validate([
            'body' => ['required', 'string', 'max:20000'],
            'attachment' => ['nullable', 'file', 'max:12288'],
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('support-tickets', 'public');
        }

        SupportTicketMessage::query()->create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'body' => $data['body'],
            'is_staff' => false,
            'attachment_path' => $attachmentPath,
        ]);

        if ($ticket->status === SupportTicket::STATUS_OPEN) {
            $ticket->update(['status' => SupportTicket::STATUS_IN_PROGRESS]);
        }

        $ticket->load(['messages.user', 'assignedTo']);

        return response()->json(['data' => $this->ticketDetail($ticket)]);
    }

    /**
     * @return array<string, mixed>
     */
    private function ticketSummary(SupportTicket $t): array
    {
        return [
            'id' => $t->id,
            'subject' => $t->subject,
            'status' => $t->status,
            'priority' => $t->priority,
            'assigned_to' => $t->assignedTo ? [
                'id' => $t->assignedTo->id,
                'name' => $t->assignedTo->name,
            ] : null,
            'created_at' => $t->created_at?->toIso8601String(),
            'updated_at' => $t->updated_at?->toIso8601String(),
            'closed_at' => $t->closed_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function ticketDetail(SupportTicket $t): array
    {
        $base = $this->ticketSummary($t);
        $base['messages'] = $t->messages->map(fn (SupportTicketMessage $m) => $this->messageRow($m));

        return $base;
    }

    /**
     * @return array<string, mixed>
     */
    private function messageRow(SupportTicketMessage $m): array
    {
        $u = $m->user;
        if (! $u && $m->user_id) {
            $u = $m->user()->first();
        }

        return [
            'id' => $m->id,
            'body' => $m->body,
            'is_staff' => (bool) $m->is_staff,
            'attachment_path' => $m->attachment_path,
            'attachment_url' => storage_public_url($m->attachment_path),
            'user' => $u ? [
                'id' => $u->id,
                'name' => $u->name,
            ] : null,
            'created_at' => $m->created_at?->toIso8601String(),
        ];
    }
}
