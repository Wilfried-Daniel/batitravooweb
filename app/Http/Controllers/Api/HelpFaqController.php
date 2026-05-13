<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * FAQ publique (app mobile).
 */
class HelpFaqController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => [
                [
                    'id' => 1,
                    'question' => 'Comment postuler à un besoin ?',
                    'answer' => 'Depuis l’accueil ou le marketplace, ouvrez une opportunité puis appuyez sur « Postuler ». Vous pouvez ajouter un message au client.',
                ],
                [
                    'id' => 2,
                    'question' => 'Comment contacter un client ?',
                    'answer' => 'Utilisez l’onglet Messages : les conversations listent vos échanges. Vous pouvez aussi ouvrir la messagerie depuis une fiche besoin ou opportunité.',
                ],
                [
                    'id' => 3,
                    'question' => 'Où voir mes candidatures ?',
                    'answer' => 'Menu Opportunités, puis « Suivi des candidatures », ou depuis le dashboard artisan selon les raccourcis disponibles.',
                ],
                [
                    'id' => 4,
                    'question' => 'Mes données sont-elles protégées ?',
                    'answer' => 'Votre compte est sécurisé par authentification. Ne partagez jamais vos identifiants. Pour toute question RGPD, contactez le support.',
                ],
                [
                    'id' => 5,
                    'question' => 'Comment utiliser la marketplace ?',
                    'answer' => 'Ouvrez Marketplace depuis l’accueil ou le menu, choisissez une catégorie (fournisseurs, artisans, entreprises BTP), parcourez les fiches puis « Voir » pour le détail. Vous pouvez contacter un prestataire ou demander un devis selon les actions proposées.',
                ],
                [
                    'id' => 6,
                    'question' => 'Comment demander plusieurs devis pour des services ?',
                    'answer' => 'Sur la fiche d’un prestataire, cochez une ou plusieurs prestations, puis appuyez sur « Demander un devis ». Décrivez votre besoin : une seule demande regroupe les services sélectionnés.',
                ],
                [
                    'id' => 7,
                    'question' => 'Comment modifier mon profil ou mon mot de passe ?',
                    'answer' => 'Allez dans Profil, puis « Modifier le profil » ou « Mot de passe ». Enregistrez vos changements avant de quitter l’écran.',
                ],
                [
                    'id' => 8,
                    'question' => 'L’application ne charge pas ou affiche une erreur',
                    'answer' => 'Vérifiez votre connexion internet, fermez puis rouvrez l’application. Si le problème continue, ouvrez une demande depuis le support en décrivant l’heure et l’écran concerné.',
                ],
                [
                    'id' => 9,
                    'question' => 'Comment suivre mes échanges avec le support ?',
                    'answer' => 'Dans Profil, ouvrez « Support — mes demandes » : vous y voyez vos tickets, leur statut et les réponses. Vous pouvez ajouter un message sur une demande existante.',
                ],
                [
                    'id' => 10,
                    'question' => 'Que faire si je ne reçois pas les notifications ?',
                    'answer' => 'Vérifiez dans les réglages du téléphone que les notifications sont autorisées pour Batitravoo. Sur l’écran Notifications de l’app, assurez-vous que les alertes ne sont pas désactivées.',
                ],
            ],
        ]);
    }
}
