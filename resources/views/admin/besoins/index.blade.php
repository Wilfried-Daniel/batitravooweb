@extends('admin.layout', ['title' => 'Marketplace — Besoins'])

@php
    $bLabels = ['open' => 'Ouvert', 'in_progress' => 'En cours', 'closed' => 'Clôturé', 'cancelled' => 'Annulé'];
    $bBadge = fn (string $s) => match ($s) {
        'open' => 'ok', 'in_progress' => 'pending', 'cancelled' => 'no', default => 'mute',
    };
@endphp

@section('content')

<div class="admin-page-head">
    <div>
        <h1>Marketplace — Besoins</h1>
        <p class="admin-page-head__sub">{{ $besoins->total() }} annonce(s) — appels d’offres &amp; recherches.</p>
    </div>
</div>

<form method="get" class="card admin-filters" role="search">
    <div class="form-row" style="flex:1; min-width:220px">
        <label for="q">Recherche</label>
        <input id="q" type="text" name="q" value="{{ request('q') }}" placeholder="Titre du besoin…">
    </div>
    <div class="form-row">
        <label for="status">Statut</label>
        <select id="status" name="status">
            <option value="">Tous les statuts</option>
            @foreach($bLabels as $k => $l)
                <option value="{{ $k }}" @selected(request('status')===$k)>{{ $l }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="admin-btn admin-btn--navy">
        <svg width="16" height="16" aria-hidden="true"><use href="#admin-ico-filter" xlink:href="#admin-ico-filter"/></svg>
        Filtrer
    </button>
    @if(request('q') || request('status'))
        <a href="{{ route('admin.besoins.index') }}" class="admin-btn admin-btn--ghost">Réinitialiser</a>
    @endif
</form>

<div class="card" style="padding:0">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Budget</th>
                    <th>Lieu</th>
                    <th>Candidatures</th>
                    <th>Statut</th>
                    <th class="row-actions"></th>
                </tr>
            </thead>
            <tbody>
            @forelse($besoins as $b)
                <tr>
                    <td>
                        <strong>{{ $b->title }}</strong>
                        <div style="color:var(--text-3); font-size:0.78rem; margin-top:2px">{{ $b->user?->name ?? '—' }}</div>
                    </td>
                    <td>{{ $b->budget ?? '—' }}</td>
                    <td>{{ $b->place ?? '—' }}</td>
                    <td><span class="badge b-mute">{{ $b->candidature_count }}</span></td>
                    <td><span class="badge b-{{ $bBadge($b->status) }}">{{ $bLabels[$b->status] ?? $b->status }}</span></td>
                    <td class="row-actions">
                        <a class="admin-link" href="{{ route('admin.besoins.show', $b) }}">
                            <svg width="14" height="14" aria-hidden="true"><use href="#admin-ico-eye" xlink:href="#admin-ico-eye"/></svg>
                            Détail
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center; padding:2rem; color:var(--text-3)">Aucun besoin ne correspond aux filtres.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:0 22px 18px">
        {{ $besoins->links() }}
    </div>
</div>
@endsection
