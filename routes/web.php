<?php

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    return view('weather');
});

Route::get('/user', function () {
    return view('user');
});

Route::get('/weather','WeatherController@getWeatherData');

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

Route::get('/fugle','StockController@getFugleDefaultData');

Route::post('/fugle','StockController@getFugleApiStockData')->name('fugle');

Route::post('/checkFugleDataTime','StockController@checkFugleApiStockData')->name('checkFugle');

Route::get('/weatherChart','WeatherController@getWeatherApiData');

Route::get('/getFugle', 'StockController@getDataTable');