<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Teams
        $devTeam = Team::create([
            'name' => 'Development Team',
            'description' => 'Software developers and engineers',
        ]);

        $hrTeam = Team::create([
            'name' => 'HR Team',
            'description' => 'Human Resources department',
        ]);

        // Create Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN,
            'team_id' => $devTeam->id,
        ]);

        // Create Regular Users
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::USER,
            'team_id' => $devTeam->id,
            'phone' => '0123456789',
            'notes' => 'Full-stack developer',
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::USER,
            'team_id' => $devTeam->id,
            'phone' => '0987654321',
            'notes' => 'Frontend developer',
        ]);

        User::create([
            'name' => 'Alice HR',
            'email' => 'alice@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::USER,
            'team_id' => $hrTeam->id,
            'phone' => '0555555555',
        ]);
    }
}
