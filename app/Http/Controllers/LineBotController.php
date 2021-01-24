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
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
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

        $this->channel_access_token = env('CHANNEL_ACCESS_TOKEN');
        $this->channel_secret = env('CHANNEL_SECRET');

        $httpClient = new CurlHTTPClient($this->channel_access_token);
        $this->bot = new LINEBot($httpClient, ['channelSecret' => $this->channel_secret]);
        $this->client = $httpClient;

        $this->responseService = app(WebhookResponseService::class);
    }

    public function sendMessage($text){
        $response = $this->lineBotService->pushMessage($text);

        return $this->assertEquals(200, $response->getHTTPStatus());
    }

    public function sendMessageWeather(){
        $datas = DB::table('weather_tomorrow')->whereIn('city', ['臺北市', '新北市'])->get();
        $count = 0;
        $message = '';
        foreach($datas as $data){
            $city = $data->city;
            $time_period = $data->time_period;
            $temperature = $data->temperature;
            $probability_of_precipitation = $data->probability_of_precipitation;
            $temperature_text = '昨天18:00-今天06:00';
            
            if($time_period == 0){
                $message = $message . $city . ':' . ' \n';
            }else if($time_period == 1){
                $temperature_text = '今天06:00-18:00';
            }else if($time_period == 2){
                $temperature_text = '今天18:00-明天06:00';
            }

            $message = $message . '【'. $temperature_text . ' 溫度為' . $temperature;
            $message = $message . '， 降雨機率為' . $probability_of_precipitation . '%】' . ' \n';
        }

        $message = rtrim($message, ' \n');
        $response = $this->sendMessage($message);

    }

    public function getMessageWeather(Request $request)
    {
        $text = $request->events[0]['message']['text'];
        $user_id = $request->events[0]['source']['userId'];

        // Log::info('Webhook has request',$request->all());
        
        // foreach ($request['events'] as $event) {

        //     $webhookRequest = $reqTransformer->tramsforRequest($event);

        //     $this->sendMessage($webhookRequest['content']);
        // }

        // return $response;

        // return $response;


        // foreach ($events as $event) {
        //     $replyToken = $event->getReplyToken();
        //     if ($event instanceof MessageEvent) {
        //         $message_type = $event->getMessageType();
        //         $text = $event->getText();
        //         Log::info($text);
        //         switch ($message_type) {
        //             case 'text':
        //                 $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('hello world!');
        //                 $bot->replyText($replyToken, $textMessageBuilder);
        //                 break;
        //         }
        //     }
        // }

        // $params = $request->all();
        // Log::info($params);
        // logger(json_encode($params, JSON_UNESCAPED_UNICODE));

        // $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('hello world!');

        // $response = $bot->replyMessage($request->events[0]->getReplyToken(), $textMessageBuilder);
        // if ($response->isSucceeded()) {
        //     return;
        // }

        
        // return response('hello world', 200);
    }
}
