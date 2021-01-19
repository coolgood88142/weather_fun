<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function saveWeatherApiData(){
        $client = new \GuzzleHttp\Client();
        $token = 'CWB-96170F0C-F4B6-4626-B946-D6892DA6D584';
        $weatherUrl = 'https://opendata.cwb.gov.tw/api';
        $cityData = Config::get('city');
        $weathers = Config::get('weather');
        $automatic = Config::get('automatic');

        $dataArray = [];
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
                $temperature = '';
                $probabilityOfPrecipitation = '';
                $windDirection = '';
                $anemometer = '';
                $relativeHumidity = '';
                $barometricPressure = '';
                $ultravioletIndex = '';
                $ozoneYearAvg = '';
                $seismicity = '';
                $smallAreaSeismicity ='';
                $acidRainPh = 0;
                $sunrise = '';
                $moonrise = '';
                $nextWeekWeather = '';
                
                foreach($weathers as $weather){
                    

                    $locationName = urlencode($key);
                    if($count == 2 || $count == 3 || $count == 4){
                        $locationName = urlencode($automatic[$key][$count-2]);
                    }
                    $url = $weatherUrl . $weather . '?Authorization=' . $token . '&locationName=' . $locationName;
                    
                    $response = $client->get($url);
                    $weatherData = json_decode($response->getBody())->records;
                    if(isset($weatherData)){
                        if($count == 0){
                            $mint = $weatherData->location[0]->weatherElement[2]->time[$type]->parameter->parameterName;
                            $maxt = $weatherData->location[0]->weatherElement[4]->time[$type]->parameter->parameterName;
                            $temperature = $mint . ' - ' . $maxt;
                            $probabilityOfPrecipitation = $weatherData->location[0]->weatherElement[1]->time[$type]->parameter->parameterName;
                        }

                        if($count == 1){
                            $nextWeekWeather = $weatherData->locations[0]->location[0]->weatherElement[10]->time[$type]->elementValue[0]->value;
                        }
    
                        if($count == 2){
                            $windDirection = $weatherData->location[0]->weatherElement[1]->elementValue;
                            $anemometer = $weatherData->location[0]->weatherElement[2]->elementValue;
                            $relativeHumidity = $weatherData->location[0]->weatherElement[4]->elementValue;
                            $barometricPressure = $weatherData->location[0]->weatherElement[5]->elementValue;
                        }

                        if($count == 5){
                            $rainPh = $weatherData->weatherElement[0]->location;
                            $length = count($rainPh);

                            if($length > 0){
                                $acidRainPh = $rainPh.value;
                            }
                        }
    
                        if($count == 6){
                            $ultravioletIndex = (String)$weatherData->weatherElement->location[0]->value;
                        }
    
                        if($count == 7){
                            $ozoneYearAvg = $weatherData->location->weatherElement[0]->time[29]->elementValue;
                        }
    
                        if($count == 8){
                            $seismicity = $weatherData->earthquake[0]->reportContent;
                        }
    
                        if($count == 9){
                            $smallAreaSeismicity = $weatherData->earthquake[0]->reportContent;
                        }

                        if($count == 11){
                            $sunrise = $weatherData->note;
                        }

                        if($count == 12){
                            $moonrise = $weatherData->note;
                        }
                        
                        $count++;
                    }
                }

                $data = [
                    'city' => $key, 'temperature' => $temperature, 
                    'probability_of_precipitation' => $probabilityOfPrecipitation, 'wind_direction' =>$windDirection,
                    'anemometer' => $anemometer, 'barometric_pressure' => $barometricPressure,
                    'relative_humidity' => $relativeHumidity, 'ultraviolet_index' => $ultravioletIndex,
                    'seismicity' => $seismicity, 'small_area_seismicity' => $smallAreaSeismicity,
                    'acid_rain_ph' => $acidRainPh, 'sunrise' => $sunrise, 'moonrise' => $moonrise,
                    'ozone_year_avg' => $ozoneYearAvg, 'next_week_weather' => $nextWeekWeather,
                ];

                array_push($dataArray, $data);
            }
        }

        try {
            $db = DB::table('weather')->insert($dataArray);

        } catch (Exception $e) {
            $status = 'error';
            $message = '新增失敗!';
            dd($e);
        }
        
    }

    public function getWeatherData(){
        $db = DB::table('weather')->get();
        return view('weather', [
            'taiwanData' => $db
        ]);
    }

    public function deleteWeatherData(){
        $db = DB::table('weather')->delete();
    }



    public function getWeatherApiData(){
        $client = new \GuzzleHttp\Client();
        $token = 'CWB-96170F0C-F4B6-4626-B946-D6892DA6D584';
        $weatherUrl = 'https://opendata.cwb.gov.tw/api';
        $cityData = Config::get('city');
        $weathers = Config::get('weather');
        $automatic = Config::get('automatic');

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
                    if($count == 2 || $count == 3 || $count == 4){
                        $locationName = urlencode($automatic[$key][$count-2]);
                    }
                    $url = $weatherUrl . $weather . '?Authorization=' . $token . '&locationName=' . $locationName;
                    
                    $response = $client->get($url);
                    $json = json_decode($response->getBody());
                    array_push($weatherArray, $json->records);
                    $count++;
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
                            
                            array_push($articlesArray, $data);
                        }
                    }else{
                        $attractionsUrl = $url . $value;
                        $content = $this->crawlerService->getOriginalData($attractionsUrl);
                        $isPagination = $content->matches('div.apus-pagination');

                        if($isPagination){
                            $lastNum = $content->filter('a.page-numbers')->last()->text();
                            $lastNum = (int)$lastNum;
                            foreach($lastNum as $num){
                                $numUrl = $attractionsUrl . '\/page\/' . $num;
                                $pageContent = $this->crawlerService->getOriginalData($numUrl);
                                $trips = $pageContent->filter('div.layout-blog style-grid > div.col-md-6');
                                foreach($trips as $trip){
                                    $data = $this->getCrawlerAttractionsData($trip);
    
                                    // $data = (object) array('date'=> $date, 'url'=> $tripUrl, 'title' => $entrytitle, 'tag' => $tag);
                                    array_push($articlesArray, $data);
                                }
                            }
                        }else{
                            $arrat = [];
                            $trips = $content->filter('article')->each(function (Crawler $node, $i) {
                                return $node;
                            });

                            dd($trips);
                            // $trips = $content->filter('div.layout-blog style-grid > div')->nextAll();
                            
                            dd($arrat);
                            foreach($trips as $trip){
                                dd($trip);
                            }
                        }
                    }
                }
            }
        }

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

    public function getCrawlerAttractionsData(Crawler $crawler, Array $articlesArray){
        foreach($trips as $trip){
            $date = $crawler->filter('span.date')->first()->text();
            $title = $crawler->filter('h4.entry-title > a')->first();
            $tripUrl = $title->getUri();
            $entrytitle = $title->text();
            $categorys = $crawler->filter('span.category > a');
            $tag = '';
            foreach($categorys as $category){
                $tag = $tag . $category->text() . '、';
            }
        
            $data = (object) array('date'=> $date, 'url'=> $tripUrl, 'title' => $entrytitle, 'tag' => $tag);
            dd($data);
        }
        return $articlesArray;
    }
}
