@extends('admin.layout', ['title' => 'Services'])

@section('content')

<div class="admin-page-head">
    <div>
        <h1>Services</h1>
        <p class="admin-page-head__sub">{{ $services->total() }} prestation(s) — artisan &amp; entrepreneur.</p>
    </div>
</div>

<form method="get" class="card admin-filters" role="search">
    <div class="form-row" style="flex:1; min-width:220px">
        <label for="q">Recherche</label>
        <input id="q" type="text" name="q" value="{{ request('q') }}" placeholder="Titre du service…">
    </div>
    <div class="form-row">
        <label for="service_kind">Type</label>
        <select id="service_kind" name="service_kind">
            <option value="">Tous les types</option>
            <option value="artisan" @selected(request('service_kind')==='artisan')>Artisan</option>
            <option value="entrepreneur" @selected(request('service_kind')==='entrepreneur')>Entrepreneur</option>
        </select>
    </div>
    <div class="form-row">
        <label for="status">Statut</label>
        <select id="status" name="status">
            <option value="">Tous les statuts</option>
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
        @if(request('q') || request('status') || request('service_kind'))
            <a href="{{ route('admin.services.index') }}" class="admin-btn admin-btn--ghost">Réinitialiser</a>
        @endif
    </div>
</form>

<div class="card" style="padding:0">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Type</th>
                    <th>Lieu</th>
                    <th>Notation</th>
                    <th>Statut</th>
                    <th class="row-actions"></th>
                </tr>
            </thead>
            <tbody>
            @forelse($services as $s)
                <tr>
                    <td>
                        <strong>{{ $s->title }}</strong>
                        <div style="color:var(--text-3); font-size:0.78rem; margin-top:2px">{{ $s->user?->name ?? '—' }}</div>
                    </td>
                    <td><span class="badge b-mute">{{ $s->service_kind }}</span></td>
                    <td>{{ $s->location ?? '—' }}</td>
                    <td>★ {{ $s->rating }} <span style="color:var(--text-3)">({{ $s->review_count }})</span></td>
                    <td>
                        @if($s->status === 'approved')<span class="badge b-ok">Approuvé</span>
                        @elseif($s->status === 'rejected')<span class="badge b-no">Rejeté</span>
                        @else <span class="badge b-pending">En attente</span>
                        @endif
                    </td>
                    <td class="row-actions">
                        <a class="admin-link" href="{{ route('admin.services.show', $s) }}">
                            <svg width="14" height="14" aria-hidden="true"><use href="#admin-ico-eye" xlink:href="#admin-ico-eye"/></svg>
                            Modérer
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center; padding:2rem; color:var(--text-3)">Aucun service ne correspond aux filtres.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:0 22px 18px">
        {{ $services->links() }}
    </div>
</div>
@endsection
