<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RepairRequest extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'car_id', 'sto_id', 'comment', 'status'
    ];

    /**
     * Get a car.
     */
    public function car()
    {
        return $this->belongsTo('App\Car');
    }
}
