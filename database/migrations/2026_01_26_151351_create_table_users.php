<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('token', 100)->nullable()->default(null);
            $table->string('email', 100)->unique();
            $table->string('password', 200)->nullable()->default(null);
            $table->dateTime('email_verified_at')->nullable()->default(null);
            $table->dateTime('last_login')->nullable()->default(null);
            $table->boolean('active')->nullable()->default(null);
            $table->dateTime('blocked_until')->nullable()->default(null);
            $table->string('role', 20);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_admin')->default(false);
            $table->softDeletes();
            $table->rememberToken();
            $table->timestamps();
        });

        // Tabela pivot para relação de permissões
        Schema::create('users_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
