<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\SignatureValidator;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use Exception;
class LineBotController extends Controller
{
    public function getMessageWeather(Request $request){
        $accessToken = 'b9z+6EeBQ6i4D9iHJKTgy4oMznvpwYJqllWaigLXXzhm7xx8/WYTsa9zLyyj5Rim2r2m+T/axKZaoRMVeaK+L7ME8RFRYr9xMRhXsh2H+nKazDoe0VP6TikA8B9SYXTRFbXuHAGbA/vIWA0+i43kdAdB04t89/1O/w1cDnyilFU=';
        $channelSecret = 'b9d3f9583e6f74c1a9a276bb7bd17ae2';

        // $signature = $request->headers->get(HTTPHeader::LINE_SIGNATURE);
        // if (!SignatureValidator::validateSignature($request->getContent(), $channelSecret, $signature)) {
           
        //     return;
        // }

        // $httpClient = new CurlHTTPClient ($accessToken);
        // $lineBot = new LINEBot($httpClient, ['channelSecret' => $channelSecret]);

        // try {
          
        //     $events = $lineBot->parseEventRequest($request->getContent(), $signature);

        //     foreach ($events as $event) {
                
        //         $replyToken = $event->getReplyToken();
        //         $text = $event->getText();// 得到使用者輸入
        //         $lineBot->replyText($replyToken, $text);// 回復使用者輸入
        //         //$textMessage = new TextMessageBuilder("你好");
        //       //  $lineBot->replyMessage($replyToken, $textMessage);
        //     }
        // } catch (Exception $e) {
           
        //     return;
        // }

        // return;

        $httpClient = new CurlHTTPClient($accessToken);
        $bot = new LINEBot($httpClient, ['channelSecret' => $channelSecret]);
        $text = $request->events[0]['message']['text'];
        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($text);

        $response = $bot->replyMessage('<reply token>', $textMessageBuilder);
        if ($response->isSucceeded()) {
            echo 'Succeeded!';
            return;
        }

        // Failed
        echo $response->getHTTPStatus() . ' ' . $response->getRawBody();

    }
}
