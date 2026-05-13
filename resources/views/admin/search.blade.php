@extends('admin.layout', ['title' => 'Recherche'])

@section('content')

<div class="admin-page-head">
    <div>
        <h1>Recherche</h1>
        <p class="admin-page-head__sub">Utilisateurs app, produits, services, devis, besoins, tickets support.</p>
    </div>
</div>

<form method="get" action="{{ route('admin.search') }}" class="card" style="margin-bottom:1rem; padding:1rem 1rem 1rem 1rem; display:flex; gap:0.75rem; flex-wrap:wrap; align-items:center">
    <input type="search" id="glob-q" name="q" value="{{ $q }}" placeholder="Au moins 2 caractères…" aria-label="Terme de recherche" class="admin-field__control" style="flex:1; min-width:220px; max-width:480px; padding:0.55rem 0.75rem; border-radius:10px; border:1px solid var(--border)" autofocus>
    <button type="submit" class="admin-btn admin-btn--primary admin-btn--sm">Rechercher</button>
</form>

@if(!$hasQuery)
    <div class="card" style="padding:1.25rem; color:var(--text-3)">
        Saisissez au moins <strong>2 caractères</strong> pour lancer une recherche.
    </div>
@elseif($total === 0)
    <div class="card" style="padding:1.25rem; color:var(--text-3)">
        Aucun résultat pour « {{ $q }} ».
    </div>
@else
    <p style="margin:0 0 1rem; color:var(--text-3); font-size:0.95rem">{{ $total }} résultat(s)</p>
@endif

@if($hasQuery && $users->isNotEmpty())
<div class="card" style="margin-bottom:1rem; padding:0">
    <div class="admin-card-h" style="padding:1rem 1rem 0">
        <h3 class="admin-card-title">Utilisateurs app</h3>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Nom</th><th>E-mail</th><th></th></tr></thead>
            <tbody>
            @foreach($users as $u)
                <tr>
                    <td><strong>{{ $u->name }}</strong></td>
                    <td>{{ $u->email }}</td>
                    <td class="row-actions"><a class="admin-link" href="{{ route('admin.users.edit', $u) }}">Fiche</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@if($hasQuery && $products->isNotEmpty())
<div class="card" style="margin-bottom:1rem; padding:0">
    <div class="admin-card-h" style="padding:1rem 1rem 0">
        <h3 class="admin-card-title">Produits</h3>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Titre</th><th>Fournisseur</th><th></th></tr></thead>
            <tbody>
            @foreach($products as $p)
                <tr>
                    <td><strong>{{ $p->title }}</strong></td>
                    <td>{{ $p->user?->name ?? '—' }}</td>
                    <td class="row-actions"><a class="admin-link" href="{{ route('admin.products.show', $p) }}">Voir</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@if($hasQuery && $services->isNotEmpty())
<div class="card" style="margin-bottom:1rem; padding:0">
    <div class="admin-card-h" style="padding:1rem 1rem 0">
        <h3 class="admin-card-title">Services</h3>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Titre</th><th>Prestataire</th><th></th></tr></thead>
            <tbody>
            @foreach($services as $s)
                <tr>
                    <td><strong>{{ $s->title }}</strong></td>
                    <td>{{ $s->user?->name ?? '—' }}</td>
                    <td class="row-actions"><a class="admin-link" href="{{ route('admin.services.show', $s) }}">Voir</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@if($hasQuery && $devis->isNotEmpty())
<div class="card" style="margin-bottom:1rem; padding:0">
    <div class="admin-card-h" style="padding:1rem 1rem 0">
        <h3 class="admin-card-title">Devis</h3>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Titre</th><th>Client</th><th></th></tr></thead>
            <tbody>
            @foreach($devis as $d)
                <tr>
                    <td><strong>{{ $d->title }}</strong></td>
                    <td>{{ $d->client_name ?? '—' }}</td>
                    <td class="row-actions"><a class="admin-link" href="{{ route('admin.devis.show', $d) }}">Voir</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@if($hasQuery && $besoins->isNotEmpty())
<div class="card" style="margin-bottom:1rem; padding:0">
    <div class="admin-card-h" style="padding:1rem 1rem 0">
        <h3 class="admin-card-title">Besoins</h3>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Titre</th><th>Auteur</th><th></th></tr></thead>
            <tbody>
            @foreach($besoins as $b)
                <tr>
                    <td><strong>{{ $b->title }}</strong></td>
                    <td>{{ $b->user?->name ?? '—' }}</td>
                    <td class="row-actions"><a class="admin-link" href="{{ route('admin.besoins.show', $b) }}">Voir</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@if($hasQuery && $tickets->isNotEmpty())
<div class="card" style="margin-bottom:1rem; padding:0">
    <div class="admin-card-h" style="padding:1rem 1rem 0">
        <h3 class="admin-card-title">Tickets support</h3>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Sujet</th><th>Demandeur</th><th></th></tr></thead>
            <tbody>
            @foreach($tickets as $t)
                <tr>
                    <td><strong>{{ $t->subject }}</strong></td>
                    <td>{{ $t->user?->name ?? '—' }}</td>
                    <td class="row-actions"><a class="admin-link" href="{{ route('admin.support.tickets.show', $t) }}">Voir</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection
