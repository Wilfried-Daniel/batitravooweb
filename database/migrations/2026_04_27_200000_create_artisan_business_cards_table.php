<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artisan_business_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('display_name')->nullable();
            $table->string('profession')->nullable();
            $table->string('experience_text')->nullable();
            $table->boolean('price_on_request')->default(true);
            $table->boolean('price_on_quote')->default(false);
            $table->string('price_text')->nullable();
            $table->json('services')->nullable();
            $table->boolean('avail_immediate')->default(true);
            $table->boolean('avail_appointment')->default(false);
            $table->boolean('avail_unavailable')->default(false);
            $table->string('location_text')->nullable();
            $table->string('portfolio_path')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artisan_business_cards');
    }
};
