<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Config;

class WeatherController extends Controller
{
    public function getWeatherApiData(){
        $client = new \GuzzleHttp\Client();
        $token = 'CWB-96170F0C-F4B6-4626-B946-D6892DA6D584';
        $weatherUrl = 'https://opendata.cwb.gov.tw/api/';
        $weathers = Config::get('weather');

        $data = [];
        foreach($weathers as $key => $value){
            $url = $weatherUrl . $value . '?Authorization=' . $token;
            $response = $client->get($url);
            $json = json_decode($response->getBody());
            array_push($data, $json);
        }

        dd($data);
    }
}
