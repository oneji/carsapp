<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DefectConclusion extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get a defect for defect conclusion.
     */
    public function defect()
    {
        return $this->belongsTo('App\Defect');
    }

    /**
     * Get service prices for defect conclusion.
     */
    public function service_prices()
    {
        return $this->hasMany('App\ServicePrice');
    }
}
