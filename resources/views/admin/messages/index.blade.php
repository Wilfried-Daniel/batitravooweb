@extends('admin.layout', ['title' => 'Messagerie'])

@section('content')

<div class="admin-page-head">
    <div>
        <h1>Messagerie</h1>
        <p class="admin-page-head__sub">Messages échangés entre utilisateurs (API mobile).</p>
    </div>
</div>

<form method="get" class="card admin-filters" role="search">
    <div class="form-row" style="flex:1; min-width:220px">
        <label for="q">Recherche dans le texte</label>
        <input id="q" type="text" name="q" value="{{ request('q') }}" placeholder="Contenu…">
    </div>
    <div class="form-row">
        <button type="submit" class="admin-btn admin-btn--navy">Filtrer</button>
    </div>
    <div class="form-row">
        @if(request('q'))
            <a href="{{ route('admin.messages.index') }}" class="admin-btn admin-btn--ghost">Réinitialiser</a>
        @endif
    </div>
</form>

<div class="card" style="padding:0">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>De</th>
                    <th>Vers</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
            @forelse($messages as $m)
                <tr>
                    <td style="white-space:nowrap">{{ $m->created_at?->format('d/m/Y H:i') }}</td>
                    <td>{{ $m->sender?->name ?? '—' }}</td>
                    <td>{{ $m->receiver?->name ?? '—' }}</td>
                    <td style="max-width:420px">{{ \Illuminate\Support\Str::limit($m->body, 160) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center; padding:1.5rem; color:var(--text-3)">Aucun message.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $messages->links() }}
</div>

@endsection
