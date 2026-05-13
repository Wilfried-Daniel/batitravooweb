@extends('admin.layout', ['title' => 'Administrateurs'])

@section('content')

<div class="admin-page-head">
    <div>
        <h1>Administrateurs</h1>
        <p class="admin-page-head__sub">Comptes avec rôle <code>admin</code> (connexion back-office). Création et suppression depuis cette page.</p>
    </div>
</div>

<div class="card" style="margin-bottom:1.25rem; padding:1rem 1.25rem">
    <h3 class="admin-card-title" style="margin-top:0">Ajouter un administrateur</h3>
    <form method="post" action="{{ route('admin.administrators.store') }}" style="display:grid; gap:1rem; max-width:520px">
        @csrf
        <x-admin.field name="name" label="Nom complet" :value="old('name')" required />
        <x-admin.field name="email" type="email" label="Adresse e-mail" :value="old('email')" required autocomplete="email" />
        <x-admin.field name="password" type="password" label="Mot de passe" required autocomplete="new-password" />
        <x-admin.field name="password_confirmation" type="password" label="Confirmation du mot de passe" required autocomplete="new-password" />
        <div>
            <button type="submit" class="admin-btn admin-btn--primary">Créer le compte</button>
        </div>
    </form>
</div>

<div class="card" style="padding:0">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Nom</th><th>E-mail</th><th>Créé le</th><th class="row-actions"></th></tr></thead>
            <tbody>
            @forelse($admins as $a)
                <tr>
                    <td><strong>{{ $a->name }}</strong></td>
                    <td>{{ $a->email }}</td>
                    <td>{{ $a->created_at?->format('d/m/Y') }}</td>
                    <td class="row-actions">
                        @if($a->id !== auth()->id())
                            <form method="post" action="{{ route('admin.administrators.destroy', $a) }}" style="display:inline" onsubmit="return confirm('Supprimer cet administrateur ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="admin-link" style="background:none;border:none;padding:0;cursor:pointer;color:#b91c1c;font:inherit">Supprimer</button>
                            </form>
                        @else
                            <span style="color:var(--text-3); font-size:0.9em">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center; padding:1.5rem">Aucun administrateur trouvé.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
