<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('avis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('point_fraicheur_id')->constrained('points_fraicheur')->cascadeOnDelete();
            $table->unsignedTinyInteger('note');
            $table->text('commentaire')->nullable();
            $table->string('statut')->default('en_attente');
            $table->timestamps();

            $table->unique(['user_id', 'point_fraicheur_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avis');
    }
};
