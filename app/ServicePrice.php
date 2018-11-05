<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServicePrice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'defect_id', 'defect_conclusion_id', 'price', 'human_hour_price'
    ];

    /**
     * 
     */
    public function defects()
    {
        return $this->belongsTo('App\Defect');
    }

    /**
     * 
     */
    public function defect_conclusions()
    {
        return $this->belongsTo('App\DefectConclusion');
    }
}
