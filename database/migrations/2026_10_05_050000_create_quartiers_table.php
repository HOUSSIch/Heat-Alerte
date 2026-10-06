<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('quartiers', function(Blueprint $table){$table->id();$table->string('nom');$table->string('ville');$table->string('code_postal',20)->nullable();$table->decimal('latitude',10,7);$table->decimal('longitude',10,7);$table->boolean('actif')->default(true);$table->timestamps();}); } public function down(): void {Schema::dropIfExists('quartiers');} };
