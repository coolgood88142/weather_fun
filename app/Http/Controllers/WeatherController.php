<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Services\ElasticService;
use Config;

class WeatherController extends Controller
{
    protected $elasticService;

    public function __construct(ElasticService $elasticService)
    {
        $this->elasticService = $elasticService;
    }

    public function getWeatherApiData(){
        $client = new \GuzzleHttp\Client();
        $token = 'CWB-96170F0C-F4B6-4626-B946-D6892DA6D584';
        $weatherUrl = 'https://opendata.cwb.gov.tw/api';
        $cityData = Config::get('city');
        $weathers = Config::get('weather');

        $data = [];
        
        foreach($cityData as $city){
            foreach($city as $key => $value){
                $weatherArray = [];
                foreach($weathers as $weather){
                    $url = $weatherUrl . $weather . '?Authorization=' . $token . '&locationName=' . $value;
                    $response = $client->get($url);
                    $json = json_decode($response->getBody());
                    array_push($weatherArray, $json);
                }
                array_push($data, $weatherArray);
            }
        }

        dd($data);
    }

    public function getArticlesApiData(){
        $articlesArray = [];
        $client = new \GuzzleHttp\Client();
        $url = 'https://smiletaiwan.cw.com.tw/city/2';
        $response = $client->get($url);
        $json = json_decode($response->getBody());
        // array_push($data, $json);
        // $cityCount = 23;
        //     for($i = 1; $i < $cityCount; $i++){
        //         $url = 'https://smiletaiwan.cw.com.tw/city/' . $i;
        //         $response = $client->get($url);
        //         $json = json_decode($response->getBody());
        //         array_push($articlesArray, $json);
        //     }

            dd($json);

        foreach($articlesArray as $article){
            $params =[
                'index' => 'elastic' . date('YmdHms'),
                'type' => 'data'
            ];
    
            //這裡要放文章資料表的欄位
            $params['body'] = [
                'id' => 'id',
                'city' => 'city_id',
                'cityName' => 'city_name',
                'title' => 'title',
                'author' => 'author',
                'createdDate' => 'created_at',
                'content' => 'content'
            ];
    
            $conn = $this->elasticService->connElastic();
            $this->elasticService->createElastic($conn, $params);
        }
    }
}
