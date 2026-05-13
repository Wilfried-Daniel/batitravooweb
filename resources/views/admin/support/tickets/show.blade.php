@extends('admin.layout', ['title' => 'Ticket #'.$ticket->id])

@php
    $stLabel = match ($ticket->status) {
        'open' => ['pending', 'Ouvert'],
        'in_progress' => ['mute', 'En cours'],
        'resolved' => ['ok', 'Résolu'],
        'closed' => ['no', 'Clos'],
        default => ['mute', $ticket->status],
    };
@endphp

@section('content')

<div class="admin-page-head">
    <div>
        <h1>{{ $ticket->subject }}</h1>
        <p class="admin-page-head__sub">Ticket #{{ $ticket->id }} — <span class="badge b-{{ $stLabel[0] }}">{{ $stLabel[1] }}</span></p>
    </div>
    <div class="admin-page-head__actions">
        <a href="{{ route('admin.support.tickets.index') }}" class="admin-btn admin-btn--ghost admin-btn--sm">← Liste</a>
    </div>
</div>

<div class="card" style="margin-bottom:1rem">
    <h3 class="admin-card-title">Demandeur</h3>
    <p style="margin:0"><strong>{{ $ticket->user?->name }}</strong> — {{ $ticket->user?->email }} — {{ $ticket->user?->phone ?? '—' }}</p>
</div>

<div class="card" style="margin-bottom:1rem">
    <h3 class="admin-card-title">Traitement</h3>
    <form method="post" action="{{ route('admin.support.tickets.update', $ticket) }}" class="admin-stack">
        @csrf
        @method('PUT')
        <div class="form-row" style="max-width:280px">
            <label for="status">Statut</label>
            <select id="status" name="status" required>
                <option value="open" @selected($ticket->status === 'open')>Ouvert</option>
                <option value="in_progress" @selected($ticket->status === 'in_progress')>En cours</option>
                <option value="resolved" @selected($ticket->status === 'resolved')>Résolu</option>
                <option value="closed" @selected($ticket->status === 'closed')>Clos</option>
            </select>
        </div>
        <div class="form-row" style="max-width:280px">
            <label for="priority">Priorité</label>
            <select id="priority" name="priority" required>
                <option value="low" @selected($ticket->priority === 'low')>Basse</option>
                <option value="normal" @selected($ticket->priority === 'normal')>Normale</option>
                <option value="high" @selected($ticket->priority === 'high')>Haute</option>
            </select>
        </div>
        <div class="form-row" style="max-width:360px">
            <label for="assigned_to_user_id">Assigné à</label>
            <select id="assigned_to_user_id" name="assigned_to_user_id">
                <option value="">— Non assigné —</option>
                @foreach($admins as $a)
                    <option value="{{ $a->id }}" @selected((int)$ticket->assigned_to_user_id === (int)$a->id)>{{ $a->name }} ({{ $a->email }})</option>
                @endforeach
            </select>
        </div>
        <div class="admin-form-actions">
            <button type="submit" class="admin-btn admin-btn--navy">Enregistrer</button>
        </div>
    </form>
</div>

<div class="card" style="margin-bottom:1rem">
    <h3 class="admin-card-title">Conversation</h3>
    <div style="display:flex; flex-direction:column; gap:12px">
        @foreach($ticket->messages as $m)
            <div style="padding:12px 14px; border-radius:10px; background: {{ $m->is_staff ? 'rgba(11,31,59,0.06)' : 'var(--surface-2)' }}; border:1px solid var(--border-1)">
                <div style="display:flex; justify-content:space-between; gap:8px; margin-bottom:6px; font-size:0.88em; color:var(--text-3)">
                    <span><strong>{{ $m->user?->name }}</strong> @if($m->is_staff)<span class="badge b-ok" style="font-size:0.75rem">Support</span>@endif</span>
                    <span>{{ $m->created_at?->format('d/m/Y H:i') }}</span>
                </div>
                <div style="white-space:pre-wrap">{{ $m->body }}</div>
                @if($m->attachment_path)
                    <p style="margin:8px 0 0"><a href="{{ storage_public_url($m->attachment_path) }}" target="_blank" rel="noopener" class="admin-link">Pièce jointe</a></p>
                @endif
            </div>
        @endforeach
    </div>
</div>

<div class="card">
    <h3 class="admin-card-title">Répondre</h3>
    <form method="post" action="{{ route('admin.support.tickets.messages.store', $ticket) }}" enctype="multipart/form-data">
        @csrf
        <x-admin.field name="body" type="textarea" label="Message" value="" required rows="5" maxWidth="100%" />
        <div class="form-row" style="max-width:400px">
            <label for="attachment">Pièce jointe (optionnel)</label>
            <input id="attachment" type="file" name="attachment" accept="image/*,.pdf,.doc,.docx">
        </div>
        <div class="admin-form-actions">
            <button type="submit" class="admin-btn admin-btn--orange">Envoyer au client</button>
        </div>
    </form>
</div>

@endsection
