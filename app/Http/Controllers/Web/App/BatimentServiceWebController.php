<?php

namespace App\Http\Controllers\Web\App;

use App\Models\Service;
use App\Models\User;
use App\Services\Web\MeApiBridge;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BatimentServiceWebController extends ShellController
{
    public function create(Request $request): View
    {
        $categories = app(MeApiBridge::class)->categoriesForServices($request);

        return $this->render($request, 'service_form', [
            'serviceFormMode' => 'create',
            'formService' => null,
            'categories' => $categories,
            'title' => 'Nouvelle prestation',
            'intro' => 'Publiez une prestation BTP : visibilité marketplace après modération — comme sur l’application mobile.',
        ]);
    }

    public function edit(Request $request, Service $service): View
    {
        $this->authorizeService($request, $service);
        $categories = app(MeApiBridge::class)->categoriesForServices($request);

        return $this->render($request, 'service_form', [
            'serviceFormMode' => 'edit',
            'formService' => $service,
            'categories' => $categories,
            'title' => 'Modifier la prestation',
            'intro' => 'Mettez à jour votre offre entreprise BTP.',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $u = $request->user();
        abort_unless($u->profile_type === User::PROFILE_ENTREPRENEUR_BATIMENT, 403);

        $data = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'location' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:10240'],
            'price_variables' => ['nullable', 'boolean'],
            'price_fixed_label' => ['nullable', 'string', 'max:255'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('services', 'public');
        }

        $slug = $this->uniqueSlug($data['title']);

        Service::query()->create([
            'user_id' => $u->id,
            'category_id' => $data['category_id'] ?? null,
            'title' => $data['title'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'location' => $data['location'] ?? null,
            'image_path' => $imagePath,
            'image_url' => null,
            'service_kind' => 'entrepreneur',
            'price_variables' => $request->boolean('price_variables'),
            'price_fixed_label' => $data['price_fixed_label'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('app.batiment.services')->with('status', 'Prestation créée. Elle sera visible après validation.');
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $this->authorizeService($request, $service);

        $data = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'location' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:10240'],
            'price_variables' => ['nullable', 'boolean'],
            'price_fixed_label' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->hasFile('image')) {
            if ($service->image_path) {
                Storage::disk('public')->delete($service->image_path);
            }
            $data['image_path'] = $request->file('image')->store('services', 'public');
            $data['image_url'] = null;
        }

        if ($data['title'] !== $service->title) {
            $service->slug = $this->uniqueSlug($data['title'], $service->id);
        }

        $data['price_variables'] = $request->boolean('price_variables');

        $service->fill([
            'category_id' => $data['category_id'] ?? null,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'location' => $data['location'] ?? null,
            'price_fixed_label' => $data['price_fixed_label'] ?? null,
            'price_variables' => $data['price_variables'],
        ]);
        if (isset($data['image_path'])) {
            $service->image_path = $data['image_path'];
            $service->image_url = null;
        }
        $service->save();

        return redirect()->route('app.batiment.services')->with('status', 'Prestation mise à jour.');
    }

    public function destroy(Request $request, Service $service): RedirectResponse
    {
        $this->authorizeService($request, $service);
        if ($service->image_path) {
            Storage::disk('public')->delete($service->image_path);
        }
        $service->delete();

        return redirect()->route('app.batiment.services')->with('status', 'Prestation supprimée.');
    }

    private function authorizeService(Request $request, Service $service): void
    {
        $u = $request->user();
        abort_unless($u->profile_type === User::PROFILE_ENTREPRENEUR_BATIMENT, 403);
        abort_unless((int) $service->user_id === (int) $u->id, 404);
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
}
