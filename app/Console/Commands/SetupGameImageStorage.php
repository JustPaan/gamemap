<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetupGameImageStorage extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'gamemap:setup-storage';

    /**
     * The console command description.
     */
    protected $description = 'Setup game image storage directories and symlinks';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up GameMap image storage...');

        // Create storage directories
        $directories = [
            storage_path('app/public'),
            storage_path('app/public/game_images'),
            storage_path('app/public/avatars'),
            public_path('storage'),
            public_path('storage/game_images'),
            public_path('storage/avatars'),
        ];

        foreach ($directories as $dir) {
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
                $this->info("Created directory: {$dir}");
            } else {
                $this->line("Directory exists: {$dir}");
            }
        }

        // Create storage symlink
        $this->call('storage:link');

        // Set proper permissions
        chmod(storage_path('app/public'), 0755);
        chmod(storage_path('app/public/game_images'), 0755);
        chmod(public_path('storage'), 0755);
        chmod(public_path('storage/game_images'), 0755);

        $this->info('✅ Storage setup completed!');
        $this->info('Game images will be stored in: storage/app/public/game_images/');
        $this->info('Public access via: public/storage/game_images/');

        return 0;
    }
}
