<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicCategoryController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $appliesTo = $request->query('applies_to');

        $q = Category::query()->where('is_active', true);

        if (in_array($appliesTo, ['product', 'service', 'both'], true)) {
            $q->where(function ($b) use ($appliesTo) {
                if ($appliesTo === 'both') {
                    $b->whereIn('applies_to', ['product', 'service', 'both']);
                } else {
                    $b->whereIn('applies_to', [$appliesTo, 'both']);
                }
            });
        }

        $items = $q->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn (Category $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'slug' => $c->slug,
                'applies_to' => $c->applies_to,
            ]);

        return response()->json(['data' => $items]);
    }
}
