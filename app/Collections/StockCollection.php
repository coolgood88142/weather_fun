<?php

namespace App\Collections;
 
use Illuminate\Database\Eloquent\Collection;
 
class StockCollection extends Collection
{
    public function active()
    {
        return $this->where('status', 1);
    }
 
    public function deactivate()
    {
        return $this->where('status', 2);
    }
}