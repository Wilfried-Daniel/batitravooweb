@extends('admin.layout', ['title' => 'Candidatures'])

@php
    $cBadge = fn (string $s) => match ($s) {
        'accepte' => ['ok', 'Accepté'],
        'rejete' => ['no', 'Rejeté'],
        default => ['pending', 'Reçu'],
    };
@endphp

@section('content')

<div class="admin-page-head">
    <div>
        <h1>Candidatures</h1>
        <p class="admin-page-head__sub">{{ $candidatures->total() }} candidature(s) — toutes annonces confondues.</p>
    </div>
</div>

<form method="get" class="card admin-filters" role="search">
    <div class="form-row">
        <label for="status">Statut</label>
        <select id="status" name="status" style="max-width:220px">
            <option value="">Tous les statuts</option>
            <option value="recu" @selected(request('status')==='recu')>Reçu</option>
            <option value="accepte" @selected(request('status')==='accepte')>Accepté</option>
            <option value="rejete" @selected(request('status')==='rejete')>Rejeté</option>
        </select>
    </div>
    <button type="submit" class="admin-btn admin-btn--navy">
        <svg width="16" height="16" aria-hidden="true"><use href="#admin-ico-filter" xlink:href="#admin-ico-filter"/></svg>
        Filtrer
    </button>
    @if(request('status'))
        <a href="{{ route('admin.candidatures.index') }}" class="admin-btn admin-btn--ghost">Réinitialiser</a>
    @endif
</form>

<div class="card" style="padding:0">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Besoin</th>
                    <th>Candidat</th>
                    <th>Métier</th>
                    <th>Statut</th>
                    <th class="row-actions">Action</th>
                </tr>
            </thead>
            <tbody>
            @forelse($candidatures as $c)
                @php([$badge, $bLabel] = $cBadge($c->status))
                <tr>
                    <td><a class="admin-link" href="{{ route('admin.besoins.show', $c->besoin) }}">{{ \Illuminate\Support\Str::limit($c->besoin?->title ?? '—', 40) }}</a></td>
                    <td><strong>{{ $c->display_name ?? $c->applicant?->name }}</strong></td>
                    <td>{{ $c->profession ?? '—' }}</td>
                    <td><span class="badge b-{{ $badge }}">{{ $bLabel }}</span></td>
                    <td>
                        <form method="post" action="{{ route('admin.candidatures.update', $c) }}" class="admin-action-form">
                            @csrf
                            @method('PUT')
                            <select name="status" class="admin-select-inline" aria-label="Statut">
                                <option value="recu" @selected($c->status==='recu')>Reçu</option>
                                <option value="accepte" @selected($c->status==='accepte')>Accepté</option>
                                <option value="rejete" @selected($c->status==='rejete')>Rejeté</option>
                            </select>
                            <button type="submit" class="admin-btn admin-btn--navy admin-btn--sm">Mettre à jour</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center; padding:2rem; color:var(--text-3)">Aucune candidature.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:0 22px 18px">
        {{ $candidatures->links() }}
    </div>
</div>
@endsection
