<?php


namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\DeleteAgentProperties;
use App\Console\Commands\DeleteOldGeneralHousingProperties;

class Kernel extends ConsoleKernel
{
    // The Artisan commands provided by your application
    protected $commands = [
        DeleteAgentProperties::class,
        DeleteOldGeneralHousingProperties::class,
    ];
    

    // Define the application's command schedule
    protected function schedule(Schedule $schedule)
    {
        // Run the command every day at midnight (you can adjust this as needed)
        $schedule->command('properties:delete-agent-old')->daily();
        $schedule->command('properties:delete-general-old')->daily();
    }

    // Register the commands for your application
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

