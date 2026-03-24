<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->string('name', 255);
            $table->enum('type', ['strategic', 'tactical', 'monitoring'])->default('monitoring');
            $table->decimal('goal', 15, 4);
            $table->string('unit', 20)->default('%'); // %, un, R$, kg, etc
            $table->text('description')->nullable(); // Como calcular/obter os dados
            $table->text('formula')->nullable(); // Fórmula de cálculo
            $table->enum('direction', ['higher_is_better', 'lower_is_better'])->default('higher_is_better');
            $table->boolean('active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['department_id', 'active']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indicators');
    }
};
