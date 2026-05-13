@php
    /** @var \App\Models\ArtisanBusinessCard|null $businessCard */
    $card = $businessCard ?? null;
    $svcRaw = old('services', $card?->services ?? []);
    $svcList = is_array($svcRaw) ? array_values($svcRaw) : [];
    while (count($svcList) < 6) {
        $svcList[] = '';
    }
    $svcList = array_slice($svcList, 0, 6);
@endphp

@if ($card && $card->portfolio_path)
    @php $pfUrl = storage_public_url($card->portfolio_path); @endphp
    @if ($pfUrl)
        <div class="app-card app-mt">
            <p class="app-muted app-mb-sm">Portfolio actuel</p>
            <a href="{{ $pfUrl }}" target="_blank" rel="noopener" class="app-text-link">Télécharger / ouvrir le fichier</a>
        </div>
    @endif
@endif

<form method="post" action="{{ route('app.artisan.business_card.update') }}" enctype="multipart/form-data" class="app-card app-mt app-form-stack">
    @csrf

    <div class="app-field">
        <label for="bc-name">Nom affiché</label>
        <input type="text" name="display_name" id="bc-name" maxlength="255" value="{{ old('display_name', $card?->display_name) }}">
        @error('display_name')<span class="app-error">{{ $message }}</span>@enderror
    </div>

    <div class="app-field">
        <label for="bc-prof">Profession</label>
        <input type="text" name="profession" id="bc-prof" maxlength="255" value="{{ old('profession', $card?->profession) }}">
        @error('profession')<span class="app-error">{{ $message }}</span>@enderror
    </div>

    <div class="app-field">
        <label for="bc-exp">Expérience (court texte)</label>
        <input type="text" name="experience_text" id="bc-exp" maxlength="255" value="{{ old('experience_text', $card?->experience_text) }}">
        @error('experience_text')<span class="app-error">{{ $message }}</span>@enderror
    </div>

    <fieldset class="app-field">
        <legend class="app-muted app-text-sm app-mb-sm">Tarification</legend>
        <label class="app-checkbox-label app-mb-sm">
            <input type="checkbox" name="price_on_request" value="1" @checked(old('price_on_request', $card?->price_on_request))>
            Prix sur demande
        </label>
        <label class="app-checkbox-label app-mb-sm">
            <input type="checkbox" name="price_on_quote" value="1" @checked(old('price_on_quote', $card?->price_on_quote))>
            Sur devis
        </label>
        <label for="bc-price">Texte tarif</label>
        <input type="text" name="price_text" id="bc-price" maxlength="255" value="{{ old('price_text', $card?->price_text) }}">
        @error('price_text')<span class="app-error">{{ $message }}</span>@enderror
    </fieldset>

    <div class="app-field">
        <label class="app-muted app-text-sm">Prestations proposées (lignes)</label>
        @foreach ($svcList as $i => $line)
            <input type="text" name="services[]" class="app-mt-sm" style="display:block;width:100%;" maxlength="500" value="{{ $line }}" placeholder="Prestation {{ $i + 1 }}">
        @endforeach
        @error('services')<span class="app-error">{{ $message }}</span>@enderror
    </div>

    <fieldset class="app-field">
        <legend class="app-muted app-text-sm app-mb-sm">Disponibilité</legend>
        <label class="app-checkbox-label app-mb-sm">
            <input type="checkbox" name="avail_immediate" value="1" @checked(old('avail_immediate', $card?->avail_immediate))>
            Disponible immédiatement
        </label>
        <label class="app-checkbox-label app-mb-sm">
            <input type="checkbox" name="avail_appointment" value="1" @checked(old('avail_appointment', $card?->avail_appointment))>
            Sur rendez-vous
        </label>
        <label class="app-checkbox-label">
            <input type="checkbox" name="avail_unavailable" value="1" @checked(old('avail_unavailable', $card?->avail_unavailable))>
            Indisponible
        </label>
    </fieldset>

    <div class="app-field">
        <label for="bc-loc">Localisation / zone</label>
        <textarea name="location_text" id="bc-loc" rows="3" maxlength="500">{{ old('location_text', $card?->location_text) }}</textarea>
        @error('location_text')<span class="app-error">{{ $message }}</span>@enderror
    </div>

    <div class="app-field">
        <label for="bc-portfolio">Portfolio (image ou PDF, max 15 Mo)</label>
        <input type="file" name="portfolio" id="bc-portfolio" accept="image/jpeg,image/png,image/webp,application/pdf,.jpg,.jpeg,.png,.webp,.pdf">
        @error('portfolio')<span class="app-error">{{ $message }}</span>@enderror
    </div>

    <div class="app-form-actions">
        <button type="submit" class="app-btn app-btn--inline">Enregistrer</button>
    </div>
</form>

@if ($card)
    <div class="app-card app-mt">
        <form method="post" action="{{ route('app.artisan.business_card.destroy') }}" onsubmit="return confirm('Supprimer toute la carte de visite ?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="app-text-link app-text-link--danger">Supprimer la carte de visite</button>
        </form>
    </div>
@endif
