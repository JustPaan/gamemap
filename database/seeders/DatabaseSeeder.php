<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        try {
            // Create admin user
            User::firstOrCreate(
                ['email' => 'admin@gamemap.com'],
                [
                    'name' => 'Admin',
                    'password' => Hash::make('AdminPassword123!'),
                    'nickname' => 'SuperAdmin',
                    'role' => 'admin',
                    'is_approved' => true,
                    'is_banned' => false,
                    'email_verified_at' => now(),
                ]
            );

            // Create organizer user
            User::firstOrCreate(
                ['email' => 'organizer@gamemap.com'],
                [
                    'name' => 'Organizer',
                    'password' => Hash::make('Organizer123!'),
                    'nickname' => 'mainOrganizer',
                    'role' => 'organizer',
                    'is_approved' => true,
                    'is_banned' => false,
                    'email_verified_at' => now(),
                ]
            );

            // Create test users manually
            if (app()->environment('local')) {
                for ($i = 1; $i <= 10; $i++) {
                    User::create([
                        'name' => "Test User {$i}",
                        'email' => "test{$i}@example.com",
                        'password' => Hash::make('password'),
                        'nickname' => "Tester{$i}",
                        'role' => 'gamer',
                        'is_approved' => true,
                        'is_banned' => false,
                        'email_verified_at' => now(),
                    ]);
                }

                User::create([
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                    'password' => Hash::make('password'),
                    'nickname' => 'Tester',
                    'role' => 'gamer',
                    'is_approved' => true,
                    'is_banned' => false,
                    'email_verified_at' => now(),
                ]);
            }

        } catch (\Exception $e) {
            $this->command->error('Database seeding failed: ' . $e->getMessage());
            Log::error('Database seeding error: ' . $e->getMessage());
        }

        $this->command->info('Database seeding completed successfully!');
    }
}