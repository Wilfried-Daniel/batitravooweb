@extends('admin.layout', ['title' => 'Utilisateur — '.$user->name])

@php
    $pLabels = [
        'entrepreneur_batiment' => 'Entrepreneur bâtiment',
        'entreprise_fournisseur' => 'Entreprise / fournisseur',
        'artisan' => 'Artisan',
        'particulier' => 'Particulier',
    ];
@endphp

@section('content')
<div class="admin-page-head">
    <div>
        <h1>{{ $user->name }}</h1>
        <p class="admin-page-head__sub">Modification du profil utilisateur — synchronisé avec l’app mobile.</p>
    </div>
    <div class="admin-page-head__actions">
        <a href="{{ route('admin.users.index') }}" class="admin-btn admin-btn--ghost">
            <svg width="16" height="16" aria-hidden="true"><use href="#admin-ico-arrow-left" xlink:href="#admin-ico-arrow-left"/></svg>
            Retour
        </a>
    </div>
</div>

<div class="card" style="max-width:760px">
    <form method="post" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PUT')

        <div class="admin-form-grid">
            <div class="admin-form-grid--full">
                <x-admin.field name="name" label="Nom complet" :value="$user->name" required placeholder="Prénom Nom" />
            </div>
            <x-admin.field name="email" type="email" label="Adresse e-mail" :value="$user->email" required autocomplete="email" placeholder="exemple@domaine.com" />
            <x-admin.field name="phone" label="Téléphone" :value="$user->phone" placeholder="+225 07 00 00 00" />

            <x-admin.field
                name="profile_type"
                type="select"
                label="Type de profil"
                :value="$user->profile_type"
                :options="$pLabels"
                required
            />

            <x-admin.field
                name="is_active"
                type="select"
                label="Compte actif"
                :value="(string) (int) $user->is_active"
                :options="['1' => 'Oui — actif', '0' => 'Non — désactivé']"
                required
            />

            <div class="admin-form-grid--full">
                <x-admin.field name="company_name" label="Raison sociale (optionnel)" :value="$user->company_name" placeholder="Nom de l’entreprise" />
            </div>

            <div class="admin-form-grid--full">
                <x-admin.field
                    name="company_address"
                    type="textarea"
                    label="Adresse (optionnel)"
                    :value="$user->company_address"
                    rows="3"
                    placeholder="Adresse complète"
                />
            </div>
        </div>

        <div class="admin-form-actions" style="border-top:1px solid var(--hairline); padding-top:18px; margin-top:8px">
            <button type="submit" class="admin-btn admin-btn--orange">
                <svg width="16" height="16" aria-hidden="true"><use href="#admin-ico-check" xlink:href="#admin-ico-check"/></svg>
                Enregistrer les modifications
            </button>
            <a href="{{ route('admin.users.index') }}" class="admin-btn admin-btn--ghost">Annuler</a>
        </div>
    </form>
</div>
@endsection
