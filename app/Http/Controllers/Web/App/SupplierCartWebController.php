<?php

namespace App\Http\Controllers\Web\App;

use App\Http\Controllers\Api\Me\DevisController as ApiMeDevisController;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Services\Web\SupplierMarketplaceCart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SupplierCartWebController extends Controller
{
    public function addProduct(Request $request, SupplierMarketplaceCart $cart): RedirectResponse
    {
        $slug = (string) $request->segment(2);
        abort_unless(in_array($slug, ['particulier', 'batiment', 'fournisseur'], true), 404);

        /** @var User $user */
        $user = $request->user();
        abort_unless($user instanceof User, 403);
        abort_if($user->profile_type === User::PROFILE_ARTISAN, 403);

        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'qty' => ['nullable', 'integer', 'min:1', 'max:9999'],
        ]);

        $product = Product::query()->with(['user', 'category'])->findOrFail($validated['product_id']);
        if ($product->status !== 'approved') {
            return back()->withErrors(['cart' => 'Produit indisponible.']);
        }

        $owner = $product->user;
        if ($owner === null
            || $owner->profile_type !== User::PROFILE_ENTREPRISE_FOURNISSEUR
            || ! $owner->is_active) {
            return back()->withErrors(['cart' => 'Vendeur indisponible.']);
        }

        if ((int) $product->user_id === (int) $user->id) {
            return back()->withErrors(['cart' => 'Vous ne pouvez pas commander vos propres produits.']);
        }

        if ((int) $product->stock_units <= 0) {
            return back()->withErrors(['cart' => 'Produit indisponible (stock épuisé).']);
        }

        $qty = (int) ($validated['qty'] ?? 1);
        $cart->addProduct($request, $product, $qty);

        return redirect()
            ->route('app.'.$slug.'.cart')
            ->with('status', 'Ajouté au panier.');
    }

    public function updateLine(Request $request, SupplierMarketplaceCart $cart, int $index): RedirectResponse
    {
        $slug = (string) $request->segment(2);
        abort_unless(in_array($slug, ['particulier', 'batiment', 'fournisseur'], true), 404);

        $validated = $request->validate([
            'qty' => ['required', 'integer', 'min:1', 'max:99999'],
        ]);

        if (! $cart->setQuantity($request, $index, (int) $validated['qty'])) {
            return redirect()->route('app.'.$slug.'.cart')->withErrors(['cart' => 'Ligne introuvable.']);
        }

        return redirect()->route('app.'.$slug.'.cart')->with('status', 'Quantité mise à jour.');
    }

    public function removeLine(Request $request, SupplierMarketplaceCart $cart, int $index): RedirectResponse
    {
        $slug = (string) $request->segment(2);
        abort_unless(in_array($slug, ['particulier', 'batiment', 'fournisseur'], true), 404);

        if (! $cart->removeAt($request, $index)) {
            return redirect()->route('app.'.$slug.'.cart')->withErrors(['cart' => 'Ligne introuvable.']);
        }

        return redirect()->route('app.'.$slug.'.cart')->with('status', 'Article retiré du panier.');
    }

    public function clear(Request $request, SupplierMarketplaceCart $cart): RedirectResponse
    {
        $slug = (string) $request->segment(2);
        abort_unless(in_array($slug, ['particulier', 'batiment', 'fournisseur'], true), 404);

        $cart->clear($request);

        return redirect()->route('app.'.$slug.'.cart')->with('status', 'Panier vidé.');
    }

    public function checkout(Request $request, SupplierMarketplaceCart $cart): RedirectResponse
    {
        $slug = (string) $request->segment(2);
        abort_unless(in_array($slug, ['particulier', 'batiment', 'fournisseur'], true), 404);

        /** @var User $buyer */
        $buyer = $request->user();
        abort_unless($buyer instanceof User, 403);
        abort_if($buyer->profile_type === User::PROFILE_ARTISAN, 403);

        $validated = $request->validate([
            'supplier_user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $supplierId = (int) $validated['supplier_user_id'];
        if ($cart->linesForSupplier($request, $supplierId) === []) {
            return redirect()->route('app.'.$slug.'.cart')->withErrors(['cart' => 'Panier vide pour ce fournisseur.']);
        }

        $supplier = User::query()->findOrFail($supplierId);
        $supplierName = trim((string) ($supplier->company_name ?? '')) !== ''
            ? trim((string) $supplier->company_name)
            : (string) ($supplier->name ?? 'Fournisseur');

        $clientName = trim((string) ($buyer->company_name ?? '')) !== ''
            ? trim((string) $buyer->company_name)
            : (string) ($buyer->name ?? 'Client');

        $lineItems = $cart->buildLineItemsPayloadForSupplier($request, $supplierId);

        $merge = [
            'owner_user_id' => $supplierId,
            'title' => 'Commande catalogue — '.$supplierName,
            'client_name' => $clientName,
            'order_reference' => 'PANIER-'.time(),
            'contact' => $buyer->phone,
            'notes' => 'Commande passée depuis le panier marketplace Batitravoo. Merci de confirmer disponibilité et frais de livraison.',
            'line_items' => $lineItems,
        ];

        $sub = $request->duplicate(
            $request->query->all(),
            array_merge($request->request->all(), $merge)
        );

        $response = app(ApiMeDevisController::class)->store($sub);

        if ($response->getStatusCode() >= 400) {
            $payload = json_decode($response->getContent(), true);
            $msg = is_array($payload) && isset($payload['message'])
                ? (string) $payload['message']
                : 'Impossible d’envoyer la commande.';

            return redirect()->route('app.'.$slug.'.cart')->withErrors(['cart' => $msg]);
        }

        $cart->clearSupplier($request, $supplierId);

        $payload = json_decode($response->getContent(), true);
        $id = is_array($payload) ? (int) data_get($payload, 'data.id', 0) : 0;

        if ($id > 0) {
            return redirect()
                ->route('app.'.$slug.'.devis.show', ['devis' => $id])
                ->with('status', 'Demande de commande envoyée.');
        }

        return redirect()->route('app.'.$slug.'.devis')->with('status', 'Demande de commande envoyée.');
    }
}
