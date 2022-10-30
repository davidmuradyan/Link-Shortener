<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily report';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        dispatch(\App\Jobs\SendReportJob::class);
    }
}
