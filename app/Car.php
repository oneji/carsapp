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
     * Get all attachment for a car.
     */
    public function attachments() 
    {
        return $this->hasMany('App\CarAttachment');
    }
}
