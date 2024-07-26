<?php

namespace App\Console\Commands;

use App\Account;
use App\Reading;
use App\Story;
use Illuminate\Console\Command;

class StartReadStory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tutien:start-reading {accountId} {storyId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $accountId = $this->argument('accountId');
        $storyId = $this->argument('storyId');

        $story = Story::where(['story_id' => $storyId])->first();;
        $account = Account::where(['account_id' => $accountId])->first();;
        Reading::create([
            'account_id' => $account->id,
            'story_id' => $story->id,
            'current_chapter' => $story->first_chapter,
            'next_chapter' => $story->first_chapter,
        ]);

        $this->info('Update reading done!!!');
    }
}
