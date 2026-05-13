@extends('vitrine.layout')

@section('title', 'Contact — '.config('app.name', 'BATITRAVOO'))
@section('h1', 'Contact')
@section('subtitle')
    Une question, un retour, un besoin de démonstration ? Écrivez-nous, on vous répond rapidement.
@endsection

@section('content')
    <div class="breadcrumb">
        <a href="{{ url('/') }}">Accueil</a> <span class="crumb-dot" aria-hidden="true"></span> <span>Contact</span>
    </div>

    <div class="page-hero">
        <div>
            <h2 style="margin-top:0">Parlons de votre projet</h2>
            <p class="muted">
                Support, partenariat, démonstration : choisissez le canal qui vous convient.
            </p>
        </div>
        <aside class="hero-aside">
            <div class="pill"><span class="dot" aria-hidden="true"></span> Réponse rapide (heures ouvrées)</div>
            <div class="pill"><span class="dot" aria-hidden="true"></span> Support & assistance</div>
        </aside>
    </div>

    <div class="grid-2">
        <section class="card" aria-label="Coordonnées">
            <div class="card-title">
                <span class="icon-badge" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 4h16v16H4V4Z" stroke="currentColor" stroke-width="2" opacity="0.35"/>
                        <path d="M4 7l8 6 8-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span>Coordonnées</span>
            </div>
            <p>
                <strong>E-mail</strong><br>
                <a href="mailto:support@batitravoo.com">support@batitravoo.com</a>
            </p>
            <p style="margin-top:0.75rem">
                <strong>WhatsApp</strong><br>
                <a href="https://wa.me/" rel="noopener noreferrer">Démarrer une conversation</a>
            </p>
            <p style="margin-top:0.75rem" class="muted">
                <strong>Horaires</strong> : Lundi – Samedi, 08:00 – 18:00.
            </p>
        </section>

        <section class="card" aria-label="Formulaire">
            <div class="card-title">
                <span class="icon-badge" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 6h16M4 12h10M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </span>
                <span>Écrire un message</span>
            </div>

            <form action="mailto:support@batitravoo.com" method="post" enctype="text/plain" class="form-row">
                <div class="field">
                    <label for="name">Nom</label>
                    <input id="name" name="Nom" type="text" placeholder="Votre nom" autocomplete="name">
                </div>
                <div class="field">
                    <label for="email">E-mail</label>
                    <input id="email" name="Email" type="email" placeholder="vous@exemple.com" autocomplete="email">
                </div>
                <div class="field">
                    <label for="subject">Objet</label>
                    <input id="subject" name="Objet" type="text" placeholder="Support, partenariat, démonstration…">
                </div>
                <div class="field">
                    <label for="body">Message</label>
                    <textarea id="body" name="Message" placeholder="Décrivez votre demande…"></textarea>
                </div>
                <div>
                    <button type="submit" class="btn-primary">Envoyer</button>
                    <p class="muted" style="margin-top:0.6rem;font-size:0.9rem">
                        Ce formulaire ouvre votre client e-mail (aucune donnée n’est stockée sur le site vitrine).
                    </p>
                </div>
            </form>
        </section>
    </div>

    <section class="card" style="margin-top:1.25rem" aria-label="Ressources">
        <div class="card-title">
            <span class="icon-badge" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 18h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                    <path d="M9.5 9a2.5 2.5 0 115 0c0 2-2.5 1.75-2.5 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10Z" stroke="currentColor" stroke-width="2" opacity="0.35"/>
                </svg>
            </span>
            <span>Avant de nous écrire</span>
        </div>
        <p class="muted">Les réponses les plus courantes sont souvent déjà disponibles ici :</p>
        <ul>
            <li><a href="{{ route('vitrine.help_center') }}">Centre d’aide</a></li>
            <li><a href="{{ route('vitrine.faq') }}">FAQ</a></li>
            <li><a href="{{ route('vitrine.terms') }}">Conditions d’utilisation</a> et <a href="{{ route('vitrine.privacy') }}">politique de confidentialité</a></li>
        </ul>
    </section>
@endsection
