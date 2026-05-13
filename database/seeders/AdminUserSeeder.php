<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Compte back-office. Changez le mot de passe en production.
     * Email: admin@batitravoo.com
     * Mot de passe: Batitrav00!
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@batitravoo.com'],
            [
                'name' => 'Administrateur BATITRAVOO',
                'password' => Hash::make('Batitrav00!'),
                'role' => User::ROLE_ADMIN,
                'profile_type' => null,
                'phone' => null,
                'is_active' => true,
            ]
        );
    }
}
