@extends('vitrine.layout')

@section('title', 'FAQ — '.config('app.name', 'BATITRAVOO'))
@section('h1', 'FAQ')
@section('subtitle')
    Les questions les plus fréquentes sur BATITRAVOO.
@endsection

@section('content')
    <div class="breadcrumb">
        <a href="{{ url('/') }}">Accueil</a> <span class="crumb-dot" aria-hidden="true"></span> <span>FAQ</span>
    </div>

    <div class="page-hero">
        <div>
            <h2 style="margin-top:0">Réponses rapides</h2>
            <p class="muted">
                Ouvrez une question pour voir la réponse. Si vous ne trouvez pas, écrivez-nous via <a href="{{ route('vitrine.contact') }}">Contact</a>.
            </p>
        </div>
        <aside class="hero-aside">
            <div class="pill"><span class="dot" aria-hidden="true"></span> Comptes & accès</div>
            <div class="pill"><span class="dot" aria-hidden="true"></span> Marketplace & devis</div>
        </aside>
    </div>

    <div class="accordion" role="region" aria-label="Questions fréquentes">
        <details class="acc">
            <summary>
                Qui peut utiliser BATITRAVOO ?
                <svg class="chev" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </summary>
            <div class="acc-body">
                <p>Les particuliers, artisans, entreprises BTP et fournisseurs.</p>
            </div>
        </details>

        <details class="acc">
            <summary>
                Comment publier un besoin ?
                <svg class="chev" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </summary>
            <div class="acc-body">
                <p>Après inscription et connexion, allez dans l’onglet « Besoins », puis « Nouveau besoin ».</p>
            </div>
        </details>

        <details class="acc">
            <summary>
                Comment commander des matériaux ?
                <svg class="chev" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </summary>
            <div class="acc-body">
                <p>Accédez à la marketplace, ajoutez les produits au panier, puis validez la commande.</p>
            </div>
        </details>

        <details class="acc">
            <summary>
                Je n’arrive pas à me connecter, que faire ?
                <svg class="chev" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </summary>
            <div class="acc-body">
                <p>Vérifiez votre e-mail et votre mot de passe. Si besoin, contactez-nous via <a href="{{ route('vitrine.contact') }}">Contact</a>.</p>
            </div>
        </details>
    </div>
@endsection
