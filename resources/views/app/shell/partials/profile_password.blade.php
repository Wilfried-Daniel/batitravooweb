<p class="app-muted app-mb-0">
    <a href="{{ route('app.'.$profileSlug.'.profile') }}" class="app-text-link">← Retour au profil</a>
</p>

<div class="app-card app-mt">
    <h2 class="app-section-title">Sécurité</h2>
    <p class="app-muted app-mb-md">Choisissez un mot de passe fort (minimum 8 caractères).</p>
    @if ($errors->has('general'))
        <div class="app-alert app-alert--error app-mb-md" role="alert">{{ $errors->first('general') }}</div>
    @endif
    <form method="post" action="{{ route('app.'.$profileSlug.'.profile.password') }}" class="app-profile-form">
        @csrf
        <input type="hidden" name="redirect_to" value="password">
        <div class="app-form-grid-profile">
            <div class="app-field app-field--full">
                <label for="pwd-page-current">Mot de passe actuel</label>
                <input type="password" name="current_password" id="pwd-page-current" required autocomplete="current-password">
                @error('current_password')
                    <div class="app-error">{{ $message }}</div>
                @enderror
            </div>
            <div class="app-field">
                <label for="pwd-page-new">Nouveau mot de passe</label>
                <input type="password" name="password" id="pwd-page-new" required minlength="8" autocomplete="new-password">
                @error('password')
                    <div class="app-error">{{ $message }}</div>
                @enderror
            </div>
            <div class="app-field">
                <label for="pwd-page-confirm">Confirmer</label>
                <input type="password" name="password_confirmation" id="pwd-page-confirm" required minlength="8" autocomplete="new-password">
            </div>
        </div>
        <button type="submit" class="app-btn app-profile-submit">Mettre à jour le mot de passe</button>
    </form>
</div>
