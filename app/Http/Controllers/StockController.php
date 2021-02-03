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
        $messageBuilder = new TextMessageBuilder('請輸入【股票】');

        if($text == '股票'){
            $messageBuilder =  new RawMessageBuilder(
                [
                    'type' => 'flex',
                    'altText' => '請問要選擇哪個股票資訊?',
                    'contents' => [
                        'type'=> 'bubble',
                        'hero'=> [
                            'type'=> 'image',
                            'url'=> 'https://i.imgur.com/FMmIbdw.jpg',
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

        }

        $response = $this->bot->replyMessage($replyToken, $messageBuilder);

        if ($response->isSucceeded()) {
            echo 'Succeeded!';
            return;
        }
    }
}
