<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $this->command->info('🚀 Starting database seeding...');
        $this->command->newLine();

        // Seed in correct order
        $this->call(PermissionsSeeder::class);
        $this->call(UsersAndPermissionsSeeder::class);
        $this->call(DepartmentsSeeder::class);
        $this->call(IndicatorsSeeder::class);
        $this->call(IndicatorValuesSeeder::class);
        $this->call(AnalysesAndActionsSeeder::class);

        Schema::enableForeignKeyConstraints();

        $this->command->newLine();
        $this->command->info('✅ All seeders completed successfully!');
        $this->command->newLine();

        // Summary
        $this->command->table(['Component', 'Count'], [
            ['Users', \App\Models\User::count()],
            ['Permissions', \App\Models\Permission::count()],
            ['Departments', \App\Models\Department::count()],
            ['Indicators', \App\Models\Indicator::count()],
            ['Indicator Values', \App\Models\IndicatorValue::count()],
            ['Analyses', \App\Models\Analysis::count()],
            ['Action Plans', \App\Models\ActionPlan::count()],
        ]);
    }
}
