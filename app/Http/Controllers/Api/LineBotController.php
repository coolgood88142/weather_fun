<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
    public function getMessageWeather(Request $request){
        $httpClient = new CurlHTTPClient(env('LINEBOT_TOKEN'));
        $bot = new LINEBot($httpClient, ['channelSecret' => env('LINEBOT_SECRET')]);
        $weatherUrl = 'https://opendata.cwb.gov.tw/api';
        $token = 'CWB-96170F0C-F4B6-4626-B946-D6892DA6D584';
        $client = new \GuzzleHttp\Client();
        $cityData = Config::get('city');
        $weathers = Config::get('weather');

        $startTime = 6;
        $endTime = 18;
        $today = Carbon::now()->timezone('Asia/Shanghai');
        $hour = (int)$today->format('H');
        $type = 0;

        if($hour > $endTime){
            $type = 2;
        }else if($hour <= $startTime){ 
            $type = 1;
        }

        $text = $request->events[0]['text'];
        $pop = '目前無降雨機率資料';
        foreach($cityData as $city){
            foreach($city as $key => $value){
                if($key == $text){
                    $locationName = urlencode($key);
                    $url = $weatherUrl  . $weathers[0] . '?Authorization=' . $token . '&locationName=' . $locationName;

                    $response = $client->get($url);
                    $json = json_decode($response->getBody());
                    $data = $json->records;
                    $pop = $text . '降雨機率為：';
                    $pop = $pop . $data->location[0]->weatherElement[1]->time[$type]->parameter->parameterName;
                    $pop = $pop . '%';
                }
            }
        }


        
        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($pop);

        $response = $bot->replyMessage($request->events[0]['replyToken'], $textMessageBuilder);
        if ($response->isSucceeded()) {
            return;
        }

    }
}
