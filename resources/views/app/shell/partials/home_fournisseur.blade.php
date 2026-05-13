@if ($profileSlug === 'fournisseur')
    @php
        $u = $profileData['user'] ?? [];
        $company = trim((string) ($u['company_name'] ?? ''));
        $displayName = $company !== '' ? $company : (string) ($u['name'] ?? 'Fournisseur');
        $initials = mb_strtoupper(mb_substr(preg_replace('/\s+/', '', $displayName), 0, 2));
        if ($initials === '') {
            $initials = '?';
        }
    @endphp

    <div class="app-card app-card--flush app-fournisseur-home-head">
        <div class="app-fournisseur-home-head__row">
            <div class="app-fournisseur-home-head__avatar">
                @if (! empty($u['avatar_url']))
                    <img src="{{ $u['avatar_url'] }}" alt="" width="48" height="48" class="app-fournisseur-home-head__avatar-img">
                @else
                    <span class="app-fournisseur-home-head__avatar-ph">{{ $initials }}</span>
                @endif
            </div>
            <div class="app-fournisseur-home-head__greet">
                <span class="app-muted app-fournisseur-home-head__hi">Bonjour,</span>
                <strong class="app-fournisseur-home-head__name">{{ $displayName }}</strong>
            </div>
        </div>
    </div>

    <div class="app-card app-mt">
        <label for="supplier-home-search" class="app-muted app-text-sm">Recherche dans mes produits</label>
        <input type="search" id="supplier-home-search" class="app-input app-input--rounded app-mt-sm" placeholder="Filtrer par titre…" autocomplete="off">
    </div>

    @include('app.shell.partials.home_shortcuts')

    <div class="app-card app-mt">
        <h2 class="app-section-title">Produits récemment publiés</h2>
        @if (empty($supplierProducts) || ! count($supplierProducts))
            <p class="app-muted">Aucun produit en base pour le moment. Ajoutez des articles depuis « Catalogue produits ».</p>
        @else
            <div id="supplier-home-grid" class="app-supplier-home-grid">
                @foreach ($supplierProducts as $p)
                    @php
                        $title = (string) ($p['title'] ?? '');
                        $slugTitle = \Illuminate\Support\Str::lower($title);
                    @endphp
                    <article class="app-supplier-home-tile" data-title-search="{{ e($slugTitle) }}">
                        <span class="app-supplier-home-tile__badge">{{ (int) ($p['views_count'] ?? 0) }} vues</span>
                        <div class="app-supplier-home-tile__img-wrap">
                            @if (! empty($p['image_url']))
                                <img src="{{ $p['image_url'] }}" alt="" class="app-supplier-home-tile__img">
                            @else
                                <div class="app-supplier-home-tile__img-ph" aria-hidden="true"></div>
                            @endif
                        </div>
                        <h3 class="app-supplier-home-tile__title">{{ $title ?: '—' }}</h3>
                        <p class="app-supplier-home-tile__price">{{ $p['price_display_fr'] ?? '—' }}</p>
                        <p class="app-supplier-home-tile__stock">Stock : {{ (int) ($p['stock_units'] ?? 0) }}</p>
                    </article>
                @endforeach
            </div>
            <p id="supplier-home-empty" class="app-muted app-mt-md" hidden>Aucun produit ne correspond à votre recherche.</p>
        @endif
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var input = document.getElementById('supplier-home-search');
            var grid = document.getElementById('supplier-home-grid');
            var empty = document.getElementById('supplier-home-empty');
            if (!input || !grid) return;
            var tiles = grid.querySelectorAll('[data-title-search]');
            function run() {
                var q = (input.value || '').trim().toLowerCase();
                var n = 0;
                tiles.forEach(function (el) {
                    var t = (el.getAttribute('data-title-search') || '').toLowerCase();
                    var show = !q || t.indexOf(q) !== -1;
                    el.hidden = !show;
                    if (show) n++;
                });
                if (empty) empty.hidden = n !== 0;
            }
            input.addEventListener('input', run);
            run();
        });
    </script>
    @endpush
@endif
