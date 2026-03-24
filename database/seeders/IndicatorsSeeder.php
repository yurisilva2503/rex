<?php
// database/seeders/IndicatorsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Indicator;
use App\Models\User;

class IndicatorsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'administrador@gmail.com')->first();

        // Get all departments
        $direcao = Department::where('name', 'Direção')->first();
        $comercial = Department::where('name', 'Comercial')->first();
        $producao = Department::where('name', 'Produção')->first();
        $logistica = Department::where('name', 'Logística')->first();
        $qualidade = Department::where('name', 'Qualidade')->first();
        $rh = Department::where('name', 'RH')->first();
        $compras = Department::where('name', 'Compras')->first();
        $manutencao = Department::where('name', 'Manutenção')->first();

        // Indicators for Direção
        $direcaoIndicators = [
            [
                'name' => 'Faturamento Mensal',
                'type' => Indicator::TYPE_STRATEGIC,
                'goal' => 1500000.00,
                'unit' => Indicator::UNIT_REAL,
                'description' => 'Faturamento bruto do mês (todas as vendas)',
                'formula' => 'Soma de todas as notas fiscais emitidas no período',
                'direction' => Indicator::DIRECTION_HIGHER_BETTER,
            ],
            [
                'name' => 'EBITDA',
                'type' => Indicator::TYPE_STRATEGIC,
                'goal' => 350000.00,
                'unit' => Indicator::UNIT_REAL,
                'description' => 'Lucro antes de juros, impostos, depreciação e amortização',
                'formula' => 'Receita líquida - custos operacionais (excluindo juros e impostos)',
                'direction' => Indicator::DIRECTION_HIGHER_BETTER,
            ],
            [
                'name' => 'Rentabilidade',
                'type' => Indicator::TYPE_STRATEGIC,
                'goal' => 5.00,
                'unit' => Indicator::UNIT_PERCENT,
                'description' => 'Margem de lucro líquido',
                'formula' => '(Lucro líquido / Receita total) × 100',
                'direction' => Indicator::DIRECTION_HIGHER_BETTER,
            ],
        ];

        // Indicators for Produção
        $producaoIndicators = [
            [
                'name' => 'Eficiência OEE',
                'type' => Indicator::TYPE_STRATEGIC,
                'goal' => 85.00,
                'unit' => Indicator::UNIT_PERCENT,
                'description' => 'Overall Equipment Effectiveness',
                'formula' => 'Disponibilidade × Performance × Qualidade',
                'direction' => Indicator::DIRECTION_HIGHER_BETTER,
            ],
            [
                'name' => 'Produtividade',
                'type' => Indicator::TYPE_TACTICAL,
                'goal' => 120.00,
                'unit' => 'un/h',
                'description' => 'Unidades produzidas por hora trabalhada',
                'formula' => 'Total produzido / Horas totais trabalhadas',
                'direction' => Indicator::DIRECTION_HIGHER_BETTER,
            ],
            [
                'name' => 'Refugo',
                'type' => Indicator::TYPE_TACTICAL,
                'goal' => 2.00,
                'unit' => Indicator::UNIT_PERCENT,
                'description' => 'Percentual de peças refugadas',
                'formula' => '(Peças refugadas / Total produzido) × 100',
                'direction' => Indicator::DIRECTION_LOWER_BETTER,
            ],
            [
                'name' => 'Horas Paradas',
                'type' => Indicator::TYPE_MONITORING,
                'goal' => 10.00,
                'unit' => 'h',
                'description' => 'Horas de máquina parada não programadas',
                'formula' => 'Somatório de horas de parada no mês',
                'direction' => Indicator::DIRECTION_LOWER_BETTER,
            ],
        ];

        // Indicators for Qualidade
        $qualidadeIndicators = [
            [
                'name' => 'PPM',
                'type' => Indicator::TYPE_STRATEGIC,
                'goal' => 5000.00,
                'unit' => 'ppm',
                'description' => 'Partes por milhão de defeitos',
                'formula' => '(Número de defeitos / Total produzido) × 1.000.000',
                'direction' => Indicator::DIRECTION_LOWER_BETTER,
            ],
            [
                'name' => 'Reclamações de Clientes',
                'type' => Indicator::TYPE_TACTICAL,
                'goal' => 5.00,
                'unit' => 'un',
                'description' => 'Número de reclamações recebidas no mês',
                'formula' => 'Contagem de reclamações registradas no SAC',
                'direction' => Indicator::DIRECTION_LOWER_BETTER,
            ],
            [
                'name' => 'Aprovação em Auditoria',
                'type' => Indicator::TYPE_MONITORING,
                'goal' => 95.00,
                'unit' => Indicator::UNIT_PERCENT,
                'description' => 'Percentual de itens aprovados em auditorias internas',
                'formula' => '(Itens conformes / Total itens auditados) × 100',
                'direction' => Indicator::DIRECTION_HIGHER_BETTER,
            ],
        ];

        // Indicators for Logística
        $logisticaIndicators = [
            [
                'name' => 'Entregas no Prazo',
                'type' => Indicator::TYPE_STRATEGIC,
                'goal' => 98.00,
                'unit' => Indicator::UNIT_PERCENT,
                'description' => 'Percentual de entregas realizadas dentro do prazo',
                'formula' => '(Entregas no prazo / Total entregas) × 100',
                'direction' => Indicator::DIRECTION_HIGHER_BETTER,
            ],
            [
                'name' => 'Custo de Frete',
                'type' => Indicator::TYPE_TACTICAL,
                'goal' => 8.50,
                'unit' => Indicator::UNIT_PERCENT,
                'description' => 'Percentual do faturamento gasto com fretes',
                'formula' => '(Custo total frete / Faturamento) × 100',
                'direction' => Indicator::DIRECTION_LOWER_BETTER,
            ],
            [
                'name' => 'Acuracidade de Estoque',
                'type' => Indicator::TYPE_TACTICAL,
                'goal' => 99.50,
                'unit' => Indicator::UNIT_PERCENT,
                'description' => 'Precisão do inventário',
                'formula' => '(Itens corretos em estoque / Total itens) × 100',
                'direction' => Indicator::DIRECTION_HIGHER_BETTER,
            ],
        ];

        // Indicators for RH
        $rhIndicators = [
            [
                'name' => 'Absenteísmo',
                'type' => Indicator::TYPE_TACTICAL,
                'goal' => 3.00,
                'unit' => Indicator::UNIT_PERCENT,
                'description' => 'Percentual de ausências não programadas',
                'formula' => '(Horas ausentes / Horas totais trabalháveis) × 100',
                'direction' => Indicator::DIRECTION_LOWER_BETTER,
            ],
            [
                'name' => 'Turnover',
                'type' => Indicator::TYPE_STRATEGIC,
                'goal' => 1.50,
                'unit' => Indicator::UNIT_PERCENT,
                'description' => 'Rotatividade de funcionários',
                'formula' => '((Admissões + Demissões) / 2) / Total funcionários × 100',
                'direction' => Indicator::DIRECTION_LOWER_BETTER,
            ],
            [
                'name' => 'Horas de Treinamento',
                'type' => Indicator::TYPE_MONITORING,
                'goal' => 8.00,
                'unit' => 'h',
                'description' => 'Média de horas de treinamento por funcionário',
                'formula' => 'Total horas treinamento / Total funcionários',
                'direction' => Indicator::DIRECTION_HIGHER_BETTER,
            ],
        ];

        // Create indicators for each department
        foreach ($direcaoIndicators as $ind) {
            $ind['department_id'] = $direcao->id;
            $ind['created_by'] = $admin->id;
            $ind['updated_by'] = $admin->id;
            Indicator::create($ind);
        }

        foreach ($producaoIndicators as $ind) {
            $ind['department_id'] = $producao->id;
            $ind['created_by'] = $admin->id;
            $ind['updated_by'] = $admin->id;
            Indicator::create($ind);
        }

        foreach ($qualidadeIndicators as $ind) {
            $ind['department_id'] = $qualidade->id;
            $ind['created_by'] = $admin->id;
            $ind['updated_by'] = $admin->id;
            Indicator::create($ind);
        }

        foreach ($logisticaIndicators as $ind) {
            $ind['department_id'] = $logistica->id;
            $ind['created_by'] = $admin->id;
            $ind['updated_by'] = $admin->id;
            Indicator::create($ind);
        }

        foreach ($rhIndicators as $ind) {
            $ind['department_id'] = $rh->id;
            $ind['created_by'] = $admin->id;
            $ind['updated_by'] = $admin->id;
            Indicator::create($ind);
        }

        $this->command->info('✅ ' . (count($direcaoIndicators) + count($producaoIndicators) + count($qualidadeIndicators) + count($logisticaIndicators) + count($rhIndicators)) . ' indicators seeded successfully!');
    }
}
