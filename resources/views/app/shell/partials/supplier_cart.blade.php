@php
    use App\Services\Web\SupplierMarketplaceCart;

    /** @var list<array<string, mixed>> $lines */
    $lines = is_array($cartLines ?? null) ? $cartLines : [];
    $cartSvc = app(SupplierMarketplaceCart::class);
    $bySupplier = [];
    foreach ($lines as $i => $line) {
        if (! is_array($line)) {
            continue;
        }
        $sid = (int) ($line['supplier_user_id'] ?? 0);
        if ($sid <= 0) {
            continue;
        }
        $bySupplier[$sid][] = ['i' => $i, 'line' => $line];
    }
@endphp

@if ($errors->has('cart'))
    <div class="app-alert app-alert--error app-mb-md" role="alert">{{ $errors->first('cart') }}</div>
@endif

@if ($lines === [])
    <div class="app-card app-card--flush">
        <h2 class="app-section-title">Mon panier</h2>
        <p class="app-muted app-mb-md">Votre panier est vide. Ajoutez des produits depuis le marketplace (onglet matériaux / produits).</p>
        <div class="app-flex-between-wrap">
            <a href="{{ route('app.'.$profileSlug.'.marketplace', ['tab' => 'produits']) }}" class="app-btn app-btn--inline">Ouvrir le marketplace</a>
            <a href="{{ route('app.'.$profileSlug.'.devis') }}" class="app-btn app-btn--inline app-btn--secondary">Mes demandes / commandes</a>
        </div>
    </div>
@else
    <div class="app-card app-card--flush app-mb-md">
        <div class="app-flex-between-wrap">
            <h2 class="app-section-title app-mb-0">Mon panier</h2>
            <form method="post" action="{{ route('app.'.$profileSlug.'.cart.clear') }}" onsubmit="return confirm('Vider tout le panier ?');">
                @csrf
                <button type="submit" class="app-btn app-btn--ghost app-btn--sm">Tout vider</button>
            </form>
        </div>
        <p class="app-muted app-text-sm app-mt-sm app-mb-0">Une demande de commande est envoyée par fournisseur (comme sur l’app mobile).</p>
    </div>

    @foreach ($bySupplier as $supplierId => $entries)
        @php
            $supplierName = (string) ($entries[0]['line']['supplier_name'] ?? 'Fournisseur');
            $subtotal = $cartSvc->subtotalForSupplier(request(), (int) $supplierId);
        @endphp
        <section class="app-card app-mt">
            <header class="app-flex-between-wrap app-mb-md">
                <div>
                    <h3 class="app-section-title app-mb-0">{{ $supplierName }}</h3>
                    <p class="app-muted app-text-sm app-mb-0">Sous-total indicatif : {{ number_format($subtotal, 0, ',', ' ') }} FCFA</p>
                </div>
                <form method="post" action="{{ route('app.'.$profileSlug.'.cart.checkout') }}" onsubmit="return confirm('Envoyer la demande de commande à ce fournisseur ?');">
                    @csrf
                    <input type="hidden" name="supplier_user_id" value="{{ (int) $supplierId }}">
                    <button type="submit" class="app-btn app-btn--inline">Commander chez ce fournisseur</button>
                </form>
            </header>
            <ul class="app-cart-lines" role="list">
                @foreach ($entries as $entry)
                    @php
                        $i = (int) $entry['i'];
                        $line = $entry['line'];
                        $title = (string) ($line['title'] ?? '—');
                        $unit = (int) ($line['unit_price_fcfa'] ?? 0);
                        $qty = (int) ($line['quantity'] ?? 1);
                        $max = (int) ($line['max_stock'] ?? 1);
                        $lineTotal = $unit * $qty;
                    @endphp
                    <li class="app-cart-line">
                        <div class="app-cart-line__main">
                            @if (! empty($line['image_url']))
                                <img src="{{ $line['image_url'] }}" alt="" class="app-cart-line__img" width="56" height="56" loading="lazy">
                            @else
                                <div class="app-cart-line__img-ph" aria-hidden="true"></div>
                            @endif
                            <div>
                                <strong class="app-cart-line__title">{{ $title }}</strong>
                                <p class="app-muted app-text-sm app-mb-0">{{ number_format($unit, 0, ',', ' ') }} FCFA × {{ $qty }} = {{ number_format($lineTotal, 0, ',', ' ') }} FCFA</p>
                            </div>
                        </div>
                        <div class="app-cart-line__actions">
                            <form method="post" action="{{ route('app.'.$profileSlug.'.cart.line', ['index' => $i]) }}" class="app-cart-line__qty-form">
                                @csrf
                                <label class="app-muted app-text-sm" for="cart-qty-{{ $i }}">Qté</label>
                                <input type="number" id="cart-qty-{{ $i }}" name="qty" value="{{ $qty }}" min="1" max="{{ max(1, $max) }}" class="app-input">
                                <button type="submit" class="app-btn app-btn--sm app-btn--ghost">OK</button>
                            </form>
                            <form method="post" action="{{ route('app.'.$profileSlug.'.cart.line.remove', ['index' => $i]) }}" onsubmit="return confirm('Retirer cet article ?');">
                                @csrf
                                <button type="submit" class="app-text-link app-text-link--danger">Retirer</button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    @endforeach

    <p class="app-muted app-text-sm app-mt-md">
        <a href="{{ route('app.'.$profileSlug.'.marketplace', ['tab' => 'produits']) }}" class="app-text-link">Continuer les achats</a>
    </p>
@endif
