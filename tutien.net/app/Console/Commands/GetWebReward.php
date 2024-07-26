<?php

namespace App\Console\Commands;

use App\Account;
use App\CookieHelper;
use App\RewardLogs;
use HTMLDomParser;
use Illuminate\Console\Command;
use Ixudra\Curl\Facades\Curl;
use Log;

class GetWebReward extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'truyencv:get-web-reward {AccountId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lụm quà trên web';

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
        $id = $this->argument('AccountId');
        $this->readStory($id);
    }

    private function readStory(int $accountId)
    {
        $account = Account::with(['reading.story'])->where(['account_id' => $accountId])->first();
        $reading = $account->reading;
        $story = $reading->story;

        $path = '/'. vn_to_str($story['name']) . '/chuong-' . $reading['reading'] . '/';
        $url = "https://tutien.net" . $path;
        $response = Curl::to($url)
            ->withHeader(':authority: tutien.net')
            ->withHeader(':method: GET')
            ->withHeader(':path: ' . $path)
            ->withHeader(':scheme: https')
            ->withHeader('accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8')
            ->withHeader('accept-language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7,de;q=0.6,ja;q=0.5')
            ->withHeader('cache-control: max-age=0')
            ->withHeader($account->cookie)
            ->withHeader('upgrade-insecure-requests: 1')
            ->withHeader('user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36')
            ->withResponseHeaders()
            ->returnResponseObject()
            ->get();

        $content = $response->content;
        $headers = $response->headers;

        // Update cookie
        if (!empty($headers['Set-Cookie'])) {
            $cookie = app(CookieHelper::class)->updateCookie($account, $headers);
            $newCookie = implode('; ', $cookie);
            $account->update(['cookie' => $newCookie]);
        }

        // $reading;
        $reading->update(['reading' => $reading->reading + 1]);

        preg_match_all('#Reading\(\d+,\d+,\d+\)#mi', $content, $readingFunction);
        if (count($readingFunction[0])) {
            $text = $readingFunction[0][0];
            $text = str_replace('Reading(', '', $text);
            $text = str_replace(')', '', $text);
            $params = explode(',', $text);

            $storyId = $params[0];
            $chapterId = $params[1];
            $remove = $params[2];

            // Fake reading request (in browser)
            $this->reading($account, $url, $storyId, $chapterId, $remove);

            sleep(5);
            $this->getReward($storyId, $path, $account);
        }
    }

    private function getReward(string $storyId, string $path, Account $account)
    {
        $response = Curl::to('https://tutien.net/index.php')
            ->withHeader(':authority: tutien.net')
            ->withHeader(':method: POST')
            ->withHeader(':path: /index.php')
            ->withHeader(':scheme: https')
            ->withHeader('accept: */*')
            ->withHeader('accept-language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7,de;q=0.6,ja;q=0.5')
            ->withHeader('content-length: 24')
            ->withHeader('content-type: application/x-www-form-urlencoded; charset=UTF-8')
            ->withHeader($account->cookie)
            ->withHeader('origin: https://tutien.net')
            ->withHeader('referer: ' . $path)
            ->withHeader('user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36')
            ->withHeader('x-requested-with: XMLHttpRequest')
            ->withHeader('upgrade-insecure-requests: 1')
            ->withData([
                'btnLumDo' => 1,
                'story_id' => $storyId
            ])
            ->withResponseHeaders()
            ->returnResponseObject()
            ->post();

        $content = $response->content;
        $headers = $response->headers;

        // Update cookie
        if (!empty($headers['Set-Cookie'])) {
            $cookie = app(CookieHelper::class)->updateCookie($account, $headers);
            $newCookie = implode('; ', $cookie);
            $account->update(['cookie' => $newCookie]);
        }

        $responseMessage = print_r($content, true);
        $account->update(['is_reward_collected' => 1]);
        RewardLogs::create([
            'account_id' => $account->id,
            'time_stop' => $account->online,
            'message' => $responseMessage,
        ]);
    }

    private function reading($account, $url, $storyId, $chapterId, $remove)
    {
        $response = Curl::to($url)
            ->withHeader(':authority: tutien.net')
            ->withHeader(':method: POST')
            ->withHeader(':path: /index.php')
            ->withHeader(':scheme: https')
            ->withHeader('accept: */*')
            ->withHeader('accept-encoding: gzip, deflate, br')
            ->withHeader('accept-language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7,de;q=0.6,ja;q=0.5')
            ->withHeader('content-length: 54')
            ->withHeader('content-type: application/x-www-form-urlencoded; charset=UTF-8')
            ->withHeader($account->cookie)
            ->withHeader('origin: https://tutien.net')
            ->withHeader('referer: ' . $url)
            ->withHeader('user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36')
            ->withHeader('x-requested-with: XMLHttpRequest')
            ->withData([
                'btnReading' => 1,
                'story_id' => $storyId,
                'chapter_id' => $chapterId,
                'remove' => $remove,
            ])
            ->withResponseHeaders()
            ->returnResponseObject()
            ->post();

        $headers = $response->headers;
        // Update cookie
        if (!empty($headers['Set-Cookie'])) {
            $cookie = app(CookieHelper::class)->updateCookie($account, $headers);
            $newCookie = implode('; ', $cookie);
            $account->update(['cookie' => $newCookie]);
        }
    }
}
