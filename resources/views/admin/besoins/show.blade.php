@extends('admin.layout', ['title' => 'Besoin — '.$besoin->title])

@php
    $statusBadge = match ($besoin->status) {
        'open' => ['ok', 'Ouvert'],
        'in_progress' => ['pending', 'En cours'],
        'closed' => ['mute', 'Clôturé'],
        'cancelled' => ['no', 'Annulé'],
        default => ['mute', $besoin->status],
    };
    $candBadge = fn (string $s) => match ($s) {
        'accepte' => ['ok', 'Accepté'],
        'rejete' => ['no', 'Rejeté'],
        default => ['pending', 'Reçu'],
    };
@endphp

@section('content')

<div class="admin-page-head">
    <div>
        <h1>{{ $besoin->title }}</h1>
        <p class="admin-page-head__sub">Annonce de {{ $besoin->user?->name }} — {{ $besoin->candidatures->count() }} candidature(s).</p>
    </div>
    <div class="admin-page-head__actions">
        <a href="{{ route('admin.besoins.index') }}" class="admin-btn admin-btn--ghost">
            <svg width="16" height="16" aria-hidden="true"><use href="#admin-ico-arrow-left" xlink:href="#admin-ico-arrow-left"/></svg>
            Retour
        </a>
    </div>
</div>

<div class="card">
    <div class="admin-card-h">
        <h3 class="admin-card-title">Détails du besoin</h3>
        <span class="badge b-{{ $statusBadge[0] }}">{{ $statusBadge[1] }}</span>
    </div>
    <dl class="admin-detail">
        <dt>Auteur</dt>
        <dd>{{ $besoin->user?->name }} <span style="color:var(--text-3)">— {{ $besoin->user?->email }}</span></dd>
        <dt>Budget</dt>
        <dd>{{ $besoin->budget ?? '—' }}</dd>
        <dt>Début</dt>
        <dd>{{ $besoin->start_label ?? '—' }}</dd>
        <dt>Lieu</dt>
        <dd>{{ $besoin->place ?? '—' }}</dd>
        <dt>Durée</dt>
        <dd>{{ $besoin->duration ?? '—' }}</dd>
        <dt>Date courte</dt>
        <dd>{{ $besoin->short_date ?? '—' }}</dd>
        <dt>Description</dt>
        <dd>{{ $besoin->description ?? '—' }}</dd>
    </dl>
</div>

<div class="card" style="max-width:520px">
    <div class="admin-card-h">
        <h3 class="admin-card-title">Statut de l’annonce</h3>
    </div>
    <form method="post" action="{{ route('admin.besoins.update', $besoin) }}">
        @csrf
        @method('PUT')
        <x-admin.field
            name="status"
            type="select"
            label="Statut"
            :value="$besoin->status"
            :options="['open' => 'Ouvert', 'in_progress' => 'En cours', 'closed' => 'Clôturé', 'cancelled' => 'Annulé']"
            required
            maxWidth="100%"
        />
        <div class="admin-form-actions">
            <button type="submit" class="admin-btn admin-btn--orange">
                <svg width="16" height="16" aria-hidden="true"><use href="#admin-ico-check" xlink:href="#admin-ico-check"/></svg>
                Enregistrer
            </button>
            <a href="{{ route('admin.besoins.index') }}" class="admin-btn admin-btn--ghost">Annuler</a>
        </div>
    </form>
</div>

<div class="card">
    <div class="admin-card-h">
        <h3 class="admin-card-title">Candidatures</h3>
        <span class="badge b-mute">{{ $besoin->candidatures->count() }}</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Candidat</th><th>Date</th><th>Statut</th><th>Action</th></tr></thead>
            <tbody>
            @forelse($besoin->candidatures as $c)
                @php([$badge, $bLabel] = $candBadge($c->status))
                <tr>
                    <td>
                        <strong>{{ $c->display_name ?? $c->applicant?->name }}</strong>
                        <div style="color:var(--text-3); font-size:0.82rem; margin-top:2px">{{ $c->profession ?? '—' }}</div>
                    </td>
                    <td>{{ $c->posted_at?->format('d/m/Y H:i') ?? '—' }}</td>
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
                <tr><td colspan="4" style="text-align:center; padding:1.5rem; color:var(--text-3)">Aucune candidature.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
