<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
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
        'service_category_name'
    ];

    /**
     * Get all services for a service category.
     */
    public function service_types()
    {
        return $this->hasMany('App\ServiceType');
    }
}
