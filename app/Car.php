<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number', 'shape_id', 'brand_id', 'model_id', 'milage', 'vin_code'
    ];
    
    /**
     * Get all attachments for a car.
     */
    public function attachments() 
    {
        return $this->hasMany('App\CarAttachment');
    }

    /**
     * Get all companies for a car.
     * 
     */
     public function cars()
     {
        return $this->belongsToMany('App\Company');
     }
}
