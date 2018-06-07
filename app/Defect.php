<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Defect extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get a defect type for a defect.
     */
    public function defect_type()
    {
        return $this->belogsTo('App\DefectType');
    }

    /**
     * Get all defect options for a defect.
     */
    public function defect_options() 
    {
        return $this->hasMany('App\DefectOption');
    }
}
