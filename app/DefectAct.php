<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DefectAct extends Model
{
    /**
     * Get all defect act's defect options.
     */
    public function defect_options()
    {
        return $this->belongsToMany('App\DefectOption');
    }

    /**
     * Get defect act's car card.
     */
    public function car_cards()
    {
        return $this->belongsTo('App\CarCard');
    }
}
