<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use App\Services\LineBotService;
use App\Services\CrawlerService;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\RawMessageBuilder;
use Config;

class StockController extends Controller
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

        $this->channel_access_token = env('STOCK_CHANNEL_ACCESS_TOKEN');
        $this->channel_secret = env('STOCK_CHANNEL_SECRET');

        $httpClient = new CurlHTTPClient($this->channel_access_token);
        $this->bot = new LINEBot($httpClient, ['channelSecret' => $this->channel_secret]);
        $this->client = $httpClient;
    }

    public function getMessageStock(Request $request){
        $replyToken = $request->events[0]['replyToken'];
        $text = $request->events[0]['message']['text'];
        $apiToken = '001ca47f2cf24652cb26f74d97251ab3';
        $symbolId = '3515';
        $fugleUrl = 'https://api.fugle.tw/realtime/v0/intraday/';
        $parameter = '?symbolId='. $symbolId . '&apiToken=' . $apiToken;
        
        $messageBuilder = new TextMessageBuilder('請輸入【股票代碼】');

        if($text == '股票'){
            $messageBuilder =  new RawMessageBuilder(
                [
                    'type' => 'flex',
                    'altText' => '請問要選擇哪個股票資訊?',
                    'contents' => [
                        'type'=> 'bubble',
                        'hero'=> [
                            'type'=> 'image',
                            'url'=> 'https://i.imgur.com/Yx8s4WL.png',
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
                                    'text'=>'請問要選擇哪個股票資訊?',
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
                                        'label' => '線圖',
                                        'text'=> '線圖',
                                    ],
                                    'height'=>'sm'
                                ],
                                [
                                    'type'=>'button',
                                    'action'=>[
                                        'type'=>'message',
                                        'label' => '統計資訊',
                                        'text'=> '統計資訊',
                                    ],
                                    'height'=>'sm'
                                ],
                                [
                                    'type'=>'button',
                                    'action'=>[
                                        'type'=>'message',
                                        'label' => '當日資訊',
                                        'text'=> '當日資訊',
                                    ],
                                    'height'=>'sm'
                                ],
                                [
                                    'type'=>'button',
                                    'action'=>[
                                        'type'=>'message',
                                        'label' => '當日成交資訊',
                                        'text'=> '當日成交資訊',
                                    ],
                                    'height'=>'sm'
                                ],
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
        }else if($text == '線圖'){
            $url = $fugleUrl . 'chart' . $parameter;
            $Guzzleclient = new \GuzzleHttp\Client();
            $response = $Guzzleclient->get($url);
            $json = json_decode($response->getBody());
            $datas = $json->data->chart;
            $deatsKeys = array_keys(get_object_vars($datas));

            if(count($deatsKeys) > 0){
                $lastKey = $deatsKeys[count($deatsKeys) - 1];
                $lastDeatlsData = $datas->$lastKey;
                $charts = Config::get('chart');

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

                foreach($charts as $chart){
                    foreach($chart as $key => $value){
                        $chartValue = $lastDeatlsData->$key;
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
                                    'text'=> (string)$chartValue,
                                    'size'=> 'sm',
                                    'color'=> '#111111',
                                    'align'=> 'end'
                                ]
                            ]
                        ];
                        array_push($messageArray, $message);

                        if(count($messageArray) == 1){
                            array_push($messageArray[1], [
                                'margin'=> 'xxl',
                                'spacing'=> 'sm',
                            ]);
                        }
                    }
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
            }else{
                $messageBuilder = new TextMessageBuilder('目前股票尚未開盤');
            }
            

        }else if($text == '統計資訊'){
            $messageBuilder = new TextMessageBuilder('');
        }else if($text == '當日資訊'){
            $url = $fugleUrl . 'meta' . $parameter;
            $Guzzleclient = new \GuzzleHttp\Client();
            $response = $Guzzleclient->get($url);
            $json = json_decode($response->getBody());
            $datas = get_object_vars($json->data->meta);

            if(count($datas) > 0){
                $metas = Config::get('meta');

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
    
                foreach($metas as $meta){
                    foreach($meta as $key => $value){
                        $fugleValue = $datas[$key];
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
                        array_push($messageArray, $message);
                    }
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
            }else{
                $messageBuilder = new TextMessageBuilder('目前股票尚未開盤');
            }
        }else if($text == '當日成交資訊'){
            $url = $fugleUrl . 'dealts' . $parameter . '&limit=1';
            $Guzzleclient = new \GuzzleHttp\Client();
            $response = $Guzzleclient->get($url);
            $json = json_decode($response->getBody());
            $datas = $json->data->dealts;
            
            
            if(count($datas) > 0){
                $dealts = Config::get('dealts');

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
    
                foreach($dealts as $dealt){
                    foreach($dealt as $key => $value){
                        $fugleValue = $datas[0]->$key;
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
                        array_push($messageArray, $message);
                    }
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
            }else{
                $messageBuilder = new TextMessageBuilder('目前股票尚未開盤');
            }
        }

        $response = $this->bot->replyMessage($replyToken, $messageBuilder);

        if ($response->isSucceeded()) {
            echo 'Succeeded!';
            return;
        }
    }

    public function getStockTemplateData(Array $datas, Array $infos){
        foreach($dealts as $dealt){
            foreach($dealt as $key => $value){
            }
        }
    }
}
