<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DoneAct extends Model
{
    /**
     * Get all done act's defects.
     */
    public function defects()
    {
        return $this->belongsToMany('App\Defect')->withPivot('done');
    }

    /**
     * Get done act's car card.
     */
    public function car_cards()
    {
        return $this->belongsTo('App\CarCard');
    }

    /**
     * Get all of the done act's attachments.
     */
    public function attachments()
    {
        return $this->morphMany('App\Attachment', 'attachable');
    }
}
