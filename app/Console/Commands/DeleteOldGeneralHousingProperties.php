<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Properties;
use Carbon\Carbon;

class DeleteOldGeneralHousingProperties extends Command
{
    protected $signature = 'properties:delete-general-old';

    protected $description = 'Delete general housing properties older than 2 months';

    public function handle()
    {
        $twoMonthsAgo = Carbon::now()->subMonths(2);

        $deletedCount = Properties::where('housing_context', 'general')
            ->where('created_at', '<', $twoMonthsAgo)
            ->delete();

        $this->info("Deleted $deletedCount old general housing property(ies).");
    }
}
