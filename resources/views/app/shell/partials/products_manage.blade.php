@php
    $mp = $profileSlug;
    $productStatus = static function (?string $s): string {
        return match ($s) {
            'draft' => 'Brouillon',
            'pending' => 'En validation',
            'approved' => 'Validé',
            'rejected' => 'Refusé',
            default => $s ?? '—',
        };
    };
@endphp

<div class="app-manage-toolbar app-card">
    <a href="{{ route('app.'.$mp.'.products.create') }}" class="app-btn app-btn--inline app-btn--sm">Nouveau produit</a>
    <a href="{{ route('app.'.$mp.'.marketplace') }}" class="app-btn app-btn--secondary app-btn--sm">Marketplace</a>
    <a href="{{ route('app.'.$mp.'.messages') }}" class="app-btn app-btn--secondary app-btn--sm">Chat</a>
    <a href="{{ route('app.'.$mp.'.devis') }}" class="app-btn app-btn--secondary app-btn--sm">Mes commandes</a>
</div>

<div class="app-card app-mt">
    <div class="app-flex-between app-mb-sm" style="flex-wrap:wrap;gap:0.65rem;">
        <h2 class="app-section-title" style="margin:0;">Catalogue</h2>
    </div>
    @if (! empty($productsList) && count($productsList))
        <div class="app-table-wrap">
            <table class="app-table app-table--bordered">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                        <th>Stock</th>
                        <th>Vues</th>
                        <th>Statut</th>
                        <th class="app-table__col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productsList as $row)
                        @php $pid = (int) ($row['id'] ?? 0); @endphp
                        <tr>
                            <td><strong>{{ $row['title'] ?? '—' }}</strong></td>
                            <td class="app-muted">{{ $row['category']['name'] ?? '—' }}</td>
                            <td>{{ $row['price_display_fr'] ?? '—' }}</td>
                            <td>{{ (int) ($row['stock_units'] ?? 0) }}</td>
                            <td class="app-muted">{{ (int) ($row['views_count'] ?? 0) }}</td>
                            <td><span class="app-pill">{{ $productStatus($row['status'] ?? null) }}</span></td>
                            <td class="app-table__col-actions">
                                @if ($pid > 0)
                                    <a href="{{ route('app.'.$mp.'.marketplace.product', ['product' => $pid]) }}" class="app-text-link">Fiche publique</a>
                                    <span class="app-muted"> · </span>
                                    <a href="{{ route('app.'.$mp.'.products.edit', ['product' => $pid]) }}" class="app-text-link">Modifier</a>
                                    <form action="{{ route('app.'.$mp.'.products.destroy', ['product' => $pid]) }}" method="post" class="app-inline-form" onsubmit="return confirm('Supprimer ce produit ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="app-text-link app-text-link--danger">Supprimer</button>
                                    </form>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="app-muted app-mb-sm">Aucune référence pour le moment.</p>
        <a href="{{ route('app.'.$mp.'.products.create') }}" class="app-btn app-btn--inline">Ajouter un produit</a>
    @endif
</div>
