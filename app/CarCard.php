<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CarCard extends Model
{
    /**
     * Get card's car.
     */
    public function car()
    {
        return $this->belongsTo('App\Car');
    }

    /**
     * Get all cars defect options.
     */
    public function defect_options()
    {
        return $this->belongsToMany('App\DefectOption');
    }

    /**
     * Get all comments for car card.
     */
    public function comments()
    {
        return $this->hasMany('App\CarCardComment');
    }
}
