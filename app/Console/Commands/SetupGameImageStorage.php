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
    protected $description = 'Setup game image storage directories for Digital Ocean';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up GameMap image storage for Digital Ocean...');

        // Create Laravel storage directories
        $directories = [
            storage_path('app'),
            storage_path('app/public'),
            storage_path('app/public/game_images'),
        ];

        foreach ($directories as $dir) {
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
                $this->info("Created directory: {$dir}");
            } else {
                $this->line("Directory exists: {$dir}");
            }
        }

        // Set proper permissions
        chmod(storage_path('app/public'), 0755);
        chmod(storage_path('app/public/game_images'), 0755);

        // Create a test image to verify storage works
        $testImageData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChAI9jU77yQAAAABJRU5ErkJggg==');
        $testImagePath = storage_path('app/public/game_images/storage-test.png');
        
        if (file_put_contents($testImagePath, $testImageData)) {
            $this->info("✅ Test image created successfully!");
        } else {
            $this->error("❌ Failed to create test image");
        }

        $this->info('✅ Storage setup completed!');
        $this->info('Game images will be stored in: storage/app/public/game_images/');
        $this->info('Images accessible via Laravel route: /storage/game_images/{filename}');

        return 0;
    }
}
