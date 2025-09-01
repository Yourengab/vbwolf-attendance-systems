<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CompanyBranch;
use App\Models\Position;
use App\Models\Employee;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => 'password',
                'role' => 'admin',
            ],
        );

        $branch = CompanyBranch::firstOrCreate([
            'name' => 'Head Office',
        ], [
            'address' => 'Main Street 1',
        ]);

        $positionOffice = Position::firstOrCreate(['name' => 'Office']);
        $positionShopkeeper = Position::firstOrCreate(['name' => 'Shopkeeper']);

        $employeeUser = User::firstOrCreate(
            ['email' => 'employee@example.com'],
            [
                'name' => 'Employee',
                'password' => 'password',
                'role' => 'employee',
            ],
        );

        Employee::firstOrCreate([
            'user_id' => $employeeUser->id,
        ], [
            'branch_id' => $branch->id,
            'position_id' => $positionShopkeeper->id,
            'nip' => 'EMP-001',
            'name' => 'John Employee',
            'employment_status' => 'active',
        ]);
    }
}
