<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FineAttachment extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get fine that owns an attachment
     */
    public function fine()
    {
        return $this->belongsTo('App\Fine');
    }
}
