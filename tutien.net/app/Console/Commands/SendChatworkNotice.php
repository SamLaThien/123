<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use wataridori\ChatworkSDK\ChatworkSDK;
use wataridori\ChatworkSDK\ChatworkRoom;

class SendChatworkNotice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chatwork:send-notice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily notice for all member';

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
        ChatworkSDK::setApiKey(env('CHATWORK_AWESOME_API_KEY'));

        $room1 = new ChatworkRoom('122048695');
        $room2 = new ChatworkRoom('25658698');
        $room3 = new ChatworkRoom('29263520');


        $room1->sendMessage(
            "[To:2672189] [To:2672266] [To:3290276] [To:3286034]\nCác đồng chí hoàn thành Daily Report nhé!"
        );
        $room2->sendMessage(
            "[To:1154961] [To:3020775] [To:3401285] [To:2611547] [To:3401309]\nCác đồng chí hoàn thành Daily Report nhé!"
        );
        $room3->sendMessage(
            "[To:1991503] [To:2771325] [To:2357269] [To:2847031]\nCác đồng chí hoàn thành Daily Report nhé!"
        );
    }
}
