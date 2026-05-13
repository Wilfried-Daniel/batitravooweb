<?php

namespace Database\Seeders;

use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Models\User;
use Illuminate\Database\Seeder;

class SupportTicketSeeder extends Seeder
{
    /** Tickets de démo pour l’admin et tests API (idempotent par sujet + user). */
    public function run(): void
    {
        $particulier = User::query()->where('email', 'particulier@demo.batitravoo')->first();
        $admin = User::query()->where('email', 'admin@batitravoo.com')->first();

        if ($particulier === null || $admin === null) {
            return;
        }

        $ticket = SupportTicket::query()->firstOrCreate(
            [
                'user_id' => $particulier->id,
                'subject' => 'Problème de connexion — démo seed',
            ],
            [
                'status' => SupportTicket::STATUS_IN_PROGRESS,
                'priority' => SupportTicket::PRIORITY_NORMAL,
                'assigned_to_user_id' => $admin->id,
                'closed_at' => null,
            ]
        );

        if ($ticket->messages()->count() === 0) {
            SupportTicketMessage::query()->create([
                'support_ticket_id' => $ticket->id,
                'user_id' => $particulier->id,
                'body' => 'Bonjour, je n’arrive pas à finaliser mon inscription (message seed).',
                'is_staff' => false,
                'attachment_path' => null,
            ]);
            SupportTicketMessage::query()->create([
                'support_ticket_id' => $ticket->id,
                'user_id' => $admin->id,
                'body' => 'Bonjour, nous avons bien reçu votre demande. Pouvez-vous confirmer la version de l’application ? (réponse seed admin)',
                'is_staff' => true,
                'attachment_path' => null,
            ]);
        }
    }
}
