<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sto extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sto_name', 'contact',
    ];

    /**
     * Get users which belong to sto.
     * 
     */
    public function users() 
    {
        return $this->belongsToMany('App\User');
    }

    /**
     * Get sto companies.
     */
    public function companies() 
    {
        return $this->belongsToMany('App\Company');
    }
}
