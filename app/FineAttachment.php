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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fine_id', 'attachment', 
    ];

    /**
     * Get fine that owns an attachment
     */
    public function fine()
    {
        return $this->belongsTo('App\Fine');
    }
}
