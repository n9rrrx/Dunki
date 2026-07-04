<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetupArchitecture extends Command
{
    protected $signature = 'setup:architecture';
    protected $description = 'Create enterprise architecture directories';

    public function handle()
    {
        $directories = [
            app_path('Enums'),
            app_path('Repositories'),
            app_path('Repositories/Contracts'),
            app_path('Services'),
            app_path('DTOs'),
        ];

        foreach ($directories as $directory) {
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
                $this->info("Created: {$directory}");
            } else {
                $this->comment("Exists: {$directory}");
            }
        }

        $this->info("\n✅ All directories created successfully!");
        return 0;
    }
}
