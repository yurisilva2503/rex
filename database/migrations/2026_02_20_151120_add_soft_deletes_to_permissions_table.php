<?php
// database/migrations/2024_02_20_000002_add_soft_deletes_to_permissions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->softDeletes(); // Adiciona a coluna deleted_at
        });
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
