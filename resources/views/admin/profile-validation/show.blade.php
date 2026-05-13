@extends('admin.layout', ['title' => 'Profil — '.$user->name])

@php
    use Illuminate\Support\Facades\Storage;
    $pLabels = [
        'entrepreneur_batiment' => 'Entreprise BTP',
        'entreprise_fournisseur' => 'Fournisseur',
        'artisan' => 'Artisan',
        'particulier' => 'Particulier',
    ];
    $vBadge = function ($s): array {
        return match ($s) {
            'pending' => ['pending', 'En attente de validation'],
            'approved' => ['ok', 'Validé'],
            'rejected' => ['no', 'Rejeté'],
            'changes_requested' => ['mute', 'Modifications demandées'],
            default => ['mute', $s ?? '—'],
        };
    };
    [$vb, $vtext] = $vBadge($user->profile_validation_status ?? \App\Models\User::VALIDATION_APPROVED);
    $card = $user->artisanBusinessCard;
    $portfolioUrl = $card?->portfolio_path ? Storage::disk('public')->url($card->portfolio_path) : null;
@endphp

@section('content')

<div class="admin-page-head">
    <div>
        <h1>{{ $user->name }}</h1>
        <p class="admin-page-head__sub">{{ $pLabels[$user->profile_type] ?? $user->profile_type }} · <span class="badge b-{{ $vb }}">{{ $vtext }}</span></p>
    </div>
    <div class="admin-page-head__actions">
        <a href="{{ route('admin.profile-validation.index', ['status' => $user->profile_validation_status]) }}" class="admin-btn admin-btn--ghost admin-btn--sm">Retour liste</a>
        <a href="{{ route('admin.users.edit', $user) }}" class="admin-btn admin-btn--soft admin-btn--sm">Fiche utilisateur</a>
    </div>
</div>

<div class="card" style="margin-bottom:1rem">
    <h3 class="admin-card-title">Informations</h3>
    <div style="display:grid; gap:0.35rem 1.5rem; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); font-size:0.95rem">
        <div><span style="color:var(--text-3)">E-mail</span><br><strong>{{ $user->email }}</strong></div>
        <div><span style="color:var(--text-3)">Téléphone</span><br>{{ $user->phone ?? '—' }}</div>
        <div><span style="color:var(--text-3)">Ville / commune</span><br>{{ $user->city ?? '—' }} @if($user->commune) · {{ $user->commune }} @endif</div>
        <div><span style="color:var(--text-3)">Profil complété</span><br>{{ $user->profile_completed_at?->format('d/m/Y H:i') ?? '—' }}</div>
    </div>
    @if($user->bio)
        <p style="margin-top:1rem"><span style="color:var(--text-3)">Description</span></p>
        <p style="white-space:pre-wrap">{{ $user->bio }}</p>
    @endif
    @if($user->company_description)
        <p style="margin-top:1rem"><span style="color:var(--text-3)">Entreprise</span></p>
        <p style="white-space:pre-wrap">{{ $user->company_description }}</p>
    @endif
</div>

<div class="card" style="margin-bottom:1rem">
    <h3 class="admin-card-title">Documents</h3>
    @forelse($documentRows as $row)
        <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:0.5rem 0; border-bottom:1px solid var(--border-1)">
            <div>
                <strong>{{ $row['label'] }}</strong>
                @if($row['original_filename'])
                    <span style="color:var(--text-3); font-size:0.9em"> — {{ $row['original_filename'] }}</span>
                @endif
            </div>
            @if($row['url'])
                <a href="{{ $row['url'] }}" target="_blank" rel="noopener" class="admin-link">Ouvrir</a>
            @else
                <span style="color:var(--text-3)">—</span>
            @endif
        </div>
    @empty
        <p style="color:var(--text-3)">Aucun document indexé.</p>
    @endforelse
</div>

@if($card)
<div class="card" style="margin-bottom:1rem">
    <h3 class="admin-card-title">Carte de visite artisan</h3>
    <p>{{ $card->display_name ?? '—' }} — {{ $card->profession ?? '' }}</p>
    @if($portfolioUrl)
        <p><a href="{{ $portfolioUrl }}" target="_blank" rel="noopener" class="admin-link">Portfolio (fichier)</a></p>
    @endif
</div>
@endif

<div class="card" style="margin-bottom:1rem">
    <h3 class="admin-card-title">Aperçu catalogue</h3>
    <p style="color:var(--text-3); font-size:0.9em">Produits et services récents liés au compte.</p>
    <div style="display:grid; gap:1rem; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); margin-top:0.75rem">
        <div>
            <strong>Produits ({{ $user->products_count }})</strong>
            <ul style="margin:0.35rem 0 0 1rem">
                @foreach($user->products as $p)
                    <li><a href="{{ route('admin.products.show', $p) }}" class="admin-link">{{ $p->title }}</a></li>
                @endforeach
            </ul>
        </div>
        <div>
            <strong>Services ({{ $user->services_count }})</strong>
            <ul style="margin:0.35rem 0 0 1rem">
                @foreach($user->services as $s)
                    <li><a href="{{ route('admin.services.show', $s) }}" class="admin-link">{{ $s->title }}</a></li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

@if($user->profile_validation_note)
<div class="card admin-card--alert" style="margin-bottom:1rem">
    <strong>Message validation</strong>
    <p style="margin:0.35rem 0 0; white-space:pre-wrap">{{ $user->profile_validation_note }}</p>
</div>
@endif

<div class="card">
    <h3 class="admin-card-title">Décision</h3>
    <form method="post" action="{{ route('admin.profile-validation.update', $user) }}" style="margin-bottom:1rem">
        @csrf
        @method('PUT')
        <input type="hidden" name="action" value="approve">
        <button type="submit" class="admin-btn admin-btn--navy">Valider le profil</button>
    </form>
    <form method="post" action="{{ route('admin.profile-validation.update', $user) }}" style="margin-bottom:1rem">
        @csrf
        @method('PUT')
        <input type="hidden" name="action" value="reject">
        <div class="form-row">
            <label for="note_reject">Motif de refus (optionnel)</label>
            <textarea id="note_reject" name="note" rows="2" style="width:100%; max-width:480px"></textarea>
        </div>
        <button type="submit" class="admin-btn" style="background:#b91c1c;border-color:#b91c1c">Rejeter</button>
    </form>
    <form method="post" action="{{ route('admin.profile-validation.update', $user) }}">
        @csrf
        @method('PUT')
        <input type="hidden" name="action" value="changes_requested">
        <div class="form-row">
            <label for="note_changes">Demander des modifications</label>
            <textarea id="note_changes" name="note" rows="3" style="width:100%; max-width:480px" required placeholder="Précisez les corrections attendues…">{{ old('note') }}</textarea>
            @error('note')<p style="color:#b91c1c;font-size:0.9em">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="admin-btn admin-btn--soft">Envoyer la demande de modifications</button>
    </form>
</div>

@endsection
