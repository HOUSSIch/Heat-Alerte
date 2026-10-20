<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entretiens_equipements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipement_id')->constrained()->cascadeOnDelete();
            $table->string('titre');
            $table->string('type');
            $table->text('description')->nullable();
            $table->date('date_prevue');
            $table->date('date_realisee')->nullable();
            $table->unsignedSmallInteger('rappel_avant_jours')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entretiens_equipements');
    }
};
