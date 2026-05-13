@php
    $u = $profileData['user'] ?? null;
@endphp

<p class="app-muted app-mb-0">
    <a href="{{ route('app.'.$profileSlug.'.profile') }}" class="app-text-link">← Retour au profil</a>
</p>

@if (empty($u))
    <div class="app-card app-mt app-muted">Aucune donnée de profil.</div>
@else
    <div class="app-card app-mt">
        <h2 class="app-section-title">Localisation</h2>
        <p class="app-muted app-mb-md">Adresse affichée sur votre fiche publique lorsque la plateforme l’utilise.</p>
        @if ($errors->has('general'))
            <div class="app-alert app-alert--error app-mb-md" role="alert">{{ $errors->first('general') }}</div>
        @endif
        <form method="post" action="{{ route('app.'.$profileSlug.'.profile.update') }}" class="app-profile-form">
            @csrf
            <input type="hidden" name="redirect_to" value="location">
            <div class="app-form-grid-profile">
                <div class="app-field app-field--full">
                    <label for="loc-address">Adresse complète</label>
                    <textarea name="company_address" id="loc-address" rows="3" maxlength="2000">{{ old('company_address', $u['company_address'] ?? '') }}</textarea>
                    @error('company_address')
                        <div class="app-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="app-field">
                    <label for="loc-ville">Ville</label>
                    <input type="text" name="ville" id="loc-ville" value="{{ old('ville', $u['ville'] ?? '') }}" maxlength="255" autocomplete="address-level2">
                    @error('ville')
                        <div class="app-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="app-field">
                    <label for="loc-commune">Commune</label>
                    <input type="text" name="commune" id="loc-commune" value="{{ old('commune', $u['commune'] ?? '') }}" maxlength="255">
                    @error('commune')
                        <div class="app-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="app-field">
                    <label for="loc-pays">Pays</label>
                    <input type="text" name="pays" id="loc-pays" value="{{ old('pays', $u['pays'] ?? '') }}" maxlength="255" autocomplete="country-name">
                    @error('pays')
                        <div class="app-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <button type="submit" class="app-btn app-profile-submit">Enregistrer la localisation</button>
        </form>
    </div>
@endif
