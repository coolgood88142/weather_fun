<?php
use Intervention\Image\Facades\Image;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@verification');

Route::get('/test', function () {
    return view('weather');
});

Route::get('/user', function () {
    return view('user');
});

Route::get('/weather','WeatherController@getWeatherData')->name('weather');

Route::get('/articles','WeatherController@getArticlesApiData');

Route::get('/weatherApi','WeatherController@saveWeatherApiData');

Route::get('/line','LineBotController@sendMessageWeather');

Route::post('/callback','LineBotController@getMessageWeather')->name('callback');

Route::post('/showWeather','LineBotController@showWeatherTemplate')->name('showWeather');

// Route::get('/webhook','LineBotController@getMessageWeather');

Route::get('/tomorrowWeatherApi','WeatherController@saveTomorrowWeatherApiData');

// Route::post('/callback', 'LineController@webhook');

Route::get('/testSymboData','LineBotController@testSymboData');

Route::post('/stock','StockController@getMessageStock')->name('stock');

Route::get('/fugle','StockController@getFugleDefaultData')->name('fugle');

Route::post('/fugle','StockController@getFugleApiStockData');

Route::post('/checkFugleDataTime','StockController@checkFugleApiStockData')->name('checkFugle');

Route::get('/weatherChart', 'WeatherController@getWeatherChartData')->name('weatherChart');

Route::get('/getFugle/{category}/{symbolId}', 'StockController@getStockDataTable');

Route::get('/getWeather/{apiNum}', 'WeatherController@getWeatherDataTable');

Route::post('/upload', 'CloudVisionController@saveCloudVision')->name('upload');

Route::get('/getVision', 'CloudVisionController@getVisionDataTable');

Route::get('/vision', function () {
    return view('vision');
});

