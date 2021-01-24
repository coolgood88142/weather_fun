<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;

class LineController extends Controller
{
    private $client;
    private $bot;
    private $channel_access_token;
    private $channel_secret;

    public function __construct()
    {
        $this->channel_access_token = env('CHANNEL_ACCESS_TOKEN');
        $this->channel_secret = env('CHANNEL_SECRET');

        $httpClient = new CurlHTTPClient($this->channel_access_token);
        $this->bot = new LINEBot($httpClient, ['channelSecret' => $this->channel_secret]);
        $this->client = $httpClient;
    }

    public function webhook(Request $request)
    {
        $bot = $this->bot;
        Log::info($request);
        $signature = $request->header(\LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE);
        $body = $request->getContent();

        try {
            $events = $bot->parseEventRequest($body, $signature);
            Log::info($events);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($body);

        $response = $bot->replyText($request->events[0]->getReplyToken(), $textMessageBuilder);
        if ($response->isSucceeded()) {
            return;
        }
    }
}