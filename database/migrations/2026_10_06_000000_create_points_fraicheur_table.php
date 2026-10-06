<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('points_fraicheur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quartier_id')->constrained()->cascadeOnDelete();
            $table->string('nom');
            $table->string('type', 100);
            $table->string('adresse');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->text('description')->nullable();
            $table->string('horaires')->nullable();
            $table->string('telephone', 50)->nullable();
            $table->boolean('climatise')->default(false);
            $table->boolean('eau_disponible')->default(false);
            $table->boolean('accessible_pmr')->default(false);
            $table->boolean('actif')->default(true);
            $table->string('source')->default('manuel');
            $table->string('geoapify_place_id')->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('points_fraicheur');
    }
};
