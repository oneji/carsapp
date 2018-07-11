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
     * 
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

    /**
     * Get all defect acts for car card.
     */
    public function defect_acts() 
    {
        return $this->hasMany('App\DefectAct');
    }

    /**
     * Get all fines for car card.
     */
    public function fines()
    {
        return $this->hasMany('App\Fine');
    }
}
