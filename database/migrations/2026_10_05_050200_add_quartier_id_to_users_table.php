<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::table('users', fn(Blueprint $table) => $table->foreignId('quartier_id')->nullable()->constrained('quartiers')->nullOnDelete()->after('role')); } public function down(): void {Schema::table('users', fn(Blueprint $table) => $table->dropConstrainedForeignId('quartier_id'));} };
