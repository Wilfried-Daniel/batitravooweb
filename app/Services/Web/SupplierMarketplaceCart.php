<?php

namespace App\Services\Web;

use App\Models\Product;
use Illuminate\Http\Request;

/**
 * Panier marketplace multi-fournisseurs (session), aligné sur {@see SupplierCartNotifier} Flutter.
 */
final class SupplierMarketplaceCart
{
    public const SESSION_KEY = 'supplier_marketplace_cart_v1';

    /**
     * @return list<array<string, mixed>>
     */
    public function lines(Request $request): array
    {
        $raw = $request->session()->get(self::SESSION_KEY, []);

        return is_array($raw) ? array_values(array_filter($raw, 'is_array')) : [];
    }

    public function save(Request $request, array $lines): void
    {
        $request->session()->put(self::SESSION_KEY, array_values($lines));
    }

    public function clear(Request $request): void
    {
        $request->session()->forget(self::SESSION_KEY);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function linesForSupplier(Request $request, int $supplierUserId): array
    {
        return array_values(array_filter(
            $this->lines($request),
            static fn (array $l): bool => (int) ($l['supplier_user_id'] ?? 0) === $supplierUserId
        ));
    }

    public function subtotalForSupplier(Request $request, int $supplierUserId): int
    {
        $sum = 0;
        foreach ($this->linesForSupplier($request, $supplierUserId) as $l) {
            $sum += (int) ($l['unit_price_fcfa'] ?? 0) * (int) ($l['quantity'] ?? 0);
        }

        return $sum;
    }

    /**
     * @return list<int>
     */
    public function supplierIds(Request $request): array
    {
        $ids = [];
        foreach ($this->lines($request) as $l) {
            $id = (int) ($l['supplier_user_id'] ?? 0);
            if ($id > 0) {
                $ids[$id] = true;
            }
        }

        return array_keys($ids);
    }

    /**
     * Payload JSON pour [POST /me/devis] — même structure que Flutter [buildLineItemsPayloadForSupplier].
     *
     * @return array<string, mixed>
     */
    public function buildLineItemsPayloadForSupplier(Request $request, int $supplierUserId): array
    {
        $rows = $this->linesForSupplier($request, $supplierUserId);
        $lignes = [];
        foreach ($rows as $l) {
            $qty = (int) ($l['quantity'] ?? 0);
            $unit = (int) ($l['unit_price_fcfa'] ?? 0);
            $row = [
                'product_id' => (int) ($l['product_id'] ?? 0),
                'label' => (string) ($l['title'] ?? ''),
                'qty' => $qty,
                'unit_price_fcfa' => $unit,
                'line_total_fcfa' => $unit * $qty,
            ];
            if (! empty($l['category_name'])) {
                $row['category'] = (string) $l['category_name'];
            }
            $lignes[] = $row;
        }
        $subtotal = $this->subtotalForSupplier($request, $supplierUserId);

        return [
            'currency' => 'XOF',
            'source' => 'marketplace_cart',
            'lignes' => $lignes,
            'totals' => [
                'subtotal_fcfa' => $subtotal,
            ],
        ];
    }

    public function addProduct(Request $request, Product $product, int $qty): void
    {
        $product->loadMissing(['user', 'category']);
        $supplier = $product->user;
        if ($supplier === null) {
            return;
        }

        $supplierUserId = (int) $supplier->id;
        $supplierName = trim((string) ($supplier->company_name ?? '')) !== ''
            ? trim((string) $supplier->company_name)
            : (string) ($supplier->name ?? 'Fournisseur');
        $categoryName = $product->category?->name;
        $imageUrl = $product->image_path
            ? storage_public_url($product->image_path)
            : null;

        $lines = $this->lines($request);
        $i = $this->indexOf($lines, (int) $product->id, $supplierUserId);
        $maxStock = max(0, (int) $product->stock_units);
        $qty = max(1, $qty);
        if ($maxStock <= 0) {
            return;
        }
        $qty = min($qty, $maxStock);

        if ($i >= 0) {
            $lines[$i]['quantity'] = min($maxStock, (int) ($lines[$i]['quantity'] ?? 0) + $qty);
        } else {
            $lines[] = [
                'product_id' => (int) $product->id,
                'title' => (string) $product->title,
                'unit_price_fcfa' => (int) $product->price_amount,
                'quantity' => $qty,
                'supplier_user_id' => $supplierUserId,
                'supplier_name' => $supplierName,
                'category_name' => $categoryName,
                'image_url' => $imageUrl,
                'max_stock' => $maxStock,
            ];
        }

        $this->save($request, $lines);
    }

    public function setQuantity(Request $request, int $index, int $qty): bool
    {
        $lines = $this->lines($request);
        if (! isset($lines[$index])) {
            return false;
        }
        $max = max(1, (int) ($lines[$index]['max_stock'] ?? 1));
        $lines[$index]['quantity'] = min($max, max(1, $qty));
        $this->save($request, $lines);

        return true;
    }

    public function removeAt(Request $request, int $index): bool
    {
        $lines = $this->lines($request);
        if (! isset($lines[$index])) {
            return false;
        }
        unset($lines[$index]);
        $this->save($request, $lines);

        return true;
    }

    public function clearSupplier(Request $request, int $supplierUserId): void
    {
        $lines = array_values(array_filter(
            $this->lines($request),
            static fn (array $l): bool => (int) ($l['supplier_user_id'] ?? 0) !== $supplierUserId
        ));
        $this->save($request, $lines);
    }

    /**
     * @param  list<array<string, mixed>>  $lines
     */
    private function indexOf(array $lines, int $productId, int $supplierUserId): int
    {
        foreach ($lines as $i => $l) {
            if ((int) ($l['product_id'] ?? 0) === $productId
                && (int) ($l['supplier_user_id'] ?? 0) === $supplierUserId) {
                return (int) $i;
            }
        }

        return -1;
    }
}
