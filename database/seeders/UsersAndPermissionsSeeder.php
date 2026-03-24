<?php
// database/seeders/UsersAndPermissionsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Department;

class UsersAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        // Truncate tables
        DB::table('users')->truncate();
        DB::table('users_permissions')->truncate();

        // Get departments
        $direcao = 1;
        $comercial = 2;
        $dev = 3;
        $producao = 4;
        $logistica = 5;
        $compras = 6;
        $manutencao = 7;
        $qualidade = 8;
        $rh = 9;

        // Create admin user (no department)
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'administrador@gmail.com',
            'password' => Hash::make('123456789'),
            'role' => 'Admin',
            'active' => true,
            'department_id' => null,
            'email_verified_at' => now(),
            'last_login' => now(),
            'token' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'is_admin' => true
        ]);

        // Create department users
        $departmentUsers = [
            [
                'name' => 'Direção',
                'email' => 'direcao@rex.com',
                'password' => '123456789',
                'role' => 'Gerente',
                'department' => $direcao
            ],
            [
                'name' => 'Comercial',
                'email' => 'comercial@rex.com',
                'password' => '123456789',
                'role' => 'Funcionário',
                'department' => $comercial
            ],
            [
                'name' => 'Desenvolvimento',
                'email' => 'dev@rex.com',
                'password' => '123456789',
                'role' => 'Funcionário',
                'department' => $dev
            ],
            [
                'name' => 'Produção',
                'email' => 'producao@rex.com',
                'password' => '123456789',
                'role' => 'Funcionário',
                'department' => $producao
            ],
            [
                'name' => 'Logística',
                'email' => 'logistica@rex.com',
                'password' => '123456789',
                'role' => 'Funcionário',
                'department' => $logistica
            ],
            [
                'name' => 'Compras',
                'email' => 'compras@rex.com',
                'password' => '123456789',
                'role' => 'Funcionário',
                'department' => $compras
            ],
            [
                'name' => 'Manutenção',
                'email' => 'manutencao@rex.com',
                'password' => '123456789',
                'role' => 'Funcionário',
                'department' => $manutencao
            ],
            [
                'name' => 'Qualidade',
                'email' => 'qualidade@rex.com',
                'password' => '123456789',
                'role' => 'Funcionário',
                'department' => $qualidade
            ],
            [
                'name' => 'RH',
                'email' => 'rh@rex.com',
                'password' => '123456789',
                'role' => 'Funcionário',
                'department' => $rh
            ],
        ];

        $createdUsers = [$admin];

        foreach ($departmentUsers as $deptUser) {
            $user = User::create([
                'name' => $deptUser['name'],
                'email' => $deptUser['email'],
                'password' => Hash::make($deptUser['password']),
                'role' => $deptUser['role'],
                'active' => true,
                'department_id' => $deptUser['department'],
                'email_verified_at' => now(),
                'last_login' => now(),
                'token' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => $admin->id,
                'updated_by' => $admin->id
            ]);

            $createdUsers[] = $user;
        }

        // Assign permissions to admin (all permissions)
        $allPermissions = DB::table('permissions')->pluck('id')->toArray();
        $adminPermissions = array_map(fn($permId) => [
            'user_id' => $admin->id,
            'permission_id' => $permId,
            'created_at' => now(),
            'updated_at' => now()
        ], $allPermissions);

        DB::table('users_permissions')->insert($adminPermissions);

        // Assign specific permissions to managers (Direção)
        $managerPermissions = DB::table('permissions')
            ->whereIn('name', [
                'view_departments',
                'create_departments',
                'edit_departments',
                'delete_departments',
                'view_indicators',
                'create_indicators',
                'edit_indicators',
                'delete_indicators',
                'view_users',
                'create_users',
                'edit_users',
                'delete_users',
                'view_action_plans',
                'create_action_plans',
                'edit_action_plans',
                'delete_action_plans',
                'view_analyses',
                'create_analyses',
                'edit_analyses',
                'delete_analyses',
                'view_document_revisions',
                'create_document_revisions',
                'edit_document_revisions',
                'delete_document_revisions',
            ])->pluck('id')->toArray();

        $direcao = User::where('email', 'direcao@rex.com')->first();
        if ($direcao) {
            $direcaoPermissions = array_map(fn($permId) => [
                'user_id' => $direcao->id,
                'permission_id' => $permId,
                'created_at' => now(),
                'updated_at' => now()
            ], $managerPermissions);

            DB::table('users_permissions')->insert($direcaoPermissions);
        }

        $viewerPermissions = DB::table('permissions')
            ->whereIn('name', [
                'view_departments',
                'view_indicators',
                'view_users',
                'view_action_plans',
                'view_analyses',
                'view_document_revisions',
            ])->pluck('id')->toArray();

        foreach (['comercial@rex.com', 'dev@rex.com', 'producao@rex.com', 'logistica@rex.com', 'compras@rex.com', 'manutencao@rex.com', 'qualidade@rex.com', 'rh@rex.com'] as $email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $userPerms = array_map(fn($permId) => [
                    'user_id' => $user->id,
                    'permission_id' => $permId,
                    'created_at' => now(),
                    'updated_at' => now()
                ], $viewerPermissions);

                DB::table('users_permissions')->insert($userPerms);
            }
        }


        Schema::enableForeignKeyConstraints();

        $this->command->info('✅ Users and permissions seeded successfully!');
        $this->command->info('👑 Admin: administrador@gmail.com / 123456789');
        $this->command->info('👥 ' . count($departmentUsers) . ' department users created with appropriate permissions');

        // Show summary
        $this->command->table(['Department', 'User', 'Role'], array_map(function ($user) {
            return [
                $user->department?->name ?? 'N/A',
                $user->email,
                $user->role_label
            ];
        }, $createdUsers));
    }
}
