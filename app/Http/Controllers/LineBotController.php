<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LineBotController extends Controller
{
    public function getMessageWeather(){
        $accessToken = 'b9z+6EeBQ6i4D9iHJKTgy4oMznvpwYJqllWaigLXXzhm7xx8/WYTsa9zLyyj5Rim2r2m+T/axKZaoRMVeaK+L7ME8RFRYr9xMRhXsh2H+nKazDoe0VP6TikA8B9SYXTRFbXuHAGbA/vIWA0+i43kdAdB04t89/1O/w1cDnyilFU=';
        $channelSecret = 'b9d3f9583e6f74c1a9a276bb7bd17ae2';

        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($accessToken);
        $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $channelSecret ]);
        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('hello');
        // dd($textMessageBuilder);
        $response = $bot->replyMessage('<reply token>', $textMessageBuilder);
        if ($response->isSucceeded()) {
            echo 'Succeeded!';
            return;
        }

        // Failed
        echo $response->getHTTPStatus() . ' ' . $response->getRawBody();

    }
}
