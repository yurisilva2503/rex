<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('indicator_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicator_id')->constrained()->onDelete('cascade');
            $table->integer('year');
            $table->integer('month'); // 1-12
            $table->decimal('value', 15, 4)->nullable();
            $table->enum('status', ['on_target', 'near_target', 'below_target', 'no_data'])->nullable();
            $table->text('notes')->nullable(); // Observações sobre o valor
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Unique constraint: apenas um valor por indicador/mês/ano
            $table->unique(['indicator_id', 'year', 'month'], 'indicator_year_month_unique');

            // Índices para buscas rápidas
            $table->index(['year', 'month']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indicator_values');
    }
};
