@extends('admin.layout', ['title' => 'Service — '.$service->title])

@php
    $statusBadge = match ($service->status) {
        'approved' => ['ok', 'En ligne'],
        'pending' => ['pending', 'En attente'],
        'rejected' => ['no', 'Rejeté'],
        default => ['mute', $service->status],
    };
@endphp

@section('content')

<div class="admin-page-head">
    <div>
        <h1>{{ $service->title }}</h1>
        <p class="admin-page-head__sub">Service — prestataire {{ $service->user?->name ?? '—' }}</p>
    </div>
    <div class="admin-page-head__actions">
        <a href="{{ route('admin.services.index') }}" class="admin-btn admin-btn--ghost">
            <svg width="16" height="16" aria-hidden="true"><use href="#admin-ico-arrow-left" xlink:href="#admin-ico-arrow-left"/></svg>
            Retour
        </a>
    </div>
</div>

<div class="card">
    <div class="admin-card-h">
        <h3 class="admin-card-title">Informations</h3>
        <span class="badge b-{{ $statusBadge[0] }}">{{ $statusBadge[1] }}</span>
    </div>
    <dl class="admin-detail">
        <dt>Prestataire</dt>
        <dd>{{ $service->user?->name ?? '—' }}</dd>
        <dt>Type</dt>
        <dd><span class="badge b-mute">{{ $service->service_kind }}</span></dd>
        <dt>Catégorie</dt>
        <dd>{{ $service->category?->name ?? '—' }}</dd>
        <dt>Description</dt>
        <dd>{{ $service->description ?: '—' }}</dd>
        <dt>Lieu</dt>
        <dd>{{ $service->location ?? '—' }}</dd>
        <dt>Mode de prix (API)</dt>
        <dd>
            @if($service->price_variables)
                <strong>Prix variable</strong>
                @if(filled($service->price_fixed_label)) — {{ $service->price_fixed_label }}@endif
            @else
                <strong>Prix fixe</strong>
                @if(filled($service->price_fixed_label)) — {{ $service->price_fixed_label }}@else <span style="color:var(--text-3)">(libellé non renseigné)</span>@endif
            @endif
        </dd>
        <dt>Prix variables (booléen)</dt>
        <dd>{{ $service->price_variables ? 'Oui' : 'Non' }}</dd>
        <dt>Libellé prix (champ brut)</dt>
        <dd>{{ $service->price_fixed_label ?: '—' }}</dd>
        @if($service->image_path)
            <dt>Image (fichier uploadé)</dt>
            <dd>
                <img src="{{ asset('storage/'.$service->image_path) }}" alt="" style="max-width:280px;border-radius:10px;display:block;margin-top:6px">
            </dd>
        @elseif($service->image_url && (str_starts_with($service->image_url, 'http://') || str_starts_with($service->image_url, 'https://')))
            <dt>Image (lien externe)</dt>
            <dd><a class="admin-link" href="{{ $service->image_url }}" target="_blank" rel="noopener">Ouvrir →</a></dd>
        @endif
    </dl>
</div>

<div class="card" style="max-width:580px">
    <div class="admin-card-h">
        <h3 class="admin-card-title">Validation</h3>
    </div>
    <form method="post" action="{{ route('admin.services.update', $service) }}">
        @csrf
        @method('PUT')
        <x-admin.field
            name="status"
            type="select"
            label="Statut"
            :value="$service->status"
            :options="['pending' => 'En attente', 'approved' => 'Approuvé', 'rejected' => 'Rejeté']"
            required
            maxWidth="100%"
        />
        <x-admin.field
            name="admin_notes"
            type="textarea"
            label="Notes internes"
            :value="$service->admin_notes"
            maxWidth="100%"
            rows="3"
        />
        <div class="admin-form-actions">
            <button type="submit" class="admin-btn admin-btn--orange">
                <svg width="16" height="16" aria-hidden="true"><use href="#admin-ico-check" xlink:href="#admin-ico-check"/></svg>
                Enregistrer
            </button>
            <a href="{{ route('admin.services.index') }}" class="admin-btn admin-btn--ghost">Annuler</a>
        </div>
    </form>
</div>
@endsection
