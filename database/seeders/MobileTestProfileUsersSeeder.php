<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Comptes de test pour l’application mobile — un utilisateur par type de profil.
 *
 * Mot de passe commun (tous les comptes) : TestMobile1!
 *
 * | Profil API              | Email                          |
 * |-------------------------|--------------------------------|
 * | particulier             | mobile.particulier@batitravoo.test |
 * | artisan                 | mobile.artisan@batitravoo.test     |
 * | entreprise_fournisseur  | mobile.fournisseur@batitravoo.test |
 * | entrepreneur_batiment   | mobile.batiment@batitravoo.test    |
 *
 * Idempotent : [updateOrCreate] sur l’email.
 *
 * Données métier associées (besoins, services, devis, commandes fournisseur « MOB-FOUR-* », etc.) :
 * {@see MobileTestContentSeeder} (appelé après dans {@see DatabaseSeeder}).
 */
class MobileTestProfileUsersSeeder extends Seeder
{
    private const PASSWORD = 'TestMobile1!';

    public function run(): void
    {
        $now = now();
        $common = [
            'password' => Hash::make(self::PASSWORD),
            'role' => User::ROLE_USER,
            'is_active' => true,
            'country' => 'CI',
            'profile_validation_status' => User::VALIDATION_APPROVED,
            'profile_validation_note' => null,
            'profile_validated_at' => $now,
            'profile_completed_at' => $now,
        ];

        User::query()->updateOrCreate(
            ['email' => 'mobile.particulier@batitravoo.test'],
            array_merge($common, [
                'name' => 'Test Particulier',
                'profile_type' => User::PROFILE_PARTICULIER,
                'phone' => '+2250102030401',
                'contact_email' => 'mobile.particulier@batitravoo.test',
                'bio' => 'Compte seed — particulier (tests mobile).',
                'city' => 'Abidjan',
                'commune' => 'Cocody',
                'company_address' => 'Cocody, Abidjan',
                'company_name' => null,
            ])
        );

        User::query()->updateOrCreate(
            ['email' => 'mobile.artisan@batitravoo.test'],
            array_merge($common, [
                'name' => 'Test Artisan',
                'profile_type' => User::PROFILE_ARTISAN,
                'phone' => '+2250102030402',
                'contact_email' => 'mobile.artisan@batitravoo.test',
                'bio' => 'Compte seed — artisan plomberie (tests mobile).',
                'city' => 'Abidjan',
                'commune' => 'Marcory',
                'company_address' => 'Marcory, Abidjan',
                'company_name' => null,
                'artisan_availability' => 'immediate',
            ])
        );

        User::query()->updateOrCreate(
            ['email' => 'mobile.fournisseur@batitravoo.test'],
            array_merge($common, [
                'name' => 'Test Fournisseur SARL',
                'profile_type' => User::PROFILE_ENTREPRISE_FOURNISSEUR,
                'phone' => '+2250102030403',
                'contact_email' => 'contact@mobile-fournisseur.test',
                'company_name' => 'Test Fournisseur SARL',
                'company_description' => 'Compte seed — fournisseur de matériaux (tests mobile).',
                'city' => 'Abidjan',
                'commune' => 'Yopougon',
                'company_address' => 'Zone industrielle, Yopougon',
                'manager_name' => 'Koffi Manager',
                'manager_contact' => '+2250102030499',
            ])
        );

        User::query()->updateOrCreate(
            ['email' => 'mobile.batiment@batitravoo.test'],
            array_merge($common, [
                'name' => 'Test BTP & Construction',
                'profile_type' => User::PROFILE_ENTREPRENEUR_BATIMENT,
                'phone' => '+2250102030404',
                'contact_email' => 'contact@mobile-btp.test',
                'company_name' => 'Test BTP & Construction',
                'company_description' => 'Compte seed — entreprise BTP (tests mobile).',
                'years_experience' => '10 ans',
                'activity_type' => 'Gros œuvre / coordination',
                'company_size' => '20–50 salariés',
                'city' => 'Abidjan',
                'commune' => 'Plateau',
                'company_address' => 'Plateau, Abidjan',
                'manager_name' => 'Awa Directrice',
                'manager_contact' => '+2250102030498',
            ])
        );
    }
}
