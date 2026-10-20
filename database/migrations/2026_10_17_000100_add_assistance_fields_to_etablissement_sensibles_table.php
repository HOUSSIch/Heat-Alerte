<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('etablissement_sensibles', function (Blueprint $table) {
            $table->string('horaires')->nullable();
            $table->text('services')->nullable();
            $table->boolean('accueil_public')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('etablissement_sensibles', function (Blueprint $table) {
            $table->dropColumn(['horaires', 'services', 'accueil_public']);
        });
    }
};
