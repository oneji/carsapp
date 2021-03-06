<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DriverAttachment extends Model
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
        'attachment', 'driver_id'
    ];

    /**
     * Get a driver which attachment is belonged to.
     */
    public function driver()
    {
        return $this->belongTo('App\Driver');
    }
}
