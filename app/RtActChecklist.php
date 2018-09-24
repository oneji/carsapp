<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RtActChecklist extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    /**
     * 
     */
    public function checklist_items()
    {
        return $this->hasMany('App\RtActChecklistItem');
    }
}
