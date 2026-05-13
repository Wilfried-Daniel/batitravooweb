@extends('admin.layout', ['title' => 'Produit — '.$product->title])

@php
    $statusBadge = match ($product->status) {
        'approved' => ['ok', 'En ligne'],
        'pending' => ['pending', 'En attente'],
        'rejected' => ['no', 'Rejeté'],
        default => ['mute', 'Brouillon'],
    };
@endphp

@section('content')

<div class="admin-page-head">
    <div>
        <h1>{{ $product->title }}</h1>
        <p class="admin-page-head__sub">Fiche produit — fournisseur {{ $product->user?->name ?? '—' }}</p>
    </div>
    <div class="admin-page-head__actions">
        <a href="{{ route('admin.products.index') }}" class="admin-btn admin-btn--ghost">
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
        <dt>Fournisseur</dt>
        <dd>{{ $product->user?->name ?? '—' }} <span style="color:var(--text-3)">— {{ $product->user?->email }}</span></dd>
        <dt>Catégorie</dt>
        <dd>{{ $product->category?->name ?? '—' }}</dd>
        <dt>Description</dt>
        <dd>{{ $product->description ?: '—' }}</dd>
        <dt>Prix</dt>
        <dd><strong>{{ number_format($product->price_amount, 0, ',', ' ') }} FCFA</strong></dd>
        <dt>Stock</dt>
        <dd>{{ $product->stock_units }}</dd>
        <dt>Vues</dt>
        <dd>{{ $product->views_count }}</dd>
        @if($product->image_path)
            <dt>Image</dt>
            <dd><code>{{ $product->image_path }}</code></dd>
        @endif
    </dl>
</div>

<div class="card" style="max-width:580px">
    <div class="admin-card-h">
        <h3 class="admin-card-title">Validation (publication)</h3>
    </div>
    <form method="post" action="{{ route('admin.products.update', $product) }}">
        @csrf
        @method('PUT')
        <x-admin.field
            name="status"
            type="select"
            label="Statut de publication"
            :value="$product->status"
            :options="['draft' => 'Brouillon', 'pending' => 'En attente', 'approved' => 'Approuvé (en ligne)', 'rejected' => 'Rejeté']"
            required
            maxWidth="100%"
        />
        <x-admin.field
            name="admin_notes"
            type="textarea"
            label="Notes internes"
            :value="$product->admin_notes"
            hint="Visible uniquement par les administrateurs"
            maxWidth="100%"
            rows="3"
        />
        <div class="admin-form-actions">
            <button type="submit" class="admin-btn admin-btn--orange">
                <svg width="16" height="16" aria-hidden="true"><use href="#admin-ico-check" xlink:href="#admin-ico-check"/></svg>
                Enregistrer
            </button>
            <a href="{{ route('admin.products.index') }}" class="admin-btn admin-btn--ghost">Annuler</a>
        </div>
    </form>
</div>
@endsection
