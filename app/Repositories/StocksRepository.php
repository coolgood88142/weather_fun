<?php

namespace App\Repositories;

use App\Models\Stocks;

class StocksRepository
{
    protected $stocks;

    public function __construct(Stocks $stocks)
    {
        $this->stocks = $stocks;
    }

    public function getAll()
    {
        return $this->stocks->all();
    }
}
