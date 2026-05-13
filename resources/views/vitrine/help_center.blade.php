@extends('vitrine.layout')

@section('title', 'Centre d’aide — '.config('app.name', 'BATITRAVOO'))
@section('h1', 'Centre d’aide')
@section('subtitle')
    Retrouvez les réponses essentielles pour bien démarrer et résoudre rapidement les problèmes courants.
@endsection

@section('content')
    <div class="breadcrumb">
        <a href="{{ url('/') }}">Accueil</a> <span class="crumb-dot" aria-hidden="true"></span> <span>Centre d’aide</span>
    </div>

    <div class="page-hero">
        <div>
            <h2 style="margin-top:0">Guides rapides</h2>
            <p class="muted">
                Des repères simples pour bien utiliser la plateforme — que vous soyez particulier, artisan, BTP ou fournisseur.
            </p>
        </div>
        <aside class="hero-aside">
            <div class="pill"><span class="dot" aria-hidden="true"></span> Démarrage en 3 minutes</div>
            <div class="pill"><span class="dot" aria-hidden="true"></span> Réponses par thème</div>
        </aside>
    </div>

    <div class="grid-2">
        <section class="card">
            <div class="card-title">
                <span class="icon-badge" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10Z" stroke="currentColor" stroke-width="2" opacity="0.35"/>
                    </svg>
                </span>
                <span>Premiers pas</span>
            </div>
            <ul>
                <li>Créer un compte et compléter son profil</li>
                <li>Publier un besoin (Particulier / BTP)</li>
                <li>Créer un service (Artisan / BTP)</li>
                <li>Commander des matériaux (Marketplace)</li>
            </ul>
        </section>

        <section class="card">
            <div class="card-title">
                <span class="icon-badge" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 11a3 3 0 100-6 3 3 0 000 6Z" stroke="currentColor" stroke-width="2"/>
                        <path d="M4 21a8 8 0 0116 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M19 8l2 2m-2-2l-2 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity="0.5"/>
                    </svg>
                </span>
                <span>Compte & sécurité</span>
            </div>
            <ul>
                <li>Mot de passe oublié / changement de mot de passe</li>
                <li>Gestion du profil et de la localisation</li>
                <li>Bonnes pratiques : garder un profil à jour</li>
            </ul>
        </section>
    </div>

    <section class="card" style="margin-top:1.25rem">
        <div class="card-title">
            <span class="icon-badge" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 15a4 4 0 01-4 4H8l-5 3V7a4 4 0 014-4h10a4 4 0 014 4v8Z" stroke="currentColor" stroke-width="2" opacity="0.35"/>
                    <path d="M8 8h8M8 12h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </span>
            <span>Besoin d’assistance ?</span>
        </div>
        <p class="muted">
            Consultez la <a href="{{ route('vitrine.faq') }}">FAQ</a> ou contactez-nous via la page
            <a href="{{ route('vitrine.contact') }}">Contact</a>.
        </p>
    </section>
@endsection
