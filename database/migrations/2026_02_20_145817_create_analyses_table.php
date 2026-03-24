<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicator_id')->constrained()->onDelete('cascade');
            $table->integer('year');
            $table->integer('month'); // 1-12
            $table->text('analysis')->nullable(); // Análise textual do indicador
            $table->text('insights')->nullable(); // Insights automáticos (opcional)
            $table->enum('trend', ['up', 'down', 'stable', 'volatile'])->nullable(); // Tendência
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Unique constraint
            $table->unique(['indicator_id', 'year', 'month'], 'analysis_indicator_year_month_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analyses');
    }
};
