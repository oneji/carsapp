<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Consumable extends Model
{
    /**
     * 
     */
    public function car_card()
    {
        return $this->belongsToMany('App\CarCard');
    }
}
