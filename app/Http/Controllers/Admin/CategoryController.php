<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()->orderBy('sort_order')->orderBy('name')->paginate(30);

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'applies_to' => ['required', Rule::in(['product', 'service', 'both'])],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'is_active' => ['required', Rule::in([0, 1, '0', '1'])],
        ]);
        $data['is_active'] = (bool) (int) $data['is_active'];
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['slug'] = $this->uniqueSlug(Str::slug($data['name']));

        Category::query()->create($data);

        return redirect()->route('admin.categories.index')->with('ok', 'Catégorie créée.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'applies_to' => ['required', Rule::in(['product', 'service', 'both'])],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'is_active' => ['required', Rule::in([0, 1, '0', '1'])],
        ]);
        $data['is_active'] = (bool) (int) $data['is_active'];
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        if ($data['name'] !== $category->name) {
            $data['slug'] = $this->uniqueSlug(Str::slug($data['name']), $category->id);
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('ok', 'Catégorie mise à jour.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists() || $category->services()->exists()) {
            return back()->with('error', 'Impossible de supprimer : produits ou services liés.');
        }
        $category->delete();

        return redirect()->route('admin.categories.index')->with('ok', 'Catégorie supprimée.');
    }

    private function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = $base ?: 'categorie';
        $i = 0;
        do {
            $try = $i === 0 ? $slug : $slug.'-'.$i;
            $q = Category::query()->where('slug', $try);
            if ($ignoreId) {
                $q->where('id', '!=', $ignoreId);
            }
            if (! $q->exists()) {
                return $try;
            }
            $i++;
        } while ($i < 1000);

        return $slug.'-'.Str::random(6);
    }
}
