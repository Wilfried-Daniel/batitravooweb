@php
    use App\Models\User;
    use Illuminate\Support\Str;

    $u = $profileData['user'] ?? null;
    $typeLabels = [
        User::PROFILE_PARTICULIER => 'Particulier',
        User::PROFILE_ARTISAN => 'Artisan',
        User::PROFILE_ENTREPRENEUR_BATIMENT => 'Entrepreneur du bâtiment',
        User::PROFILE_ENTREPRISE_FOURNISSEUR => 'Entreprise fournisseur',
    ];
    $validationLabels = [
        User::VALIDATION_PENDING => 'En attente de validation',
        User::VALIDATION_APPROVED => 'Validé',
        User::VALIDATION_REJECTED => 'Refusé',
        User::VALIDATION_CHANGES_REQUESTED => 'Modifications demandées',
    ];
    $availabilityLabels = [
        'immediate' => 'Disponible immédiatement',
        'appointment' => 'Sur rendez-vous',
        'unavailable' => 'Indisponible',
    ];
    $formatDate = function ($iso) {
        if ($iso === null || $iso === '') {
            return '—';
        }
        try {
            return \Carbon\Carbon::parse($iso)->locale('fr')->translatedFormat('d MMM Y');
        } catch (\Throwable) {
            return (string) $iso;
        }
    };
@endphp

@if (empty($u))
    <div class="app-card app-mt app-muted">Aucune donnée de profil.</div>
@else
    @if ($errors->has('general'))
        <div class="app-alert app-alert--error app-mt" role="alert">{{ $errors->first('general') }}</div>
    @endif

    @php
        $initials = Str::of($u['name'] ?? '?')
            ->trim()
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn ($s) => Str::upper(Str::substr($s, 0, 1)))
            ->implode('') ?: '?';
    @endphp

    <div class="app-profile-hero app-card app-mt">
        <div class="app-profile-hero__visual">
            @if (! empty($u['avatar_url']))
                <img src="{{ $u['avatar_url'] }}" alt="" class="app-profile-avatar" width="112" height="112">
            @else
                <span class="app-profile-avatar app-profile-avatar--placeholder" aria-hidden="true">{{ $initials }}</span>
            @endif
            <form action="{{ route('app.'.$profileSlug.'.profile.avatar') }}" method="post" enctype="multipart/form-data" class="app-profile-avatar-form">
                @csrf
                <label class="app-profile-avatar-upload">
                    <span class="app-btn app-btn--sm app-btn--ghost">Changer la photo</span>
                    <input type="file" name="avatar" accept="image/*" class="app-profile-avatar-input">
                </label>
                @error('avatar')
                    <span class="app-error">{{ $message }}</span>
                @enderror
            </form>
        </div>
        <div class="app-profile-hero__meta">
            @if (in_array($profileSlug, ['batiment', 'fournisseur'], true) && ! empty($u['company_name']))
                <h2 class="app-profile-name">{{ $u['company_name'] }}</h2>
                @if (($u['name'] ?? '') !== '' && ($u['name'] ?? null) !== ($u['company_name'] ?? null))
                    <p class="app-muted app-profile-tagline">{{ $u['name'] }}</p>
                @endif
            @else
                <h2 class="app-profile-name">{{ $u['name'] ?? '—' }}</h2>
            @endif
            <p class="app-muted app-profile-email">{{ $u['email'] ?? '' }}</p>
            <div class="app-profile-badges">
                <span class="app-chip">{{ $typeLabels[$u['profile_type'] ?? ''] ?? ($u['profile_type'] ?? '—') }}</span>
                @php $vs = $u['profile_validation_status'] ?? User::VALIDATION_APPROVED; @endphp
                <span class="app-chip app-chip--validation app-chip--validation-{{ $vs }}">{{ $validationLabels[$vs] ?? $vs }}</span>
            </div>
        </div>
    </div>

    <div class="app-card app-mt">
        @include('app.partials.profile-form-heading', ['profileKey' => $profileSlug])
        <p class="app-muted app-mb-md">Champs selon votre type de compte.</p>

        <form method="post" action="{{ route('app.'.$profileSlug.'.profile.update') }}" class="app-profile-form">
            @csrf
            <div class="app-form-grid-profile">
                @if (in_array($profileSlug, ['particulier', 'artisan'], true))
                    <div class="app-field">
                        <label for="profil-name">Nom complet</label>
                        <input type="text" name="name" id="profil-name" value="{{ old('name', $u['name'] ?? '') }}" maxlength="255" autocomplete="name">
                        @error('name')
                            <div class="app-error">{{ $message }}</div>
                        @enderror
                    </div>
                @elseif (in_array($profileSlug, ['batiment', 'fournisseur'], true))
                    <div class="app-field app-field--full">
                        <label for="profil-company">Raison sociale</label>
                        <input type="text" name="company_name" id="profil-company" value="{{ old('company_name', $u['company_name'] ?? '') }}" maxlength="255" autocomplete="organization">
                        @error('company_name')
                            <div class="app-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="app-field">
                        <label for="profil-name">Nom d’affichage sur la plateforme</label>
                        <input type="text" name="name" id="profil-name" value="{{ old('name', $u['name'] ?? '') }}" maxlength="255" autocomplete="name">
                        <span class="app-field-hint">Souvent identique à la raison sociale.</span>
                        @error('name')
                            <div class="app-error">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                <div class="app-field">
                    <label for="profil-email">E-mail de connexion</label>
                    <input type="email" id="profil-email" value="{{ $u['email'] ?? '' }}" disabled class="app-input-disabled">
                    <span class="app-field-hint">Non modifiable ici.</span>
                </div>
                <div class="app-field">
                    <label for="profil-phone">Téléphone</label>
                    <input type="text" name="phone" id="profil-phone" value="{{ old('phone', $u['phone'] ?? '') }}" maxlength="32" autocomplete="tel">
                    @error('phone')
                        <div class="app-error">{{ $message }}</div>
                    @enderror
                </div>
                <div id="profil-localisation" class="app-contents" aria-label="Localisation">
                    <div class="app-field app-field--full">
                        <label for="profil-address">Adresse complète</label>
                        <textarea name="company_address" id="profil-address" rows="2" maxlength="2000">{{ old('company_address', $u['company_address'] ?? '') }}</textarea>
                        @error('company_address')
                            <div class="app-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="app-field">
                        <label for="profil-ville">Ville</label>
                        <input type="text" name="ville" id="profil-ville" value="{{ old('ville', $u['ville'] ?? '') }}" maxlength="255" autocomplete="address-level2">
                        @error('ville')
                            <div class="app-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="app-field">
                        <label for="profil-commune">Commune</label>
                        <input type="text" name="commune" id="profil-commune" value="{{ old('commune', $u['commune'] ?? '') }}" maxlength="255">
                        @error('commune')
                            <div class="app-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="app-field">
                        <label for="profil-pays">Pays</label>
                        <input type="text" name="pays" id="profil-pays" value="{{ old('pays', $u['pays'] ?? '') }}" maxlength="255" autocomplete="country-name">
                        @error('pays')
                            <div class="app-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                @if (in_array($profileSlug, ['particulier', 'artisan'], true))
                    <div class="app-field app-field--full">
                        <label for="profil-bio">Présentation</label>
                        <textarea name="bio" id="profil-bio" rows="4" maxlength="5000">{{ old('bio', $u['bio'] ?? '') }}</textarea>
                        @error('bio')
                            <div class="app-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="app-field">
                        <label for="profil-contact-email">E-mail de contact</label>
                        <input type="email" name="contact_email" id="profil-contact-email" value="{{ old('contact_email', $u['contact_email'] ?? '') }}" maxlength="255" autocomplete="email">
                        @error('contact_email')
                            <div class="app-error">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                @if ($profileSlug === 'artisan')
                    <div class="app-field app-field--full">
                        <label for="profil-availability">Disponibilité</label>
                        <select name="artisan_availability" id="profil-availability">
                            <option value="">— Choisir —</option>
                            <option value="immediate" @selected(old('artisan_availability', $u['artisan_availability'] ?? '') === 'immediate')>{{ $availabilityLabels['immediate'] }}</option>
                            <option value="appointment" @selected(old('artisan_availability', $u['artisan_availability'] ?? '') === 'appointment')>{{ $availabilityLabels['appointment'] }}</option>
                            <option value="unavailable" @selected(old('artisan_availability', $u['artisan_availability'] ?? '') === 'unavailable')>{{ $availabilityLabels['unavailable'] }}</option>
                        </select>
                        @error('artisan_availability')
                            <div class="app-error">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                @if (in_array($profileSlug, ['batiment', 'fournisseur'], true))
                    <div class="app-field app-field--full">
                        <label for="profil-company-desc">@if ($profileSlug === 'fournisseur') Description de l’entreprise @else Description de l’activité @endif</label>
                        <textarea name="company_description" id="profil-company-desc" rows="4" maxlength="5000">{{ old('company_description', $u['company_description'] ?? '') }}</textarea>
                        @error('company_description')
                            <div class="app-error">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                @if ($profileSlug === 'batiment')
                    <div class="app-field">
                        <label for="profil-years">Années d’expérience</label>
                        <input type="text" name="years_experience" id="profil-years" value="{{ old('years_experience', $u['years_experience'] ?? '') }}" maxlength="64">
                        @error('years_experience')
                            <div class="app-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="app-field">
                        <label for="profil-activity">Type d’activité</label>
                        <input type="text" name="activity_type" id="profil-activity" value="{{ old('activity_type', $u['activity_type'] ?? '') }}" maxlength="128">
                        @error('activity_type')
                            <div class="app-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="app-field">
                        <label for="profil-size">Taille de l’entreprise</label>
                        <input type="text" name="company_size" id="profil-size" value="{{ old('company_size', $u['company_size'] ?? '') }}" maxlength="128">
                        @error('company_size')
                            <div class="app-error">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                @if (in_array($profileSlug, ['batiment', 'fournisseur'], true))
                    <div class="app-field">
                        <label for="profil-contact-email-ent">E-mail de contact</label>
                        <input type="email" name="contact_email" id="profil-contact-email-ent" value="{{ old('contact_email', $u['contact_email'] ?? '') }}" maxlength="255" autocomplete="email">
                        @error('contact_email')
                            <div class="app-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="app-field">
                        <label for="profil-manager">Nom du responsable</label>
                        <input type="text" name="manager_name" id="profil-manager" value="{{ old('manager_name', $u['manager_name'] ?? '') }}" maxlength="255">
                        @error('manager_name')
                            <div class="app-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="app-field">
                        <label for="profil-manager-contact">Contact du responsable</label>
                        <input type="text" name="manager_contact" id="profil-manager-contact" value="{{ old('manager_contact', $u['manager_contact'] ?? '') }}" maxlength="255">
                        @error('manager_contact')
                            <div class="app-error">{{ $message }}</div>
                        @enderror
                    </div>
                @endif
            </div>
            <button type="submit" class="app-btn app-profile-submit">Enregistrer les modifications</button>
        </form>
    </div>

    <div class="app-card app-mt">
        <h2 class="app-section-title">Sécurité</h2>
        <p class="app-muted app-mb-md">Le changement de mot de passe se fait sur une page dédiée (comme sur l’application mobile).</p>
        <a href="{{ route('app.'.$profileSlug.'.profile.password.page') }}" class="app-btn app-btn--secondary app-btn--inline">Modifier le mot de passe</a>
    </div>

    @if (! empty($u['profile_validation_note']) || ! empty($u['profile_completed_at']) || ! empty($u['profile_validated_at']))
        <div class="app-card app-mt">
            <h2 class="app-section-title">Validation du profil</h2>
            @if (! empty($u['profile_validation_note']))
                <div class="app-alert app-alert--warn app-mb-md" role="status">
                    <strong>Message équipe :</strong> {{ $u['profile_validation_note'] }}
                </div>
            @endif
            <dl class="app-profile-dl">
                <div class="app-profile-dl__row">
                    <dt>Profil complété le</dt>
                    <dd>{{ $formatDate($u['profile_completed_at'] ?? null) }}</dd>
                </div>
                @if (! empty($u['profile_validated_at']))
                    <div class="app-profile-dl__row">
                        <dt>Dernière validation</dt>
                        <dd>{{ $formatDate($u['profile_validated_at']) }}</dd>
                    </div>
                @endif
            </dl>
        </div>
    @endif

    <div class="app-profile-actions app-mt">
        @if (auth()->user()?->profile_completed_at === null)
            <a href="{{ route('app.complete-profile') }}" class="app-btn app-btn--ghost">Compléter mon profil (pièces, bio…)</a>
        @else
            <p class="app-muted app-mb-0">Pièces justificatives : dépôt depuis le mobile ou lors de la validation du compte.</p>
        @endif
    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('.app-profile-avatar-input').forEach(function (input) {
                input.addEventListener('change', function () {
                    if (input.files && input.files.length) {
                        input.closest('form').submit();
                    }
                });
            });
        </script>
    @endpush
@endif
