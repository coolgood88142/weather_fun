<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WeatherController;
use App\Services\LineBotService;
use App\Services\CrawlerService;
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
    private $crawlerService;
    private $client;
    private $bot;
    private $channel_access_token;
    private $channel_secret;

    public function __construct()
    {
        $this->lineBotService = app(LineBotService::class);
        $this->crawlerService = app(CrawlerService::class);

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
        $now = Carbon::now()->timezone('Asia/Taipei');
        $yesterday = $now->yesterday()->format('m/d');
        $today = $now->format('m/d');
        $tomorrow = $now->tomorrow('Asia/Taipei')->format('m/d');
        $carouselData = [];
        $carouselContentsData = [];

        if($type == 1){
            if($cityText != null){
                $cityArray = [$cityText];
            }

            $datas = DB::table('weather_tomorrow')->whereIn('city', $cityArray)->get();

            foreach($datas as $data){
                $city = $data->city;
                $temperature = $data->temperature;
                $probability_of_precipitation = $data->probability_of_precipitation;
                $temperature_text = $yesterday . ' 18:00 - '. $today .' 06:00';
                $carousel = [];

                $time_period = $data->time_period;
                $date = $today;
                $temperature_text = ' 06:00 - 18:00';
                if($time_period == 1){
                    $temperature_text = ' 18:00 - 06:00';
                    $date = $today . '-' . $tomorrow;
                }else if($time_period == 2){ 
                    $date = $tomorrow;
                }

                $url = $this->getProbabilityOfPrecipitationImage($probability_of_precipitation);
                $carousel = $this->getCarouselArray($url, $date, $temperature_text, $temperature, $probability_of_precipitation);
                array_push($carouselData, $carousel);
            }


            $carouselContentsData = [
                'type' => 'flex',
                'altText' => '氣候',
                'contents' => [
                    'type' => 'carousel',
                    'contents' => $carouselData
                ]
            ];
        }else{
            $weatherController = app(WeatherController::class);
            $client = new \GuzzleHttp\Client();
            $weathers = Config::get('weather');
            $locationName = urlencode($cityText);
            $todayDate = $now->format('Y-m-d');
            $hour = (int)$now->format('H');
            $count = 0;
            $probabilityNum = 0;
            $messageArray = [
                [
                    'type' => 'text',
                    'text' => $now->format('m/d'),
                    'weight' =>'bold',
                    'size'=>'xl'
                ]
            ];

            $weatherData = $weatherController->getCrawlerData($client, $weathers[13], $locationName);
            if(isset($weatherData->locations[0])){
                $weatherForecast = $weatherData->locations[0]->location[0]->weatherElement[6]->time;
                foreach($weatherForecast as $forecast){
                    $startTime = mb_substr($forecast->startTime, 0, 10, "utf-8");
                    $time = mb_substr($forecast->startTime, 11, 5, "utf-8");
                    $nowHour = (int)mb_substr($time, 0, 2, "utf-8");
                    if($startTime == $todayDate){
                        $data = mb_split('。', $forecast->elementValue[0]->value);
                        $probabilityOfPrecipitation = mb_substr($data[1], 5, 2, "utf-8");
                        $probabilityOfPrecipitation = str_replace('%','',$probabilityOfPrecipitation);
                        $probabilityNum = $probabilityNum . (int)$probabilityOfPrecipitation;
                        $temperature = mb_substr($data[2], 4, 2, "utf-8");
                        $temperature = str_replace('度','',$temperature);

                        $timeWeatherData = [
                            'type' => 'text',
                            'size'=>'xl',
                            'weight' =>'bold',
                            'text' => $time . ' 🌡️' . $temperature . '° 💧' . $probabilityOfPrecipitation . '%',
                        ];

                        array_push($messageArray, $timeWeatherData);
                        $count++;
                    }
                }
            }

            $rain = $probabilityNum != 0 && $count > 1 ? round($probabilityNum/$count) : $probabilityNum;
            
            $carouselContentsData = [
                'type' => 'flex',
                'altText' => '氣候',
                'contents' => [
                    "type"=> "bubble",
                    "size"=> "giga",
                    "hero"=> [
                        "type"=> "image",
                        "url"=> $this->getProbabilityOfPrecipitationImage((string) $rain),
                        "size"=> "full",
                        "aspectRatio"=> "20:13",
                    ],
                    "body"=> [
                        "type"=> "box",
                        "layout"=> "vertical",
                        "contents"=> $messageArray
                    ]
                ]
            ];


        }

        if($cityText != null){
            return $carouselContentsData;
        }
    }

    public function getMessageWeather(Request $request)
    {
        $replyToken = $request->events[0]['replyToken'];
        $messageBuilder = null;
        if(isset($request->events[0]['postback'])){
            $messageBuilder =  new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($request->events[0]['postback']['data']);
        }else{
            $messageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('請輸入【氣候】');
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
            }else if($text == '雷達'){
                $url = 'https://www.cwb.gov.tw';
                $radarUrl = $url . '/V8/C/W/OBS_Radar.html';
                $content = $this->crawlerService->getOriginalData($radarUrl);
                $image =  $content->filter('div > img')->first()->attr('src');
                $radarImage = $url . $image;

                $messageBuilder =  new RawMessageBuilder(
                    [
                        'type' => 'flex',
                        'altText' => '氣象雷達圖',
                        'contents' => [
                            'type'=> 'bubble',
                            'body'=> [
                            'type'=> 'box',
                            'layout'=> 'vertical',
                                'contents'=> [
                                    [
                                        'type'=> 'image',
                                        'url'=> $radarImage,
                                        'size'=> 'full',
                                        'aspectMode'=> 'cover',
                                        'aspectRatio'=> '1:1',
                                        'gravity'=> 'center'
                                    ],
                                    [
                                        'type'=> 'box',
                                        'layout'=> 'vertical',
                                        'contents'=> [],
                                        'position'=> 'absolute',
                                        'background'=> [
                                            'type'=> 'linearGradient',
                                            'angle'=> '0deg',
                                            'endColor'=> '#00000000',
                                            'startColor'=> '#00000099'
                                        ],
                                        'width'=> '100%',
                                        'height'=> '40%',
                                        'offsetBottom'=> '0px',
                                        'offsetStart'=> '0px',
                                        'offsetEnd'=> '0px'
                                    ],
                                    [
                                        'type'=> 'box',
                                        'layout'=> 'horizontal',
                                        'contents'=> [
                                            [
                                                'type'=> 'box',
                                                'layout'=> 'vertical',
                                                'contents'=> [
                                                    [
                                                        'type'=> 'box',
                                                        'layout'=> 'horizontal',
                                                        'contents'=> [
                                                            [
                                                                'type'=> 'text',
                                                                'text'=> '氣象雷達圖',
                                                                'size'=> 'xl',
                                                                'color'=> '#ffffff'
                                                            ]
                                                        ]
                                                    ]
                                                ],
                                                'spacing'=> 'xs'
                                            ]
                                        ],
                                        'position'=> 'absolute',
                                        'offsetBottom'=> '0px',
                                        'offsetStart'=>'0px',
                                        'offsetEnd'=> '0px',
                                        'paddingAll'=> '20px'
                                    ],
                                ],
                                'paddingAll'=> '0px'
                            ]
                        ]
                    ]
                );
            }else if($text == '富果'){
                $apiToken = '001ca47f2cf24652cb26f74d97251ab3';
                $symbolId = '3515';
                $fugleUrl = 'https://api.fugle.tw/realtime/v0/intraday/dealts';
                $url = $fugleUrl . '?symbolId='. $symbolId . '&apiToken=' . $apiToken . '&limit=1';
                $Guzzleclient = new \GuzzleHttp\Client();
                                        
                $response = $Guzzleclient->get($url);
                $json = json_decode($response->getBody());
                $dealts = $json->data->dealts;
                dd($json);

                $fugles = Config::get('dealts');
                $messageArray = [
                    [
                        'type'=> 'text',
                        'text'=> '華擎股票',
                        'weight'=> 'bold',
                        'size'=> 'xxl',
                        'margin'=> 'md'
                    ],
                    [
                        'type'=> 'separator',
                        'margin'=> 'xxl'
                    ],
                ];
                $count = 0;
                foreach($fugles as $fugle){
                    foreach($fugle as $key => $value){
                        $fugleValue = $meta->$key;
                        // $fugleText = $fugleValue == true ? '是' : ($fugleValue == false ? '否' : $fugleValue);
                        $fugleText = is_numeric($fugleValue) ? '$' . $fugleValue : $fugleValue;
                        
                        $message = [
                            'type'=> 'box',
                            'layout'=> 'horizontal',
                            'contents'=> [
                                [
                                    'type'=> 'text',
                                    'text'=> $value,
                                    'size'=> 'sm',
                                    'color'=> '#555555',
                                    'flex'=> 0
                                ],
                                [
                                    'type'=> 'text',
                                    'text'=> $fugleText,
                                    'size'=> 'sm',
                                    'color'=> '#111111',
                                    'align'=> 'end'
                                ]
                            ]
                        ];
                        array_push($messageArray, $message);
                    }

                    if($count == 0){
                        array_push($messageArray, [
                            'type'=> 'separator',
                            'margin'=> 'xxl'
                        ]);
                    }
                    $count++;
                }

                $messageBuilder =  new RawMessageBuilder(
                    [
                        'type' => 'flex',
                        'altText' => '華擎股票資訊',
                        'contents' => [
                            'type'=> 'bubble',
                                'body'=> [
                                  'type'=> 'box',
                                  'layout'=> 'vertical',
                                  'contents'=> $messageArray
                                ]  
                        ]
                        
                    ]
                );
            }
        }
       
        Log::info('發送前');
        $response = $this->bot->replyMessage($replyToken, $messageBuilder);

        if ($response->isSucceeded()) {
            echo 'Succeeded!';
            return;
        }
    }

    public function testSymboData(){
        $apiToken = '001ca47f2cf24652cb26f74d97251ab3';
            $symbolId = '3515';
            $fugleUrl = 'https://api.fugle.tw/realtime/v0/intraday/quote';
            $url = $fugleUrl . '?symbolId='. $symbolId . '&apiToken=' . $apiToken;
            $Guzzleclient = new \GuzzleHttp\Client();
                                        
            $response = $Guzzleclient->get($url);
            $json = json_decode($response->getBody());
            $data = get_object_vars($json->data->quote);
      
            // $deatsKeys = array_keys($dealts);
            // $lastKey = $deatsKeys[count($deatsKeys) - 1];
            // $lastDeatlsData = $dealts->$lastKey;
            // $last = array_slice($dealts,-1,1);
            // $test = $dealts[0]->at;
            // dd($data);
               

        $fugles = Config::get('quote');
        // dd($data);
          
        $messageArray = [
            [
                'type'=> 'text',
                'text'=> '華擎股票',
                'weight'=> 'bold',
                'size'=> 'xxl',
                'margin'=> 'md'
            ],
            [
                'type'=> 'separator',
                'margin'=> 'xxl'
            ],
        ];
        $count = 0;
        $typeArray = [];
        foreach($fugles as $fugle){
            foreach($fugle as $key => $value){
                $fugleValue = '';
                $message = [];
                if(is_numeric($key)){
                    $keys = array_keys($value);
                    $firstKey = $keys[0];
                    
                    $message = [
                        'type'=> 'box',
                        'layout'=> 'horizontal',
                        'contents'=> [
                            [
                                'type'=> 'text',
                                'text'=> (string)$value[$firstKey],
                                'size'=> 'sm',
                                'color'=> '#555555',
                                'flex'=> 0
                            ],
                            [
                                'type'=> 'box',
                                'layout'=> 'vertical',
                                'margin'=> 'xxl',
                                'spacing'=> 'sm',
                                'contents'=> [
                                    [
                                        'type'=> 'text',
                                        'text'=> 'info',
                                        'size'=> 'md',
                                        'color'=> '555555',
                                        'flex'=> 0,
                                    ]
                                ]
                            ],
                            [
                                [
                                    'type'=> 'separator',
                                    'margin'=> 'none',
                                ]
                            ],
                        ]
                    ];

                    foreach($keys as $index => $key){
                        if($index != 0){
                            $keyMessage = [
                                'type'=> 'box',
                                'layout'=> 'horizontal',
                                'contents'=> [
                                    [
                                        'type'=> 'text',
                                        'text'=> (string)$value[$key],
                                        'size'=> 'sm',
                                        'color'=> '#555555',
                                        'flex'=> 0
                                    ],
                                    [
                                        'type'=> 'text',
                                        'text'=> (string)$data[$firstKey]->$key,
                                        'size'=> 'sm',
                                        'color'=> '#111111',
                                        'align'=> 'end'
                                    ]
                                ]
                            ];

                            array_push($message['contents'], $keyMessage);
                        }
                    }
                }else{
                    $fugleValue = $data[$key];
                    if(is_bool($fugleValue)){
                        if($fugleValue){
                            $fugleValue = '是';
                        }else{
                            $fugleValue = '否';
                        }
                    }else if(is_numeric($fugleValue)){
                        $fugleValue = '$' . $fugleValue;
                    }

                    $message = [
                        'type'=> 'box',
                        'layout'=> 'horizontal',
                        'contents'=> [
                            [
                                'type'=> 'text',
                                'text'=> (string)$value,
                                'size'=> 'sm',
                                'color'=> '#555555',
                                'flex'=> 0
                            ],
                            [
                                'type'=> 'text',
                                'text'=> (string)$fugleValue,
                                'size'=> 'sm',
                                'color'=> '#111111',
                                'align'=> 'end'
                            ]
                        ]
                    ];
                }
                
                // $fugleText = '';
                // $fugleText = is_bool($fugleValue) ? (($fugleValue == true) ? '是' : '否') :  $fugleValue;
                // $fugleText = is_numeric($fugleValue) ? '$' . $fugleValue : $fugleValue;

                
                // array_push($typeArray, is_bool($fugleValue));

                
                array_push($messageArray, $message);

                // if(count($messageArray) == 1){
                //     array_push($messageArray[0], [
                //         'margin'=> 'xxl',
                //         'spacing'=> 'sm',
                //     ]);
                //     dd($messageArray);
                // }
            }

            // dd($messageArray);

            // if($count == 0){
            //     array_push($messageArray, [
            //         'type'=> 'separator',
            //         'margin'=> 'xxl'
            //     ]);
            // }
            // $count++;
        }

        $messageBuilder =  new RawMessageBuilder(
            [
                'type' => 'flex',
                'altText' => '華擎線圖',
                'contents' => [
                    'type'=> 'bubble',
                        'body'=> [
                        'type'=> 'box',
                        'layout'=> 'vertical',
                        'contents'=> $messageArray
                        ]  
                ]
                
            ]
        );
            // dd($messageArray);
    }

    public function getProbabilityOfPrecipitationImage(String $probability_of_precipitation){
        $rain = (int)$probability_of_precipitation;
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
