<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('client_name');
            $table->string('order_reference', 64)->nullable();
            $table->string('place')->nullable();
            $table->string('contact')->nullable();
            /** non_traite | en_cours | envoye | valide | rejete (BatimentDevisStatus) */
            $table->string('status', 32)->default('non_traite');
            $table->date('processed_at')->nullable();
            $table->json('line_items')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devis');
    }
};
