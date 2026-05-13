@php
    $slug = $profileSlug ?? 'particulier';
    $defaultTab = $slug === 'artisan' ? 'besoins' : 'produits';
    $mpTab = request('tab', $defaultTab);
    if (! in_array($mpTab, ['produits', 'services', 'besoins'], true)) {
        $mpTab = $defaultTab;
    }
    if ($slug === 'particulier' && $mpTab === 'besoins') {
        $mpTab = 'produits';
    }
    if ($slug === 'artisan' && $mpTab === 'services') {
        $mpTab = 'besoins';
    }
    if (in_array($slug, ['batiment', 'fournisseur'], true) && $mpTab === 'besoins') {
        $mpTab = 'produits';
    }
    $showBesoinsTab = $slug === 'artisan';
    $showServicesTab = $slug !== 'artisan';
    $mpSk = (string) request('service_kind', '');
    // Libellés sections / vides (particulier_marketplace_screen, artisan_marketplace_screen, fournisseur/batiment marketplace Flutter)
    if ($slug === 'particulier') {
        $mpProdPill = 'Fournisseurs';
        $mpProdTitle = 'Matériaux et produits';
        $mpProdEmpty = 'Aucun produit pour le moment.';
        if ($mpSk === 'entrepreneur') {
            $mpSrvPill = 'Prestataires BTP';
            $mpSrvTitle = 'Prestations';
            $mpSrvEmpty = 'Aucune prestation BTP pour le moment.';
        } elseif ($mpSk === 'artisan') {
            $mpSrvPill = 'Artisans';
            $mpSrvTitle = 'Services artisans';
            $mpSrvEmpty = 'Aucun service artisan pour le moment.';
        } else {
            $mpSrvPill = '';
            $mpSrvTitle = 'Services';
            $mpSrvEmpty = 'Aucune prestation pour ces critères.';
        }
    } elseif ($slug === 'artisan') {
        $mpProdPill = '';
        $mpProdTitle = 'Entreprises disponibles';
        $mpProdEmpty = 'Aucune entreprise / produit ne correspond à votre recherche.';
        $mpSrvPill = '';
        $mpSrvTitle = '';
        $mpSrvEmpty = '';
    } else {
        $mpProdPill = 'Fournisseurs';
        $mpProdTitle = 'Produits';
        $mpProdEmpty = 'Aucun résultat.';
        if ($mpSk === 'entrepreneur') {
            $mpSrvPill = 'Entreprise bâtiment';
            $mpSrvTitle = 'Prestations entreprise BTP';
        } elseif ($mpSk === 'artisan') {
            $mpSrvPill = 'Artisans';
            $mpSrvTitle = 'Prestations artisans';
        } else {
            $mpSrvPill = '';
            $mpSrvTitle = 'Services';
        }
        $mpSrvEmpty = 'Aucun résultat.';
    }
    $mpBesPill = '';
    $mpBesTitle = 'Opportunités disponibles';
    $mpBesEmpty = 'Aucune opportunité ne correspond à votre recherche.';
    $baseMarketplaceUrl = route('app.'.$profileSlug.'.marketplace');
    $mkQuery = static function (array $merge = []) use ($baseMarketplaceUrl, $mpTab) {
        $params = array_merge(
            array_filter([
                'q' => request('q'),
                'category_id' => request('category_id'),
                'service_kind' => request('service_kind'),
                'owner' => request('owner'),
                'user_id' => request('user_id'),
                'cat_scope' => request('cat_scope'),
                'per_page' => request('per_page'),
            ], fn ($v) => $v !== null && $v !== ''),
            ['tab' => request('tab', $mpTab)],
            $merge
        );

        return $baseMarketplaceUrl.'?'.http_build_query(array_filter($params, fn ($v) => $v !== null && $v !== ''));
    };
@endphp

<form method="get" action="{{ $baseMarketplaceUrl }}" class="mp-toolbar app-card">
    <div class="mp-toolbar__search">
        <label class="mp-visually-hidden" for="mq">Rechercher une annonce</label>
        <span class="mp-toolbar__search-icon" aria-hidden="true">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
        </span>
        <input type="search" name="q" id="mq" value="{{ request('q') }}" placeholder="Recherche" class="mp-toolbar__input" autocomplete="off">
        <input type="hidden" name="tab" value="{{ $mpTab }}">
        <button type="submit" class="mp-toolbar__submit app-btn app-btn--inline">Rechercher</button>
    </div>
    <div class="mp-toolbar__filters">
        <div class="app-field app-field--inline mp-toolbar__field">
            <label for="mcat">Catégorie</label>
            <select name="category_id" id="mcat" class="mp-select">
                <option value="">Toutes</option>
                @if (! empty($marketplaceData['categories']))
                    @foreach ($marketplaceData['categories'] as $c)
                        <option value="{{ $c['id'] ?? '' }}" @selected((string) request('category_id') === (string) ($c['id'] ?? ''))>
                            {{ $c['name'] ?? '—' }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>
        @if ($mpTab === 'services')
            <div class="app-field app-field--inline mp-toolbar__field">
                <label for="msk">Type</label>
                <select name="service_kind" id="msk" class="mp-select">
                    <option value="">Tous</option>
                    <option value="artisan" @selected(request('service_kind') === 'artisan')>Artisans</option>
                    <option value="entrepreneur" @selected(request('service_kind') === 'entrepreneur')>Entreprise bâtiment</option>
                </select>
            </div>
        @endif
        @if ($mpTab === 'besoins')
            <div class="app-field app-field--inline mp-toolbar__field">
                <label for="mowner">{{ ($profileSlug ?? '') === 'artisan' ? 'Type de client' : 'Auteur' }}</label>
                <select name="owner" id="mowner" class="mp-select">
                    <option value="">Tous</option>
                    <option value="particulier" @selected(request('owner') === 'particulier')>Particuliers</option>
                    @if (($profileSlug ?? '') === 'artisan')
                        <option value="pro" @selected(in_array(request('owner'), ['pro', 'entrepreneur_batiment'], true))>Professionnels (BTP)</option>
                    @else
                        <option value="entrepreneur_batiment" @selected(request('owner') === 'entrepreneur_batiment')>Entreprise bâtiment</option>
                    @endif
                </select>
            </div>
        @endif
        @if ($mpTab === 'produits')
            <div class="app-field app-field--inline mp-toolbar__field">
                <label for="mcatscope">Rayon</label>
                <select name="cat_scope" id="mcatscope" class="mp-select">
                    <option value="">Tous</option>
                    <option value="product" @selected(request('cat_scope') === 'product')>Produits</option>
                    <option value="service" @selected(request('cat_scope') === 'service')>Services</option>
                    <option value="both" @selected(request('cat_scope') === 'both')>Mixtes</option>
                </select>
            </div>
            <div class="app-field app-field--inline mp-toolbar__field">
                <label for="muid">N° vendeur</label>
                <input type="number" name="user_id" id="muid" class="mp-select" style="min-width:7rem;" min="1" step="1" value="{{ old('user_id', request('user_id')) }}" placeholder="—">
            </div>
        @endif
        <div class="app-field app-field--inline mp-toolbar__field">
            <label for="mpp">Par page</label>
            <select name="per_page" id="mpp" class="mp-select">
                @foreach ([12, 24, 48] as $pp)
                    <option value="{{ $pp }}" @selected((int) request('per_page', 12) === $pp)>{{ $pp }}</option>
                @endforeach
            </select>
        </div>
        @php
            $hasMpFilters = filled(request('q'))
                || filled(request('category_id'))
                || ($mpTab === 'services' && filled(request('service_kind')))
                || ($mpTab === 'besoins' && filled(request('owner')))
                || ($mpTab === 'produits' && (filled(request('user_id')) || filled(request('cat_scope'))))
                || (int) request('per_page', 12) !== 12;
        @endphp
        @if ($hasMpFilters)
            <a href="{{ $baseMarketplaceUrl }}?{{ http_build_query(['tab' => $mpTab]) }}" class="mp-toolbar__reset app-text-link">Réinitialiser</a>
        @endif
    </div>
</form>

@if (! empty($marketplaceData))
    @php
        $categories = $marketplaceData['categories'] ?? [];
        $products = $marketplaceData['products']['data'] ?? [];
        $services = $marketplaceData['services']['data'] ?? [];
        $besoins = $marketplaceData['besoins']['data'] ?? [];
        $detailQueryBase = array_filter([
            'q' => request('q'),
            'category_id' => request('category_id'),
            'service_kind' => request('service_kind'),
            'owner' => request('owner'),
            'user_id' => request('user_id'),
            'cat_scope' => request('cat_scope'),
            'per_page' => request('per_page'),
        ], fn ($v) => $v !== null && $v !== '');
        $dqProducts = http_build_query(array_merge($detailQueryBase, ['tab' => 'produits']));
        $dqServices = http_build_query(array_merge($detailQueryBase, ['tab' => 'services']));
        $dqBesoins = http_build_query(array_merge($detailQueryBase, ['tab' => 'besoins']));
    @endphp

    <nav class="mp-tabs app-card app-mt" aria-label="Vue par type d’annonce">
        @if ($showBesoinsTab)
            <a href="{{ $mkQuery(['tab' => 'besoins']) }}" class="mp-tab {{ $mpTab === 'besoins' ? 'is-active' : '' }}" @if ($mpTab === 'besoins') aria-current="page" @endif>Opportunités</a>
        @endif
        @if ($showServicesTab)
            <a href="{{ $mkQuery(['tab' => 'services']) }}" class="mp-tab {{ $mpTab === 'services' ? 'is-active' : '' }}" @if ($mpTab === 'services') aria-current="page" @endif>Services</a>
        @endif
        <a href="{{ $mkQuery(['tab' => 'produits']) }}" class="mp-tab {{ $mpTab === 'produits' ? 'is-active' : '' }}" @if ($mpTab === 'produits') aria-current="page" @endif>{{ ($profileSlug ?? '') === 'artisan' ? 'Catalogue' : 'Produits' }}</a>
    </nav>

    @if ($mpTab === 'produits')
        <section class="mp-section app-mt" aria-labelledby="mp-products-title">
            <div class="mp-section__head">
                <h2 id="mp-products-title" class="mp-section-title">
                    @if (($mpProdPill ?? '') !== '')
                        <span class="mp-pill mp-pill--supply">{{ $mpProdPill }}</span>
                    @endif
                    {{ $mpProdTitle }}
                </h2>
            </div>
            @if (count($products))
                <div class="mp-grid">
                    @foreach ($products as $row)
                        @php
                            $title = $row['title'] ?? 'Sans titre';
                            $img = $row['image_url'] ?? null;
                            $initial = mb_strtoupper(mb_substr($title, 0, 1));
                            $pid = $row['id'] ?? null;
                            $href = $pid !== null
                                ? route('app.'.$profileSlug.'.marketplace.product', $pid).($dqProducts !== '' ? '?'.$dqProducts : '')
                                : '#';
                        @endphp
                        <a href="{{ $href }}" class="mp-card mp-card--clickable">
                            <div class="mp-card__media-wrap">
                                @if (! empty($img))
                                    <img src="{{ $img }}" alt="" class="mp-card__img" loading="lazy">
                                @else
                                    <div class="mp-card__placeholder mp-card__placeholder--product" aria-hidden="true">
                                        <span>{{ $initial }}</span>
                                    </div>
                                @endif
                                @if (! empty($row['price_display_fr']))
                                    <span class="mp-card__price-tag">{{ $row['price_display_fr'] }}</span>
                                @endif
                            </div>
                            <div class="mp-card__body">
                                @if (! empty($row['category']['name']))
                                    <p class="mp-card__category">{{ $row['category']['name'] }}</p>
                                @endif
                                <h3 class="mp-card__title">{{ $title }}</h3>
                                @if (! empty($row['description']))
                                    <p class="mp-card__excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($row['description']), 100) }}</p>
                                @endif
                                <div class="mp-card__footer">
                                    @if (! empty($row['owner']['company_name']))
                                        <span class="mp-card__seller">{{ $row['owner']['company_name'] }}</span>
                                    @elseif (! empty($row['owner']['name']))
                                        <span class="mp-card__seller">{{ $row['owner']['name'] }}</span>
                                    @endif
                                    @if (isset($row['views_count']))
                                        <span class="mp-card__views">{{ (int) $row['views_count'] }} vues</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="mp-empty app-muted">{{ $mpProdEmpty }}</p>
            @endif
        </section>
    @elseif ($mpTab === 'services')
        <section class="mp-section app-mt" aria-labelledby="mp-services-title">
            <div class="mp-section__head">
                <h2 id="mp-services-title" class="mp-section-title">
                    @if (($mpSrvPill ?? '') !== '')
                        <span class="mp-pill mp-pill--service">{{ $mpSrvPill }}</span>
                    @endif
                    {{ $mpSrvTitle }}
                </h2>
            </div>
            @if (count($services))
                <div class="mp-grid">
                    @foreach ($services as $row)
                        @php
                            $title = $row['title'] ?? 'Sans titre';
                            $img = $row['image_url'] ?? null;
                            $initial = mb_strtoupper(mb_substr($title, 0, 1));
                            $kind = $row['service_kind'] ?? '';
                            $kindLabel = match ($kind) {
                                'artisan' => 'Artisans',
                                'entrepreneur' => 'Entreprise bâtiment',
                                default => $kind,
                            };
                            $pricing = $row['pricing'] ?? [];
                            $priceLine = $pricing['title_fr'] ?? '';
                            if (! empty($pricing['detail_fr'])) {
                                $priceLine = $pricing['detail_fr'];
                            } elseif (! empty($row['price_fixed_label'])) {
                                $priceLine = $row['price_fixed_label'];
                            }
                            $sid = $row['id'] ?? null;
                            $href = $sid !== null
                                ? route('app.'.$profileSlug.'.marketplace.service', $sid).($dqServices !== '' ? '?'.$dqServices : '')
                                : '#';
                        @endphp
                        <a href="{{ $href }}" class="mp-card mp-card--service mp-card--clickable">
                            <div class="mp-card__media-wrap">
                                @if (! empty($img))
                                    <img src="{{ $img }}" alt="" class="mp-card__img" loading="lazy">
                                @else
                                    <div class="mp-card__placeholder mp-card__placeholder--service" aria-hidden="true">
                                        <span>{{ $initial }}</span>
                                    </div>
                                @endif
                                @if ($kindLabel !== '')
                                    <span class="mp-card__badge mp-card__badge--kind">{{ $kindLabel }}</span>
                                @endif
                            </div>
                            <div class="mp-card__body">
                                @if (! empty($row['category']['name']))
                                    <p class="mp-card__category">{{ $row['category']['name'] }}</p>
                                @endif
                                <h3 class="mp-card__title">{{ $title }}</h3>
                                @if (! empty($row['location']))
                                    <p class="mp-card__location">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 1118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                        {{ $row['location'] }}
                                    </p>
                                @endif
                                @if ($priceLine !== '')
                                    <p class="mp-card__price-line">{{ \Illuminate\Support\Str::limit($priceLine, 80) }}</p>
                                @endif
                                <div class="mp-card__footer">
                                    @if (! empty($row['owner']['name']))
                                        <span class="mp-card__seller">{{ $row['owner']['name'] }}</span>
                                    @endif
                                    @if (! empty($row['rating']) && (float) $row['rating'] > 0)
                                        <span class="mp-card__rating" title="Note">{{ number_format((float) $row['rating'], 1, ',', ' ') }} ★</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="mp-empty app-muted">{{ $mpSrvEmpty }}</p>
            @endif
        </section>
    @else
        <section class="mp-section app-mt" aria-labelledby="mp-besoins-title">
            <div class="mp-section__head">
                <h2 id="mp-besoins-title" class="mp-section-title">
                    @if (($mpBesPill ?? '') !== '')
                        <span class="mp-pill mp-pill--demand">{{ $mpBesPill }}</span>
                    @endif
                    {{ $mpBesTitle }}
                </h2>
            </div>
            @if (count($besoins))
                <div class="mp-grid mp-grid--besoins">
                    @foreach ($besoins as $row)
                        @php
                            $title = $row['title'] ?? 'Sans titre';
                            $img = $row['image_url'] ?? null;
                            $initial = mb_strtoupper(mb_substr($title, 0, 1));
                            $bid = $row['id'] ?? null;
                            $href = $bid !== null
                                ? route('app.'.$profileSlug.'.marketplace.besoin', $bid).($dqBesoins !== '' ? '?'.$dqBesoins : '')
                                : '#';
                        @endphp
                        <a href="{{ $href }}" class="mp-card mp-card--besoin mp-card--clickable">
                            <div class="mp-card__media-wrap mp-card__media-wrap--besoin">
                                @if (! empty($img))
                                    <img src="{{ $img }}" alt="" class="mp-card__img" loading="lazy">
                                @else
                                    <div class="mp-card__placeholder mp-card__placeholder--besoin" aria-hidden="true">
                                        <span>{{ $initial }}</span>
                                    </div>
                                @endif
                                @if (! empty($row['budget']))
                                    <span class="mp-card__price-tag mp-card__price-tag--budget">{{ $row['budget'] }}</span>
                                @endif
                            </div>
                            <div class="mp-card__body">
                                <h3 class="mp-card__title">{{ $title }}</h3>
                                @if (! empty($row['place']))
                                    <p class="mp-card__location">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 1118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                        {{ $row['place'] }}
                                    </p>
                                @endif
                                @if (! empty($row['description']))
                                    <p class="mp-card__excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($row['description']), 120) }}</p>
                                @endif
                                <div class="mp-card__footer">
                                    @if (! empty($row['owner']['name']))
                                        <span class="mp-card__seller">{{ $row['owner']['name'] }}</span>
                                    @endif
                                    @if (isset($row['candidature_count']))
                                        <span class="mp-card__views">{{ (int) $row['candidature_count'] }} réponse(s)</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="mp-empty app-muted">{{ $mpBesEmpty }}</p>
            @endif
        </section>
    @endif
@else
    @if (empty($apiError))
        <div class="app-card app-mt">
            <p class="app-muted">Les annonces ne peuvent pas être affichées pour le moment. Réessayez plus tard.</p>
        </div>
    @endif
@endif
