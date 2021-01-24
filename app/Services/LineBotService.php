<?php
namespace App\Services;

use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class LineBotService
{
    /** @var LINEBot */
    private $lineBot;
    private $lineUserId;

    public function __construct($lineUserId)
    {
        $this->lineUserId = $lineUserId;
        $this->lineBot = app(LINEBot::class);
    }

    public function fake()
    {
    }

    /**
     * @param TemplateMessageBuilder|string $content
     * @return Response
     */
    public function pushMessage($content)
    {
        if (is_string($content)) {
            $content = new TextMessageBuilder($content);
        }

        $response = $this->lineBot->pushMessage($this->lineUserId, $content);

        if ($response->isSucceeded()) {
            return;
        }
    }
}