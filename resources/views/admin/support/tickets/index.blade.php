@extends('admin.layout', ['title' => 'Support — tickets'])

@section('content')

<div class="admin-page-head">
    <div>
        <h1>Tickets support</h1>
        <p class="admin-page-head__sub">Demandes utilisateurs — statut, priorité, assignation.</p>
    </div>
</div>

<form method="get" class="card admin-filters" role="search">
    <div class="form-row">
        <label for="status">Statut</label>
        <select id="status" name="status" style="max-width:220px">
            <option value="">Tous</option>
            <option value="open" @selected(request('status') === 'open')>Ouverts</option>
            <option value="in_progress" @selected(request('status') === 'in_progress')>En cours</option>
            <option value="resolved" @selected(request('status') === 'resolved')>Résolus</option>
            <option value="closed" @selected(request('status') === 'closed')>Clos</option>
        </select>
    </div>
    <div class="form-row" style="flex:1; min-width:200px">
        <label for="q">Recherche</label>
        <input id="q" type="text" name="q" value="{{ request('q') }}" placeholder="Sujet, nom, e-mail…">
    </div>
    <div class="form-row">
        <button type="submit" class="admin-btn admin-btn--navy">Filtrer</button>
    </div>
    <div class="form-row">
        @if(request('q') || request('status'))
            <a href="{{ route('admin.support.tickets.index') }}" class="admin-btn admin-btn--ghost">Réinitialiser</a>
        @endif
    </div>
</form>

<div class="card" style="padding:0">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Sujet</th>
                    <th>Demandeur</th>
                    <th>Priorité</th>
                    <th>Statut</th>
                    <th>Assigné</th>
                    <th>Date</th>
                    <th class="row-actions"></th>
                </tr>
            </thead>
            <tbody>
            @forelse($tickets as $t)
                @php
                    $st = match ($t->status) {
                        'open' => ['pending', 'Ouvert'],
                        'in_progress' => ['mute', 'En cours'],
                        'resolved' => ['ok', 'Résolu'],
                        'closed' => ['no', 'Clos'],
                        default => ['mute', $t->status],
                    };
                    $pr = match ($t->priority) {
                        'high' => 'Haute',
                        'low' => 'Basse',
                        default => 'Normale',
                    };
                @endphp
                <tr>
                    <td>{{ $t->id }}</td>
                    <td><strong>{{ $t->subject }}</strong></td>
                    <td>{{ $t->user?->name ?? '—' }}<br><span style="color:var(--text-3);font-size:0.88em">{{ $t->user?->email }}</span></td>
                    <td>{{ $pr }}</td>
                    <td><span class="badge b-{{ $st[0] }}">{{ $st[1] }}</span></td>
                    <td>{{ $t->assignedTo?->name ?? '—' }}</td>
                    <td style="white-space:nowrap;font-size:0.9em">{{ $t->created_at?->format('d/m/Y H:i') }}</td>
                    <td class="row-actions"><a class="admin-link" href="{{ route('admin.support.tickets.show', $t) }}">Traiter</a></td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--text-3)">Aucun ticket.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:0 22px 18px">{{ $tickets->links() }}</div>
</div>

@endsection
