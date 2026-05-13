<?php

namespace App\Http\Controllers\Api\Me;

use App\Http\Controllers\Api\Concerns\FormatsServiceApi;
use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    use FormatsServiceApi;

    public function index(Request $request): JsonResponse
    {
        $u = $request->user();
        abort_unless(in_array($u->profile_type, [
            User::PROFILE_ARTISAN,
            User::PROFILE_ENTREPRENEUR_BATIMENT,
        ], true), 403);

        $items = Service::query()->where('user_id', $u->id)
            ->with('category')
            ->orderByDesc('id')
            ->get()
            ->map(fn (Service $s) => $this->row($s));

        return response()->json(['data' => $items]);
    }

    public function store(Request $request): JsonResponse
    {
        $u = $request->user();
        abort_unless(in_array($u->profile_type, [
            User::PROFILE_ARTISAN,
            User::PROFILE_ENTREPRENEUR_BATIMENT,
        ], true), 403);

        $data = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'location' => ['nullable', 'string', 'max:255'],
            'image_url' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'image', 'max:10240'],
            'service_kind' => ['required', Rule::in(['artisan', 'entrepreneur'])],
            'price_variables' => ['nullable', 'boolean'],
            'price_fixed_label' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('services', 'public');
            $data['image_url'] = null;
        }
        unset($data['image']);

        if ($u->profile_type === User::PROFILE_ARTISAN) {
            $data['service_kind'] = 'artisan';
        }
        if ($u->profile_type === User::PROFILE_ENTREPRENEUR_BATIMENT) {
            $data['service_kind'] = 'entrepreneur';
        }

        $slug = $this->uniqueSlug($data['title']);

        $service = Service::query()->create([
            'user_id' => $u->id,
            'category_id' => $data['category_id'] ?? null,
            'title' => $data['title'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'location' => $data['location'] ?? null,
            'image_url' => $data['image_url'] ?? null,
            'image_path' => $data['image_path'] ?? null,
            'service_kind' => $data['service_kind'],
            'price_variables' => $request->boolean('price_variables'),
            'price_fixed_label' => $data['price_fixed_label'] ?? null,
            'status' => 'pending',
            'is_visible' => true,
        ]);
        $service->load('category');

        return response()->json(['data' => $this->row($service)], 201);
    }

    public function update(Request $request, Service $service): JsonResponse
    {
        $u = $request->user();
        abort_unless(in_array($u->profile_type, [
            User::PROFILE_ARTISAN,
            User::PROFILE_ENTREPRENEUR_BATIMENT,
        ], true), 403);
        abort_unless($service->user_id === $u->id, 404);

        $data = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'location' => ['nullable', 'string', 'max:255'],
            'image_url' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'image', 'max:10240'],
            'price_variables' => ['nullable', 'boolean'],
            'price_fixed_label' => ['nullable', 'string', 'max:255'],
            /** Affichage catalogue public (si statut validé). */
            'is_visible' => ['sometimes', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            if ($service->image_path) {
                Storage::disk('public')->delete($service->image_path);
            }
            $data['image_path'] = $request->file('image')->store('services', 'public');
            $data['image_url'] = null;
        }
        unset($data['image']);

        if (! $request->hasFile('image') && array_key_exists('image_url', $data)) {
            $ext = trim((string) ($data['image_url'] ?? ''));
            if ($ext !== '' && (str_starts_with($ext, 'http://') || str_starts_with($ext, 'https://'))) {
                if ($service->image_path) {
                    Storage::disk('public')->delete($service->image_path);
                }
                $data['image_path'] = null;
            }
        }

        if (array_key_exists('title', $data) && $data['title'] !== $service->title) {
            $service->slug = $this->uniqueSlug($data['title'], $service->id);
        }
        if (array_key_exists('price_variables', $data)) {
            $data['price_variables'] = $request->boolean('price_variables');
        }

        $service->fill($data);
        $service->save();
        $service->load('category');

        return response()->json(['data' => $this->row($service)]);
    }

    public function destroy(Request $request, Service $service): JsonResponse
    {
        $u = $request->user();
        abort_unless(in_array($u->profile_type, [
            User::PROFILE_ARTISAN,
            User::PROFILE_ENTREPRENEUR_BATIMENT,
        ], true), 403);
        abort_unless($service->user_id === $u->id, 404);
        if ($service->image_path) {
            Storage::disk('public')->delete($service->image_path);
        }
        $service->delete();

        return response()->json(['ok' => true]);
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'service';
        $slug = $base;
        $i = 1;
        while (Service::query()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()
        ) {
            $slug = $base.'-'.(++$i);
        }

        return $slug;
    }

    /**
     * @return array<string, mixed>
     */
    private function row(Service $s): array
    {
        $imageUrl = $this->resolveServiceImageUrl($s);

        $r = [
            'id' => $s->id,
            'title' => $s->title,
            'slug' => $s->slug,
            'description' => $s->description,
            'location' => $s->location,
            'image_path' => $s->image_path,
            'image_url' => $imageUrl,
            'has_image' => $imageUrl !== null,
            'service_kind' => $s->service_kind,
            'price_variables' => (bool) $s->price_variables,
            'price_fixed_label' => $s->price_fixed_label,
            'pricing' => $this->servicePricingPayload($s),
            'rating' => (float) $s->rating,
            'review_count' => (int) $s->review_count,
            'status' => $s->status,
            'is_visible' => (bool) $s->is_visible,
            'category_id' => $s->category_id,
        ];
        if ($s->relationLoaded('category') && $s->category) {
            $r['category'] = [
                'id' => $s->category->id,
                'name' => $s->category->name,
            ];
        }
        $r['created_at'] = $s->created_at?->toIso8601String();

        return $r;
    }
}
