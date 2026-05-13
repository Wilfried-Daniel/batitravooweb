@extends('admin.layout', ['title' => 'Catégories'])

@php
    $appliesLabels = ['product' => 'Produits', 'service' => 'Services', 'both' => 'Les deux'];
@endphp

@section('content')

<div class="admin-page-head">
    <div>
        <h1>Catégories</h1>
        <p class="admin-page-head__sub">Libellés utilisés côté produits et services dans la marketplace.</p>
    </div>
    <div class="admin-page-head__actions">
        <a href="{{ route('admin.categories.create') }}" class="admin-btn admin-btn--orange">
            <svg width="16" height="16" aria-hidden="true"><use href="#admin-ico-plus" xlink:href="#admin-ico-plus"/></svg>
            Nouvelle catégorie
        </a>
    </div>
</div>

<div class="card" style="padding:0">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Applique à</th>
                    <th>Ordre</th>
                    <th>État</th>
                    <th class="row-actions"></th>
                </tr>
            </thead>
            <tbody>
            @forelse($categories as $c)
                <tr>
                    <td>
                        <strong>{{ $c->name }}</strong>
                        <div style="color:var(--text-3); font-size:0.78rem; margin-top:2px"><code>{{ $c->slug }}</code></div>
                    </td>
                    <td><span class="badge b-mute">{{ $appliesLabels[$c->applies_to] ?? $c->applies_to }}</span></td>
                    <td>{{ $c->sort_order }}</td>
                    <td>
                        @if($c->is_active)
                            <span class="badge b-ok">Actif</span>
                        @else
                            <span class="badge b-no">Désactivé</span>
                        @endif
                    </td>
                    <td class="row-actions" style="display:flex; flex-wrap:wrap; gap:6px">
                        <a class="admin-link" href="{{ route('admin.categories.edit', $c) }}">
                            <svg width="14" height="14" aria-hidden="true"><use href="#admin-ico-pencil" xlink:href="#admin-ico-pencil"/></svg>
                            Modifier
                        </a>
                        <form method="post" action="{{ route('admin.categories.destroy', $c) }}" class="admin-inline-form" onsubmit="return confirm('Supprimer cette catégorie ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="admin-link admin-link--danger" style="background:none;border:none;cursor:pointer;font:inherit">
                                <svg width="14" height="14" aria-hidden="true"><use href="#admin-ico-trash" xlink:href="#admin-ico-trash"/></svg>
                                Supprimer
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center; padding:2rem; color:var(--text-3)">Aucune catégorie.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:0 22px 18px">
        {{ $categories->links() }}
    </div>
</div>
@endsection
