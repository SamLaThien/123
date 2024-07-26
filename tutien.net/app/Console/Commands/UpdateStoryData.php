<?php

namespace App\Console\Commands;

use App\Story;
use Illuminate\Console\Command;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class UpdateStoryData extends Command
{
    const BASE_CHAPTER_URL = 'http://aios.tutien.net/story/';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tutien:test';
    // protected $signature = 'tutien:update-story-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lấy dữ liệu của 1 chapter';

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
        $b = [
            "2月に大阪へ引っ越しの予定なので、3月からの勤務を希望します。\\n\\n3月の予定は全く入っていないですし、生活費のためにもなるべく長時間勤務させていただけるとこちらも助かります！\\n\\n3月出勤数20日(8日)",
            "お世話になっております。\\n満留です。\\n夜分遅くに失礼致します。\\n本日夕方に発熱し、先程アルバイト先でコロナ感染者が出たとの報告がありました為、PCR検査を受けることになってしまいました。\\n今後の流れがまだ掴めそうにありませんので、6日の面接を延期していただくことはできますでしょうか。\\n大変申し訳ございません。\\nご迷惑をおかけいたしますが何卒よろしくお願い致します。",
        ];
        
        foreach ($b as $key => $text) {
            $a = json_encode([
                'type' => 'text',
                'text' => $text,
            ]);
            $this->info($a);
        }
        return;
    }
}
