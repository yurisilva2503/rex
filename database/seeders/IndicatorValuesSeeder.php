<?php
// database/seeders/IndicatorValuesSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Indicator;
use App\Models\IndicatorValue;
use App\Models\User;

class IndicatorValuesSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'administrador@gmail.com')->first();
        $currentYear = 2024;

        // Get all indicators
        $indicators = Indicator::all();

        foreach ($indicators as $indicator) {
            // Generate values for all 12 months
            for ($month = 1; $month <= 12; $month++) {
                // Generate realistic values based on indicator type and goal
                $value = $this->generateRealisticValue($indicator, $month);

                // Create the value
                $indicatorValue = IndicatorValue::create([
                    'indicator_id' => $indicator->id,
                    'year' => $currentYear,
                    'month' => $month,
                    'value' => $value,
                    'notes' => $this->generateNotes($value, $indicator),
                    'created_by' => $admin->id,
                    'updated_by' => $admin->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Status will be auto-calculated by the model
            }
        }

        $totalValues = IndicatorValue::count();
        $this->command->info("✅ {$totalValues} indicator values seeded successfully!");
    }

    private function generateRealisticValue($indicator, $month): ?float
    {
        $goal = $indicator->goal;

        // Different patterns for different indicators
        $indicatorName = $indicator->name;

        // Pattern: improving throughout the year (produção, qualidade)
        if (in_array($indicatorName, ['Eficiência OEE', 'Produtividade', 'PPM', 'Aprovação em Auditoria'])) {
            // Start lower, improve over time
            $base = $goal * (0.85 + ($month * 0.015));
            $variation = rand(-5, 8) / 100;
            return round($base * (1 + $variation), 2);
        }

        // Pattern: seasonal (vendas, entregas)
        if (in_array($indicatorName, ['Faturamento Mensal', 'Entregas no Prazo'])) {
            // Peaks in March, June, September, December
            $seasonalFactor = 1.0;
            if (in_array($month, [3, 6, 9])) $seasonalFactor = 1.15;
            if ($month == 12) $seasonalFactor = 1.25;

            $base = $goal * $seasonalFactor;
            $variation = rand(-8, 12) / 100;
            return round($base * (1 + $variation), 2);
        }

        // Pattern: random around goal (most indicators)
        $variation = rand(-15, 20) / 100;
        return round($goal * (1 + $variation), 2);
    }

    private function generateNotes($value, $indicator): ?string
    {
        if ($value === null) return null;

        $notes = [];

        if ($value >= $indicator->goal * 1.1) {
            $notes[] = "✅ Desempenho excepcional este mês";
        } elseif ($value < $indicator->goal * 0.8) {
            $notes[] = "⚠️ Abaixo da meta, necessidade de análise";
        }

        // Add random context notes
        $contextNotes = [
            "Produção estável durante o mês",
            "Nova máquina contribuiu para resultado",
            "Feriado impactou produção",
            "Equipe focada no resultado",
            "Manutenção preventiva realizada",
            "Treinamento da equipe concluído",
            "Demanda acima do esperado",
            "Problemas com fornecedor",
            "Ajustes no processo produtivo",
            "Indicador dentro do esperado"
        ];

        if (rand(0, 3) === 0) { // 25% chance
            $notes[] = $contextNotes[array_rand($contextNotes)];
        }

        return empty($notes) ? null : implode('. ', $notes);
    }
}
