<?php

namespace App\Models;

use App\Collections\StockCollection;
use Illuminate\Database\Eloquent\Model;

class Stocks extends Model
{

    public function newCollection(array $models = [])
    {
        return new StockCollection($models);
    }
}



