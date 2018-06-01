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

    public function attachments() 
    {
        return $this->hasMany('App\DriverAttachment');
    }
}
