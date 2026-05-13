@extends('admin.layout', ['title' => 'Devis #'.$devis->id])

@php
    $sLabels = [
        'non_traite' => 'Non traité',
        'en_cours' => 'En cours',
        'envoye' => 'Envoyé',
        'valide' => 'Validé',
        'rejete' => 'Rejeté',
    ];
    $statusBadge = match ($devis->status) {
        'valide' => ['ok', 'Validé'],
        'rejete' => ['no', 'Rejeté'],
        'envoye' => ['ok', 'Envoyé'],
        'en_cours', 'non_traite' => ['pending', $sLabels[$devis->status] ?? $devis->status],
        default => ['mute', $devis->status],
    };
@endphp

@section('content')

<div class="admin-page-head">
    <div>
        <h1>Devis #{{ $devis->id }} — {{ $devis->title }}</h1>
        <p class="admin-page-head__sub">Réf {{ $devis->order_reference ?? '—' }} · Client {{ $devis->client_name }}</p>
    </div>
    <div class="admin-page-head__actions">
        <a href="{{ route('admin.devis.index') }}" class="admin-btn admin-btn--ghost">
            <svg width="16" height="16" aria-hidden="true"><use href="#admin-ico-arrow-left" xlink:href="#admin-ico-arrow-left"/></svg>
            Retour
        </a>
    </div>
</div>

<div class="card">
    <div class="admin-card-h">
        <h3 class="admin-card-title">Détails du devis</h3>
        <span class="badge b-{{ $statusBadge[0] }}">{{ $statusBadge[1] }}</span>
    </div>
    <dl class="admin-detail">
        <dt>Entrepreneur</dt>
        <dd>{{ $devis->user?->name }} <span style="color:var(--text-3)">— {{ $devis->user?->email }}</span></dd>
        <dt>Titre</dt>
        <dd>{{ $devis->title }}</dd>
        <dt>Client</dt>
        <dd>{{ $devis->client_name }}</dd>
        <dt>Référence</dt>
        <dd>{{ $devis->order_reference ?? '—' }}</dd>
        <dt>Lieu</dt>
        <dd>{{ $devis->place ?? '—' }}</dd>
        <dt>Contact</dt>
        <dd>{{ $devis->contact ?? '—' }}</dd>
        <dt>Date traitement</dt>
        <dd>{{ $devis->processed_at?->format('d/m/Y') ?? '—' }}</dd>
    </dl>
    @if($devis->line_items)
        <div style="margin-top:18px">
            <p style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--text-3); margin:0 0 8px">Lignes du devis</p>
            <div class="table-wrap" style="border-radius:10px;border:1px solid var(--border);">
                <table style="width:100%">
                    <thead>
                        <tr><th>Désignation</th><th style="width:90px">Qté</th><th style="width:120px">Unité</th><th style="width:140px;text-align:right">Total (FCFA)</th></tr>
                    </thead>
                    <tbody>
                        @foreach($devis->line_items as $line)
                            <tr>
                                <td><strong>{{ $line['name'] ?? '—' }}</strong></td>
                                <td>{{ $line['qty'] ?? '—' }}</td>
                                <td>{{ $line['unit'] ?? '—' }}</td>
                                <td style="text-align:right;font-variant-numeric:tabular-nums">{{ isset($line['total']) ? number_format((int) $line['total'], 0, ',', ' ') : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<div class="card" style="max-width:560px">
    <div class="admin-card-h">
        <h3 class="admin-card-title">Mise à jour du devis</h3>
    </div>
    <form method="post" action="{{ route('admin.devis.update', $devis) }}">
        @csrf
        @method('PUT')
        <x-admin.field
            name="status"
            type="select"
            label="Statut"
            :value="$devis->status"
            :options="$sLabels"
            required
            maxWidth="100%"
        />
        <x-admin.field
            name="processed_at"
            type="date"
            label="Date de traitement"
            :value="$devis->processed_at?->format('Y-m-d')"
            maxWidth="100%"
        />
        <x-admin.field
            name="notes"
            type="textarea"
            label="Notes"
            :value="$devis->notes"
            maxWidth="100%"
            rows="3"
        />
        <div class="admin-form-actions">
            <button type="submit" class="admin-btn admin-btn--orange">
                <svg width="16" height="16" aria-hidden="true"><use href="#admin-ico-check" xlink:href="#admin-ico-check"/></svg>
                Enregistrer
            </button>
            <a href="{{ route('admin.devis.index') }}" class="admin-btn admin-btn--ghost">Annuler</a>
        </div>
    </form>
</div>
@endsection
