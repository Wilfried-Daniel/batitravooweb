<footer class="site-footer section-reveal section-reveal--d6" role="contentinfo">
    <div class="site-footer__main">
        <div>
            <p class="site-footer__brand">Batitravoo</p>
            <ul class="site-footer__social">
                <li><a href="#" rel="noopener noreferrer">Facebook</a></li>
                <li><a href="#" rel="noopener noreferrer">LinkedIn</a></li>
                <li><a href="#" rel="noopener noreferrer">Instagram</a></li>
                <li><a href="#" rel="noopener noreferrer">WhatsApp</a></li>
            </ul>
        </div>
        <nav aria-label="Navigation pied de page">
            <p class="site-footer__col-title">Navigation</p>
            <ul class="site-footer__links">
                <li><a href="{{ url('/') }}#accueil">Accueil</a></li>
                <li><a href="{{ url('/') }}#solution">Solution</a></li>
                <li><a href="{{ url('/') }}#pour-qui">Pour qui</a></li>
                <li><a href="{{ url('/') }}#fonctionnalites">Fonctionnalités</a></li>
                <li><a href="{{ url('/') }}#temoignages">Témoignages</a></li>
            </ul>
        </nav>
        <nav aria-label="Utilisateurs">
            <p class="site-footer__col-title">Utilisateurs</p>
            <ul class="site-footer__links">
                <li><a href="#">Entrepreneur Bâtiment</a></li>
                <li><a href="#">Entreprise fournisseur</a></li>
                <li><a href="#">Artisan</a></li>
                <li><a href="#">Particulier</a></li>
            </ul>
        </nav>
        <nav aria-label="Support">
            <p class="site-footer__col-title">Support</p>
            <ul class="site-footer__links">
                <li><a href="{{ url('/admin/login') }}">Espace admin</a></li>
                <li><a href="{{ route('vitrine.contact') }}">Contact</a></li>
                <li><a href="{{ route('vitrine.help_center') }}">Centre d'aide</a></li>
                <li><a href="{{ route('vitrine.faq') }}">FAQ</a></li>
                <li><a href="{{ route('vitrine.terms') }}">Conditions d'utilisation</a></li>
                <li><a href="{{ route('vitrine.privacy') }}">Politique de confidentialité</a></li>
            </ul>
        </nav>
    </div>
    <div class="site-footer__bottom">
        <p class="site-footer__copyright">© 2026 BATITRAVOO – Tous droits réservés</p>
    </div>
</footer>
