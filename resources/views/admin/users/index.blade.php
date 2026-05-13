@extends('admin.layout', ['title' => 'Utilisateurs'])

@php
    $pLabels = [
        'entrepreneur_batiment' => 'Entrepreneur bâtiment',
        'entreprise_fournisseur' => 'Entreprise / fournisseur',
        'artisan' => 'Artisan',
        'particulier' => 'Particulier',
    ];
    $vShort = function ($user): string {
        if (! $user->profile_completed_at) {
            return 'Profil incomplet';
        }
        return match ($user->profile_validation_status ?? \App\Models\User::VALIDATION_APPROVED) {
            'pending' => 'Validation : attente',
            'approved' => 'Validation : OK',
            'rejected' => 'Validation : refus',
            'changes_requested' => 'Validation : modifs',
            default => '—',
        };
    };
@endphp

@section('content')

<div class="admin-page-head">
    <div>
        <h1>Utilisateurs</h1>
        <p class="admin-page-head__sub">{{ $users->total() }} compte(s) — comptes mobiles synchronisés.</p>
    </div>
</div>

<form method="get" class="card admin-filters" role="search">
    <div class="form-row" style="flex:1; min-width:220px">
        <label for="q">Recherche</label>
        <input id="q" type="text" name="q" value="{{ request('q') }}" placeholder="Nom, e-mail, téléphone…">
    </div>
    <div class="form-row">
        <label for="profile_type">Profil</label>
        <select id="profile_type" name="profile_type" style="max-width:240px">
            <option value="">Tous les profils</option>
            @foreach($pLabels as $k => $l)
                <option value="{{ $k }}" @selected(request('profile_type') === $k)>{{ $l }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-row">
        <label for="validation_status">Validation profil</label>
        <select id="validation_status" name="validation_status" style="max-width:220px">
            <option value="">Tous</option>
            <option value="pending" @selected(request('validation_status') === 'pending')>En attente</option>
            <option value="approved" @selected(request('validation_status') === 'approved')>Validé</option>
            <option value="rejected" @selected(request('validation_status') === 'rejected')>Rejeté</option>
            <option value="changes_requested" @selected(request('validation_status') === 'changes_requested')>Modifs demandées</option>
        </select>
    </div>
    <div class="form-row">
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-weight:500;color:var(--text-2);text-transform:none;letter-spacing:normal;font-size:0.86rem;margin-bottom:6px">
            <input type="checkbox" name="inactive_only" value="1" @checked(request('inactive_only'))> Inactifs uniquement
        </label>
    </div>
    <button type="submit" class="admin-btn admin-btn--navy">
        <svg width="16" height="16" aria-hidden="true"><use href="#admin-ico-filter" xlink:href="#admin-ico-filter"/></svg>
        Filtrer
    </button>
    @if(request('q') || request('profile_type') || request('inactive_only') || request('validation_status'))
        <a href="{{ route('admin.users.index') }}" class="admin-btn admin-btn--ghost">Réinitialiser</a>
    @endif
</form>

<div class="card" style="padding:0">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>E-mail</th>
                    <th>Profil</th>
                    <th>Validation</th>
                    <th>Téléphone</th>
                    <th>État</th>
                    <th class="row-actions"></th>
                </tr>
            </thead>
            <tbody>
            @forelse($users as $u)
                <tr>
                    <td><strong>{{ $u->name }}</strong></td>
                    <td>{{ $u->email }}</td>
                    <td>{{ $pLabels[$u->profile_type] ?? $u->profile_type ?? '—' }}</td>
                    <td style="font-size:0.9em">{{ $vShort($u) }}</td>
                    <td>{{ $u->phone ?? '—' }}</td>
                    <td>
                        @if($u->is_active)
                            <span class="badge b-ok">Actif</span>
                        @else
                            <span class="badge b-no">Inactif</span>
                        @endif
                    </td>
                    <td class="row-actions">
                        <a class="admin-link" href="{{ route('admin.users.edit', $u) }}">
                            <svg width="14" height="14" aria-hidden="true"><use href="#admin-ico-pencil" xlink:href="#admin-ico-pencil"/></svg>
                            Modifier
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center; padding:2rem; color:var(--text-3)">Aucun utilisateur ne correspond aux filtres.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:0 22px 18px">
        {{ $users->links() }}
    </div>
</div>
@endsection
