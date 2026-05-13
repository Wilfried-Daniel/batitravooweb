<?php

namespace Database\Seeders;

use App\Models\ArtisanBusinessCard;
use App\Models\Devis;
use App\Models\Message;
use App\Models\User;
use App\Models\UserDocument;
use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * Données pour tester toutes les vues admin (validation profils, messagerie, détails, etc.).
 * À exécuter après CatalogSeeder + DemoContentSeeder (comptes démo).
 * Idempotent : updateOrCreate / firstOrCreate par e-mail ou références stables.
 */
class AdminUiTestSeeder extends Seeder
{
    public function run(): void
    {
        $this->secondAdmin();
        $this->enrichDemoUsersAndValidation();
        $this->seedDocumentsAndBusinessCards();
        $this->seedMessages();
        $this->linkDevisToClient();
    }

    private function secondAdmin(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'supervisor@batitravoo.demo'],
            [
                'name' => 'Superviseur QA',
                'password' => Hash::make('Batitrav00!'),
                'role' => User::ROLE_ADMIN,
                'profile_type' => null,
                'phone' => null,
                'is_active' => true,
                'profile_validation_status' => User::VALIDATION_APPROVED,
            ]
        );
    }

    private function enrichDemoUsersAndValidation(): void
    {
        $t = now()->subDays(14);

        $rows = [
            'artisan.plomberie@demo.batitravoo' => [
                'profile_validation_status' => User::VALIDATION_PENDING,
                'profile_validation_note' => null,
                'profile_validated_at' => null,
                'profile_completed_at' => $t->copy()->subDays(3),
                'bio' => 'Plombier chauffagiste — démo compte en attente de validation admin.',
                'city' => 'Abidjan',
                'country' => 'CI',
                'commune' => 'Cocody',
                'contact_email' => 'contact@yao-plomberie.demo',
                'artisan_availability' => 'immediate',
            ],
            'fournisseur@demo.batitravoo' => [
                'profile_validation_status' => User::VALIDATION_APPROVED,
                'profile_validation_note' => null,
                'profile_validated_at' => $t->copy()->subDays(5),
                'profile_completed_at' => $t->copy()->subDays(6),
                'company_description' => 'Fournisseur de matériaux — compte validé (démo).',
                'city' => 'Abidjan',
                'country' => 'CI',
                'commune' => 'Yopougon',
                'contact_email' => 'ventes@materiauxpro.demo',
            ],
            'artisan.electricite@demo.batitravoo' => [
                'profile_validation_status' => User::VALIDATION_CHANGES_REQUESTED,
                'profile_validation_note' => 'Merci de renouveler votre attestation électrique (document illisible).',
                'profile_validated_at' => null,
                'profile_completed_at' => $t->copy()->subDays(7),
                'bio' => 'Électricien — démo demande de modifications.',
                'city' => 'Grand-Bassam',
                'country' => 'CI',
                'commune' => 'N’zima',
                'contact_email' => 'contact@elec-fils.demo',
                'artisan_availability' => 'appointment',
            ],
            'particulier@demo.batitravoo' => [
                'profile_validation_status' => User::VALIDATION_REJECTED,
                'profile_validation_note' => 'Pièces d’identité non conformes — démo rejet.',
                'profile_validated_at' => $t->copy()->subDays(2),
                'profile_completed_at' => $t->copy()->subDays(4),
                'bio' => 'Particulier — profil rejeté pour tests admin.',
                'city' => 'Abidjan',
                'country' => 'CI',
                'commune' => 'Marcory',
                'contact_email' => 'mariam.diallo@demo.mail',
            ],
            'entrepreneur@demo.batitravoo' => [
                'profile_validation_status' => User::VALIDATION_APPROVED,
                'profile_validation_note' => null,
                'profile_validated_at' => $t->copy()->subDays(10),
                'profile_completed_at' => $t->copy()->subDays(11),
                'company_description' => 'Entreprise BTP — profil validé (démo).',
                'city' => 'Abidjan',
                'country' => 'CI',
                'commune' => 'Plateau',
                'contact_email' => 'projets@kouassi-patrimoine.demo',
                'years_experience' => '12 ans',
                'activity_type' => 'Gros œuvre',
                'company_size' => '20–50',
                'manager_name' => 'Kouassi Jean',
                'manager_contact' => '+225 07 11 22 01',
            ],
        ];

        foreach ($rows as $email => $data) {
            $user = User::query()->where('email', $email)->first();
            if ($user === null) {
                continue;
            }
            $user->forceFill($data);
            $user->save();
        }
    }

    private function seedDocumentsAndBusinessCards(): void
    {
        $disk = Storage::disk('public');
        $pdfStub = "%PDF-1.4\n%Document démo BATITRAVOO (fictif)\n%%EOF";

        $artisanPlombier = User::query()->where('email', 'artisan.plomberie@demo.batitravoo')->first();
        if ($artisanPlombier) {
            $this->putDoc($disk, $artisanPlombier->id, UserDocument::KIND_CNI, 'cni-plombier.pdf', $pdfStub);
            $this->putDoc($disk, $artisanPlombier->id, UserDocument::KIND_CERTIFICATE, 'certificat-plombier.pdf', $pdfStub);

            ArtisanBusinessCard::query()->updateOrCreate(
                ['user_id' => $artisanPlombier->id],
                [
                    'display_name' => 'Yao Plomberie Pro',
                    'profession' => 'Plomberie & sanitaire',
                    'experience_text' => '15 ans d’expérience — démo admin.',
                    'price_on_request' => true,
                    'price_on_quote' => true,
                    'price_text' => 'Sur devis',
                    'services' => ['Installation', 'Dépannage', 'Rénovation salle de bains'],
                    'avail_immediate' => true,
                    'avail_appointment' => true,
                    'avail_unavailable' => false,
                    'location_text' => 'Abidjan et périphérie',
                    'portfolio_path' => $this->putPortfolio($disk, $artisanPlombier->id, $pdfStub),
                ]
            );
        }

        $particulier = User::query()->where('email', 'particulier@demo.batitravoo')->first();
        if ($particulier) {
            $this->putDoc($disk, $particulier->id, UserDocument::KIND_CNI, 'cni-particulier.pdf', $pdfStub);
            $this->putDoc($disk, $particulier->id, UserDocument::KIND_OTHER, 'justificatif.pdf', $pdfStub);
        }

        $fournisseur = User::query()->where('email', 'fournisseur@demo.batitravoo')->first();
        if ($fournisseur) {
            $this->putDoc($disk, $fournisseur->id, UserDocument::KIND_COMMERCE_REGISTER, 'rc-materiaux.pdf', $pdfStub);
        }
    }

    private function putPortfolio(Filesystem $disk, int $userId, string $content): string
    {
        $path = "user_documents/{$userId}/portfolio-demo.pdf";
        if (! $disk->exists($path)) {
            $disk->put($path, $content);
        }

        return $path;
    }

    private function putDoc(Filesystem $disk, int $userId, string $kind, string $filename, string $bytes): void
    {
        $path = "user_documents/{$userId}/{$kind}-seed.pdf";
        if (! $disk->exists($path)) {
            $disk->put($path, $bytes);
        }

        UserDocument::query()->updateOrCreate(
            [
                'user_id' => $userId,
                'kind' => $kind,
            ],
            [
                'storage_path' => $path,
                'original_filename' => $filename,
            ]
        );
    }

    private function seedMessages(): void
    {
        $entrepreneur = User::query()->where('email', 'entrepreneur@demo.batitravoo')->first();
        $fournisseur = User::query()->where('email', 'fournisseur@demo.batitravoo')->first();
        $artisan = User::query()->where('email', 'artisan.plomberie@demo.batitravoo')->first();
        $particulier = User::query()->where('email', 'particulier@demo.batitravoo')->first();

        $pairs = [
            [$entrepreneur, $fournisseur, 'Bonjour, pouvez-vous confirmer la dispo ciment 42,5 pour vendredi ?'],
            [$fournisseur, $entrepreneur, 'Bonjour, stock OK pour 120 tonnes — merci de valider le bon de commande.'],
            [$particulier, $artisan, 'Bonjour, j’ai besoin d’une réparation fuite sous évier (Marcory).'],
            [$artisan, $particulier, 'Bonjour Mme Diallo, je peux passer demain 14h — merci de confirmer l’adresse.'],
            [$entrepreneur, $artisan, 'Offre reçue pour le lot plomberie — merci d’envoyer le planning détaillé.'],
        ];

        foreach ($pairs as [$from, $to, $body]) {
            if ($from === null || $to === null) {
                continue;
            }
            $exists = Message::query()
                ->where('sender_id', $from->id)
                ->where('receiver_id', $to->id)
                ->where('body', $body)
                ->exists();
            if ($exists) {
                continue;
            }
            Message::query()->create([
                'sender_id' => $from->id,
                'receiver_id' => $to->id,
                'body' => $body,
                'read_at' => Carbon::now()->subHours(rand(1, 48)),
            ]);
        }
    }

    private function linkDevisToClient(): void
    {
        $particulier = User::query()->where('email', 'particulier@demo.batitravoo')->first();
        $entrepreneur = User::query()->where('email', 'entrepreneur@demo.batitravoo')->first();
        if ($particulier === null || $entrepreneur === null) {
            return;
        }

        Devis::query()
            ->where('user_id', $entrepreneur->id)
            ->where('order_reference', 'N°78HGG')
            ->update(['client_user_id' => $particulier->id]);
    }
}
