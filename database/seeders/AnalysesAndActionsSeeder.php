<?php
// database/seeders/AnalysesAndActionsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Indicator;
use App\Models\Analysis;
use App\Models\ActionPlan;
use App\Models\User;

class AnalysesAndActionsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'administrador@gmail.com')->first();
        $currentYear = 2024;

        // Get all indicators
        $indicators = Indicator::all();

        foreach ($indicators as $indicator) {
            // Create analyses for some months (not all)
            for ($month = 1; $month <= 12; $month++) {
                // 70% chance of having analysis for months 1-10, 90% for 11-12
                $probability = $month >= 10 ? 0.9 : 0.7;

                if (rand(0, 100) / 100 <= $probability) {
                    $analysis = $this->createAnalysis($indicator, $month, $currentYear, $admin);

                    // Create action plans for some analyses
                    if (rand(0, 100) / 100 <= 0.6) { // 60% chance
                        $numActions = rand(1, 4);
                        for ($i = 0; $i < $numActions; $i++) {
                            $this->createActionPlan($analysis, $i + 1, $admin);
                        }
                    }
                }
            }
        }

        $totalAnalyses = Analysis::count();
        $totalActions = ActionPlan::count();

        $this->command->info("✅ {$totalAnalyses} analyses and {$totalActions} action plans seeded successfully!");
    }

    private function createAnalysis($indicator, $month, $year, $admin): Analysis
    {
        // Get the indicator value for this month
        $value = $indicator->getValueForMonth($year, $month);
        $valueNum = $value ? (float) $value->value : null;

        // Determine trend based on previous months
        $prevValue = null;
        if ($month > 1) {
            $prev = $indicator->getValueForMonth($year, $month - 1);
            $prevValue = $prev ? (float) $prev->value : null;
        }

        $trend = $this->determineTrend($valueNum, $prevValue);

        // Generate analysis text
        $analysis = Analysis::create([
            'indicator_id' => $indicator->id,
            'year' => $year,
            'month' => $month,
            'analysis' => $this->generateAnalysisText($indicator, $month, $valueNum, $trend),
            'insights' => implode("\n", array_column($this->generateInsights($indicator, $valueNum), 'text')),
            'trend' => $trend,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return $analysis;
    }

    private function determineTrend($current, $previous): string
    {
        if ($current === null || $previous === null) {
            return Analysis::TREND_STABLE;
        }

        $diff = (($current - $previous) / $previous) * 100;

        if (abs($diff) < 3) {
            return Analysis::TREND_STABLE;
        }

        if ($diff > 10) {
            return Analysis::TREND_UP;
        }

        if ($diff < -10) {
            return Analysis::TREND_DOWN;
        }

        return rand(0, 1) ? Analysis::TREND_UP : Analysis::TREND_DOWN;
    }

    private function generateAnalysisText($indicator, $month, $value, $trend): string
    {
        $monthNames = [
            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março',
            4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
            7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro',
            10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
        ];

        $monthName = $monthNames[$month];

        if ($value === null) {
            return "**{$monthName}**: Sem dados registrados para este mês.";
        }

        $goal = $indicator->goal;
        $diff = (($value - $goal) / $goal) * 100;

        $parts = [];
        $parts[] = "**Análise de {$monthName}:**";

        if ($indicator->direction === Indicator::DIRECTION_HIGHER_BETTER) {
            if ($value >= $goal) {
                $parts[] = "✅ O indicador atingiu {$value}{$indicator->unit}, superando a meta de {$goal}{$indicator->unit} em " . number_format(abs($diff), 1) . "%.";
            } elseif ($value >= $goal * 0.8) {
                $parts[] = "⚠️ O indicador ficou em {$value}{$indicator->unit}, próximo à meta de {$goal}{$indicator->unit} (diferença de " . number_format(abs($diff), 1) . "%).";
            } else {
                $parts[] = "❌ O indicador ficou em {$value}{$indicator->unit}, abaixo da meta de {$goal}{$indicator->unit} (diferença de " . number_format(abs($diff), 1) . "%).";
            }
        } else {
            if ($value <= $goal) {
                $parts[] = "✅ O indicador atingiu {$value}{$indicator->unit}, dentro da meta de {$goal}{$indicator->unit}.";
            } elseif ($value <= $goal * 1.5) {
                $parts[] = "⚠️ O indicador ficou em {$value}{$indicator->unit}, próximo à meta de {$goal}{$indicator->unit}.";
            } else {
                $parts[] = "❌ O indicador ficou em {$value}{$indicator->unit}, acima da meta de {$goal}{$indicator->unit}.";
            }
        }

        // Add trend analysis
        $trendText = [
            Analysis::TREND_UP => "📈 Tendência de alta em relação ao mês anterior.",
            Analysis::TREND_DOWN => "📉 Tendência de queda em relação ao mês anterior.",
            Analysis::TREND_STABLE => "➡️ Estável em relação ao mês anterior.",
            Analysis::TREND_VOLATILE => "📊 Comportamento volátil no período."
        ];

        $parts[] = $trendText[$trend] ?? '';

        // Add context-specific analysis
        $contexts = [
            "Fatores que influenciaram: " . $this->getRandomFactors($indicator),
            "Recomendações: " . $this->getRandomRecommendations($indicator),
        ];

        if (rand(0, 1)) {
            $parts[] = $contexts[0];
        }

        if (rand(0, 1)) {
            $parts[] = $contexts[1];
        }

        return implode("\n\n", $parts);
    }

    private function generateInsights($indicator, $value): array
    {
        $insights = [];

        if ($value !== null) {
            $insights[] = [
                'type' => 'value',
                'text' => "Valor atual: {$value}{$indicator->unit}",
                'color' => '#3b82f6'
            ];

            if ($value >= $indicator->goal) {
                $insights[] = [
                    'type' => 'success',
                    'text' => 'Meta atingida! 🎯',
                    'color' => '#10b981'
                ];
            } else {
                $gap = $indicator->goal - $value;
                $insights[] = [
                    'type' => 'warning',
                    'text' => "Gap de {$gap}{$indicator->unit} para atingir a meta",
                    'color' => '#f59e0b'
                ];
            }
        }

        return $insights;
    }

    private function getRandomFactors($indicator): string
    {
        $factors = [
            'Produção' => [
                'Manutenção preventiva realizada no início do mês',
                'Problemas com matéria-prima na segunda quinzena',
                'Nova máquina em fase de adaptação',
                'Equipe completa durante todo o período',
                'Feriados impactaram a produção'
            ],
            'Qualidade' => [
                'Novo procedimento implementado com sucesso',
                'Treinamento da equipe de inspeção',
                'Auditoria interna realizada',
                'Fornecedor com problemas de qualidade',
                'Cliente reportou não conformidade'
            ],
            'default' => [
                'Mercado estável durante o período',
                'Alta demanda no início do mês',
                'Concorrência mais agressiva',
                'Condições climáticas adversas',
                'Equipe motivada e engajada'
            ]
        ];

        $list = $factors[$indicator->department->name] ?? $factors['default'];
        return $list[array_rand($list)];
    }

    private function getRandomRecommendations($indicator): string
    {
        $recs = [
            'Manter o foco nas ações que estão dando resultado',
            'Revisar processo para evitar desvios',
            'Intensificar treinamento da equipe',
            'Acompanhar indicador diariamente na próxima semana',
            'Realizar análise de causa raiz',
            'Comparar com benchmark do setor',
            'Documentar lições aprendidas',
            'Compartilhar boas práticas com outras áreas'
        ];

        return $recs[array_rand($recs)];
    }

    private function createActionPlan($analysis, $order, $admin): void
    {
        $templates = [
            [
                'action' => 'Realizar análise de causa raiz para identificar origem do desvio',
                'responsible' => 'Coordenador da área',
                'deadline' => now()->addDays(rand(15, 45)),
                'status' => $this->getRandomStatus()
            ],
            [
                'action' => 'Implementar plano de ação corretiva',
                'responsible' => 'Equipe técnica',
                'deadline' => now()->addDays(rand(30, 60)),
                'status' => $this->getRandomStatus()
            ],
            [
                'action' => 'Treinar equipe nos novos procedimentos',
                'responsible' => 'RH e Gestor',
                'deadline' => now()->addDays(rand(20, 40)),
                'status' => $this->getRandomStatus()
            ],
            [
                'action' => 'Revisar meta para próximo período',
                'responsible' => 'Diretoria',
                'deadline' => now()->addDays(rand(45, 90)),
                'status' => $this->getRandomStatus(['pending'])
            ],
            [
                'action' => 'Acompanhamento diário do indicador',
                'responsible' => 'Supervisor',
                'deadline' => now()->addDays(rand(7, 15)),
                'status' => $this->getRandomStatus(['in_progress', 'completed'])
            ],
            [
                'action' => 'Aquisição de novos equipamentos',
                'responsible' => 'Compras',
                'deadline' => now()->addDays(rand(60, 120)),
                'status' => $this->getRandomStatus(['pending', 'in_progress'])
            ],
        ];

        $template = $templates[array_rand($templates)];

        ActionPlan::create([
            'analysis_id' => $analysis->id,
            'action' => $template['action'],
            'responsible' => $template['responsible'],
            'deadline' => $template['deadline'],
            'status' => $template['status'],
            'comments' => rand(0, 1) ? 'Acompanhamento semanal realizado' : null,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    private function getRandomStatus($allowed = null): string
    {
        $statuses = $allowed ?? [
            ActionPlan::STATUS_PENDING,
            ActionPlan::STATUS_IN_PROGRESS,
            ActionPlan::STATUS_COMPLETED,
            ActionPlan::STATUS_DELAYED
        ];

        $weights = [
            ActionPlan::STATUS_PENDING => 30,
            ActionPlan::STATUS_IN_PROGRESS => 40,
            ActionPlan::STATUS_COMPLETED => 20,
            ActionPlan::STATUS_DELAYED => 10
        ];

        $total = 0;
        $weighted = [];

        foreach ($statuses as $status) {
            $weight = $weights[$status] ?? 25;
            $total += $weight;
            $weighted[$status] = $weight;
        }

        $rand = rand(1, $total);
        $cumulative = 0;

        foreach ($weighted as $status => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                return $status;
            }
        }

        return $statuses[0];
    }
}
