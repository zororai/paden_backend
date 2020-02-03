<?php



namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Properties;
use Carbon\Carbon;

class DeleteAgentProperties extends Command
{
    // The name and signature of the console command.
    protected $signature = 'delete:agent-properties';

    // The console command description.
    protected $description = 'Delete agent properties older than 2 months';

    public function __construct()
    {
        parent::__construct();
    }

    // Execute the console command.
    public function handle()
    {
        $twoMonthsAgo = Carbon::now()->subMonths(2);

        // Delete properties older than 2 months
        Properties::where('created_at', '<', $twoMonthsAgo)->delete();

        $this->info('Old agent properties deleted successfully.');
    }
}

