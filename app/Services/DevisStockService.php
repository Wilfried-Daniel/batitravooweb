<?php

namespace App\Services;

use App\Models\Devis;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

/**
 * Déduction du stock catalogue lors de la validation d’une commande marketplace.
 */
class DevisStockService
{
    public function deductForValidatedOrder(Devis $devis): void
    {
        if ((string) $devis->status !== 'valide') {
            return;
        }

        $items = $devis->line_items;
        if (! is_array($items)) {
            return;
        }

        if (! empty($items['stock_deducted_at'])) {
            return;
        }

        if (! $this->isCatalogueOrder($devis, $items)) {
            return;
        }

        $productLines = $this->extractProductLines($items);
        if ($productLines === []) {
            return;
        }

        $supplierId = (int) $devis->user_id;

        DB::transaction(function () use ($devis, $items, $productLines, $supplierId): void {
            foreach ($productLines as $line) {
                $productId = (int) ($line['product_id'] ?? 0);
                $qty = (int) ($line['qty'] ?? $line['quantity'] ?? $line['quantite'] ?? 0);
                if ($productId <= 0 || $qty <= 0) {
                    continue;
                }

                $product = Product::query()
                    ->where('id', $productId)
                    ->where('user_id', $supplierId)
                    ->lockForUpdate()
                    ->first();

                if ($product === null) {
                    continue;
                }

                $product->stock_units = max(0, (int) $product->stock_units - $qty);
                $product->save();
            }

            $items['stock_deducted_at'] = now()->toIso8601String();
            $devis->line_items = $items;
            $devis->save();
        });
    }

    /**
     * Conserve les lignes panier (product_id) quand le fournisseur envoie un devis formulaire.
     *
     * @param  array<string, mixed>  $previous
     * @param  array<string, mixed>  $incoming
     * @return array<string, mixed>
     */
    public function mergeLineItemsPreservingOrderProducts(array $previous, array $incoming): array
    {
        $prevLignes = $previous['lignes'] ?? null;
        if (! is_array($prevLignes)) {
            return $incoming;
        }

        $prevHasProducts = false;
        foreach ($prevLignes as $l) {
            if (is_array($l) && ! empty($l['product_id'])) {
                $prevHasProducts = true;
                break;
            }
        }
        if (! $prevHasProducts) {
            return $incoming;
        }

        $incLignes = $incoming['lignes'] ?? null;
        if (is_array($incLignes)) {
            foreach ($incLignes as $l) {
                if (is_array($l) && ! empty($l['product_id'])) {
                    return $incoming;
                }
            }
        }

        $incoming['order_lignes'] = $prevLignes;
        if (! isset($incoming['source']) && isset($previous['source'])) {
            $incoming['source'] = $previous['source'];
        }
        if (! isset($incoming['currency']) && isset($previous['currency'])) {
            $incoming['currency'] = $previous['currency'];
        }

        return $incoming;
    }

    /**
     * @param  array<string, mixed>  $items
     */
    private function isCatalogueOrder(Devis $devis, array $items): bool
    {
        $source = $items['source'] ?? null;
        if (is_string($source) && str_contains($source, 'marketplace')) {
            return true;
        }

        $ref = (string) ($devis->order_reference ?? '');
        if (str_starts_with($ref, 'PANIER-')) {
            return true;
        }

        $title = strtolower((string) $devis->title);
        if (str_contains($title, 'commande catalogue') || str_contains($title, 'demande marketplace')) {
            return true;
        }

        return $this->extractProductLines($items) !== [];
    }

    /**
     * @param  array<string, mixed>  $items
     * @return list<array<string, mixed>>
     */
    private function extractProductLines(array $items): array
    {
        $out = [];

        foreach (['order_lignes', 'lignes'] as $key) {
            $list = $items[$key] ?? null;
            if (! is_array($list)) {
                continue;
            }
            foreach ($list as $row) {
                if (! is_array($row) || empty($row['product_id'])) {
                    continue;
                }
                $pid = (int) $row['product_id'];
                $qty = (int) ($row['qty'] ?? $row['quantity'] ?? $row['quantite'] ?? 0);
                if ($pid <= 0 || $qty <= 0) {
                    continue;
                }
                $out[] = [
                    'product_id' => $pid,
                    'qty' => $qty,
                ];
            }
        }

        if ($out !== []) {
            return $this->mergeQuantitiesByProduct($out);
        }

        if (! isset($items[0])) {
            return [];
        }

        foreach ($items as $row) {
            if (! is_array($row) || empty($row['product_id'])) {
                continue;
            }
            $pid = (int) $row['product_id'];
            $qty = (int) ($row['qty'] ?? $row['quantity'] ?? $row['quantite'] ?? 0);
            if ($pid <= 0 || $qty <= 0) {
                continue;
            }
            $out[] = [
                'product_id' => $pid,
                'qty' => $qty,
            ];
        }

        return $this->mergeQuantitiesByProduct($out);
    }

    /**
     * @param  list<array<string, mixed>>  $lines
     * @return list<array<string, mixed>>
     */
    private function mergeQuantitiesByProduct(array $lines): array
    {
        $byProduct = [];
        foreach ($lines as $line) {
            $pid = (int) $line['product_id'];
            $byProduct[$pid] = ($byProduct[$pid] ?? 0) + (int) $line['qty'];
        }

        $merged = [];
        foreach ($byProduct as $pid => $qty) {
            $merged[] = ['product_id' => $pid, 'qty' => $qty];
        }

        return $merged;
    }
}
