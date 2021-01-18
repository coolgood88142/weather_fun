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

Route::get('/weather','WeatherController@getWeatherApiData');

Route::get('/articles','WeatherController@getArticlesApiData');

Route::get('/bot','LineBotController@getMessageWeather');

Route::group(['namespace' => 'Api'], function() {
    Route::get('/line/webhook', 'LineBotController@getMessageWeather')->name('line.webhook');
});
