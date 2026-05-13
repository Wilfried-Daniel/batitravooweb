@extends('admin.layout', ['title' => 'Rapports'])

@php
    $devisLabels = [
        'non_traite' => 'Non traité',
        'en_cours' => 'En cours',
        'envoye' => 'Envoyé',
        'valide' => 'Validé',
        'rejete' => 'Rejeté',
    ];
    $productLabels = [
        'draft' => 'Brouillon',
        'pending' => 'En attente',
        'approved' => 'Validé',
        'rejected' => 'Rejeté',
    ];
    $besoinLabels = [
        'open' => 'Ouvert',
        'in_progress' => 'En cours',
        'closed' => 'Clôturé',
        'cancelled' => 'Annulé',
    ];

    $devisData = collect($devisLabels)->mapWithKeys(fn ($l, $k) => [$l => (int) ($devisByStatus[$k] ?? 0)]);
    $prodData = collect($productLabels)->mapWithKeys(fn ($l, $k) => [$l => (int) ($productsByStatus[$k] ?? 0)]);
    $servData = collect($productLabels)->mapWithKeys(fn ($l, $k) => [$l => (int) ($servicesByStatus[$k] ?? 0)]);
    $besData = collect($besoinLabels)->mapWithKeys(fn ($l, $k) => [$l => (int) ($besoinsByStatus[$k] ?? 0)]);
@endphp

@section('content')

<div class="admin-page-head">
    <div>
        <h1>Rapports</h1>
        <p class="admin-page-head__sub">Vue analytique de l’activité par statut.</p>
    </div>
</div>

<div class="admin-chart-grid">
    <div class="admin-chart-card">
        <h3>Devis par statut</h3>
        <div class="admin-chart-canvas"><canvas id="chartDevis"></canvas></div>
    </div>
    <div class="admin-chart-card">
        <h3>Produits par statut</h3>
        <div class="admin-chart-canvas"><canvas id="chartProducts"></canvas></div>
    </div>
    <div class="admin-chart-card">
        <h3>Services par statut</h3>
        <div class="admin-chart-canvas"><canvas id="chartServices"></canvas></div>
    </div>
    <div class="admin-chart-card">
        <h3>Besoins par statut</h3>
        <div class="admin-chart-canvas"><canvas id="chartBesoins"></canvas></div>
    </div>
</div>

<div class="card">
    <div class="admin-card-h">
        <h3 class="admin-card-title">Inscriptions sur les 6 derniers mois</h3>
    </div>
    @if($userGrowth->isEmpty())
        <p class="admin-empty-hint">Pas encore d’inscriptions sur la période.</p>
    @else
        <div class="admin-chart-canvas admin-chart-canvas--lg"><canvas id="chartGrowth"></canvas></div>
    @endif
</div>

@push('scripts')
<script>
(function () {
    const navy = '#001f3f', orange = '#f19b34';
    const semantic = {
        ok: '#10b981', warn: '#f59e0b', no: '#ef4444', mute: '#94a3b8', info: '#3b82f6',
    };

    const data = {
        devis: {
            labels: @json($devisData->keys()),
            values: @json($devisData->values()),
            colors: [semantic.mute, semantic.warn, semantic.info, semantic.ok, semantic.no],
        },
        products: {
            labels: @json($prodData->keys()),
            values: @json($prodData->values()),
            colors: [semantic.mute, semantic.warn, semantic.ok, semantic.no],
        },
        services: {
            labels: @json($servData->keys()),
            values: @json($servData->values()),
            colors: [semantic.mute, semantic.warn, semantic.ok, semantic.no],
        },
        besoins: {
            labels: @json($besData->keys()),
            values: @json($besData->values()),
            colors: [semantic.ok, semantic.warn, semantic.mute, semantic.no],
        },
    };

    const growthLabels = @json($userGrowth->pluck('m'));
    const growthValues = @json($userGrowth->pluck('c')->map(fn ($v) => (int) $v));

    function ready(fn) {
        if (typeof Chart === 'undefined') {
            return setTimeout(() => ready(fn), 60);
        }
        Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
        Chart.defaults.color = '#5b6b7d';
        Chart.defaults.borderColor = '#e6eaf0';
        fn();
    }

    function pieChart(id, ds) {
        const el = document.getElementById(id);
        if (!el) return;
        new Chart(el, {
            type: 'doughnut',
            data: {
                labels: ds.labels,
                datasets: [{
                    data: ds.values,
                    backgroundColor: ds.colors,
                    borderColor: '#fff',
                    borderWidth: 2,
                    hoverOffset: 6,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, boxHeight: 10, padding: 10, usePointStyle: true } },
                },
            },
        });
    }

    ready(function () {
        pieChart('chartDevis', data.devis);
        pieChart('chartProducts', data.products);
        pieChart('chartServices', data.services);
        pieChart('chartBesoins', data.besoins);

        const growthEl = document.getElementById('chartGrowth');
        if (growthEl && growthValues.length) {
            new Chart(growthEl, {
                type: 'line',
                data: {
                    labels: growthLabels,
                    datasets: [{
                        label: 'Inscriptions',
                        data: growthValues,
                        borderColor: orange,
                        backgroundColor: 'rgba(241, 155, 52, 0.12)',
                        borderWidth: 2,
                        tension: 0.35,
                        fill: true,
                        pointBackgroundColor: orange,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false } },
                        y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#eef2f7' } },
                    },
                },
            });
        }
    });
})();
</script>
@endpush

@endsection
