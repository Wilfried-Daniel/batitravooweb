@extends('admin.layout', ['title' => 'Produits'])

@section('content')

<div class="admin-page-head">
    <div>
        <h1>Produits</h1>
        <p class="admin-page-head__sub">{{ $products->total() }} produit(s) — modération &amp; mise en ligne.</p>
    </div>
</div>

<form method="get" class="card admin-filters" role="search">
    <div class="form-row" style="flex:1; min-width:220px">
        <label for="q">Recherche</label>
        <input id="q" type="text" name="q" value="{{ request('q') }}" placeholder="Titre du produit…">
    </div>
    <div class="form-row">
        <label for="status">Statut</label>
        <select id="status" name="status" style="max-width:200px">
            <option value="">Tous les statuts</option>
            <option value="draft" @selected(request('status')==='draft')>Brouillon</option>
            <option value="pending" @selected(request('status')==='pending')>En attente</option>
            <option value="approved" @selected(request('status')==='approved')>Approuvé</option>
            <option value="rejected" @selected(request('status')==='rejected')>Rejeté</option>
        </select>
    </div>
    <div class="form-row">
        <button type="submit" class="admin-btn admin-btn--navy">
            <svg width="16" height="16" aria-hidden="true"><use href="#admin-ico-filter" xlink:href="#admin-ico-filter"/></svg>
            Filtrer
        </button>
    </div>
    <div class="form-row">
        @if(request('q') || request('status'))
            <a href="{{ route('admin.products.index') }}" class="admin-btn admin-btn--ghost">Réinitialiser</a>
        @endif
    </div>
</form>

<div class="card" style="padding:0">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Catégorie</th>
                    <th>Prix (FCFA)</th>
                    <th>Stock</th>
                    <th>Vues</th>
                    <th>Statut</th>
                    <th class="row-actions"></th>
                </tr>
            </thead>
            <tbody>
            @forelse($products as $p)
                <tr>
                    <td>
                        <strong>{{ $p->title }}</strong>
                        <div style="color:var(--text-3); font-size:0.78rem; margin-top:2px">{{ $p->user?->name ?? '—' }}</div>
                    </td>
                    <td>{{ $p->category?->name ?? '—' }}</td>
                    <td><strong>{{ number_format($p->price_amount, 0, ',', ' ') }}</strong></td>
                    <td>{{ $p->stock_units }}</td>
                    <td>{{ $p->views_count }}</td>
                    <td>
                        @if($p->status === 'approved')<span class="badge b-ok">Approuvé</span>
                        @elseif($p->status === 'rejected')<span class="badge b-no">Rejeté</span>
                        @elseif($p->status === 'pending')<span class="badge b-pending">En attente</span>
                        @else <span class="badge b-mute">Brouillon</span>
                        @endif
                    </td>
                    <td class="row-actions">
                        <a class="admin-link" href="{{ route('admin.products.show', $p) }}">
                            <svg width="14" height="14" aria-hidden="true"><use href="#admin-ico-eye" xlink:href="#admin-ico-eye"/></svg>
                            Modérer
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center; padding:2rem; color:var(--text-3)">Aucun produit ne correspond aux filtres.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:0 22px 18px">
        {{ $products->links() }}
    </div>
</div>
@endsection
