<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DriverQueue extends Model
{
    public function driver() 
    {
        return $this->belongsTo('App\Driver');
    }
}
