<?php

namespace App;

use App\Company;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
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
        'number', 'shape_id', 'brand_id', 'model_id', 'vin_code', 'engine_capacity', 'engine_type_id', 'transmission_id', 'type'
    ];
    
    /**
     * Get all attachments for a car.
     */
    public function attachments() 
    {
        return $this->hasMany('App\CarAttachment');
    }

    /**
     * Get all companies for a car.
     * 
     */
    public function companies()
    {
        return $this->belongsToMany('App\Company');
    }

    /**
     * Get all drivers for a car.
     */
    public function drivers() 
    {
        return $this->belongsToMany('App\Driver')->withPivot('active', 'start_date', 'end_date');
    }

    /**
     * Get all equipment for a car.
     */
    public function equipment()
    {
        return $this->belongsToMany('App\EquipmentType');
    }

    /**
     * Get a card for a car.
     */
    public function card()
    {
        return $this->hasOne('App\CarCard');
    }

    /**
     * Get sold cars.
     * 
     * @param   string $company_slug
     * 
     * @return  collection
     */
    public static function getSold($company_slug) 
    {
        $company = Company::where('slug', $company_slug)->with([
            'cars' => function($query) {
                $query->select('cars.*', 'shape_name', 'brand_name', 'model_name')
                        ->join('car_shapes', 'car_shapes.id', '=', 'cars.shape_id')
                        ->join('car_models', 'car_models.id', '=', 'cars.model_id')
                        ->join('car_brands', 'car_brands.id', '=', 'cars.brand_id')
                        ->join('engine_types', 'engine_types.id', '=', 'cars.engine_type_id')
                        ->join('transmissions', 'transmissions.id', '=', 'cars.transmission_id')
                        ->where('sold', 1)                         
                        ->with([ 'drivers' => function($q) {
                            $q->where('active', 1)->get();
                        }])->get();
            }
        ])->first();

        return $company;
    }

    /**
     * Get all repair requests for a car.
     */
    public function repair_requests()
    {
        return $this->hasMany('App\RepairRequest');
    }
}
