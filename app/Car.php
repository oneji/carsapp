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
        'number', 'shape_id', 'brand_id', 'model_id', 'milage', 'vin_code', 'engine_type_id', 'transmission_id'
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
    public function companies()
    {
        return $this->belongsToMany('App\Company');
    }

    /**
     * Get all drivers for a car.
     */
    public function drivers() 
    {
        return $this->belongsToMany('App\Driver');
    }

    /**
     * Get all equipment for a car.
     */
    public function equipments() 
    {
        return $this->belongsToMany('App\CarEquipment');
    }
}
