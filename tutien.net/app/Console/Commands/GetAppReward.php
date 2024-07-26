<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GetAppReward extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tutien:get-app-reward';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get app reward';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
    }
}
