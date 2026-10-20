<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publications_quartier', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quartier_id')->constrained()->cascadeOnDelete();
            $table->string('titre');
            $table->text('contenu');
            $table->enum('type', ['probleme', 'information', 'entraide', 'alerte']);
            $table->enum('statut', ['signale', 'en_cours', 'resolu'])->default('signale');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->index(['quartier_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publications_quartier');
    }
};
