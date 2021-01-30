<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\LineBotService;
use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\RawMessageBuilder;
use LINE\LINEBot\MessageBuilder\FlexMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\RichMenuBuilder;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Config;
use Exception;

class LineBotController extends Controller
{
    private $lineBotService;
    private $client;
    private $bot;
    private $channel_access_token;
    private $channel_secret;

    public function __construct()
    {
        $this->lineBotService = app(LineBotService::class);

        $this->channel_access_token = env('LINE_BOT_CHANNEL_ACCESS_TOKEN');
        $this->channel_secret = env('LINE_BOT_CHANNEL_SECRET');

        $httpClient = new CurlHTTPClient($this->channel_access_token);
        $this->bot = new LINEBot($httpClient, ['channelSecret' => $this->channel_secret]);
        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient('<channel access token>');
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
        $tomorrow = $now->tomorrow('Asia/Taipei')->format('m/d');
        $carouselData = [];
        
        foreach($datas as $data){
            $city = $data->city;
            $temperature = $data->temperature;
            $probability_of_precipitation = $data->probability_of_precipitation;
            $temperature_text = $yesterday . ' 18:00 - '. $today .' 06:00';
            $carousel = [];
            
            if($type == 1){
                $time_period = $data->time_period;
                $date = $today;
                $temperature_text = ' 06:00 - 18:00';
                if($time_period == 0){
                    $message = $message . $city . '明天氣候：' . "\n";
                }else if($time_period == 1){
                    $temperature_text = ' 18:00 - ' . ' 06:00';
                    $date = $today . '-' . $tomorrow;
                }else if($time_period == 2){ 
                    // $temperature_text = $tomorrow . ' 06:00 - 18:00';
                    $date = $tomorrow;
                }

                $message = $message . '【'. $temperature_text . ' 溫度為' . $temperature;
                $message = $message . '， 降雨機率為' . $probability_of_precipitation . '%】' . "\n";
                $url = $this->getProbabilityOfPrecipitationImage($probability_of_precipitation);
                $carousel = $this->getCarouselArray($url, $date, $temperature_text, $temperature, $probability_of_precipitation);
                array_push($carouselData, $carousel);
            }else{
                $message = $city . '今天氣候：' . "\n";
                $message = $message . '【'. ' 溫度為' . $temperature;
                $message = $message . '， 降雨機率為' . $probability_of_precipitation . '%】';
                $url = $this->getProbabilityOfPrecipitationImage($probability_of_precipitation);
                $carousel = $this->getCarouselArray($url, $today, '整天', $temperature, $probability_of_precipitation);
                array_push($carouselData, $carousel);
            }
        }
        $message = rtrim($message, "\n");

        $carouselContentsData = [
            'type' => 'flex',
            'altText' => '氣候',
            'contents' => [
                'type' => 'carousel',
                'contents' => $carouselData
            ]
        ];

        if($cityText != null){
            return $carouselContentsData;
        }else{
            $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
            $response = $this->sendMessage($textMessageBuilder);
        }
    }

    public function getMessageWeather(Request $request)
    {
        $replyToken = $request->events[0]['replyToken'];
        $messageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('請輸入【氣候】');
        if(isset($request->events[0]['postback'])){
            $messageBuilder =  new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($request->events[0]['postback']['data']);
        }else{
            $text = $request->events[0]['message']['text'];
            $cityData = Config::get('city');
            $len = mb_strlen($text, 'utf-8');
            $text = str_replace('台','臺',$text);
            $messageBuilder = '';

            if($text == '氣候'){
                $cityText = '請輸入要查詢的縣市：' . "\n";
                foreach($cityData as $city){
                    $cityText = $cityText . $city . "\n";
                }
    
                $cityText = rtrim($cityText, "\n");
                $messageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($cityText);
            }else if(in_array($text, $cityData)){
                // $fix1 = $this->sendMessageWeather(0, $text);
                // $fix2 = $this->sendMessageWeather(1, $text);

                $messageBuilder =  new RawMessageBuilder(
                    [
                        'type' => 'flex',
                        'altText' => '請問要選擇哪一天?',
                        'contents' => [
                            'type'=> 'bubble',
                            'hero'=> [
                                'type'=> 'image',
                                'url'=> 'https://i.imgur.com/l8yNat5.jpg',
                                'size'=> 'full',
                                'aspectRatio'=> '20:13',
                                'aspectMode'=> 'cover'
                            ],
                            'body'=> [
                                'type'=> 'box',
                                'layout'=> 'vertical',
                                'contents'=> [
                                    [
                                        'type'=>'text',
                                        'text'=>'請問要選擇哪一天?',
                                        'weight'=> 'bold',
                                        'size'=> 'xl',
                                        'margin'=>'md'
                                    ],
                                    [
                                        'type'=>'spacer'
                                    ],
                                ],
                            ],
                            'footer'=> [
                                'type'=>'box',
                                'layout'=>'vertical',
                                'spacing'=>'sm',
                                'contents'=> [
                                    [
                                        'type'=>'button',
                                        'action'=>[
                                            'type'=>'message',
                                            'label' => '今天',
                                            'text'=> $text . '今天氣候',
                                        ],
                                        'height'=>'sm'
                                    ],
                                    [
                                        'type'=>'button',
                                        'action'=>[
                                            'type'=>'message',
                                            'label' => '明天',
                                            'text'=> $text . '明天氣候',
                                        ],
                                        'height'=>'sm'
                                    ]
                                ],
                                'flex'=> 0,
                            ],
                            'styles'=>[
                                'footer'=>[
                                    'separator'=> true
                                ],
                            ],
                        ]
                    ],
                );
                Log::info('組好了');
            }else if(strpos($text,'今天氣候') || strpos($text,'明天氣候')){
                $cityWeather = mb_substr($text , 0 , 3, 'utf-8');
                if(in_array($cityWeather, $cityData)){
                    $fix = '';
                    if(strpos($text,'今天氣候')){
                        $fix = $this->sendMessageWeather(0, $cityWeather);
                    }else if(strpos($text,'明天氣候')){
                        $fix = $this->sendMessageWeather(1, $cityWeather);
                    }
                }

                $messageBuilder = new RawMessageBuilder($fix);
            }
        }
       
        Log::info('發送前');
        // $richMenuBuilder = new \LINE\LINEBot\RichMenuBuilder();
        // $response = $this->$bot->createRichMenu($richMenuBuilder);
        $response = $this->bot->replyMessage($replyToken, $messageBuilder);
        


        if ($response->isSucceeded()) {
            echo 'Succeeded!';
            return;
        }
    }

    public function sendFlexMessageBuilder(FlexMessageBuilder $flexMessageBuilder){
        $response = $this->bot->replyMessage($replyToken, $messageBuilder);

        if ($response->isSucceeded()) {
            echo 'Succeeded!';
            return;
        }
    }

    public function getProbabilityOfPrecipitationImage(String $probability_of_precipitation){
        $rain = (int)$probability_of_precipitation;
        Log::info($rain);
        $image = 'https://i.imgur.com/C5CarmM.jpg';

        if($rain >= 50){
            $image = 'https://i.imgur.com/fzUnYi1.jpg';
        }else if($rain > 20 && $rain < 50){
            $image = 'https://i.imgur.com/WRsK9Dg.jpg';
        }

        return $image;
    }

    public function getCarouselArray(String $url, String $date, String $time, String $temperature, String $probability_of_precipitation){
        $carousel = [
            'type' => 'bubble', 
            'size' => 'micro',
            'hero' => [
                'type' => 'image',
                'url' => $url,
                'size' => 'full',
                'aspectMode' => 'cover',
                'aspectRatio' => '320:213'
            ],
            'body' => [
                'type' => 'box',
                'layout' => 'vertical',
                'contents' => [
                    [
                        'type' => 'text',
                        'weight' => 'bold',
                        'size' => 'sm',
                        'wrap' => true,
                        'text' => $date
                    ],
                    [
                        'type' => 'text',
                        'text' => $time,
                        'size' => 'sm',
                        'weight' => 'bold',
                        'wrap' => true
                    ],
                    [
                        'type' => 'text',
                        'text' => '🌡️' . $temperature,
                        'size' => 'sm',
                        'weight' => 'bold'
                    ],
                    [
                        'type' => 'text',
                        'text' => '💧' . $probability_of_precipitation . '%',
                        'size' => 'sm',
                        'weight' => 'bold'
                    ],
                ]
            ],
        ];

        return $carousel;
    }
}
