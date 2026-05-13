@extends('app.layouts.shell')

@section('content')
    @php
        /** @var array<string, mixed> $marketplaceItem */
        $item = $marketplaceItem;
        $kind = $marketplaceDetailKind;
        $ownerId = isset($item['owner']['id']) ? (int) $item['owner']['id'] : (isset($item['user_id']) ? (int) $item['user_id'] : null);
        $me = auth()->id();
        $canContact = $ownerId !== null && $ownerId !== (int) $me;
        $backQs = request()->query();
        $backUrl = route('app.'.$profileSlug.'.marketplace');
        if ($backQs !== []) {
            $backUrl .= '?'.http_build_query($backQs);
        }
    @endphp

    <div class="mp-detail-back">
        <a href="{{ $backUrl }}" class="app-text-link">← Retour aux annonces</a>
    </div>

    <article class="app-card mp-detail">
        @if ($kind === 'product')
            @php
                $img = $item['image_url'] ?? null;
                $title = $item['title'] ?? '—';
            @endphp
            <div class="mp-detail__media">
                @if (! empty($img))
                    <img src="{{ $img }}" alt="" class="mp-detail__img">
                @else
                    <div class="mp-card__placeholder mp-card__placeholder--product mp-detail__placeholder">
                        <span>{{ mb_strtoupper(mb_substr($title, 0, 1)) }}</span>
                    </div>
                @endif
                @if (! empty($item['price_display_fr']))
                    <span class="mp-detail__price">{{ $item['price_display_fr'] }}</span>
                @endif
            </div>
            <div class="mp-detail__body">
                @if (! empty($item['category']['name']))
                    <p class="mp-card__category">{{ $item['category']['name'] }}</p>
                @endif
                <h2 class="mp-detail__title">{{ $title }}</h2>
                @if (! empty($item['description']))
                    <div class="mp-detail__desc app-readable">{!! nl2br(e($item['description'])) !!}</div>
                @endif
                @if (isset($item['stock_units']))
                    <p class="app-muted app-mt-sm">Stock indicatif : {{ (int) $item['stock_units'] }} unité(s)</p>
                @endif
                @if (! empty($item['views_count']))
                    <p class="app-muted app-mt-sm">{{ (int) $item['views_count'] }} vues</p>
                @endif
                @php
                    $productId = (int) ($item['id'] ?? 0);
                    $stockUnits = (int) ($item['stock_units'] ?? 0);
                    $canCart = in_array($profileSlug, ['particulier', 'batiment', 'fournisseur'], true)
                        && $ownerId
                        && (int) $me !== (int) $ownerId
                        && $productId > 0
                        && $stockUnits > 0;
                @endphp
                @if ($canCart)
                    <div class="app-mt-md app-cart-add">
                        <form method="post" action="{{ route('app.'.$profileSlug.'.cart.add') }}" class="app-cart-add__form">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $productId }}">
                            <label for="mp-add-qty" class="app-muted app-text-sm">Quantité</label>
                            <input type="number" name="qty" id="mp-add-qty" class="app-input" value="1" min="1" max="{{ $stockUnits }}" required>
                            <button type="submit" class="app-btn app-btn--sm">Ajouter au panier</button>
                        </form>
                        <p class="app-muted app-text-sm app-mb-0 app-mt-sm">
                            <a href="{{ route('app.'.$profileSlug.'.cart') }}" class="app-text-link">Voir le panier</a>
                        </p>
                    </div>
                @endif
                @if (in_array($profileSlug, ['particulier', 'batiment', 'fournisseur'], true) && $ownerId)
                    <p class="app-mt-sm" style="margin-bottom:0;">
                        <a href="{{ route('app.'.$profileSlug.'.devis.create', ['owner_user_id' => $ownerId]) }}" class="app-btn app-btn--secondary app-btn--sm">Demander un devis</a>
                    </p>
                @endif
            </div>
        @elseif ($kind === 'service')
            @php
                $img = $item['image_url'] ?? null;
                $title = $item['title'] ?? '—';
                $pricing = $item['pricing'] ?? [];
                $priceLine = $pricing['detail_fr'] ?? $pricing['title_fr'] ?? ($item['price_fixed_label'] ?? '');
            @endphp
            <div class="mp-detail__media">
                @if (! empty($img))
                    <img src="{{ $img }}" alt="" class="mp-detail__img">
                @else
                    <div class="mp-card__placeholder mp-card__placeholder--service mp-detail__placeholder">
                        <span>{{ mb_strtoupper(mb_substr($title, 0, 1)) }}</span>
                    </div>
                @endif
            </div>
            <div class="mp-detail__body">
                @if (! empty($item['category']['name']))
                    <p class="mp-card__category">{{ $item['category']['name'] }}</p>
                @endif
                <h2 class="mp-detail__title">{{ $title }}</h2>
                @if (! empty($item['location']))
                    <p class="mp-card__location mp-detail__location">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 1118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        {{ $item['location'] }}
                    </p>
                @endif
                @if ($priceLine !== '')
                    <p class="mp-detail__pricing">{{ $priceLine }}</p>
                @endif
                @if (! empty($item['description']))
                    <div class="mp-detail__desc app-readable">{!! nl2br(e($item['description'])) !!}</div>
                @endif
                @if (! empty($item['rating']) && (float) $item['rating'] > 0)
                    <p class="app-muted">Note : {{ number_format((float) $item['rating'], 1, ',', ' ') }} / 5 @if (! empty($item['review_count'])) ({{ (int) $item['review_count'] }} avis) @endif</p>
                @endif
                @if (in_array($profileSlug, ['particulier', 'batiment', 'fournisseur'], true) && $ownerId)
                    <p class="app-mt-sm" style="margin-bottom:0;">
                        <a href="{{ route('app.'.$profileSlug.'.devis.create', ['owner_user_id' => $ownerId]) }}" class="app-btn app-btn--secondary app-btn--sm">Demander un devis</a>
                    </p>
                @endif
            </div>
        @else
            @php
                $img = $item['image_url'] ?? null;
                $title = $item['title'] ?? '—';
            @endphp
            <div class="mp-detail__media">
                @if (! empty($img))
                    <img src="{{ $img }}" alt="" class="mp-detail__img">
                @else
                    <div class="mp-card__placeholder mp-card__placeholder--besoin mp-detail__placeholder">
                        <span>{{ mb_strtoupper(mb_substr($title, 0, 1)) }}</span>
                    </div>
                @endif
                @if (! empty($item['budget']))
                    <span class="mp-detail__price mp-detail__price--budget">{{ $item['budget'] }}</span>
                @endif
            </div>
            <div class="mp-detail__body">
                <h2 class="mp-detail__title">{{ $title }}</h2>
                @if (! empty($item['place']))
                    <p class="mp-card__location mp-detail__location">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 1118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        {{ $item['place'] }}
                    </p>
                @endif
                @if (! empty($item['start_label']) || ! empty($item['duration']) || ! empty($item['short_date']))
                    <p class="app-muted">
                        @foreach (array_filter([$item['start_label'] ?? null, $item['duration'] ?? null, $item['short_date'] ?? null]) as $bit)
                            <span class="mp-detail__meta-bit">{{ $bit }}</span>
                        @endforeach
                    </p>
                @endif
                @if (! empty($item['description']))
                    <div class="mp-detail__desc app-readable">{!! nl2br(e($item['description'])) !!}</div>
                @endif
                @if (isset($item['candidature_count']))
                    <p class="app-muted app-mt-sm">{{ (int) $item['candidature_count'] }} réponse(s) à ce besoin</p>
                @endif
                @if (! empty($besoinAlreadyApplied ?? false))
                    <p class="app-muted app-mt-sm app-mb-0">Vous avez déjà répondu à ce besoin.</p>
                @elseif (($item['status'] ?? '') !== 'open' && ! empty($item['status']))
                    <p class="app-muted app-mt-sm app-mb-0">Ce besoin n’accepte plus de nouvelles candidatures (statut : {{ $item['status'] }}).</p>
                @endif
            </div>
        @endif

        @if (! empty($item['owner']))
            <div class="mp-detail__owner">
                <h3 class="mp-detail__owner-title">Annonceur</h3>
                <p class="mp-detail__owner-name">{{ $item['owner']['company_name'] ?? $item['owner']['name'] ?? '—' }}</p>
                @if (! empty($item['owner']['company_address']))
                    <p class="app-muted app-readable">{{ $item['owner']['company_address'] }}</p>
                @endif
            </div>
        @endif
    </article>

    @if ($kind === 'besoin' && ! empty($besoinShowApplicantForms ?? false))
        @php
            $bid = (int) ($item['id'] ?? 0);
            $ownerName = $item['owner']['name'] ?? '';
            $defaultDisplay = old('display_name', auth()->user()->name ?? '');
        @endphp
        @if (! ($besoinIsArtisanApplicant ?? false))
            <div class="app-card app-mt">
                <h3 class="app-section-title">Répondre au besoin</h3>
                <p class="app-muted app-mb-md app-text-sm">Envoyez une candidature avec un court message. Une seule réponse par besoin.</p>
                @error('besoin_apply')
                    <div class="app-alert app-alert--error app-mb-md" role="alert">{{ $message }}</div>
                @enderror
                <form method="post" action="{{ route('app.'.$profileSlug.'.marketplace.besoin.candidature', ['besoin' => $bid]) }}" class="app-form-stack">
                    @csrf
                    <input type="hidden" name="besoin_id" value="{{ $bid }}">
                    <div class="app-field">
                        <label for="cand-display">Nom affiché</label>
                        <input type="text" name="display_name" id="cand-display" value="{{ $defaultDisplay }}" maxlength="255" autocomplete="name">
                    </div>
                    <div class="app-field">
                        <label for="cand-prof">Métier / spécialité</label>
                        <input type="text" name="profession" id="cand-prof" value="{{ old('profession') }}" maxlength="255" placeholder="Ex. électricité, gros œuvre…">
                    </div>
                    <div class="app-field">
                        <label for="cand-msg">Message</label>
                        <textarea name="message" id="cand-msg" rows="5" maxlength="10000" placeholder="Présentez votre proposition…">{{ old('message') }}</textarea>
                    </div>
                    <button type="submit" class="app-btn app-btn--inline">Envoyer la candidature</button>
                </form>
            </div>
        @else
            <div class="app-card app-mt">
                <h3 class="app-section-title">Candidature courte</h3>
                <p class="app-muted app-mb-md app-text-sm">Message pour le porteur du besoin (sans montant détaillé).</p>
                @error('besoin_apply')
                    <div class="app-alert app-alert--error app-mb-md" role="alert">{{ $message }}</div>
                @enderror
                <form method="post" action="{{ route('app.'.$profileSlug.'.marketplace.besoin.candidature', ['besoin' => $bid]) }}" class="app-form-stack">
                    @csrf
                    <input type="hidden" name="besoin_id" value="{{ $bid }}">
                    <div class="app-field">
                        <label for="cand2-display">Nom affiché</label>
                        <input type="text" name="display_name" id="cand2-display" value="{{ $defaultDisplay }}" maxlength="255">
                    </div>
                    <div class="app-field">
                        <label for="cand2-prof">Métier</label>
                        <input type="text" name="profession" id="cand2-prof" value="{{ old('profession') }}" maxlength="255">
                    </div>
                    <div class="app-field">
                        <label for="cand2-msg">Message</label>
                        <textarea name="message" id="cand2-msg" rows="4" maxlength="10000">{{ old('message') }}</textarea>
                    </div>
                    <button type="submit" class="app-btn app-btn--secondary app-btn--inline">Postuler</button>
                </form>
            </div>
            <div class="app-card app-mt">
                <h3 class="app-section-title">Proposition de devis chiffré</h3>
                <p class="app-muted app-mb-md app-text-sm">Crée un devis côté client et une entrée sur ce besoin. À utiliser si vous préférez un montant (une seule réponse au total).</p>
                @error('besoin_devis')
                    <div class="app-alert app-alert--error app-mb-md" role="alert">{{ $message }}</div>
                @enderror
                <form method="post" action="{{ route('app.'.$profileSlug.'.marketplace.besoin.devis', ['besoin' => $bid]) }}" class="app-form-stack">
                    @csrf
                    <div class="app-field">
                        <label for="adv-title">Titre du devis</label>
                        <input type="text" name="title" id="adv-title" required maxlength="255" value="{{ old('title', 'Proposition — '.($item['title'] ?? '')) }}">
                    </div>
                    <div class="app-field">
                        <label for="adv-client">Client (nom affiché)</label>
                        <input type="text" name="client_name" id="adv-client" required maxlength="255" value="{{ old('client_name', $ownerName) }}">
                    </div>
                    <div class="app-field">
                        <label for="adv-place">Lieu / chantier</label>
                        <input type="text" name="place" id="adv-place" maxlength="255" value="{{ old('place', $item['place'] ?? '') }}">
                    </div>
                    <div class="app-field">
                        <label for="adv-contact">Contact</label>
                        <input type="text" name="contact" id="adv-contact" maxlength="255" value="{{ old('contact', auth()->user()->phone ?? '') }}" placeholder="Téléphone ou email">
                    </div>
                    <div class="app-field">
                        <label for="adv-amt">Montant total (FCFA)</label>
                        <input type="number" name="amount_fcfa" id="adv-amt" min="0" step="1" value="{{ old('amount_fcfa') }}" placeholder="Optionnel — laissez vide pour un devis sans montant">
                    </div>
                    <div class="app-field">
                        <label for="adv-lbl">Libellé de la ligne (si montant)</label>
                        <input type="text" name="line_label" id="adv-lbl" maxlength="255" value="{{ old('line_label', 'Prestation') }}">
                    </div>
                    <div class="app-field">
                        <label for="adv-notes">Notes</label>
                        <textarea name="notes" id="adv-notes" rows="4" maxlength="10000">{{ old('notes') }}</textarea>
                    </div>
                    <button type="submit" class="app-btn app-btn--inline">Envoyer le devis</button>
                </form>
            </div>
        @endif
    @endif

    <div class="mp-detail-actions">
        @if ($canContact)
            <a href="{{ route('app.'.$profileSlug.'.messages', ['peer_id' => $ownerId]) }}" class="app-btn">Envoyer un message</a>
        @else
            <p class="app-muted app-mb-0">C’est votre publication ou contact non disponible.</p>
        @endif
        <a href="{{ route('app.'.$profileSlug.'.devis') }}" class="app-btn app-btn--secondary">Voir mes devis</a>
    </div>
@endsection
