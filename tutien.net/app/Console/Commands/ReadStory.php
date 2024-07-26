<?php

namespace App\Console\Commands;

use App\Reading;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Ixudra\Curl\Facades\Curl;
use Log;

class ReadStory extends Command
{
    const BASE_CHAPTER_URL = 'http://api.tutien.net/chapter/';
    const SECRET_KEY = 'jD95wSNRZQ';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'truyencv:read-story';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Giả lập đọc truyện';

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
        /**
         * http://api.tutien.net/chapter/?
         * sig=50f4326e3ca5d2f148bc4ec7f9741194
         * &chapter_id=8390299
         * &time=1578628443
         * &userid=555227
         * &deviceid=d7e4621d1feddb18
         * &os=android
         * &app_version=1.0.4
         */

        /*
         * http://api.tutien.net/registDeviceToken?user_id=555227&device_token=Android-c1z2vodf_UE:APA91bHX9t3QHLGv7Fb5YZ6yBJpTEPypPtxO4Hy7ZPCVGsYGi_qxObY1wRGQpwpCE7ftSXOZ3rG-Skk0YEbzcglMJSmO3Htw-93MR2wayNsXU7taXY6m2HWNRivju0k0Ba9-VzaR99Ko&status=1
         */

        $minute = Carbon::now()->minute;
        if (!($minute === 10 || $minute === 30 || $minute === 50)) {
            return;
        }

        $readings = Reading::with('account')->get();
        Log::info("GIẢ LẬP ĐỌC TRUYỆN TRÊN APP");

        foreach ($readings as $key => $reading) {
            $this->info("User reading: " . $reading['account']['account_name']);
            $chapterId = $reading['next_chapter'];
            $accountId = $reading['account']['account_id'];
            $deviceId = $reading['account']['device_id'];
            $time = time();

            $sig = md5($accountId . '.' . self::SECRET_KEY . '.' . $time . '.' . $chapterId);
            $response = Curl::to(self::BASE_CHAPTER_URL)
                ->withData([
                    'sig' => $sig,
                    'chapter_id' => $chapterId,
                    'time' => $time,
                    'userid' => $accountId,
                    // 'deviceid' => 'd7e4621d1feddb18',
                    // 'os' => 'android',
                    // 'app_version' => '1.0.4',
                ])
                ->get();

            $result = json_decode($response, true);
            if ($result['success']) {
                $nextId = $result['data']['NEXT'];
                $reading->update([
                    'current_chapter' => $reading['next_chapter'],
                    'next_chapter' => $nextId,
                ]);
            }

            sleep(5);
        }
    }
}
