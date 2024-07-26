<?php

namespace App\Console\Commands;

use App\Story;
use Curl;
use Illuminate\Console\Command;

class AddStory extends Command
{
    const BASE_CHAPTER_URL = 'http://aios.tutien.net/story/';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tutien:add-story {storyId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add new story';

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
        $storyId = $this->argument('storyId');
        $response = Curl::to(self::BASE_CHAPTER_URL)
            ->withData([
                'page_numer' => 1,
                'story_id' => $storyId,
                'page_size' => 1,
            ])->get();

        $result = json_decode($response, true);
        if ($result['success']) {
            $data = $result['data'];
            Story::create([
                'story_id' => $storyId,
                'name' => $data['NAME'],
                'first_chapter' => $data['CHAPTER'][0]['id'],
                'total_chapter' => $data['TOTALCHAPTER'],
            ]);
        }

        $this->info('New story added!!!');
    }
}
