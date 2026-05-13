<?php

namespace App\Http\Controllers\Web\App;

use App\Http\Controllers\Controller;
use App\Models\Devis;
use App\Models\SupportTicket;
use App\Models\User;
use App\Services\Web\MeApiBridge;
use App\Services\Web\SupplierMarketplaceCart;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShellController extends Controller
{
    public function home(Request $request): View
    {
        return $this->render($request, 'home');
    }

    public function dashboard(Request $request): View
    {
        return $this->render($request, 'dashboard_tab');
    }

    public function messages(Request $request): View
    {
        return $this->render($request, 'messages');
    }

    public function profile(Request $request): View
    {
        return $this->render($request, 'profile');
    }

    public function profilePassword(Request $request): View
    {
        return $this->render($request, 'profile_password');
    }

    public function profileLocation(Request $request): View
    {
        $slug = (string) $request->segment(2);
        abort_unless(in_array($slug, ['batiment', 'fournisseur'], true), 404);

        return $this->render($request, 'profile_location');
    }

    public function serviceClient(Request $request): View
    {
        return $this->render($request, 'service_client');
    }

    public function supplierCart(Request $request): View
    {
        return $this->render($request, 'supplier_cart', [
            'cartLines' => app(SupplierMarketplaceCart::class)->lines($request),
        ]);
    }

    public function marketplace(Request $request): View
    {
        return $this->render($request, 'marketplace');
    }

    public function devis(Request $request): View
    {
        return $this->render($request, 'devis');
    }

    public function devisShow(Request $request, Devis $devis): View
    {
        return $this->render($request, 'devis_show', ['routeDevis' => $devis]);
    }

    public function devisCreate(Request $request): View
    {
        abort_if($request->user()?->profile_type === User::PROFILE_ARTISAN, 403);

        return $this->render($request, 'devis_create');
    }

    public function support(Request $request): View
    {
        return $this->render($request, 'support');
    }

    public function supportCreate(Request $request): View
    {
        return $this->render($request, 'support_create');
    }

    public function supportShow(Request $request, SupportTicket $ticket): View
    {
        abort_unless((int) $ticket->user_id === (int) $request->user()->id, 404);

        return $this->render($request, 'support_show', ['routeTicket' => $ticket]);
    }

    public function notificationsPage(Request $request): View
    {
        return $this->render($request, 'notifications');
    }

    public function besoinsManage(Request $request): View
    {
        return $this->render($request, 'besoins_manage');
    }

    public function besoinCreate(Request $request): View
    {
        return $this->render($request, 'besoin_create');
    }

    public function servicesManage(Request $request): View
    {
        return $this->render($request, 'services_manage');
    }

    public function productsManage(Request $request): View
    {
        return $this->render($request, 'products_manage');
    }

    public function documents(Request $request): View
    {
        return $this->render($request, 'documents');
    }

    public function helpFournisseur(Request $request): View
    {
        return $this->render($request, 'help_fournisseur');
    }

    public function helpBatiment(Request $request): View
    {
        return $this->render($request, 'help_batiment');
    }

    public function helpParticulier(Request $request): View
    {
        return $this->render($request, 'help_particulier');
    }

    public function helpArtisan(Request $request): View
    {
        return $this->render($request, 'help_artisan');
    }

    public function supplierPublicPreview(Request $request): View
    {
        return $this->render($request, 'vue_publique');
    }

    public function batimentPublicPreview(Request $request): View
    {
        return $this->render($request, 'vue_publique_batiment');
    }

    public function candidatures(Request $request): View
    {
        $slug = (string) $request->segment(2);
        $default = match ($slug) {
            'artisan', 'fournisseur' => 'envoyees',
            'particulier' => 'recues',
            'batiment' => 'recues',
            default => 'recues',
        };
        $vue = (string) $request->query('vue', $default);
        if (! in_array($vue, ['recues', 'envoyees'], true)) {
            $vue = $default;
        }
        if ($slug === 'particulier' && $vue === 'envoyees') {
            $vue = 'recues';
        }
        if ($slug === 'artisan' && $vue === 'recues') {
            $vue = 'envoyees';
        }
        if ($slug === 'fournisseur' && $vue === 'recues') {
            $vue = 'envoyees';
        }

        return $this->render($request, 'candidatures', ['candidatureVue' => $vue]);
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    protected function render(Request $request, string $page, array $extra = []): View
    {
        $slug = $request->segment(2);

        $titles = [
            'home' => 'Accueil',
            'dashboard_tab' => 'Tableau de bord',
            'messages' => 'Messages',
            'profile' => 'Profil',
            'profile_password' => 'Mot de passe',
            'profile_location' => 'Localisation',
            'service_client' => 'Service client',
            'supplier_cart' => 'Panier',
            'marketplace' => 'Petites annonces',
            'devis' => $slug === 'fournisseur' ? 'Mes commandes' : 'Mes devis',
            'devis_create' => $slug === 'fournisseur' ? 'Nouvelle proposition' : 'Nouveau devis',
            'devis_show' => $slug === 'fournisseur' ? 'Détail commande' : 'Détail devis',
            'support' => 'Support',
            'support_create' => 'Nouveau ticket',
            'support_show' => 'Ticket support',
            'notifications' => 'Notifications',
            'besoins_manage' => 'Mes besoins',
            'besoin_create' => 'Publier un besoin',
            'services_manage' => 'Mes services',
            'products_manage' => 'Mes produits',
            'product_form' => 'Produit',
            'documents' => 'Mes documents',
            'help_fournisseur' => 'Centre d’aide',
            'help_batiment' => 'Centre d’aide',
            'help_particulier' => 'Centre d’aide',
            'help_artisan' => 'Centre d’aide',
            'artisan_carte_visite' => 'Carte de visite',
            'vue_publique' => 'Vue publique',
            'vue_publique_batiment' => 'Vue publique',
            'besoin_form' => 'Besoin',
            'service_form' => 'Prestation',
            'candidatures' => 'Candidatures',
        ];

        /** @var MeApiBridge $bridge */
        $bridge = app(MeApiBridge::class);

        $routeDevis = $extra['routeDevis'] ?? null;
        $routeTicket = $extra['routeTicket'] ?? null;
        $routeBesoin = $extra['routeBesoin'] ?? null;
        $candidatureVue = $extra['candidatureVue'] ?? null;

        $candidatureBesoinFilter = 0;
        if ($page === 'candidatures') {
            $candidatureBesoinFilter = (int) $request->query('besoin_id', 0);
        }

        $viewData = [
            'profileSlug' => $slug,
            'page' => $page,
            'title' => $titles[$page] ?? ucfirst(str_replace('_', ' ', $page)),
            'intro' => $this->introFor($slug, $page),
            'unreadNotifications' => $bridge->unreadNotificationsCount($request),
            'apiError' => null,
            'dashboard' => null,
            'profileData' => null,
            'conversations' => [],
            'thread' => null,
            'peerId' => (int) $request->query('peer_id', 0),
            'notificationsPreview' => null,
            'notificationsFull' => null,
            'marketplaceData' => null,
            'devisList' => null,
            'devisDetail' => null,
            'supportList' => null,
            'ticketDetail' => null,
            'routeDevis' => $routeDevis,
            'routeTicket' => $routeTicket,
            'routeBesoin' => $routeBesoin,
            'candidatureVue' => $candidatureVue,
            'candidatureBesoinFilter' => $candidatureBesoinFilter,
            'besoinsList' => null,
            'servicesList' => null,
            'productsList' => null,
            'candidaturesList' => null,
            'supportFormOptions' => null,
            'metricsPeriodRoute' => null,
            'batimentAnalytics' => null,
            'supplierProducts' => null,
            'documentsList' => null,
            'helpFaqsList' => null,
            'categories' => null,
            'productFormMode' => null,
            'formProduct' => null,
            'serviceFormMode' => null,
            'formService' => null,
            'besoinFormMode' => null,
            'previewBesoins' => null,
            'previewServices' => null,
            'devisCanManage' => false,
            'devisOwnerUserId' => 0,
        ];

        try {
            if ($page === 'support_create') {
                $viewData['supportFormOptions'] = $bridge->supportTicketFormOptions($request);
            }
            if ($page === 'dashboard_tab') {
                $viewData['dashboard'] = $bridge->dashboard($request, $slug);
                $viewData['metricsPeriodRoute'] = route('app.'.$slug.'.dashboard');
                if ($slug === 'batiment') {
                    $viewData['batimentAnalytics'] = $bridge->batimentDashboardAnalytics($request);
                }
            }
            if ($page === 'home') {
                $viewData['notificationsPreview'] = $bridge->notifications($request, 8);
                if ($slug === 'batiment') {
                    $viewData['dashboard'] = $bridge->dashboard($request, $slug);
                    $viewData['metricsPeriodRoute'] = route('app.'.$slug.'.dashboard');
                    $viewData['profileData'] = $bridge->profile($request);
                }
                if ($slug === 'fournisseur') {
                    $viewData['supplierProducts'] = $bridge->myProducts($request);
                    $viewData['profileData'] = $bridge->profile($request);
                }
                if ($slug === 'particulier' || $slug === 'artisan') {
                    $viewData['profileData'] = $bridge->profile($request);
                }
            }
            if ($page === 'notifications') {
                $perPage = min(50, max(5, (int) $request->query('per_page', 30)));
                $viewData['notificationsFull'] = $bridge->notifications($request, $perPage);
            }
            if (in_array($page, ['profile', 'profile_password', 'profile_location'], true)) {
                $viewData['profileData'] = $bridge->profile($request);
            }
            if ($page === 'service_client' || $page === 'support') {
                $viewData['supportList'] = $bridge->supportTickets($request);
            }
            if ($page === 'devis_create') {
                $viewData['profileData'] = $bridge->profile($request);
                $viewData['devisOwnerUserId'] = (int) $request->query('owner_user_id', 0);
            }
            if ($page === 'messages') {
                $viewData['conversations'] = $bridge->conversations($request);
                $viewData['thread'] = $bridge->messagesThread($request, $viewData['peerId']);
            }
            if ($page === 'marketplace') {
                $viewData['marketplaceData'] = $bridge->marketplace($request, $slug);
            }
            if ($page === 'devis') {
                $viewData['devisList'] = $bridge->devisIndex($request);
            }
            if ($page === 'devis_show' && $routeDevis instanceof Devis) {
                $viewData['devisDetail'] = $bridge->devisShow($request, $routeDevis);
            }
            if ($page === 'support_show' && $routeTicket instanceof SupportTicket) {
                $viewData['ticketDetail'] = $bridge->supportTicket($request, $routeTicket);
            }
            if ($page === 'besoins_manage') {
                $viewData['besoinsList'] = $bridge->myBesoins($request);
            }
            if ($page === 'services_manage') {
                $viewData['servicesList'] = $bridge->myServices($request);
            }
            if ($page === 'products_manage') {
                $viewData['productsList'] = $bridge->myProducts($request);
            }
            if ($page === 'documents') {
                $viewData['documentsList'] = $bridge->userDocuments($request);
            }
            if (in_array($page, ['help_fournisseur', 'help_batiment', 'help_particulier', 'help_artisan'], true)) {
                $viewData['helpFaqsList'] = $bridge->helpFaqs();
            }
            if ($page === 'vue_publique') {
                $viewData['profileData'] = $bridge->profile($request);
                $viewData['productsList'] = $bridge->myProducts($request);
            }
            if ($page === 'vue_publique_batiment') {
                $viewData['profileData'] = $bridge->profile($request);
                $viewData['previewBesoins'] = $bridge->myBesoins($request);
                $viewData['previewServices'] = $bridge->myServices($request);
            }
            if ($page === 'candidatures' && $candidatureVue !== null) {
                $viewData['candidaturesList'] = $candidatureVue === 'recues'
                    ? $bridge->candidaturesReceived($request)
                    : $bridge->candidaturesAsApplicant($request);
            }
        } catch (\Throwable $e) {
            $viewData['apiError'] = config('app.debug')
                ? $e->getMessage()
                : 'Impossible de charger les données. Réessayez plus tard.';
        }

        if ($page === 'devis_show' && $routeDevis instanceof Devis) {
            $viewData['devisCanManage'] = $this->userCanManageDevis($request, $routeDevis);
        }

        $mergeExtra = $extra;
        unset(
            $mergeExtra['routeDevis'],
            $mergeExtra['routeTicket'],
            $mergeExtra['routeBesoin'],
            $mergeExtra['candidatureVue'],
        );
        $viewData = array_merge($viewData, $mergeExtra);

        return view('app.shell.page', $viewData);
    }

    protected function userCanManageDevis(Request $request, Devis $devis): bool
    {
        $u = $request->user();
        if (! $u instanceof User) {
            return false;
        }

        return (int) $devis->user_id === (int) $u->id
            && in_array($u->profile_type, [
                User::PROFILE_ENTREPRENEUR_BATIMENT,
                User::PROFILE_ENTREPRISE_FOURNISSEUR,
                User::PROFILE_ARTISAN,
            ], true);
    }

    protected function introFor(string $slug, string $page): string
    {
        return '';
    }
}
