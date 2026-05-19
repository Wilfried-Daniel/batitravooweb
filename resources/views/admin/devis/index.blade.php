@extends('admin.layout', ['title' => 'Devis'])

@php
    $sLabels = [
        'non_traite' => 'Non traité', 'en_cours' => 'En cours', 'envoye' => 'Envoyé', 'valide' => 'Validé', 'rejete' => 'Rejeté',
    ];
    $sBadge = function (string $s): string {
        return match ($s) {
            'valide', 'envoye' => 'ok',
            'en_cours', 'non_traite' => 'pending',
            'rejete' => 'no',
            default => 'mute',
        };
    };
@endphp

@section('content')

<div class="admin-page-head">
    <div>
        <h1>Devis</h1>
        <p class="admin-page-head__sub">{{ $devis->total() }} devis — suivi du cycle de vie côté entrepreneurs.</p>
    </div>
</div>

<form method="get" class="card admin-filters" role="search">
    <div class="form-row" style="flex:1; min-width:220px">
        <label for="q">Recherche</label>
        <input id="q" type="text" name="q" value="{{ request('q') }}" placeholder="Titre, client, référence…">
    </div>
    <div class="form-row">
        <label for="status">Statut</label>
        <select id="status" name="status">
            <option value="">Tous les statuts</option>
            @foreach($sLabels as $k => $l)
                <option value="{{ $k }}" @selected(request('status')===$k)>{{ $l }}</option>
            @endforeach
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
            <a href="{{ route('admin.devis.index') }}" class="admin-btn admin-btn--ghost">Réinitialiser</a>
        @endif
    </div>
</form>

<div class="card" style="padding:0">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Client</th>
                    <th>Référence</th>
                    <th>Lieu</th>
                    <th>Statut</th>
                    <th class="row-actions"></th>
                </tr>
            </thead>
            <tbody>
            @forelse($devis as $d)
                <tr>
                    <td><strong>{{ $d->title }}</strong></td>
                    <td>{{ $d->client_name }}</td>
                    <td><code>{{ $d->order_reference ?? '—' }}</code></td>
                    <td>{{ $d->place ?? '—' }}</td>
                    <td><span class="badge b-{{ $sBadge($d->status) }}">{{ $sLabels[$d->status] ?? $d->status }}</span></td>
                    <td class="row-actions">
                        <a class="admin-link" href="{{ route('admin.devis.show', $d) }}">
                            <svg width="14" height="14" aria-hidden="true"><use href="#admin-ico-eye" xlink:href="#admin-ico-eye"/></svg>
                            Détail
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center; padding:2rem; color:var(--text-3)">Aucun devis ne correspond aux filtres.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:0 22px 18px">
        {{ $devis->links() }}
    </div>
</div>
@endsection
