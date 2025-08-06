<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Event;
use App\Models\Game;
use App\Models\Organizer;
use App\Models\Participant;
use App\Models\Payment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ResetDatabaseData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:reset-data {--force : Force reset without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Safely reset all database data and uploaded files (keeps structure intact)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            $this->warn('⚠️  WARNING: This will permanently delete ALL data from your database!');
            $this->warn('This includes: Users, Events, Games, Organizers, Participants, Payments');
            $this->warn('This will also delete all uploaded files (avatars, game images, event images)');
            
            if (!$this->confirm('Are you absolutely sure you want to continue?')) {
                $this->info('Operation cancelled.');
                return;
            }
            
            if (!$this->confirm('This cannot be undone. Continue anyway?')) {
                $this->info('Operation cancelled.');
                return;
            }
        }

        $this->info('🗑️  Starting database reset...');

        try {
            // Disable foreign key checks to avoid constraint issues
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Clear database tables in proper order (children first)
            $this->clearTable('payments', Payment::class);
            $this->clearTable('participants', Participant::class);
            $this->clearTable('events', Event::class);
            $this->clearTable('organizers', Organizer::class);
            $this->clearTable('games', Game::class);
            $this->clearTable('users', User::class);
            
            // Clear other Laravel tables
            $this->clearTable('personal_access_tokens');
            $this->clearTable('password_reset_tokens');
            $this->clearTable('failed_jobs');
            $this->clearTable('sessions');
            $this->clearTable('notifications');

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            // Clear uploaded files
            $this->clearUploadedFiles();

            $this->info('✅ Database reset completed successfully!');
            $this->info('📝 You can now add fresh data to your application.');

        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1'); // Re-enable even if error occurs
            $this->error('❌ Error during reset: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function clearTable($tableName, $modelClass = null)
    {
        try {
            if ($modelClass) {
                $count = $modelClass::count();
                if ($count > 0) {
                    $modelClass::truncate();
                    $this->info("🗑️  Cleared {$count} records from {$tableName}");
                } else {
                    $this->info("✓ Table {$tableName} was already empty");
                }
            } else {
                DB::table($tableName)->truncate();
                $this->info("🗑️  Cleared table {$tableName}");
            }
        } catch (\Exception $e) {
            $this->warn("⚠️  Could not clear {$tableName}: " . $e->getMessage());
        }
    }

    private function clearUploadedFiles()
    {
        $this->info('🗑️  Clearing uploaded files...');

        // Clear avatars
        $avatarFiles = Storage::disk('public')->files('avatars');
        $avatarCount = 0;
        foreach ($avatarFiles as $file) {
            if (basename($file) !== '.gitkeep') {
                Storage::disk('public')->delete($file);
                $avatarCount++;
            }
        }
        $this->info("🗑️  Cleared {$avatarCount} avatar files");

        // Clear game images
        $gameFiles = Storage::disk('public')->files('game_images');
        $gameCount = 0;
        foreach ($gameFiles as $file) {
            if (basename($file) !== '.gitkeep') {
                Storage::disk('public')->delete($file);
                $gameCount++;
            }
        }
        $this->info("🗑️  Cleared {$gameCount} game image files");

        // Clear event images
        $eventFiles = Storage::disk('public')->files('event_images');
        $eventCount = 0;
        foreach ($eventFiles as $file) {
            if (basename($file) !== '.gitkeep') {
                Storage::disk('public')->delete($file);
                $eventCount++;
            }
        }
        $this->info("🗑️  Cleared {$eventCount} event image files");

        // Clear background images if they exist
        if (Storage::disk('public')->exists('backgrounds')) {
            $backgroundFiles = Storage::disk('public')->files('backgrounds');
            $backgroundCount = 0;
            foreach ($backgroundFiles as $file) {
                if (basename($file) !== '.gitkeep') {
                    Storage::disk('public')->delete($file);
                    $backgroundCount++;
                }
            }
            $this->info("🗑️  Cleared {$backgroundCount} background files");
        }
    }
}
