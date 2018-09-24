<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RtActChecklistItem extends Model
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
        'item_name', 'rt_act_checklist_id'
    ];

    /**
     * 
     */
    public function rt_act_checklist()
    {
        return $this->belongsTo('App\RtActChecklist');
    }

    /**
     * 
     */
    public function rt_act()
    {
        return $this->belongsToMany('App\RtAct')->withPivot('status', 'comment');;
    }
}
