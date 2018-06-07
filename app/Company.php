<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_name', 'contact',
    ];

    /**
     * Get users which belong to a company. 
     */
    public function users() 
    {
        return $this->belongsToMany('App\User');
    }
    
    /**
     * Get cars which belong to a company. 
     */
    public function cars()
    {
        return $this->belongsToMany('App\Car');
    }
    
    /**
     * Get drivers which belong to a company. 
     */
    public function drivers()
    {
        return $this->belongsToMany('App\Driver');
    }

    /**
     * Get company stos.
     */
    public function stos()
    {
        return $this->belongsToMany('App\Sto');
    }
}
