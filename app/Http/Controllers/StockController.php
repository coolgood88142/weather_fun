<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DomCrawler\Crawler;
use App\Services\LineBotService;
use App\Services\CrawlerService;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\RawMessageBuilder;
use Carbon\Carbon;
use Config;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;
use App\Models\Stocks;
use App\Repositories\StocksRepository;
use \Firebase\JWT\JWT;

class StockController extends Controller
{
    private $lineBotService;
    private $crawlerService;
    private $client;
    private $bot;
    private $channel_access_token;
    private $channel_secret;
    private $stockRepo;

    public function __construct(StocksRepository $stockRepo)
    {
        $this->lineBotService = app(LineBotService::class);
        $this->crawlerService = app(CrawlerService::class);

        $this->channel_access_token = env('STOCK_CHANNEL_ACCESS_TOKEN');
        $this->channel_secret = env('STOCK_CHANNEL_SECRET');

        $httpClient = new CurlHTTPClient($this->channel_access_token);
        $this->bot = new LINEBot($httpClient, ['channelSecret' => $this->channel_secret]);
        $this->client = $httpClient;
        $this->stockRepo = $stockRepo;
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
                        $message = $this->getStockHorizontalTemplate((string)$value, (string)$chartValue);
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
                            'size'=> 'mega',
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
            $url = $fugleUrl . 'quote' . $parameter;
            $Guzzleclient = new \GuzzleHttp\Client();
            $response = $Guzzleclient->get($url);
            $json = json_decode($response->getBody());
            $datas = get_object_vars($json->data->quote);

            if(count($datas) > 0){
                $quotes = Config::get('quote');

                $messageArray1 = [
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

                $messageArray2 = [
                    
                ];

                

                foreach($quotes as $quote){
                    foreach($quote as $key => $value){
                        $fugleValue = '';
                        $message = [];
                        if(end($quotes) != $quote){
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
                                                    'type'=> 'box',
                                                    'layout'=> 'horizontal',
                                                    'contents'=> [
                                                        [
                                                            'type'=> 'text',
                                                            'text'=> 'info',
                                                            'size'=> 'md',
                                                            'color'=> '#555555',
                                                        ]
                                                    ]
                                                ],
                                                [
                                                    'type'=> 'separator',
                                                    'margin'=> 'none',
                                                ]
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
                                                ],
                                                [
                                                    'type'=> 'text',
                                                    'text'=> (string)$datas[$firstKey]->$key,
                                                    'size'=> 'sm',
                                                    'color'=> '#111111',
                                                    'align'=> 'end'
                                                ]
                                            ]
                                        ];
        
                                        array_push($message['contents'][1]['contents'], $keyMessage);
                                    }
                                }
                                    
                            }else{
                                $fugleValue = $datas[$key]; 
                                if(is_bool($fugleValue)){
                                    if($fugleValue){
                                        $fugleValue = '是';
                                    }else{
                                        $fugleValue = '否';
                                    }
                                }else if(is_numeric($fugleValue)){
                                    $fugleValue = '$' . $fugleValue;
                                }else if($fugleValue == null){
                                    $fugleValue = '無';
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
                            array_push($messageArray1, $message);
                        }else{
                            if(is_array($value)){
                                if(count($value) == 1){
                                    $keys = array_keys($value);
                                    $message = [
                                        'type'=> 'box',
                                        'layout'=> 'horizontal',
                                        'contents'=> [
                                            [
                                                'type'=> 'text',
                                                'text'=> $value[$keys[0]],
                                                'size'=> 'sm',
                                                'color'=> '#555555',
                                            ],
                                            [
                                                'type'=> 'text',
                                                'text'=> $datas['order']->at,
                                                'size'=> 'sm',
                                                'color'=> '#555555',
                                            ],
                                        ]
                                        
                                    ];  
                                }else{
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
                                                        'type'=> 'box',
                                                        'layout'=> 'horizontal',
                                                        'contents'=> [
                                                            [
                                                                'type'=> 'text',
                                                                'text'=> 'info',
                                                                'size'=> 'md',
                                                                'color'=> '#555555',
                                                            ]
                                                        ]
                                                    ],
                                                    [
                                                        'type'=> 'separator',
                                                        'margin'=> 'none',
                                                    ]
                                                ]
                                            ],
                                        ]
                                    ];
            
                                    $sumCount = 0;
                                    foreach($keys as $key){
                                        if(is_numeric($key)){
                                            foreach($value[$key] as $sumKey => $sum){
                                                $keyMessage = [
                                                    'type'=> 'box',
                                                    'layout'=> 'horizontal',
                                                    'contents'=> [
                                                        [
                                                            'type'=> 'text',
                                                            'text'=> (string)$sum,
                                                            'size'=> 'sm',
                                                            'color'=> '#555555',
                                                        ],
                                                        [
                                                            'type'=> 'text',
                                                            'text'=> (string)$datas['order']->$firstKey[$sumCount]->$sumKey,
                                                            'size'=> 'sm',
                                                            'color'=> '#111111',
                                                            'align'=> 'end'
                                                        ]
                                                    ]
                                                ];
                                                array_push($message['contents'][1]['contents'], $keyMessage);
                                            }
                                            $sumCount++;
                                        }
                                    }    
                                }
                            }else{
                                $message = [
                                    'type'=> 'box',
                                    'layout'=> 'horizontal',
                                    'contents'=> [
                                        [
                                            'type'=> 'text',
                                            'text'=> $value,
                                            'size'=> 'sm',
                                            'color'=> '#555555',
                                        ],
                                    ]
                                    
                                ];  
                            }
                            
                            array_push($messageArray2, $message);
                        }
                    }
                }

                $messageBuilder =  new RawMessageBuilder(
                    [
                        'type' => 'flex',
                        'altText' => '華擎線圖',
                        'contents' => [
                            'type' => 'carousel',
                            'contents' => [
                                [
                                    'type'=> 'bubble',
                                    'size'=> 'giga',
                                    'body'=> [
                                        'type'=> 'box',
                                        'layout'=> 'vertical',
                                        'contents'=> $messageArray1
                                    ]
                                ],
                                [
                                    'type'=> 'bubble',
                                    'size'=> 'giga',
                                    'body'=> [
                                        'type'=> 'box',
                                        'layout'=> 'vertical',
                                        'contents'=> $messageArray2
                                    ]
                                ]
                            ]
                            
                        ]
                        
                    ]
                );
            }else{
                $messageBuilder = new TextMessageBuilder('目前股票尚未開盤');
            }
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
                                    'align'=> 'start'
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

                $dealt = end($dealts);
                
                    foreach($dealt as $key => $value){
                        $fugleValue = $datas[0]->$key;
                        if($key == 'at'){
                            $date = (new Carbon($fugleValue))->timezone('Asia/Taipei');
                            $fugleValue = $date->format('Y-m-d h:m');
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

    public function getStockHorizontalTemplate(String $key, String $value){
        $message = [
            'type'=> 'box',
            'layout'=> 'horizontal',
            'contents'=> [
                [
                    'type'=> 'text',
                    'text'=> $key,
                    'size'=> 'sm',
                    'color'=> '#555555',
                    'flex'=> 0
                ],
                [
                    'type'=> 'text',
                    'text'=> $value,
                    'size'=> 'sm',
                    'color'=> '#111111',
                    'align'=> 'end'
                ]
            ]
        ];

        return $message;
    }

    public function getFugleDefaultData(){
        $key = '344'; //key
		$time = time(); //当前时间
       		$token = [
        	'iss' => 'http://www.helloweba.net', //签发者 可选
           	'aud' => 'http://www.helloweba.net', //接收该JWT的一方，可选
           	'iat' => $time, //签发时间
           	'nbf' => $time , //(Not Before)：某个时间点后才能访问，比如设置time+30，表示当前时间30秒后才能使用
           	'exp' => $time+7200, //过期时间,这里设置2个小时
            	'data' => [ //自定义信息，不要定义敏感信息
             		'page' => 'fugle',
            ]
        ];

        $dataArray =[
            'chart' => [
                'time' => [],
                'open' => [],
                'close' => [],
                'high' => [],
                'low' => [],
                'unit' => [],
                'volume' => [],
                'source' => 'https://developer.fugle.tw/realtime/v0/intraday/chart',
            ],
            'dealts' => [
                'at' => [],
                'price' => [],
                'unit' => [],
                'serial' => [],
                'source' => 'https://developer.fugle.tw/realtime/v0/intraday/dealts',
            ],
            'datatable' => [],
            'symbolId' => '',
            'symbolIdMessage' => '',
            'chartMessage' => '',
            'dealtsMessage' => '',
            'bTime' => '',
            'eTime' => '',
            'nowChart' => 1,
            'token' => '/?token=' . (JWT::encode($token, $key)),
        ];
        
        return view('stock', $dataArray);
    }

    public function getFugleApiStockData(Request $request){
        $apiToken = '001ca47f2cf24652cb26f74d97251ab3';
        $symbolId = $request->symbolId;
        $beginTime = (String)$request->begintime;
        $endTime = (String)$request->endtime;
        $nowChart = (int)$request->nowChart;
        $categorys = ['chart', 'dealts'];
        $noData = '此區間無資料';
        $noSymbolId = '無此股票代號';
        $dataArray =[
            'chart' => [
                'time' => [],
                'open' => [],
                'close' => [],
                'high' => [],
                'low' => [],
                'unit' => [],
                'volume' => [],
            ],
            'dealts' => [
                'at' => [],
                'price' => [],
                'unit' => [],
                'serial' => [],
            ],
            'datatable' => [],
            'symbolId' => '',
            'symbolIdMessage' => '',
            'chartMessage' => '',
            'dealtsMessage' => '',
            'bTime' => '',
            'eTime' => '',
            'nowChart' => 1
        ];

        if($beginTime != ''){
            $dataArray['bTime'] = $beginTime;
        }

        if($endTime != ''){
            $dataArray['eTime'] = $endTime;
        }

        if($nowChart != ''){
            $dataArray['nowChart'] = $nowChart;
        }

        if($symbolId != ''){
            $dataArray['symbolId'] = $symbolId;
        }

        try {
            foreach($categorys as $category){
                $fugleUrl = 'https://api.fugle.tw/realtime/v0/intraday/' . $category;
                $parameter = '?symbolId='. $symbolId . '&apiToken=' . $apiToken;
                $url = $fugleUrl . $parameter;

                if($category == 'dealts' && $beginTime == '' && $endTime == ''){
                    $url = $url . '&limit=20';
                }

                $Guzzleclient = new \GuzzleHttp\Client();
                $response = $Guzzleclient->get($url);
                $json = json_decode($response->getBody());
                $datas = $json->data->$category;
                $length = count((array)$datas) - 20 - 1;
                $count = 0;

                foreach($datas as $key => $data){
                    $bTime = '';
                    $eTime = '';

                    if($category == 'chart'){
                        if(($count > $length) || ($beginTime != '' || $endTime != '')){
                            $now = new Carbon($key);
                            $date = $now->timezone('Asia/Taipei');
                            $time = $date->format('H:i');
                            $isAdd = $this->checkDate($date, $beginTime, $endTime);

                            if($isAdd){
                                array_push($dataArray['chart']['time'], $time);
                                array_push($dataArray['chart']['open'], $data->open);
                                array_push($dataArray['chart']['close'], $data->close);
                                array_push($dataArray['chart']['high'], $data->high);
                                array_push($dataArray['chart']['low'], $data->low);
                                array_push($dataArray['chart']['unit'], $data->unit);
                                array_push($dataArray['chart']['volume'], $data->volume);
                            }
                        }
                    }else if($category == 'dealts'){
                        $now = new Carbon($data->at);
                        $date = $now->timezone('Asia/Taipei');
                        $time = $date->format('H:i');
                        $isAdd = $this->checkDate($date, $beginTime, $endTime);    
                        
                        if($isAdd){
                            array_push($dataArray['dealts']['at'], $time);
                            array_push($dataArray['dealts']['price'], $data->price);
                            array_push($dataArray['dealts']['unit'], $data->unit);
                            array_push($dataArray['dealts']['serial'], $data->serial);
                        }
                    }
                    $count++;
                }
            }
        } catch (RequestException $e) {;
            $dataArray['symbolIdMessage'] = $symbolId . $noSymbolId;
            $dataArray['symbolId'] = '';
        }

        if(count($dataArray['dealts']) > 0){
            $dataArray['dealts']['at'] = array_reverse($dataArray['dealts']['at']);
            $dataArray['dealts']['price'] = array_reverse($dataArray['dealts']['price']);
            $dataArray['dealts']['unit'] = array_reverse($dataArray['dealts']['unit']);
            $dataArray['dealts']['serial'] = array_reverse($dataArray['dealts']['serial']);
        }

        if(count($dataArray['chart']['time']) == 0 && ($beginTime != '' || $endTime != '')){
            $dataArray['chartMessage'] = '線圖-' . $noData;
        }

        if(count($dataArray['dealts']['at']) == 0 && ($beginTime != '' || $endTime != '')){
            $dataArray['dealtsMessage'] = '當日成交資訊-' . $noData;
        }

        $dataArray['datatable'] = new DataTableCollectionResource($dataArray['chart']);
        
        // dd($dataArray);

        return $dataArray;
    }

    public function checkDate(Carbon $now, String $begin, String $end){
        $bTime = '';
        $eTime = '';
        $isAdd = true;

        if($begin != ''){
            $begin = new Carbon($begin);
            $bTime = $begin->timezone('Asia/Taipei')->setDate($now->year, $now->month, $now->day)->subHours(8);
        }

        if($end != ''){
            $end = new Carbon($end);
            $eTime = $end->timezone('Asia/Taipei')->setDate($now->year, $now->month, $now->day)->subHours(8);
        }
        
        if($bTime != '' && $eTime != ''){
            if($now->between($bTime, $eTime) == false){
                $isAdd = false;
            }
        }else if($bTime != ''){
            if($bTime->lt($now) == false){
                $isAdd = false;
            }
        }else if($eTime != ''){
            if($eTime->gt($now) == false){
                $isAdd = false;
            }
        }

        return $isAdd;
    }

    public function getStockDataTable(Request $request){
        $apiToken = '001ca47f2cf24652cb26f74d97251ab3';
        $length = $request->input('length');
        $sortBy = $request->input('column');
        $orderBy = $request->input('dir');
        $searchValue = $request->input('search');
        $beginTime = (String)$request->begintime;
        $endTime = (String)$request->endtime;
        $symbolId = $request->symbolId;
        $category = $request->category;

        $fugleUrl = 'https://api.fugle.tw/realtime/v0/intraday/' . $category;
        $parameter = '?symbolId='. $symbolId . '&apiToken=' . $apiToken;
        $url = $fugleUrl . $parameter;

        if($category == 'dealts' && $beginTime == '' && $endTime == ''){
            $url = $url . '&limit=20';
        }

        $dataArray = [];

        $Guzzleclient = new \GuzzleHttp\Client();
        $response = $Guzzleclient->get($url);
        $json = json_decode($response->getBody());
        $datas = $json->data->$category;
        $matchlength = count((array)$datas) - 20 - 1;
        $count = 0;

        if($category == 'meta'){
            $metas = Config::get('meta');
            $datas = get_object_vars($datas);
            $array = [];
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

                    $array['item'] = (String)$value;
                    $array['value'] = (String)$fugleValue;
                    array_push($dataArray, (object)$array);
                }
            }
            
            
        }else if($category == 'quote'){
            $quotes = Config::get('quote');
            $datas = get_object_vars($datas);
            foreach($quotes as $quote){
                foreach($quote as $key => $value){
                    $fugleValue = '';
                    $array = [];
                    if(end($quotes) != $quote){
                        if(is_numeric($key)){
                            $keys = array_keys($value);
                            $firstKey = $keys[0];

                            $array['type'] = (string)$value[$firstKey];
    
                            foreach($keys as $index => $key){
                                if($index != 0){
                                    if($key == 'at'){
                                        $now = new Carbon($datas[$firstKey]->$key);
                                        $date = $now->timezone('Asia/Taipei');
                                        $time = $date->format('H:i');
                                        $array[$key] = $time;
                                    }else{
                                        $array[$key] = (string)$datas[$firstKey]->$key;
                                    }
                                }
                            }
                            array_push($dataArray, (object)$array);
                        }
                        
                    }
                }
            }
        }else{
            foreach($datas as $key => $data){
                if($category == 'chart'){
                    if(($count > $matchlength) || ($beginTime != '' || $endTime != '')){
                        $now = new Carbon($key);
                        $date = $now->timezone('Asia/Taipei');
                        $time = $date->format('H:i');
                        $isAdd = $this->checkDate($date, $beginTime, $endTime);

                        if($isAdd){
                            $array = [
                                'time' => $time,
                                'open' => $data->open,
                                'close' => $data->close,
                                'high' => $data->high,
                                'low' => $data->low,
                                'unit' => $data->unit,
                                'volume' => $data->volume,
                            ];
                            array_push($dataArray, (object)$array);
                        }
                    }
                }else if($category == 'dealts'){
                    $now = new Carbon($data->at);
                    $date = $now->timezone('Asia/Taipei');
                    $time = $date->format('H:i'); 
                    $isAdd = $this->checkDate($date, $beginTime, $endTime);   
                    
                    if($isAdd){
                        $array = [
                            'at' => $time,
                            'price' => $data->price,
                            'unit' => $data->unit,
                            'serial' => $data->serial,
                        ];
                        array_push($dataArray, (object)$array);
                    }
                }
                $count++;
            }
        }
        // dd($dataArray);
        $collection = collect($dataArray);

        if($searchValue != '' && $searchValue != null){
            $collection->search(function ($item, $key) {
                return $item->$key == $searchValue;
            });
        }
        
        if($sortBy != '' && $orderBy != ''){
            if($orderBy == 'asc'){
                $collection = $collection->sortBy($sortBy);
            }else if($orderBy == 'desc'){
                $collection = $collection->sortByDesc($sortBy);
            }
        }

        if($length != ''){
            $collection = $collection->forPage(1, $length);
        }

        return new DataTableCollectionResource($collection->values()->all());
    }
}
