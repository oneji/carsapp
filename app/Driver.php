<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fullname', 'address', 'email', 'phone'
    ];

    /**
     * Get all attachments for a driver.
     * 
     */
    public function attachments() 
    {
        return $this->hasMany('App\DriverAttachment');
    }

    /**
     * Get all companies for a driver.
     * 
     */
    public function companies()
    {
        return $this->belongsToMany('App\Company');
    }

    /**
     * Get all cars for a driver.
     * 
     */
    public function cars()
    {
        return $this->belongsToMany('App\Car');
    }

    public function queue()
    {
        return $this->hasOne('App\DriverQueue');
    }
}
