<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $q = Product::query()->with(['user', 'category']);

        if ($request->filled('status')) {
            $q->where('status', $request->string('status'));
        }

        if ($search = $request->string('q')->trim()) {
            $q->where('title', 'like', "%{$search}%");
        }

        $products = $q->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.products.index', ['products' => $products]);
    }

    public function show(Product $product): View
    {
        $product->load('user', 'category');

        return view('admin.products.show', ['product' => $product]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['draft', 'pending', 'approved', 'rejected'])],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ]);
        $product->update($data);

        return redirect()->route('admin.products.show', $product)->with('ok', 'Produit mis à jour.');
    }
}
