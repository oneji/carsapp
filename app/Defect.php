<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Defect extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get a defect act for a defect.
     */
    public function defect_acts()
    {
        return $this->belongsToMany('App\DefectAct')->withPivot('to_report');
    }

    /**
     * Get a defect type for a defect.
     */
    public function defect_type()
    {
        return $this->belogsTo('App\DefectType');
    }

    /**
     * Get all defect options for a defect.
     */
    public function defect_options() 
    {
        return $this->hasMany('App\DefectOption');
    }
    
    /**
     * Get all defect conclusions for a defect.
     */
    public function defect_conclusions() 
    {
        return $this->hasMany('App\DefectConclusion');
    }

    /**
     * Get all of the defect's comments.
     */
    public function comments()
    {
        return $this->morphMany('App\Comment', 'commentable');
    }

    /**
     * Get all of the service prices defect's.
     */
    public function service_prices()
    {
        return $this->hasMany('App\ServicePrice');
    }
}
