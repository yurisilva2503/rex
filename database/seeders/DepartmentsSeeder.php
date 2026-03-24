<?php
// database/seeders/DepartmentsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Department;
use App\Models\User;

class DepartmentsSeeder extends Seeder
{
    public function run(): void
    {
        // Get admin user for created_by
        $admin = User::where('email', 'administrador@gmail.com')->first();

        $departments = [
            [
                'id' => 1,
                'name' => 'Direção',
                'description' => 'Diretoria executiva e estratégica da empresa',
                'icon' => 'bi-briefcase-fill', // Ícone de pasta/executivo
                'active' => true,
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'name' => 'Comercial',
                'description' => 'Vendas, relacionamento com clientes e prospecção',
                'icon' => 'bi-graph-up-arrow', // Ícone de crescimento/vendas
                'active' => true,
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 3,
                'name' => 'Desenvolvimento',
                'description' => 'Engenharia, P&D e inovação',
                'icon' => 'bi-code-square', // Ícone de código/desenvolvimento
                'active' => true,
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 4,
                'name' => 'Produção',
                'description' => 'Manufatura, montagem e operações',
                'icon' => 'bi-gear-fill', // Ícone de engrenagem/produção
                'active' => true,
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 5,
                'name' => 'Logística',
                'description' => 'Transporte, armazenagem e distribuição',
                'icon' => 'bi-truck', // Ícone de caminhão/logística
                'active' => true,
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 6,
                'name' => 'Compras',
                'description' => 'Aquisições, suprimentos e negociações com fornecedores',
                'icon' => 'bi-cart-check-fill', // Ícone de carrinho/compras
                'active' => true,
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 7,
                'name' => 'Manutenção',
                'description' => 'Manutenção preventiva e corretiva de equipamentos',
                'icon' => 'bi-wrench', // Ícone de chave inglesa/manutenção
                'active' => true,
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 8,
                'name' => 'Qualidade',
                'description' => 'Controle de qualidade, ISO e certificações',
                'icon' => 'bi-check-circle-fill', // Ícone de check/qualidade
                'active' => true,
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 9,
                'name' => 'RH',
                'description' => 'Recursos humanos, treinamento e desenvolvimento',
                'icon' => 'bi-people-fill', // Ícone de pessoas/RH
                'active' => true,
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }

        $this->command->info('✅ ' . count($departments) . ' departments seeded successfully!');
    }
}
