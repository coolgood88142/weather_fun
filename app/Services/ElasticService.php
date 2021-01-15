<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Elasticsearch\ClientBuilder;
use Config;

class ElasticService
{
    protected $client;
    protected $host;

    public function __construct()
    {
        $host = '127.0.0.1';
        $elastic = Config::get('elastic');
        $client = ClientBuilder::create()->setHosts($elastic['hosts'])->build();
    }

    // public function connElastic()
    // {
    //     $elastic = Config::get('elastic');

    //     $client = ClientBuilder::create()->setHosts($elastic['hosts'])->build();

    //     return $client;
    // }

    public function createElastic($data)
    {
        $response = $this->$client->create($data);
    }

    public function updateElastic($data)
    {
        $response = $this->$client->update($data);
    }

    public function deleteElastic($data)
    {
        $response = $this->$client->delete($data);
    }

    public function addInfo($message)
    {
        $this->addElastic($message, 'INFO');
    }

    public function addError($message)
    {
        $this->addElastic($message, 'ERROR');
    }

    public function addElastic($message, $level)
    {
        $params =[
            'index' => 'laravel-' . date('Y.m.d'),
            'type' => 'doc',
            'id' => Hash::make(date('YmdHms'))
        ];
    
        $params['body'] = [
            'type' =>  'product',
            'message' => $message,
            '@version' => '1',
            'fields' => [
                'tag' => 'laravel',
                'type' => 'laravel-log-cw-cms.cwg.tw'
            ],
            'host' => $this->$host,
            "channel" => "product",
            'requestType' =>  'cmd',
            '@timestamp' => date_default_timezone_set('Asia/Taipei'),
            "input" => [
                "type" => "log"
            ],
            'queryString' => '/',
            'level' => $level,
            'fields' => [
                "@timestamp" => date_default_timezone_set('Asia/Taipei')
            ]
        ];

        // "@version": 1,
        //         "level": "ERROR",
        //         "queryString": "/",
        //         "@timestamp": "2020-06-19T15:42:51.000Z",
        //         "host": "cw-cms-cwg-tw-queue-64c67d5c6-nnrkj",
        //         "message": "Trying to get property 'updated_at' of non-object",
        //         "requestType": "cmd",
        //         "type": "product",
        //         "fields": {
        //         "tag": "laravel",
        //         "type": "laravel-log-cw-cms.cwg.tw"
        //         },
        //         "ctx_exception": {
        //         "trace": [
        //             "/var/www/laravel/app/Jobs/SendLineArticles.php:50",
        //         ],
        //         "message": "Trying to get property 'updated_at' of non-object",
        //         "code": 0,
        //         "class": "ErrorException",
        //         "file": "/var/www/laravel/app/Jobs/SendLineArticles.php:50"
        //         },
        //         "input": {
        //         "type": "log"
        //         },
        //         "channel": "product"
    
        $this->createElastic($params);
    }
}
