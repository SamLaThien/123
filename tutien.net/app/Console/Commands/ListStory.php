<?php

namespace App\Console\Commands;

use App\Story;
use Illuminate\Console\Command;

class ListStory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tutien:list-story';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all stories';

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
        $headers = ['Story id', 'Name', 'Total chapter'];
        $stories = Story::select('story_id', 'name', 'total_chapter')->get()->toArray();
        $this->table($headers, $stories);
    }
}
