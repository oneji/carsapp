<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CarAttachment extends Model
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
        'attachment', 'attachment_ext', 'car_id'
    ];
    
    /**
     * Get a car which attachment is belonged to.
     */
    public function car() 
    {
        return $this->belongTo('App\Car');
    }
}
