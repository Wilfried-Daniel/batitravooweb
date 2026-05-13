<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('besoins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('budget')->nullable();
            $table->string('start_label')->nullable();
            $table->string('place')->nullable();
            $table->text('description')->nullable();
            $table->string('duration')->nullable();
            $table->string('short_date')->nullable();
            $table->unsignedInteger('candidature_count')->default(0);
            /** open | in_progress | closed | cancelled */
            $table->string('status', 32)->default('open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('besoins');
    }
};
