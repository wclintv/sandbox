<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
//use App\Http\Controllers\QuickbooksController;
use App\Utility\QLog;

class QbOnlineSync implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //QLog::test("Quickbooks Sync Begin..." . PHP_EOL);
        //QLog::test(Auth::user()->qbo);
        //$response = QuickbooksController::sync();
        //QLog::test($response);
    }
}
