@extends('admin.layout', ['title' => 'Tableau de bord'])

@php
    $pLabels = [
        'entrepreneur_batiment' => 'Entrepreneur bâtiment',
        'entreprise_fournisseur' => 'Entreprise / fournisseur',
        'artisan' => 'Artisan',
        'particulier' => 'Particulier',
    ];

    $statusBadge = function (?string $s): array {
        return match ($s) {
            'approved', 'valide', 'accepte', 'open' => ['ok', 'Validé'],
            'pending', 'non_traite', 'recu', 'in_progress', 'en_cours' => ['pending', 'En attente'],
            'rejected', 'rejete', 'cancelled' => ['no', 'Refusé'],
            default => ['mute', $s ?? '—'],
        };
    };

    $kpiPending = $counts['pending_products'];

    $productsByStatus = ['approved' => 0, 'pending' => 0, 'rejected' => 0, 'draft' => 0];
    foreach (\App\Models\Product::query()->selectRaw('status, count(*) as c')->groupBy('status')->pluck('c', 'status') as $k => $c) {
        $productsByStatus[$k] = (int) $c;
    }
    $servicesByStatus = ['approved' => 0, 'pending' => 0, 'rejected' => 0, 'draft' => 0];
    foreach (\App\Models\Service::query()->selectRaw('status, count(*) as c')->groupBy('status')->pluck('c', 'status') as $k => $c) {
        $servicesByStatus[$k] = (int) $c;
    }

    $profileLabelsForChart = [];
    foreach ($usersByProfile->keys() as $k) {
        $profileLabelsForChart[] = $pLabels[$k] ?? $k;
    }
    $profileValuesForChart = array_values($usersByProfile->map(fn ($v) => (int) $v)->toArray());

    $moderationProducts = [
        $productsByStatus['approved'] ?? 0,
        $productsByStatus['pending'] ?? 0,
        $productsByStatus['rejected'] ?? 0,
        $productsByStatus['draft'] ?? 0,
    ];
    $moderationServices = [
        $servicesByStatus['approved'] ?? 0,
        $servicesByStatus['pending'] ?? 0,
        $servicesByStatus['rejected'] ?? 0,
        $servicesByStatus['draft'] ?? 0,
    ];
@endphp

@section('content')

<div class="admin-page-head">
    <div>
        <h1>Vue d’ensemble</h1>
        <p class="admin-page-head__sub">Activité de la plateforme &amp; modération en attente.</p>
    </div>
    <div class="admin-page-head__actions">
        <a href="{{ url('/api') }}" class="admin-btn admin-btn--ghost" title="Préfixe des routes de l’API (JSON, Sanctum)">
            <svg width="16" height="16" aria-hidden="true" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.22.22-1.8L8 14v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.99-2.1 5.38z"/></svg>
            API mobile (JSON)
        </a>
        <a href="{{ route('admin.reports') }}" class="admin-btn admin-btn--ghost">
            <svg width="16" height="16" aria-hidden="true"><use href="#admin-ico-chart" xlink:href="#admin-ico-chart"/></svg>
            Rapports détaillés
        </a>
    </div>
</div>

<div class="admin-kpi-grid">
    @if(($counts['pending_profile_validation'] ?? 0) > 0)
    <article class="admin-kpi" style="grid-column: 1 / -1; border:1px solid rgba(241,155,52,.35); background:linear-gradient(135deg, rgba(241,155,52,.08), transparent)">
        <div class="admin-kpi__head">
            <div class="admin-kpi__ico admin-kpi__ico--orange">
                <svg width="20" height="20" aria-hidden="true"><use href="#admin-ico-check" xlink:href="#admin-ico-check"/></svg>
            </div>
        </div>
        <p class="admin-kpi__label">Profils à valider (onboarding)</p>
        <p class="admin-kpi__value">{{ $counts['pending_profile_validation'] }}</p>
        <p class="admin-kpi__sub" style="margin-top:0.5rem">
            <a href="{{ route('admin.profile-validation.index', ['status' => 'pending']) }}" class="admin-link">Ouvrir la validation des profils →</a>
        </p>
    </article>
    @endif
    <article class="admin-kpi">
        <div class="admin-kpi__head">
            <div class="admin-kpi__ico admin-kpi__ico--blue">
                <svg width="20" height="20" aria-hidden="true"><use href="#admin-ico-users" xlink:href="#admin-ico-users"/></svg>
            </div>
        </div>
        <p class="admin-kpi__label">Utilisateurs app</p>
        <p class="admin-kpi__value">{{ number_format($counts['users'], 0, ',', ' ') }}</p>
    </article>
    <article class="admin-kpi">
        <div class="admin-kpi__head">
            <div class="admin-kpi__ico admin-kpi__ico--violet">
                <svg width="20" height="20" aria-hidden="true"><use href="#admin-ico-cube" xlink:href="#admin-ico-cube"/></svg>
            </div>
        </div>
        <p class="admin-kpi__label">Produits</p>
        <p class="admin-kpi__value">{{ number_format($counts['products'], 0, ',', ' ') }}</p>
    </article>
    <article class="admin-kpi">
        <div class="admin-kpi__head">
            <div class="admin-kpi__ico admin-kpi__ico--green">
                <svg width="20" height="20" aria-hidden="true"><use href="#admin-ico-wrench" xlink:href="#admin-ico-wrench"/></svg>
            </div>
        </div>
        <p class="admin-kpi__label">Services</p>
        <p class="admin-kpi__value">{{ number_format($counts['services'], 0, ',', ' ') }}</p>
    </article>
    <article class="admin-kpi">
        <div class="admin-kpi__head">
            <div class="admin-kpi__ico admin-kpi__ico--navy">
                <svg width="20" height="20" aria-hidden="true"><use href="#admin-ico-doc" xlink:href="#admin-ico-doc"/></svg>
            </div>
        </div>
        <p class="admin-kpi__label">Devis</p>
        <p class="admin-kpi__value">{{ number_format($counts['devis'], 0, ',', ' ') }}</p>
    </article>
    <article class="admin-kpi">
        <div class="admin-kpi__head">
            <div class="admin-kpi__ico admin-kpi__ico--rose">
                <svg width="20" height="20" aria-hidden="true"><use href="#admin-ico-store" xlink:href="#admin-ico-store"/></svg>
            </div>
        </div>
        <p class="admin-kpi__label">Besoins</p>
        <p class="admin-kpi__value">{{ number_format($counts['besoins'], 0, ',', ' ') }}</p>
    </article>
    <article class="admin-kpi">
        <div class="admin-kpi__head">
            <div class="admin-kpi__ico admin-kpi__ico--navy">
                <svg width="20" height="20" aria-hidden="true"><use href="#admin-ico-doc" xlink:href="#admin-ico-doc"/></svg>
            </div>
            @if (($counts['support_tickets_active'] ?? 0) > 0)
                <span class="admin-kpi__delta admin-kpi__delta--down">À traiter</span>
            @endif
        </div>
        <p class="admin-kpi__label">Tickets support (actifs)</p>
        <p class="admin-kpi__value">{{ number_format($counts['support_tickets_active'] ?? 0, 0, ',', ' ') }}</p>
        <p class="admin-kpi__sub">Statuts « ouvert » et « en cours »</p>
        <p class="admin-kpi__sub" style="margin-top:0.35rem">
            <a href="{{ route('admin.support.tickets.index') }}" class="admin-link">Ouvrir le support →</a>
        </p>
    </article>
    <article class="admin-kpi">
        <div class="admin-kpi__head">
            <div class="admin-kpi__ico admin-kpi__ico--orange">
                <svg width="20" height="20" aria-hidden="true"><use href="#admin-ico-clipboard" xlink:href="#admin-ico-clipboard"/></svg>
            </div>
            @if ($kpiPending > 0)
                <span class="admin-kpi__delta admin-kpi__delta--down">À traiter</span>
            @endif
        </div>
        <p class="admin-kpi__label">Modération</p>
        <p class="admin-kpi__value">{{ $kpiPending }}</p>
        <p class="admin-kpi__sub">Produits en attente</p>
    </article>
</div>

<div class="admin-chart-grid">
    <div class="admin-chart-card">
        <h3>Inscrits par profil</h3>
        <div class="admin-chart-canvas"><canvas id="chartProfiles"></canvas></div>
    </div>
    <div class="admin-chart-card">
        <h3>Modération produits</h3>
        <div class="admin-chart-canvas"><canvas id="chartModeration"></canvas></div>
    </div>
</div>

<div class="card">
    <div class="admin-card-h">
        <div>
            <h3 class="admin-card-title">Derniers produits</h3>
            <p class="admin-card-sub">5 dernières fiches déposées par les fournisseurs.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="admin-btn admin-btn--soft admin-btn--sm">Tout voir</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Titre</th><th>Statut</th><th>Fournisseur</th><th class="row-actions"></th></tr></thead>
            <tbody>
            @forelse($recentProducts as $p)
                @php([$badge, $bLabel] = $statusBadge($p->status))
                <tr>
                    <td><strong>{{ $p->title }}</strong></td>
                    <td><span class="badge b-{{ $badge }}">{{ $bLabel }}</span></td>
                    <td>{{ $p->user?->name ?? '—' }}</td>
                    <td class="row-actions"><a class="admin-link" href="{{ route('admin.products.show', $p) }}">Voir</a></td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center; padding:1.5rem; color:var(--text-3)">Aucun produit.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="admin-card-h">
        <div>
            <h3 class="admin-card-title">Derniers services</h3>
            <p class="admin-card-sub">5 dernières prestations publiées (artisan / entrepreneur).</p>
        </div>
        <a href="{{ route('admin.services.index') }}" class="admin-btn admin-btn--soft admin-btn--sm">Tout voir</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Titre</th><th>Type</th><th>Visibilité</th><th>Prestataire</th><th class="row-actions"></th></tr></thead>
            <tbody>
            @forelse($recentServices as $s)
                <tr>
                    <td><strong>{{ $s->title }}</strong></td>
                    <td><span class="badge b-mute">{{ $s->service_kind }}</span></td>
                    <td>
                        @if($s->is_visible)
                            <span class="badge b-ok">Visible</span>
                        @else
                            <span class="badge b-mute">Masqué</span>
                        @endif
                    </td>
                    <td>{{ $s->user?->name ?? '—' }}</td>
                    <td class="row-actions"><a class="admin-link" href="{{ route('admin.services.show', $s) }}">Voir</a></td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center; padding:1.5rem; color:var(--text-3)">Aucun service.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const navy = '#0b1f3b', orange = '#f19b34';
    const palette = ['#1e40af', '#6d28d9', '#047857', '#b76208', '#be123c', '#0891b2'];
    const profileLabels = @json($profileLabelsForChart);
    const profileValues = @json($profileValuesForChart);
    const moderationLabels = ['Validé', 'En attente', 'Refusé', 'Brouillon'];
    const moderationProducts = @json($moderationProducts);
    const moderationServices = @json($moderationServices);

    function ready(fn) {
        if (typeof Chart === 'undefined') {
            return setTimeout(() => ready(fn), 60);
        }
        Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
        Chart.defaults.color = '#6b7689';
        Chart.defaults.borderColor = '#eef0f4';
        fn();
    }

    ready(function () {
        const ctxP = document.getElementById('chartProfiles');
        if (ctxP && profileValues.length) {
            new Chart(ctxP, {
                type: 'doughnut',
                data: {
                    labels: profileLabels,
                    datasets: [{
                        data: profileValues,
                        backgroundColor: palette,
                        borderColor: '#fff',
                        borderWidth: 3,
                        hoverOffset: 8,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '64%',
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 8, boxHeight: 8, padding: 14, usePointStyle: true } },
                        tooltip: { backgroundColor: '#0b1220', padding: 10, cornerRadius: 8 },
                    },
                },
            });
        }

        const ctxM = document.getElementById('chartModeration');
        if (ctxM) {
            new Chart(ctxM, {
                type: 'bar',
                data: {
                    labels: moderationLabels,
                    datasets: [
                        { label: 'Produits', data: moderationProducts, backgroundColor: navy, borderRadius: 8, barPercentage: 0.7, categoryPercentage: 0.65 },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 8, boxHeight: 8, padding: 14, usePointStyle: true } },
                        tooltip: { backgroundColor: '#0b1220', padding: 10, cornerRadius: 8 },
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: '#6b7689' } },
                        y: { beginAtZero: true, ticks: { precision: 0, color: '#6b7689' }, grid: { color: '#eef0f4' } },
                    },
                },
            });
        }
    });
})();
</script>
@endpush

@endsection
