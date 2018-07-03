<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoRequest extends Model
{
    /**
     * Check status of the request.
     * 
     * @return  string $message
     */
    public function checkStatus() 
    {
        $message = '';
        if($this->status === 0)
            $message = 'Ваша заявка ждет принятия.';
        
        if($this->status === 1)
            $message = 'Ваша заявка уже принята.';

        if($this->status === 2) {
            $message = 'Ваша отклоненная заявка была переотправлена.';
            $this->status = 0;
            $this->save();
        }

        return $message;
    }
}
