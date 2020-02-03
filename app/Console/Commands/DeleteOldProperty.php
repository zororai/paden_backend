<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Properties;
use Carbon\Carbon;

class DeleteOldProperty extends Command
{
    // The name and signature of the console command
    protected $signature = 'properties:delete-agent-old';

    // The console command description
    protected $description = 'Delete properties older than 2 months for agents only';

    // Execute the console command
    public function handle()
    {
        // Get the current date and subtract 2 months
        $twoMonthsAgo = Carbon::now()->subMonths(2);

        // Delete properties older than 2 months for users who are agents
        $deletedCount = Properties::where('created_at', '<', $twoMonthsAgo)
            ->whereHas('user', function ($query) {
                // Assuming there is a "role" or similar attribute to differentiate users (e.g. agent)
                $query->where('role', 'agent');
            })
            ->delete();

        // Output the result to the console
        $this->info("Deleted $deletedCount old property(ies) for agent users.");
    }
}
