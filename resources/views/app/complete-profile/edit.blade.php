@extends('app.layouts.shell')

@section('content')
    @php
        $completeHeading = match ($user->profile_type) {
            \App\Models\User::PROFILE_PARTICULIER => 'Particulier',
            \App\Models\User::PROFILE_ARTISAN => 'Artisan',
            \App\Models\User::PROFILE_ENTREPRENEUR_BATIMENT => 'Entrepreneur du bâtiment',
            \App\Models\User::PROFILE_ENTREPRISE_FOURNISSEUR => 'Entreprise fournisseur',
            default => 'Profil',
        };
    @endphp

    @if ($errors->any())
        <div class="app-alert app-alert--error app-mb-md" role="alert">
            @foreach ($errors->all() as $err)
                <div>{{ $err }}</div>
            @endforeach
        </div>
    @endif

    <div class="app-complete-profile-inner">
        <div class="app-card app-card--flush app-mb-md">
            <p class="app-complete-kicker">{{ $completeHeading }}</p>
            <p class="app-muted app-mb-0">Champs requis selon votre type de compte.</p>
        </div>

        <form method="post" action="{{ route('app.complete-profile.store') }}" enctype="multipart/form-data" class="app-complete-form">
            @csrf

            @switch($user->profile_type)
                @case(\App\Models\User::PROFILE_PARTICULIER)
                    @include('app.complete-profile.forms.particulier')
                    @break
                @case(\App\Models\User::PROFILE_ARTISAN)
                    @include('app.complete-profile.forms.artisan')
                    @break
                @case(\App\Models\User::PROFILE_ENTREPRENEUR_BATIMENT)
                    @include('app.complete-profile.forms.batiment')
                    @break
                @case(\App\Models\User::PROFILE_ENTREPRISE_FOURNISSEUR)
                    @include('app.complete-profile.forms.fournisseur')
                    @break
                @default
                    <p class="app-muted">Type de profil inconnu. Contactez le support.</p>
            @endswitch

            <div class="app-complete-actions">
                <button type="submit" class="app-btn app-btn--inline">Valider mon profil</button>
                <a href="{{ route('app.home') }}" class="app-text-link app-complete-skip">Plus tard</a>
            </div>
        </form>
    </div>
@endsection
