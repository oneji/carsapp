<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fine extends Model
{
    /**
     * Get all fine's attachments.
     */
    public function attachments()
    {
        return $this->hasMany('App\FineAttachment');
    }

    /**
     * Get car card that owns a fine.
     */
    public function card()
    {
        return $this->belongsTo('App\CarCard');
    }
}
