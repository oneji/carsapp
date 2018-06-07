<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
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
        'service_type_name'
    ];

    /**
     * Get a category for a service. 
     */
    public function service_category()
    {
        return $this->belongsTo('App\ServiceCategory');
    }
}
