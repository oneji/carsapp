<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Image;

class Driver extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fullname', 'address', 'email', 'phone', 'driver_license_category'
    ];

    private $fileFolder = '/uploads/driver_photos';

    /**
     * Get all attachments for a driver.
     * 
     */
    public function attachments() 
    {
        return $this->hasMany('App\DriverAttachment');
    }

    /**
     * Get all companies for a driver.
     * 
     */
    public function companies()
    {
        return $this->belongsToMany('App\Company');
    }

    /**
     * Get all cars for a driver.
     * 
     */
    public function cars()
    {
        return $this->belongsToMany('App\Car');
    }

    public function queue()
    {
        return $this->hasOne('App\DriverQueue');
    }

    /**
     * File uploading.
     * 
     * @param   \Illuminate\Http\Request $file
     * 
     * @return  bool
     */
    public static function uploadFile($file, $path)
    {
        $fileExtension = $file->getClientOriginalExtension();
        $fileNameToStore = uniqid().'.'.$fileExtension;
        // $result = $file->move(public_path($path), $fileNameToStore);
        $fileDetails = getimagesize($file); 
        // Compressing image
        $compressedImage = Image::make($file->getRealPath());
        if($fileDetails[0] > 1290) {
            $compressedImage->resize(1290, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        }        
        $compressedImage->save(public_path($path.'/'.$fileNameToStore));

        $fileNameToStore = $path.'/'.$fileNameToStore;
        
        return $fileNameToStore;
    }
}
