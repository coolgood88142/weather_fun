<?php

namespace App\Providers;

use App\Services\LineBotService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Schema::defaultStringLength(191);
        $this->lineBotRegister();
        $this->lineBotServiceRegister();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    private function lineBotRegister()
    {
        $this->app->singleton(LINEBot::class, function () {
            $httpClient = new CurlHTTPClient(env('LINEBOT_TOKEN'));
            return new LINEBot($httpClient, ['channelSecret' => env('LINEBOT_SECRET')]);
        });
    }

    private function lineBotServiceRegister()
    {
        $this->app->singleton(LineBotService::class, function () {
            return new LineBotService(env('LINE_USER_ID'));
        });
    }
}
