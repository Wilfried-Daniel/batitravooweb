@extends('admin.layout', ['title' => 'Validation des profils'])

@php
    $pLabels = [
        'entrepreneur_batiment' => 'Entreprise BTP',
        'entreprise_fournisseur' => 'Fournisseur',
        'artisan' => 'Artisan',
        'particulier' => 'Particulier',
    ];
    $statusLabel = match ($currentStatus) {
        \App\Models\User::VALIDATION_PENDING => 'en attente',
        \App\Models\User::VALIDATION_APPROVED => 'validés',
        \App\Models\User::VALIDATION_REJECTED => 'rejetés',
        \App\Models\User::VALIDATION_CHANGES_REQUESTED => 'modifications demandées',
        default => $currentStatus,
    };
    $vBadge = function ($s): array {
        return match ($s) {
            'pending' => ['pending', 'En attente'],
            'approved' => ['ok', 'Validé'],
            'rejected' => ['no', 'Rejeté'],
            'changes_requested' => ['mute', 'Modifications'],
            default => ['mute', $s ?? '—'],
        };
    };
@endphp

@section('content')

<div class="admin-page-head">
    <div>
        <h1>Validation des profils</h1>
        <p class="admin-page-head__sub">Comptes {{ $statusLabel }} (profil complété). En attente : <strong>{{ number_format($counts['pending'], 0, ',', ' ') }}</strong></p>
    </div>
    <div class="admin-page-head__actions">
        <a href="{{ route('admin.profile-validation.index', ['status' => 'pending']) }}" class="admin-btn admin-btn--soft admin-btn--sm">Comptes en attente</a>
    </div>
</div>

<div class="admin-kpi-grid" style="margin-bottom:1rem">
    <article class="admin-kpi">
        <p class="admin-kpi__label">En attente</p>
        <p class="admin-kpi__value">{{ $counts['pending'] }}</p>
    </article>
    <article class="admin-kpi">
        <p class="admin-kpi__label">Validés</p>
        <p class="admin-kpi__value">{{ $counts['approved'] }}</p>
    </article>
    <article class="admin-kpi">
        <p class="admin-kpi__label">Rejetés</p>
        <p class="admin-kpi__value">{{ $counts['rejected'] }}</p>
    </article>
    <article class="admin-kpi">
        <p class="admin-kpi__label">Modifs demandées</p>
        <p class="admin-kpi__value">{{ $counts['changes'] }}</p>
    </article>
</div>

<form method="get" class="card admin-filters" role="search">
    <input type="hidden" name="status" value="{{ $currentStatus }}">
    <div class="form-row" style="flex:1; min-width:200px">
        <label for="q">Recherche</label>
        <input id="q" type="text" name="q" value="{{ request('q') }}" placeholder="Nom, e-mail, téléphone…">
    </div>
    <div class="form-row">
        <label for="profile_type">Type</label>
        <select id="profile_type" name="profile_type" style="max-width:240px">
            <option value="">Tous</option>
            @foreach($pLabels as $k => $l)
                <option value="{{ $k }}" @selected(request('profile_type') === $k)>{{ $l }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-row">
        <button type="submit" class="admin-btn admin-btn--navy">Filtrer</button>
    </div>
    <div class="form-row">
        @if(request('q') || request('profile_type'))
            <a href="{{ route('admin.profile-validation.index', ['status' => $currentStatus]) }}" class="admin-btn admin-btn--ghost admin-btn--sm">Réinitialiser</a>
        @endif
    </div>
</form>

<div class="card" style="padding:0">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Profil</th>
                    <th>Statut</th>
                    <th>Complété le</th>
                    <th class="row-actions"></th>
                </tr>
            </thead>
            <tbody>
            @forelse($users as $u)
                @php([$vb, $vtext] = $vBadge($u->profile_validation_status ?? \App\Models\User::VALIDATION_APPROVED))
                <tr>
                    <td><strong>{{ $u->name }}</strong><br><span style="color:var(--text-3);font-size:0.9em">{{ $u->email }}</span></td>
                    <td>{{ $pLabels[$u->profile_type] ?? $u->profile_type ?? '—' }}</td>
                    <td><span class="badge b-{{ $vb }}">{{ $vtext }}</span></td>
                    <td>{{ $u->profile_completed_at?->format('d/m/Y H:i') ?? '—' }}</td>
                    <td class="row-actions">
                        <a class="admin-link" href="{{ route('admin.profile-validation.show', $u) }}">Détail</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center; padding:1.5rem; color:var(--text-3)">Aucun compte.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $users->links() }}
</div>

@endsection
