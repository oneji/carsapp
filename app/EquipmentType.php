<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EquipmentType extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get all cars for equipment.
     */
    public function cars() 
    {
        return $this->belongsToMany('App\Car');
    }
}
