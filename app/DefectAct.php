<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DefectAct extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'comment'
    ];

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

    /**
     * Get defect act's equipment.
     */
    public function equipment()
    {
        return $this->belongsToMany('App\EquipmentType');
    }
}
