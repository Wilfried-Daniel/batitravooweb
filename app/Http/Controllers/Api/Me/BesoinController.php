<?php

namespace App\Http\Controllers\Api\Me;

use App\Http\Controllers\Controller;
use App\Models\Besoin;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BesoinController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $u = $request->user();
        abort_unless(in_array($u->profile_type, [
            User::PROFILE_ENTREPRENEUR_BATIMENT,
            User::PROFILE_PARTICULIER,
        ], true), 403);

        $items = Besoin::query()->where('user_id', $u->id)
            ->orderByDesc('id')
            ->get()
            ->map(fn (Besoin $b) => $this->row($b));

        return response()->json(['data' => $items]);
    }

    public function store(Request $request): JsonResponse
    {
        $u = $request->user();
        abort_unless(in_array($u->profile_type, [
            User::PROFILE_ENTREPRENEUR_BATIMENT,
            User::PROFILE_PARTICULIER,
        ], true), 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'budget' => ['nullable', 'string', 'max:128'],
            'start_label' => ['nullable', 'string', 'max:128'],
            'place' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'duration' => ['nullable', 'string', 'max:128'],
            'short_date' => ['nullable', 'string', 'max:64'],
            'image' => ['nullable', 'image', 'max:10240'],
        ]);

        if (array_key_exists('budget', $data)) {
            $b = $data['budget'];
            $data['budget'] = ($b === null || (is_string($b) && trim($b) === '')) ? null : $b;
        }

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('besoins', 'public');
        }
        unset($data['image']);

        $besoin = Besoin::query()->create(array_merge($data, [
            'user_id' => $u->id,
            'candidature_count' => 0,
            'status' => 'open',
        ]));

        return response()->json(['data' => $this->row($besoin)], 201);
    }

    public function update(Request $request, Besoin $besoin): JsonResponse
    {
        $u = $request->user();
        abort_unless(in_array($u->profile_type, [
            User::PROFILE_ENTREPRENEUR_BATIMENT,
            User::PROFILE_PARTICULIER,
        ], true), 403);
        abort_unless($besoin->user_id === $u->id, 404);

        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'budget' => ['nullable', 'string', 'max:128'],
            'start_label' => ['nullable', 'string', 'max:128'],
            'place' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'duration' => ['nullable', 'string', 'max:128'],
            'short_date' => ['nullable', 'string', 'max:64'],
            'status' => ['sometimes', 'string', Rule::in(['open', 'in_progress', 'closed', 'cancelled'])],
            'image' => ['nullable', 'image', 'max:10240'],
        ]);

        if (array_key_exists('budget', $data) && is_string($data['budget']) && trim($data['budget']) === '') {
            $data['budget'] = null;
        }

        if ($request->hasFile('image')) {
            if ($besoin->image_path) {
                Storage::disk('public')->delete($besoin->image_path);
            }
            $data['image_path'] = $request->file('image')->store('besoins', 'public');
        }
        unset($data['image']);

        $besoin->fill($data);
        $besoin->save();

        return response()->json(['data' => $this->row($besoin)]);
    }

    public function destroy(Request $request, Besoin $besoin): JsonResponse
    {
        $u = $request->user();
        abort_unless(in_array($u->profile_type, [
            User::PROFILE_ENTREPRENEUR_BATIMENT,
            User::PROFILE_PARTICULIER,
        ], true), 403);
        abort_unless($besoin->user_id === $u->id, 404);
        if ($besoin->image_path) {
            Storage::disk('public')->delete($besoin->image_path);
        }
        $besoin->delete();

        return response()->json(['ok' => true]);
    }

    /**
     * @return array<string, mixed>
     */
    private function row(Besoin $b): array
    {
        $imageUrl = $b->image_path
            ? storage_public_url($b->image_path)
            : null;

        return [
            'id' => $b->id,
            'title' => $b->title,
            'budget' => $b->budget,
            'start_label' => $b->start_label,
            'place' => $b->place,
            'description' => $b->description,
            'duration' => $b->duration,
            'short_date' => $b->short_date,
            'image_path' => $b->image_path,
            'image_url' => $imageUrl,
            'has_image' => $b->image_path !== null && $b->image_path !== '',
            'candidature_count' => (int) $b->candidature_count,
            'status' => $b->status,
            'user_id' => $b->user_id,
            'created_at' => $b->created_at?->toIso8601String(),
        ];
    }
}
