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
use LINE\LINEBot\Constant\Flex\ComponentMargin;
use LINE\LINEBot\Constant\Flex\ComponentLayout;
use LINE\LINEBot\Constant\Flex\ComponentSpacing;
use LINE\LINEBot\Constant\Flex\ComponentButtonHeight;
use LINE\LINEBot\SignatureValidator;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\RawMessageBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\BubbleContainerBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\TextComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ButtonComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\SpacerComponentBuilder;
use LINE\LINEBot\MessageBuilder\FlexMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\QuickReplyBuilder\QuickReplyMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\Uri\AltUriBuilder;
use LINE\LINEBot\QuickReplyBuilder\ButtonBuilder\QuickReplyButtonBuilder;
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
                $temperature_text = $today . ' 06:00 - 18:00';
                if($time_period == 0){
                    $message = $message . $city . '明天氣候：' . "\n";
                }else if($time_period == 1){
                    $temperature_text = $today . ' 18:00 - ' . $tomorrow . ' 06:00';
                }else if($time_period == 2){ 
                    $temperature_text = $tomorrow . ' 06:00 - 18:00';
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
        Log::info($request->all());
        $replyToken = $request->events[0]['replyToken'];
        $messageBuilder =  new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('請輸入【氣候】');
        if(isset($request->events[0]['postback'])){
            $messageBuilder =  new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($request->events[0]['postback']['data']);
        }else{
            $text = $request->events[0]['message']['text'];
            $cityData = Config::get('city');
            $len = mb_strlen($text, 'utf-8');
            $text = str_replace('台','臺',$text);
            
    
            if($len = 3){
                // $text = mb_substr($text , 0 , 3, 'utf-8');
                // $messageBuilder = null;
                if($text == '氣候'){
                    $cityText = '請輸入要查詢的縣市：' . "\n";
                    foreach($cityData as $city){
                        $cityText = $cityText . $city . "\n";
                    }
        
                    $cityText = rtrim($cityText, "\n");
                    $messageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($cityText);
                }else if(in_array($text, $cityData)){
                    // $Flex1 = FlexMessageBuilder::builder()
                    // ->setAltText('alt test')
                    // ->setContents(
                    //     BubbleContainerBuilder::builder()
                    //         ->setBody(
                    //             BoxComponentBuilder::builder()
                    //                 ->setLayout(ComponentLayout::VERTICAL)
                    //                 ->setContents([
                    //                     $this->sendMessageWeather(0, $text)
                    //                 ])
                    //         )
                    // );
    
                    // $Flex2 = FlexMessageBuilder::builder()
                    // ->setAltText('alt test')
                    // ->setContents(
                    //     BubbleContainerBuilder::builder()
                    //         ->setBody(
                    //             BoxComponentBuilder::builder()
                    //                 ->setLayout(ComponentLayout::VERTICAL)
                    //                 ->setContents([
                    //                     $this->sendMessageWeather(1, $text)
                    //                 ])
                    //         )
                    // );
    
                    // $messageBuilder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder(
                    //     '詢問'. $text .'的氣候',
                    //     new ConfirmTemplateBuilder('請問要選擇哪一天?', [
                    //         new MessageTemplateActionBuilder('今天', $this->bot->replyMessage($replyToken, $Flex1)),
                    //         new MessageTemplateActionBuilder('明天', $this->bot->replyMessage($replyToken, $Flex2)),
                    //     ])
                    // );
                    
    
                    // $messageBuilder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder(
                    //     FlexMessageBuilder::builder()
                    // ->setAltText('alt test')
                    // ->setContents(
                    //     BubbleContainerBuilder::builder()
                    //         ->setBody(
                    //             BoxComponentBuilder::builder()
                    //                 ->setLayout(ComponentLayout::VERTICAL)
                    //                 ->setContents([
                    //                     new MessageTemplateActionBuilder('今天', '今天'),
                    //                     new MessageTemplateActionBuilder('明天', '明天'),
                    //                 ])
                    //         )
                    // )
                    // ->setQuickReply(
                    //     new QuickReplyMessageBuilder([
                    //         new QuickReplyButtonBuilder(
                    //             new MessageTemplateActionBuilder('reply1', 'Reply1')
                    //         ),
                    //         new QuickReplyButtonBuilder(
                    //             new MessageTemplateActionBuilder('reply2', 'Reply2')
                    //         )
                    //     ])
                    //         ));
    
                    // $messageBuilder = FlexMessageBuilder::builder()
                    // ->setAltText('alt test')
                    // ->setContents(
                    //     BubbleContainerBuilder::builder()
                    //         ->setBody(
                    //             BoxComponentBuilder::builder()
                    //                 ->setLayout(ComponentLayout::VERTICAL)
                    //                 ->setContents([
                    //                     new TextComponentBuilder('今天'),
                    //                     new TextComponentBuilder('明天')
                    //                 ])
                    //         )
                    // );
    
    
    
                    // $messageBuilder = FlexMessageBuilder::builder()
                    // ->setAltText('AltText')
                    // ->setContents(BubbleContainerBuilder::builder()
                    //     ->setBody(BoxComponentBuilder::builder()
                    //         ->setLayout(ComponentLayout::VERTICAL)
                    //         ->setContents([
                    //             TextComponentBuilder::builder()
                    //                 ->setText("Are you sure?")
                    //                 ->setMargin(ComponentMargin::MD)
                    //         ])
                    //         ->setAction(new UriTemplateActionBuilder("View detail", "http://linecorp.com/", new AltUriBuilder("[object Object]"))))
                    //     ->setFooter(BoxComponentBuilder::builder()
                    //         ->setLayout(ComponentLayout::HORIZONTAL)
                    //         ->setSpacing(ComponentSpacing::SM)
                    //         ->setFlex(0)
                    //         ->setContents([
                    //             ButtonComponentBuilder::builder()
                    //                 ->setHeight(ComponentButtonHeight::SM)
                    //                 ->setAction(new MessageTemplateActionBuilder("Yes")),
                    //             ButtonComponentBuilder::builder()
                    //                 ->setHeight(ComponentButtonHeight::SM)
                    //                 ->setAction(new MessageTemplateActionBuilder("No"))
                    //         ]))
                    //     ->setStyles(new BlockStyleBuilder())
                    // );
    
                    $fix1 = $this->sendMessageWeather(0, $text);
                    $fix2 = $this->sendMessageWeather(1, $text);
    
                    $messageBuilder =  new RawMessageBuilder(
                        [
                            'type' => 'flex',
                            'altText' => 'alt test',
                            'contents' => [
                                'type'=> 'bubble',
                                'body'=> [
                                    'type'=> 'box',
                                    'layout'=> 'vertical',
                                    'contents'=> [
                                        [
                                            'type'=>'text',
                                            'text'=>'請問要選擇哪一天?',
                                            'margin'=>'md'
                                        ],
                                        [
                                            'type'=>'spacer'
                                        ],
                                    ],
                                ],
                                'footer'=> [
                                    'type'=>'box',
                                    'layout'=>'horizontal',
                                    'spacing'=>'sm',
                                    'contents'=> [
                                        [
                                            'type'=>'button',
                                            'action'=>[
                                                'type'=>'postback',
                                                'label' => '今天',
                                                'text'=> '今天',
                                                'data' => $fix1,
                                            ],
                                            'height'=>'sm'
                                        ],
                                        [
                                            'type'=>'button',
                                            'action'=>[
                                                'type'=>'postback',
                                                'label' => '明天',
                                                'text'=> '明天',
                                                'data' => $fix2,
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
                }
            }
        }
       

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
}
