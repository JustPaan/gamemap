<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create';
    protected $description = 'Create an admin user';

    public function handle()
    {
        User::firstOrCreate(
            ['email' => 'admin@gamemap.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('AdminPassword123!'),
                'role' => 'admin',
                'is_approved' => true,
                'email_verified_at' => now()
            ]
        );
        $this->info('Admin user created:');
        $this->line('Email: admin@gamemap.com');
        $this->line('Password: AdminPassword123!');
    }
}