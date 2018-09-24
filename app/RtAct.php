<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RtAct extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'car_card_id', 'company_from', 'responsible_employee', 'company_to', 'driver_id'
    ];

    /**
     * 
     */
    public function car_card()
    {
        return $this->belongsTo('App\CarCard');
    }

    /**
     * 
     */
    public function checklist_items()
    {
        return $this->belongsToMany('App\RtActChecklistItem')->withPivot('status', 'comment');
    }
}
