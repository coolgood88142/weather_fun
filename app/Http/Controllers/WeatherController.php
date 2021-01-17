<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
// use App\Services\ElasticService;
use App\Services\CrawlerService;
use Carbon\Carbon;
use Config;

class WeatherController extends Controller
{
    // protected $elasticService;
    // private $client;
    private $crawlerService;

    public function __construct(CrawlerService $crawlerService)
    {
        // $this->elasticService = $elasticService;
        $this->crawlerService = $crawlerService;
        // $this->client = app(Client::class);
    }

    public function getWeatherApiData(){
        $client = new \GuzzleHttp\Client();
        $token = 'CWB-96170F0C-F4B6-4626-B946-D6892DA6D584';
        $weatherUrl = 'https://opendata.cwb.gov.tw/api';
        $cityData = Config::get('city');
        $weathers = Config::get('weather');

        $data = [];
        $startTime = 6;
        $endTime = 18;
        $today = Carbon::now()->timezone('Asia/Shanghai');
        $hour = (int)$today->format('H');
        $type = 0;

        if($hour > $endTime){
            $type = 2;
        }else if($hour <= $startTime){ 
            $type = 1;
        }

        foreach($cityData as $city){
            foreach($city as $key => $value){
                $weatherArray = [];
                $count = 0;
                foreach($weathers as $weather){
                    $locationName = urlencode($key);
                    $url = $weatherUrl . $weather . '?Authorization=' . $token . '&locationName=' . $locationName;
                    $response = $client->get($url);
                    $json = json_decode($response->getBody());
                    array_push($weatherArray, $json->records);
                }

                $name = (object) array('ch'=> $key, 'en'=> $value, 'type' => $type);

                array_push($weatherArray, $name);
                array_push($data, $weatherArray);
            }
        }

        return view('weather', [
            'taiwanData' => $data
        ]);
    }

    public function getArticlesApiData(){
        $articlesArray = [];
        $url = 'https://www.welcometw.com/category/';
        $attractionsData = Config::get('attractions');

        foreach($attractionsData as $attractions){
            foreach($attractions as $key => $value){
                if($value != ''){
                    if(is_array($value)){
                        foreach($value as $city){
                            $attractionsUrl = $url . $city;
                            $content = $this->crawlerService->getOriginalData($attractionsUrl);
                            $trip = $pageContent->filter('div.layout-blog style-grid > div.col-md-6');
                            $date = $trip->filter('span.date')->first()->text();
                            $title = $trip->filter('h4.entry-title > a')->first();
                            $tripUrl = $title->getUri();
                            $entrytitle = $title->text();
                            $categorys = $trip->filter('span.category > a');
                            $tag = '';
                            foreach($categorys as $category){
                                $tag = $tag . $category->text() . '、';
                            }
    
                            $data = (object) array('date'=> $date, 'url'=> $tripUrl, 'title' => $entrytitle, 'tag' => $tag);
                            array_push($articlesArray, $data);
                        }
                    }else{
                        $attractionsUrl = $url . $value;
                        $content = $this->crawlerService->getOriginalData($attractionsUrl)->html();
                        $content = new Crawler($content);
                        dd($content->filter('a.page-numbers')->html());
                        // $lastNum = $content->filter('a.page-numbers')->last()->text();
                        
    
                        if($lastNum != null){
                            $lastNum = (int)$lastNum;
                            foreach($lastNum as $num){
                                $numUrl = $attractionsUrl . '\/page\/' . $num;
                                $pageContent = $this->crawlerService->getOriginalData($numUrl)->html();
                                $pageContent = new Crawler($pageContent);
                                $trips = $pageContent->filter('div.layout-blog style-grid > div.col-md-6');
                                foreach($trips as $trip){
                                    $date = $trip->filter('span.date')->first()->text();
                                    $title = $trip->filter('h4.entry-title > a')->first();
                                    $tripUrl = $title->getUri();
                                    $entrytitle = $title->text();
                                    $categorys = $trip->filter('span.category > a');
                                    $tag = '';
                                    foreach($categorys as $category){
                                        $tag = $tag . $category->text() . '、';
                                    }
    
                                    $data = (object) array('date'=> $date, 'url'=> $tripUrl, 'title' => $entrytitle, 'tag' => $tag);
                                    array_push($articlesArray, $data);
                                }
                            }
                            
                        }
                    }
                }
            }
        }

        dd($articlesArray);
        // $content = $this->crawlerService->getOriginalData($url);
        // dd($content->html());

        // $response = $client->get($url);
        // $json = json_decode($response->getBody());
        // array_push($data, $json);
        // $cityCount = 23;
        //     for($i = 1; $i < $cityCount; $i++){
        //         $url = 'https://smiletaiwan.cw.com.tw/city/' . $i;
        //         $response = $client->get($url);
        //         $json = json_decode($response->getBody());
        //         array_push($articlesArray, $json);
        //     }

        dd($crawler);

        //這段是要寫到elesastic
        // foreach($articlesArray as $article){
        //     $params =[
        //         'index' => 'elastic' . date('YmdHms'),
        //         'type' => 'data'
        //     ];
    
        //     //這裡要放文章資料表的欄位
        //     $params['body'] = [
        //         'id' => 'id',
        //         'city' => 'city_id',
        //         'cityName' => 'city_name',
        //         'title' => 'title',
        //         'author' => 'author',
        //         'createdDate' => 'created_at',
        //         'content' => 'content'
        //     ];
    
        //     $conn = $this->elasticService->connElastic();
        //     $this->elasticService->createElastic($conn, $params);
        // }
    }
}
