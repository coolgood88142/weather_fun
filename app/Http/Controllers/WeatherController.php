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
                $array1 = [];
                $array1['no'] = $no;
                $array1['city'] = $city;
                $i = 0;
                if(isset($weatherData->locations[0])){
                    $location = $weatherData->locations[0];
                    $el = $location->location[0]->weatherElement;
                    $array1['pop'] = $el[0]->time[$i]->elementValue[0]->value;
                    $array1['t'] = $el[1]->time[$i]->elementValue[0]->value;
                    $array1['rh'] = $el[2]->time[$i]->elementValue[0]->value;
                    $array1['minci'] = $el[3]->time[$i]->elementValue[0]->value;
                    $array1['ws'] = $el[4]->time[$i]->elementValue[0]->value;
                    $array1['maxat'] = $el[5]->time[$i]->elementValue[0]->value;
                    $array1['wx'] = $el[6]->time[$i]->elementValue[0]->value;
                    $array1['maxci'] = $el[7]->time[$i]->elementValue[0]->value;
                    $array1['mini'] = $el[8]->time[$i]->elementValue[0]->value;

                    if($i < 6){
                        $array1['uvi'] = $el[9]->time[$i]->elementValue[0]->value;
                    }else{
                        $array1['uvi'] = '';
                    }
                    
                    $array1['weatherdescription'] = $el[10]->time[$i]->elementValue[0]->value;
                    $array1['minat'] = $el[11]->time[$i]->elementValue[0]->value;
                    $array1['maxt'] = $el[12]->time[$i]->elementValue[0]->value;
                    $array1['wd'] = $el[13]->time[$i]->elementValue[0]->value;
                    $array1['td'] = $el[14]->time[$i]->elementValue[0]->value;

                    array_push($dataArray, (object)$array1);    
                }
            }else if($apiNum == 2){
                $automaticWeatherData = $this->getCrawlerData($client, $weathers[$apiNum], urlencode($automatic[$city][0]));
                if(isset($automaticWeatherData->location[0])){
                    $array['elev'] = $automaticWeatherData->location[0]->weatherElement[0]->elementValue;
                    $array['wdir'] = $automaticWeatherData->location[0]->weatherElement[1]->elementValue;
                    $array['wdsd'] = $automaticWeatherData->location[0]->weatherElement[2]->elementValue;
                    $array['temp'] = $automaticWeatherData->location[0]->weatherElement[3]->elementValue;
                    $array['humd'] = $automaticWeatherData->location[0]->weatherElement[4]->elementValue;
                    $array['pres'] = $automaticWeatherData->location[0]->weatherElement[5]->elementValue;
                    $array['h_24r'] = $automaticWeatherData->location[0]->weatherElement[6]->elementValue;

                    $h_fx =  (int)$automaticWeatherData->location[0]->weatherElement[7]->elementValue;
                    $array['h_fx'] = $h_fx > 0 ? $h_fx : 0;

                    $h_xd =  (int)$automaticWeatherData->location[0]->weatherElement[8]->elementValue;
                    $array['h_xd'] = $h_xd > 0 ? $h_xd : 0;

                    $h_fxt = (int)$automaticWeatherData->location[0]->weatherElement[9]->elementValue;
                    $array['h_fxt'] = $h_fxt > 0 ? $h_fxt : 0;

                    $array['d_tx'] = $automaticWeatherData->location[0]->weatherElement[10]->elementValue;
                    $d_txt = $automaticWeatherData->location[0]->weatherElement[11]->elementValue;
                    $d_Txt = new Carbon($d_txt);
                    $array['d_txt'] =  $d_Txt->timezone('Asia/Taipei')->format('H:i');

                    $array['d_tn'] = $automaticWeatherData->location[0]->weatherElement[12]->elementValue;
                    
                    $d_tnt = $automaticWeatherData->location[0]->weatherElement[13]->elementValue;
                    $d_Tnt = new Carbon($d_tnt);
                    $array['d_tnt'] =  $d_Tnt->timezone('Asia/Taipei')->format('H:i');
                }

                array_push($dataArray, (object)$array);
            }else if($apiNum == 3){
                $automaticWeatherData = $this->getCrawlerData($client, $weathers[$apiNum], urlencode($automatic[$city][1]));
                if(isset($automaticWeatherData->location[0])){
                    $array['elev'] = $automaticWeatherData->location[0]->weatherElement[0]->elementValue;

                    $rain = (float)$automaticWeatherData->location[0]->weatherElement[1]->elementValue;
                    $rain = $rain > 0 ? $rain : '0.00';
                    $array['rain'] = $rain;

                    $min_10 = (float)$automaticWeatherData->location[0]->weatherElement[2]->elementValue;
                    $min_10 = $min_10 > 0 ? $min_10 : '0.00';
                    $array['min_10'] = $min_10;

                    $hour_3 = (float)$automaticWeatherData->location[0]->weatherElement[3]->elementValue;
                    $hour_3 = $hour_3 > 0 ? $hour_3 : '0.00';
                    $array['hour_3'] = $hour_3;

                    $hour_6 = (float)$automaticWeatherData->location[0]->weatherElement[4]->elementValue;
                    $hour_6 = $hour_6 > 0 ? $hour_6 : '0.00';
                    $array['hour_6'] = $hour_6;

                    $array['hour_12'] = $automaticWeatherData->location[0]->weatherElement[5]->elementValue;
                    $array['hour_24'] = $automaticWeatherData->location[0]->weatherElement[6]->elementValue;
                    $array['now'] = $automaticWeatherData->location[0]->weatherElement[7]->elementValue;
                    $array['latest_2days'] = $automaticWeatherData->location[0]->weatherElement[8]->elementValue;
                    $array['latest_3days'] = $automaticWeatherData->location[0]->weatherElement[9]->elementValue;
                }

                array_push($dataArray, (object)$array);
            }else if($apiNum == 4){
                $automaticWeatherData = $this->getCrawlerData($client, $weathers[$apiNum], urlencode($automatic[$city][2]));
                if(isset($automaticWeatherData->location[0])){
                    $array['elev'] = $automaticWeatherData->location[0]->weatherElement[0]->elementValue;
                    $array['wdir'] = $automaticWeatherData->location[0]->weatherElement[1]->elementValue;
                    $array['wdsd'] = $automaticWeatherData->location[0]->weatherElement[2]->elementValue;
                    $array['temp'] = $automaticWeatherData->location[0]->weatherElement[3]->elementValue;
                    $array['humd'] = $automaticWeatherData->location[0]->weatherElement[4]->elementValue;
                    $array['pres'] = $automaticWeatherData->location[0]->weatherElement[5]->elementValue;
                    $array['24r'] = $automaticWeatherData->location[0]->weatherElement[6]->elementValue;
                    $array['h_fx'] = $automaticWeatherData->location[0]->weatherElement[7]->elementValue;
                    $array['h_xd'] = $automaticWeatherData->location[0]->weatherElement[8]->elementValue;
                    $array['h_fxt'] = $automaticWeatherData->location[0]->weatherElement[9]->elementValue;
                    $array['h_f10'] = $automaticWeatherData->location[0]->weatherElement[10]->elementValue;
                    $array['h_10d'] = $automaticWeatherData->location[0]->weatherElement[11]->elementValue;
                    $array['h_f10t'] = $automaticWeatherData->location[0]->weatherElement[12]->elementValue;
                    $array['h_uvi'] = $automaticWeatherData->location[0]->weatherElement[13]->elementValue;
                    $array['d_tx'] = $automaticWeatherData->location[0]->weatherElement[14]->elementValue;
                    $array['d_txt'] = $automaticWeatherData->location[0]->weatherElement[15]->elementValue;
                    $array['d_tn'] = $automaticWeatherData->location[0]->weatherElement[16]->elementValue;
                    $array['d_tnt'] = $automaticWeatherData->location[0]->weatherElement[17]->elementValue;
                    $array['d_ts'] = $automaticWeatherData->location[0]->weatherElement[18]->elementValue;
                    $array['vis'] = $automaticWeatherData->location[0]->weatherElement[19]->elementValue;
                    $array['weather'] = $automaticWeatherData->location[0]->weatherElement[19]->elementValue;
                }

                array_push($dataArray, (object)$array);
            }else if($apiNum == 5){
                $automaticWeatherData = $this->getCrawlerData($client, $weathers[$apiNum], urlencode($automatic[$city][3]));
                if(isset($automaticWeatherData->weatherElement[0]->location[0])){
                    $array['mean'] = $automaticWeatherData->weatherElement[0]->location[0]->parameter[1]->parameterValue;
                    $array['max'] = $automaticWeatherData->weatherElement[0]->location[0]->parameter[2]->parameterValue;
                    $array['min'] = $automaticWeatherData->weatherElement[0]->location[0]->parameter[3]->parameterValue;
                }

                array_push($dataArray, (object)$array);
            }else if($apiNum == 6){
                $ultravioletIndex = (String)$weatherData->weatherElement->location[0]->value;
                $array['uvi'] = (float)$ultravioletIndex;
                array_push($dataArray, (object)$array);
            }else if($apiNum == 7){
                $ozoneYearAvg = $weatherData->location->weatherElement[0]->time[29]->elementValue;
                $array['ozoneYear'] = (int)$ozoneYearAvg;
                array_push($dataArray, (object)$array);
            }else if($apiNum == 8){
                $seismicity = $weatherData->earthquake[0]->reportContent;
                $array['seismi'] = $seismicity;
                array_push($dataArray, (object)$array);
            }else if($apiNum == 9){
                $smallAreaSeismicity = $weatherData->earthquake[0]->reportContent;
                $array['smallSeiSmi'] = $smallAreaSeismicity;
                array_push($dataArray, (object)$array);
            }else if($apiNum == 10){
                $alarm = $weatherData->location[0]->hazardConditions->hazards;
                if(count($alarm) == 0){
                    $array['alarm'] = '無警報';
                }else{
                    $array['alarm'] = $alarm[0];
                }
                
                array_push($dataArray, (object)$array);
            }else if($apiNum == 11){
                $sunriseData = $weatherData->note;
                $array['sunrise'] = $sunriseData;
                array_push($dataArray, (object)$array);
            }else if($apiNum == 12){
                $moonriseData = $weatherData->note;
                $array['moonrise'] = $moonriseData;
                array_push($dataArray, (object)$array);
            }

            $no++;
        }
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
