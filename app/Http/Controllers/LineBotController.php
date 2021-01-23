<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WeatherController;
use App\Services\LineBotService;
use LINE\LINEBot;
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

    public function __construct()
    {
        $this->lineBotService = app(LineBotService::class);
        $this->weatherController = app(WeatherController::class);
    }

    public function sendMessage($text){
        $response = $this->lineBotService->pushMessage($text);

        return $this->assertEquals(200, $response->getHTTPStatus());
    }

    public function sendMessageWeather(){
        $db = DB::table('weather_tomorrow')->get();
        dd($db);

        // $text = $request->events[0]->getText();
        $cityRain = '';
        $text = '';
        foreach($cityData as $city){
            foreach($city as $key => $value){
                $text = '';
                $locationName = urlencode($key);
                $url = $weatherUrl  . $weathers[0] . '?Authorization=' . $token . '&locationName=' . $locationName;

                $response = $client->get($url);
                $json = json_decode($response->getBody());
                $data = $json->records;
                $probabilityOfPrecipitation = $data->location[0]->weatherElement[1]->time[$type]->parameter->parameterName;

                if($probabilityOfPrecipitation > 50){
                    $cityRain = $cityRain . $key . '、';
                }
            }
        }

        if($cityRain == ''){
            $text = '明天沒有降雨機率50%的城市';
        }else{
            $text = '明天降雨機率50%超過的城市，有' . rtrim($cityRain, "、");
        }

        $response = $this->sendMessage($text);


        // Log::info($text);
        // $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($pop);

        // $response = $bot->replyText($request->events[0]->getReplyToken(), $textMessageBuilder);
        // if ($response->isSucceeded()) {
        //     return;
        // }

    }

    public function getMessageWeather(Request $request)
    {
        $params = $request->all();
        logger(json_encode($params, JSON_UNESCAPED_UNICODE));
        return response('hello world', 200);
    }
}
