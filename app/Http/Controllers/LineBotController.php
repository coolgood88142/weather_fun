<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WeatherController;
use App\Services\LineBotService;
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

        $httpClient   = new CurlHTTPClient($this->channel_access_token);
        $this->bot    = new LINEBot($httpClient, ['channelSecret' => $this->channel_secret]);
        $this->client = $httpClient;
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

        // $text = $request->events[0]->getText();
        // $cityRain = '';
        // $text = '';
        // foreach($cityData as $city){
        //     foreach($city as $key => $value){
        //         $text = '';
        //         $locationName = urlencode($key);
        //         $url = $weatherUrl  . $weathers[0] . '?Authorization=' . $token . '&locationName=' . $locationName;

        //         $response = $client->get($url);
        //         $json = json_decode($response->getBody());
        //         $data = $json->records;
        //         $probabilityOfPrecipitation = $data->location[0]->weatherElement[1]->time[$type]->parameter->parameterName;

        //         if($probabilityOfPrecipitation > 50){
        //             $cityRain = $cityRain . $key . '、';
        //         }
        //     }
        // }

        // if($cityRain == ''){
        //     $text = '明天沒有降雨機率50%的城市';
        // }else{
        //     $text = '明天降雨機率50%超過的城市，有' . rtrim($cityRain, "、");
        // }

        // $response = $this->sendMessage($text);


        // Log::info($text);
        // $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($pop);

        // $response = $bot->replyText($request->events[0]->getReplyToken(), $textMessageBuilder);
        // if ($response->isSucceeded()) {
        //     return;
        // }

    }

    public function getMessageWeather(Request $request)
    {
        $bot       = $this->bot;
        $signature = $request->header(\LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE);
        $body      = $request->getContent();

        try {
            $events = $bot->parseEventRequest($body, $signature);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        foreach ($events as $event) {
            $replyToken = $event->getReplyToken();
            if ($event instanceof MessageEvent) {
                $message_type = $event->getMessageType();
                $text = $event->getText();
                Log::info($text);
                switch ($message_type) {
                    case 'text':
                        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('hello world!');
                        $bot->replyText($replyToken, $textMessageBuilder);
                        break;
                }
            }
        }

        // $params = $request->all();
        // logger(json_encode($params, JSON_UNESCAPED_UNICODE));

        // Log::info($text);
        // $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('hello world!');

        // $response = $bot->replyText($request->events[0]->getReplyToken(), $textMessageBuilder);
        // if ($response->isSucceeded()) {
        //     return;
        // }

        
        // return response('hello world', 200);
    }
}
