@extends('admin.layout', ['title' => 'Modération'])

@section('content')

<div class="admin-page-head">
    <div>
        <h1>Modération</h1>
        <p class="admin-page-head__sub">Produits et services en attente de validation catalogue.</p>
    </div>
</div>

<div class="admin-kpi-grid" style="margin-bottom:1rem">
    <article class="admin-kpi">
        <p class="admin-kpi__label">Produits en attente</p>
        <p class="admin-kpi__value">{{ $counts['products_pending'] }}</p>
    </article>
    <article class="admin-kpi">
        <p class="admin-kpi__label">Services en attente</p>
        <p class="admin-kpi__value">{{ $counts['services_pending'] }}</p>
    </article>
</div>

<div class="card" style="margin-bottom:1rem; padding:0">
    <div class="admin-card-h" style="padding:1rem 1rem 0">
        <h3 class="admin-card-title">Produits — en attente</h3>
        <a href="{{ route('admin.products.index') }}" class="admin-btn admin-btn--soft admin-btn--sm">Liste complète</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Titre</th><th>Fournisseur</th><th></th></tr></thead>
            <tbody>
            @forelse($pendingProducts as $p)
                <tr>
                    <td><strong>{{ $p->title }}</strong></td>
                    <td>{{ $p->user?->name ?? '—' }}</td>
                    <td class="row-actions"><a class="admin-link" href="{{ route('admin.products.show', $p) }}">Modérer</a></td>
                </tr>
            @empty
                <tr><td colspan="3" style="text-align:center; padding:1rem; color:var(--text-3)">Aucun.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card" style="padding:0">
    <div class="admin-card-h" style="padding:1rem 1rem 0">
        <h3 class="admin-card-title">Services — en attente</h3>
        <a href="{{ route('admin.services.index') }}" class="admin-btn admin-btn--soft admin-btn--sm">Liste complète</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Titre</th><th>Prestataire</th><th></th></tr></thead>
            <tbody>
            @forelse($pendingServices as $s)
                <tr>
                    <td><strong>{{ $s->title }}</strong></td>
                    <td>{{ $s->user?->name ?? '—' }}</td>
                    <td class="row-actions"><a class="admin-link" href="{{ route('admin.services.show', $s) }}">Modérer</a></td>
                </tr>
            @empty
                <tr><td colspan="3" style="text-align:center; padding:1rem; color:var(--text-3)">Aucun.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
