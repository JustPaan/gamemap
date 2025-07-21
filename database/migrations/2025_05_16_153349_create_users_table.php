<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Changed to standard primary key
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('nickname')->nullable();
            $table->string('phone')->nullable();
            $table->date('birthday')->nullable();
            $table->text('bio')->nullable();
            $table->string('location')->nullable();
            $table->json('games')->nullable();
            $table->json('platforms')->nullable();
            $table->string('avatar')->nullable();
            $table->string('background')->nullable();
            $table->enum('role', ['admin', 'organizer', 'gamer'])->default('gamer');
            $table->boolean('notify')->default(true);
            
            // Organizer approval related columns
            $table->boolean('is_approved')->nullable()->default(null);
            $table->boolean('is_banned')->default(false);
            $table->boolean('is_rejected')->nullable();
            $table->boolean('requested_organizer')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('demoted_at')->nullable();
            $table->timestamp('last_active_at')->nullable();
            
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        // If you need to update existing users (for new installations)
        if (Schema::hasTable('users') && app()->environment('local')) {
            DB::table('users')->update([
                'role' => 'gamer',
                'is_approved' => true,
                'notify' => true
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};