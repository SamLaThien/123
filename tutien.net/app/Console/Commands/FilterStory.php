<?php

namespace App\Console\Commands;

use App\Story;
use Illuminate\Console\Command;
use Ixudra\Curl\Facades\Curl;

class FilterStory extends Command
{
    const BASE_FILTER_URL = 'http://aios.tutien.net/filter';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tutien:filter-story';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lấy danh sách truyện và sắp xếp theo số lượng chap';

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
        // http://aios.tutien.net/filter?totalchapter=5&sort=9&page=1&kind=0&cat=0&status=2
        $response = Curl::to(self::BASE_FILTER_URL)
            ->withData([
                'totalchapter' => 5,
                'sort' => 9,
                'page' => 1,
                'kind' => 0,
                'cat' => 0,
                'status' => 2,
            ])
            ->get();

        $results = json_decode($response, true);
        // {
        //     "success": 1,
        //     "pager": {
        //         "page": 1,
        //         "size": 20,
        //         "total_count": "798"
        //     },
        //     "data": [
        //         {
        //             "ID": "3161",
        //             "NAME": "Long Huyết Vũ Đế",
        //             "THUMB": "http://tutien.net/images/poster/long-huyet-vu-de-poster-20150528-100x140.jpg",
        //             "PROCESS": "Chương 5378",
        //             "AUTHOR": "Lưu Thủy Vô Ngân",
        //             "VIEWED": "4k",
        //             "RATING": 8.5,
        //             "COUNT": 1
        //         },
        //         ...
        //     ],
        //     "errorCode": 0,
        //     "errorMessage": ""
        // }

        $stories = [];
        if ($results['success']) {
            $data = $results['data'];
            for ($i = 0; $i < count($data); $i++) {
                $story = $data[$i];
                $stories[] = [
                    'story_id' => $story['ID'],
                    'name' => $story['NAME'],
                    'thumb' => $story['THUMB'],
                    'process' => $story['PROCESS'],
                    'author' => $story['AUTHOR'],
                    'viewed' => $story['VIEWED'],
                    'rating' => $story['RATING'],
                    'total_chapter' => 0,
                    'categories' => '',
                ];
            }
        }

        Story::insert($stories);
    }
}
