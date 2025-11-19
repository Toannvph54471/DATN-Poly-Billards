<?php

namespace App\Console\Commands;

use App\Http\Controllers\BillController;
use Illuminate\Console\Command;

class AutoStopCombos extends Command
{
    protected $signature = 'combos:auto-stop';
    protected $description = 'Automatically stop expired combos';

    public function handle()
    {
        $billController = new BillController();
        $stoppedCount = $billController->autoStopExpiredCombos();

        $this->info("Auto stopped {$stoppedCount} expired combos");

        return Command::SUCCESS;
    }
}
