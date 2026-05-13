# Plan — Parité web / Flutter (BATITRAVOO)

Document de suivi des corrections et évolutions pour aligner l’app web Laravel sur le shell et les flux Flutter, **sans OTP** pour l’instant.

## Légende

- [ ] À faire
- [x] Fait (mettre à jour au fil des itérations)

---

## Phase A — Stabilisation UX & navigation

- [x] A1. Distinction accueil / tableau de bord (KPI + graphiques uniquement sur `/dashboard` sauf accueil BTP = KPI seuls).
- [x] A2. Graphiques fournisseur (donut + barres) sur le dashboard web, API alignée mobile.
- [x] A3. Routes dédiées : `service-client` (particulier), `profil/mot-de-passe`, `profil/localisation` (BTP + fournisseur), `panier` (fournisseur, placeholder).
- [x] A4. Panier fournisseur fonctionnel (session ou API) + tunnel commande aligné mobile.
- [x] A5. Icônes sidebar : différencier « Localisation » (globe) vs « Mes besoins » (pin) sur BTP ; cadenas pour « Mot de passe ».

## Phase B — Tableaux de bord & métriques

- [x] B1. Période Mois/Année : liens cohérents (BTP accueil → période sur `/dashboard`).
- [x] B2. Données dashboard null-safe dans `dashboard_metrics`.
- [x] B3. Affichage KPI monétaires (ex. `ca_fcfa`) : valeur + unité FCFA lisible.
- [x] B4. Harmoniser libellés / ordre des KPI avec les écrans Flutter par profil (revue texte).

## Phase C — Profil & compte

- [x] C1. Pages dédiées mot de passe et localisation (contenu + navigation).
- [x] C2. Redirection après enregistrement : rester sur la sous-page quand le formulaire est soumis depuis celle-ci (`redirect_to` contrôlé côté serveur).
- [x] C3. Optionnel : retirer le bloc mot de passe du profil principal pour n’avoir qu’un lien vers la page dédiée (éviter double formulaire).

## Phase D — Marketplace & fournisseur

- [x] D1. Page panier placeholder + liens marketplace / commandes.
- [x] D2. Éditeur de devis / commandes fournisseur : rapprocher champs et étapes de `fournisseur_quote_editor_screen` (mobile).
- [x] D3. Catalogue public dédié si distinct de la vue publique actuelle (analyse Flutter vs routes web).

## Phase E — Support, messages, notifications

- [x] E1. Service client particulier = liste tickets (route dédiée + même API que support).
- [x] E2. États vides notifications + messages (copie d’accueil liste vide / fil vide, alignée usage mobile).

## Phase F — Documents & offres métier par rôle

- [x] F1. Vérifier exposition `/documents` et contenus par profil vs mobile.
- [ ] F2. Parcours artisan (carte de visite, opportunités, candidatures) : cohérence URLs + libellés (revue manuelle restante).
- [ ] F3. Parcours BTP (besoins, services, candidatures, devis, vue publique) : même ordre logique que le menu mobile (revue manuelle restante).

## Phase G — Qualité

- [ ] G1. Tests manuels par profil (navigation, POST profil, période dashboard).
- [x] G2. `php artisan view:cache` après changements Blade significatifs.

---

## Journal (dernière mise à jour)

| Date       | Actions réalisées |
|------------|-------------------|
| (itération courante) | Plan créé ; **B3** KPI FCFA, **C2** redirections `redirect_to` (localisation / mot de passe), mise en page panier, icônes **lock** / **globe** dans le sprite + sidebar. |
| (suite) | **A4** Panier marketplace session (particulier / BTP / fournisseur) : ajout depuis fiche produit, regroupement par vendeur, commande → `POST /me/devis` (payload type Flutter), CA fournisseur avec `lignes`/`line_total_fcfa`. **E2** textes vides notifications + liste messages. |
| (suite 2) | **C3** Profil : lien vers page mot de passe uniquement. **D2** Formulaire nouveau devis : lignes dynamiques, remise/TVA, e-mail client → `line_items` type proposition ; détail devis avec récap TTC. **D3** Constat : catalogue public mobile ≈ marketplace produits + `/vue-publique` web. **B4** Titre « Vue d’ensemble » sur dashboard complet. **F1** texte vide documents. |
