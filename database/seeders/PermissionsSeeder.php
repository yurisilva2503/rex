<?php
// database/seeders/PermissionsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks
        Schema::disableForeignKeyConstraints();

        // Truncate tables
        DB::table('users_permissions')->truncate();
        DB::table('permissions')->truncate();

        // Permissions with descriptions
        $permissions = [
            // Users
            [
                'name' => 'view_users',
                'description' => 'Visualizar usuários',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'create_users',
                'description' => 'Criar usuários',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'edit_users',
                'description' => 'Editar usuários',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'delete_users',
                'description' => 'Excluir usuários',
                'created_at' => now(),
                'updated_at' => now()
            ],
            // Department
            [
                'name' => 'view_departments',
                'description' => 'Visualizar departamentos',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'create_departments',
                'description' => 'Criar departamentos',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'edit_departments',
                'description' => 'Editar departamentos',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'delete_departments',
                'description' => 'Excluir departamentos',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Indicator
            [
                'name' => 'view_indicators',
                'description' => 'Visualizar indicadores',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'create_indicators',
                'description' => 'Criar indicadores',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'edit_indicators',
                'description' => 'Editar indicadores',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'delete_indicators',
                'description' => 'Excluir indicadores',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Analyses
            [
                'name' => 'view_analyses',
                'description' => 'Visualizar análises',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'create_analyses',
                'description' => 'Criar análises',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'edit_analyses',
                'description' => 'Editar análises',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'delete_analyses',
                'description' => 'Excluir análises',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Action plans
            [
                'name' => 'view_action_plans',
                'description' => 'Visualizar planos de ação',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'create_action_plans',
                'description' => 'Criar planos de ação',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'edit_action_plans',
                'description' => 'Editar planos de ação',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'delete_action_plans',
                'description' => 'Excluir planos de ação',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        DB::table('permissions')->insert($permissions);

        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();

        $this->command->info('✅ Permissions seeded successfully!');
        $this->command->info('📝 Total permissions: ' . count($permissions));
    }
}
