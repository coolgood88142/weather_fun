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
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;

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
        $this->deleteWeatherData();
        $client = new \GuzzleHttp\Client();
        
        $weatherUrl = 'https://opendata.cwb.gov.tw/api';
        $cityData = Config::get('city');
        $weathers = Config::get('weather');
        $automatic = Config::get('automatic');

        $dataArray = [];
        $startTime = 6;
        $endTime = 18;
        $today = Carbon::now()->timezone('Asia/Taipei');
        $hour = (int)$today->format('H');
        $type = 0;

        if($hour > $endTime){
            $type = 2;
        }else if($hour <= $startTime){ 
            $type = 1;
        }

        foreach($cityData as $city){
            $weatherArray = [];
            $acidRainPh = 0;
            $locationName = urlencode($city);
            $temperature = '';
            $probabilityOfPrecipitation = '';

            $weatherForecastData = $this->getCrawlerData($client, $weathers[0], $locationName);
            if(isset($weatherForecastData->location[0])){
                $mint = $weatherForecastData->location[0]->weatherElement[2]->time[$type]->parameter->parameterName;
                $maxt = $weatherForecastData->location[0]->weatherElement[4]->time[$type]->parameter->parameterName;
                $temperature = $mint . '°-' . $maxt . '°';
                $probabilityOfPrecipitation = $weatherForecastData->location[0]->weatherElement[1]->time[$type]->parameter->parameterName;
            }

            $nextWeekWeather = '';
            $townshipWeatherForecastData = $this->getCrawlerData($client, $weathers[1], $locationName);
            if(isset($townshipWeatherForecastData->locations[0])){
                $nextWeekWeather = $townshipWeatherForecastData->locations[0]->location[0]->weatherElement[10]->time[$type]->elementValue[0]->value;
            }

            $windDirection = '';
            $anemometer = '';
            $relativeHumidity = '';
            $barometricPressure = '';
            $automaticWeatherData = $this->getCrawlerData($client, $weathers[2], urlencode($automatic[$city][0]));
            if(isset($automaticWeatherData->location[0])){
                $windDirection = $automaticWeatherData->location[0]->weatherElement[1]->elementValue;
                $anemometer = $automaticWeatherData->location[0]->weatherElement[2]->elementValue;
                $relativeHumidity = $automaticWeatherData->location[0]->weatherElement[4]->elementValue;
                $barometricPressure = $automaticWeatherData->location[0]->weatherElement[5]->elementValue;
            }
            

            $acidRainPh = 0;
            $acidRainData = $this->getCrawlerData($client, $weathers[5], $locationName);
            $rainPh = $acidRainData->weatherElement[0]->location;
            if(count($rainPh) > 0){
                $acidRainPh = $rainPh.value;
            }

            $ultravioletRaysData = $this->getCrawlerData($client, $weathers[6], $locationName);
            $ultravioletIndex = (String)$ultravioletRaysData->weatherElement->location[0]->value;

            $ozoneYearAvgData = $this->getCrawlerData($client, $weathers[7], $locationName);
            $ozoneYearAvg = $ozoneYearAvgData->location->weatherElement[0]->time[29]->elementValue;

            $seismicityData = $this->getCrawlerData($client, $weathers[8], $locationName);
            $seismicity = $seismicityData->earthquake[0]->reportContent;

            $smallAreaSeismicityData = $this->getCrawlerData($client, $weathers[9], $locationName);
            $smallAreaSeismicity = $smallAreaSeismicityData->earthquake[0]->reportContent;

            $sunriseData = $this->getCrawlerData($client, $weathers[11], $locationName);
            $sunrise = $sunriseData->note;

            $moonriseData = $this->getCrawlerData($client, $weathers[12], $locationName);
            $moonrise = $moonriseData->note;

            $data = [
                'city' => $city, 'temperature' => $temperature, 
                'probability_of_precipitation' => $probabilityOfPrecipitation, 'wind_direction' => (int)$windDirection,
                'anemometer' => $anemometer, 'barometric_pressure' => $barometricPressure,
                'relative_humidity' => $relativeHumidity, 'ultraviolet_index' => $ultravioletIndex,
                'seismicity' => $seismicity, 'small_area_seismicity' => $smallAreaSeismicity,
                'acid_rain_ph' => $acidRainPh, 'sunrise' => $sunrise, 'moonrise' => $moonrise,
                'ozone_year_avg' => $ozoneYearAvg, 'next_week_weather' => $nextWeekWeather,
                'created_at' => Carbon::now()->timezone('Asia/Taipei')
            ];

            array_push($dataArray, $data);
        }

        try {
            $db = DB::table('weather_info')->insert($dataArray);

        } catch (Exception $e) {
            $status = 'error';
            $message = '新增失敗!';
            dd($e);
        }
        
    }

    public function saveTomorrowWeatherApiData(){
        $this->deleteWeatherTomorrowData();
        $client = new \GuzzleHttp\Client();
        $cityData = Config::get('city');
        $weathers = Config::get('weather');
        $timePeriodCount = 3;
        $dataArray = [];
        foreach($cityData as $city){
            $locationName = urlencode($city);
            $weatherForecastData = $this->getCrawlerData($client, $weathers[0], $locationName);
                
            for($i = 0; $i < $timePeriodCount; $i++){
                $mint = $weatherForecastData->location[0]->weatherElement[2]->time[$i]->parameter->parameterName;
                $maxt = $weatherForecastData->location[0]->weatherElement[4]->time[$i]->parameter->parameterName;
                $temperature = $mint . '°-' . $maxt . '°';
                $probabilityOfPrecipitation = $weatherForecastData->location[0]->weatherElement[1]->time[$i]->parameter->parameterName;
                    
                $data = [
                    'city' => $city, 'temperature' => $temperature, 
                    'probability_of_precipitation' => $probabilityOfPrecipitation, 'time_period'=> $i,
                    'created_at' => Carbon::now()->timezone('Asia/Taipei'),
                ];

                array_push($dataArray, $data);
            }
        }

        try {
            $db = DB::table('weather_tomorrow')->insert($dataArray);

        } catch (Exception $e) {
            $status = 'error';
            $message = '新增失敗!';
            dd($e);
        }
    }

    public function getCrawlerData(Client $client, String $weather, String $locationName){
        $token = 'CWB-96170F0C-F4B6-4626-B946-D6892DA6D584';
        $weatherUrl = 'https://opendata.cwb.gov.tw/api';
        $url = $weatherUrl . $weather . '?Authorization=' . $token;
        
        if($locationName!=null && $locationName!=''){
            $url = $url . '&locationName=' . $locationName;
        }
        
        $response = $client->get($url);
        $weatherData = json_decode($response->getBody())->records;

        return $weatherData;
    }

    public function getWeatherData(){
        $db = DB::table('weather_info')->get();
        return view('weather', [
            'taiwanData' => $db
        ]);
    }

    public function deleteWeatherData(){
        $db = DB::table('weather_info')->delete();
    }

    public function deleteWeatherTomorrowData(){
        $db = DB::table('weather_tomorrow')->delete();
    }

    public function getWeatherApiData(){
        $client = new \GuzzleHttp\Client();
        $token = 'CWB-96170F0C-F4B6-4626-B946-D6892DA6D584';
        $weatherUrl = 'https://opendata.cwb.gov.tw/api';
        $cityData = Config::get('city');
        $weathers = Config::get('weather');
        $automatic = Config::get('automatic');

        $dataArray = [
            'forecast' => [
                'city' => [],
                'maxT' => [],
                'minT' => [],
                'pop' => [],
            ],
            'observation' => [
                'city' => [],
                'wdir' => [],
                'wdsd' => [],
                'humd' => [],
                'pres' => [],
            ],
            'total' => [
                'city' => [],
                'ph' => [],
                'uvi' => [],
                'ozoneYear' => [],
                'seismi' => [],
                'smallSeiSmi' => [],
                'sunrise' => [],
                'moonrise' => []
            ],
        ];

        $startTime = 6;
        $endTime = 18;
        $today = Carbon::now()->timezone('Asia/Taipei');
        $hour = (int)$today->format('H');
        $type = 0;

        if($hour > $endTime){
            $type = 2;
        }else if($hour <= $startTime){ 
            $type = 1;
        }

        foreach($cityData as $city){
            $weatherArray = [];
            $acidRainPh = 0;
            $locationName = urlencode($city);
            $temperature = '';
            $probabilityOfPrecipitation = '';

            $weatherForecastData = $this->getCrawlerData($client, $weathers[0], $locationName);
            if(isset($weatherForecastData->location[0])){
                $maxt = $weatherForecastData->location[0]->weatherElement[4]->time[$type]->parameter->parameterName;
                $mint = $weatherForecastData->location[0]->weatherElement[2]->time[$type]->parameter->parameterName;
                $pop = $weatherForecastData->location[0]->weatherElement[1]->time[$type]->parameter->parameterName;

                array_push($dataArray['forecast']['city'], $city);
                array_push($dataArray['forecast']['maxT'], (int)$maxt);
                array_push($dataArray['forecast']['minT'], (int)$mint);
                array_push($dataArray['forecast']['pop'], (int)$pop);
            }

            $automaticWeatherData = $this->getCrawlerData($client, $weathers[2], urlencode($automatic[$city][0]));
            if(isset($automaticWeatherData->location[0])){
                $wdir = $automaticWeatherData->location[0]->weatherElement[1]->elementValue;
                $wdsd = $automaticWeatherData->location[0]->weatherElement[2]->elementValue;
                $humd = $automaticWeatherData->location[0]->weatherElement[4]->elementValue;
                $pres = $automaticWeatherData->location[0]->weatherElement[5]->elementValue;

                array_push($dataArray['observation']['city'], $city);
                array_push($dataArray['observation']['wdir'], (int)$wdir);
                array_push($dataArray['observation']['wdsd'], (float)$wdsd);
                array_push($dataArray['observation']['humd'], (int)($humd * 100));
                array_push($dataArray['observation']['pres'], (float)$pres);
            }
            

            $acidRainPh = 0;
            $acidRainData = $this->getCrawlerData($client, $weathers[5], $locationName);
            $rainPh = $acidRainData->weatherElement[0]->location;
            if(count($rainPh) > 0){
                $acidRainPh = $rainPh.value;
            }
            array_push($dataArray['total']['ph'], (int)$acidRainPh);

            $ultravioletRaysData = $this->getCrawlerData($client, $weathers[6], $locationName);
            $ultravioletIndex = (String)$ultravioletRaysData->weatherElement->location[0]->value;
            array_push($dataArray['total']['uvi'], (float)$ultravioletIndex);

            $ozoneYearAvgData = $this->getCrawlerData($client, $weathers[7], $locationName);
            $ozoneYearAvg = $ozoneYearAvgData->location->weatherElement[0]->time[29]->elementValue;
            array_push($dataArray['total']['ozoneYear'], (int)$ozoneYearAvg);

            $seismicityData = $this->getCrawlerData($client, $weathers[8], $locationName);
            $seismicity = $seismicityData->earthquake[0]->reportContent;
            array_push($dataArray['total']['seismi'], $seismicity);

            $smallAreaSeismicityData = $this->getCrawlerData($client, $weathers[9], $locationName);
            $smallAreaSeismicity = $smallAreaSeismicityData->earthquake[0]->reportContent;
            array_push($dataArray['total']['smallSeiSmi'], $smallAreaSeismicity);

            $sunriseData = $this->getCrawlerData($client, $weathers[11], $locationName);
            $sunrise = $sunriseData->note;
            array_push($dataArray['total']['sunrise'], $sunrise);

            $moonriseData = $this->getCrawlerData($client, $weathers[12], $locationName);
            $moonrise = $moonriseData->note;
            array_push($dataArray['total']['moonrise'], $moonrise);
            array_push($dataArray['total']['city'], $city);
        }
        

        return view('weatherChart', $dataArray);
    }

    public function getWeatherDataTable(Request $request){
        $client = new \GuzzleHttp\Client();
        $token = 'CWB-96170F0C-F4B6-4626-B946-D6892DA6D584';
        $weatherUrl = 'https://opendata.cwb.gov.tw/api';
        $cityData = Config::get('city');
        $weathers = Config::get('weather');
        $automatic = Config::get('automatic');
        $length = $request->input('length');
        $sortBy = $request->input('column');
        $orderBy = $request->input('dir');
        $searchValue = $request->input('search');
        $apiNum = $request->apiNum;
        $weather = $weathers[(int)$apiNum];

        $startTime = 6;
        $endTime = 18;
        $today = Carbon::now()->timezone('Asia/Taipei');
        $hour = (int)$today->format('H');
        $type = 0;

        if($hour > $endTime){
            $type = 2;
        }else if($hour <= $startTime){ 
            $type = 1;
        }

        $dataArray = [];
        $no = 1;
        $count = 15;
        foreach($cityData as $city){
            $array = [];
            $acidRainPh = 0;
            $locationName = urlencode($city);
            $temperature = '';
            $probabilityOfPrecipitation = '';

            $weatherData = $this->getCrawlerData($client, $weather, $locationName);
            $array['no'] = $no;
            $array['city'] = $city;
            if($apiNum == 0){
                if(isset($weatherData->location[0])){
                    $location = $weatherData->location[0];
                    $array['wx'] = $location->weatherElement[0]->time[$type]->parameter->parameterName;
                    $array['pop'] = $location->weatherElement[1]->time[$type]->parameter->parameterName;
                    $array['mint'] = $location->weatherElement[2]->time[$type]->parameter->parameterName;
                    $array['ci'] = $location->weatherElement[3]->time[$type]->parameter->parameterName;
                    $array['maxt'] = $location->weatherElement[4]->time[$type]->parameter->parameterName;
                }

                array_push($dataArray, (object)$array);
            }else if($apiNum == 1){
                $array['pop'] = [];
                $array['t'] = [];
                $array['rh'] = [];
                $array['minci'] = [];
                $array['ws'] = [];
                $array['maxat'] = [];
                $array['wx'] = [];
                $array['maxci'] = [];
                $array['mini'] = [];
                $array['uvi'] = [];
                $array['weatherdescription'] = [];
                $array['minat'] = [];
                $array['maxt'] = [];
                $array['wd'] = [];
                $array['td'] = [];

                if(isset($weatherData->locations[0])){
                    $location = $weatherData->locations[0];
                    $length = count($location->location[0]->weatherElement[0]->time);
                    for($i = 0; $i < $length; $i++){
                        dd($location->location[0])->weatherElement;
                        if(isset($location->weatherElement[0])){
                            $pop = $location->weatherElement[0]->time[$i]->elementValue[0]->value;
                            $t = $location->weatherElement[1]->time[$i]->elementValue[0]->value;
                            $rh = $location->weatherElement[2]->time[$i]->elementValue[0]->value;
                            $minci = $location->weatherElement[3]->time[$i]->elementValue[0]->value;
                            $ws = $location->weatherElement[4]->time[$i]->elementValue[0]->value;
                            $maxat = $location->weatherElement[5]->time[$i]->elementValue[0]->value;
                            $wx = $location->weatherElement[6]->time[$i]->elementValue[0]->value;
                            $maxci = $location->weatherElement[7]->time[$i]->elementValue[0]->value;
                            $mini = $location->weatherElement[8]->time[$i]->elementValue[0]->value;
                            $uvi = $location->weatherElement[9]->time[$i]->elementValue[0]->value;
                            $weatherdescription = $location->weatherElement[10]->time[$i]->elementValue[0]->value;
                            $minat = $location->weatherElement[11]->time[$i]->elementValue[0]->value;
                            $maxt = $location->weatherElement[12]->time[$i]->elementValue[0]->value;
                            $wd = $location->weatherElement[13]->time[$i]->elementValue[0]->value;
                            $td = $location->weatherElement[14]->time[$i]->elementValue[0]->value;

                            array_push($array['pop'], $pop);
                            dd($array);
                            array_push($array['t'], $t);
                            array_push($array['rh'], $rh);
                            array_push($array['minci'], $minci);
                            array_push($array['ws'], $ws);
                            array_push($array['maxat'], $maxat);
                            array_push($array['wx'], $wx);
                            array_push($array['maxci'], $maxci);
                            array_push($array['mini'], $mini);
                            array_push($array['uvi'], $uvi);
                            array_push($array['weatherdescription'], $weatherdescription);
                            array_push($array['minat'], $minat);
                            array_push($array['maxt'], $maxt);
                            array_push($array['wd'], $wd);
                            array_push($array['td'], $td);
                            dd($array);
                        }
                    }

                }
                array_push($dataArray, (object)$array);
            }
            
            $no++;

            // if(isset($weatherData->location[0])){

            // }
        }
        // dd($dataArray);

        $collection = collect($dataArray);

        if($searchValue != ''){
            $collection->search(function ($item, $key) {
                return $item->$key == $searchValue;
            });
        }
        
        if($sortBy != '' && $orderBy != ''){
            if($orderBy == 'asc'){
                $collection = $collection->sortBy($sortBy);
            }else if($orderBy == 'desc'){
                $collection = $collection->sortByDesc($sortBy);
            }
        }

        if($length != ''){
            $collection = $collection->forPage(1, $length);
        }

        return new DataTableCollectionResource($collection->values()->all());
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
