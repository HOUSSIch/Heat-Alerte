<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('conseil_equipement', function (Blueprint $table) { $table->id(); $table->foreignId('equipement_id')->constrained()->cascadeOnDelete(); $table->foreignId('conseil_id')->constrained()->cascadeOnDelete(); $table->timestamps(); $table->unique(['equipement_id', 'conseil_id']); }); } public function down(): void { Schema::dropIfExists('conseil_equipement'); } };
