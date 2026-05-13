<?php

namespace App\Http\Controllers\Api\Me;

use App\Http\Controllers\Controller;
use App\Models\InAppNotification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        /** @var User $u */
        $u = $request->user();

        $perPage = min(50, max(1, (int) $request->query('per_page', 30)));

        $q = InAppNotification::query()->where('user_id', $u->id);

        $unread = (clone $q)->whereNull('read_at')->count();

        $items = (clone $q)
            ->orderByDesc('created_at')
            ->limit($perPage)
            ->get()
            ->map(fn (InAppNotification $n) => $this->row($n));

        return response()->json([
            'data' => $items,
            'meta' => [
                'unread_count' => $unread,
            ],
        ]);
    }

    public function markRead(Request $request, int $id): JsonResponse
    {
        /** @var User $u */
        $u = $request->user();

        $n = InAppNotification::query()
            ->where('user_id', $u->id)
            ->whereKey($id)
            ->firstOrFail();
        $n->markRead();

        return response()->json(['data' => $this->row($n->refresh())]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        /** @var User $u */
        $u = $request->user();

        $updated = InAppNotification::query()
            ->where('user_id', $u->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'data' => ['updated' => $updated],
            'meta' => ['unread_count' => 0],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function row(InAppNotification $n): array
    {
        return [
            'id' => $n->id,
            'type' => $n->type,
            'title' => $n->title,
            'body' => $n->body,
            'data' => $n->data,
            'read' => $n->read_at !== null,
            'read_at' => $n->read_at?->toIso8601String(),
            'created_at' => $n->created_at?->toIso8601String(),
        ];
    }
}
