<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WeatherController;
use App\Services\LineBotService;
use App\Services\WebhookResponseService;
use App\Transformers\Requests\WebhookRequestTransformer;
use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\SignatureValidator;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Config;
use Exception;

class LineBotController extends Controller
{
    private $lineBotService;
    private $weatherController;
    private $client;
    private $bot;
    private $channel_access_token;
    private $channel_secret;

    public function __construct()
    {
        $this->lineBotService = app(LineBotService::class);
        $this->weatherController = app(WeatherController::class);

        $this->channel_access_token = env('LINE_BOT_CHANNEL_ACCESS_TOKEN');
        $this->channel_secret = env('LINE_BOT_CHANNEL_SECRET');

        $httpClient = new CurlHTTPClient($this->channel_access_token);
        $this->bot = new LINEBot($httpClient, ['channelSecret' => $this->channel_secret]);
        $this->client = $httpClient;
    }

    public function sendMessage($text){
        $response = $this->lineBotService->pushMessage($text);
    }

    public function sendMessageWeather(int $type, String $cityText){
        $cityArray = ['臺北市', '新北市'];
        $tableName = 'weather_info';

        if($type == 1){
            $tableName = 'weather_tomorrow';
        }

        if($cityText != null){
            $cityArray = [$cityText];
        }

        $datas = DB::table($tableName)->whereIn('city', $cityArray)->get();
        $count = 0;
        $message = '';
        $now = Carbon::now()->timezone('Asia/Taipei');
        $yesterday = $now->yesterday()->format('m/d');
        $today = $now->format('m/d');
        $tomorrow = $now->tomorrow()->format('m/d');
        
        foreach($datas as $data){
            $city = $data->city;
            $temperature = $data->temperature;
            $probability_of_precipitation = $data->probability_of_precipitation;
            $temperature_text = $yesterday . ' 18:00 - '. $today .' 06:00';
            
            if($type == 1){
                $time_period = $data->time_period;
                if($time_period == 0){
                    $message = $message . $city . '明天氣候：' . "\n";
                }else if($time_period == 1){
                    $temperature_text = $today . ' 06:00 - 18:00';
                }else if($time_period == 2){
                    $temperature_text = $today . ' 18:00 - ' . $tomorrow . ' 06:00';
                }

                $message = $message . '【'. $temperature_text . ' 溫度為' . $temperature;
                $message = $message . '， 降雨機率為' . $probability_of_precipitation . '%】' . "\n";
            }else{
                $message = $city . '今天氣候：' . "\n";
                $message = $message . '【'. ' 溫度為' . $temperature;
                $message = $message . '， 降雨機率為' . $probability_of_precipitation . '%】';
            }
        }

        $message = rtrim($message, "\n");

        if($cityText != null){
            return $message;
        }else{
            $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
            $response = $this->sendMessage($textMessageBuilder);
        }
    }

    public function getMessageWeather(Request $request)
    {
        $text = $request->events[0]['message']['text'];
        $replyToken = $request->events[0]['replyToken'];
        $cityData = Config::get('city');
        $len = mb_strlen($text, 'utf-8');
        $text = str_replace('台','臺',$text);
        $messageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('請輸入【氣候】');
        
        if($len > 3){
            $text = mb_substr($text , 0 , 3, 'utf-8');
        }else if($text == '氣候'){
            $cityText = '請輸入下列任一個縣市名稱：' . "\n";
            foreach($cityData as $city){
                $cityText = $cityText . $city . '、';
            }

            $cityText = rtrim($cityText, '、');
            $messageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($cityText);
        }

        if(in_array($text, $cityData)){
            Log::info('有近來');
            $messageBuilder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder(
                '詢問'. $text .'的氣候',
                new ConfirmTemplateBuilder('請問要選擇哪一天?', [
                    new MessageTemplateActionBuilder('今天', $this->sendMessageWeather(0, $text)),
                    new MessageTemplateActionBuilder('明天', $this->sendMessageWeather(1, $text)),
                ])
            );
            Log::info($messageBuilder);
        }

        $response = $this->bot->replyMessage($replyToken, $messageBuilder);

        if ($response->isSucceeded()) {
            echo 'Succeeded!';
            return;
        }
    }
}
