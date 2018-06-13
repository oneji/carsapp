<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CarCardComment extends Model
{
    /**
     * Get car card that owns a comment.
     */
    public function car_card()
    {
        return $this->belongsTo('App\CarCard');
    }
}
