<?php

namespace App\Http\Controllers\Company;

use App\Driver;
use App\Company;
use App\DriverAttachment;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class DriverController extends Controller
{
    /**
     * Get all drivers of a company. 
     */
    public function get($company_slug) 
    {
        $company = Company::where('slug', $company_slug)->with(['drivers'])->first();
        $boundDrivers = DB::table('car_driver')->get();
        return response()->json([
            'company' => $company,
            'boundDrivers' => $boundDrivers
        ]);
    }

    /**
     * Store a newly created driver to the database.
     * 
     * @param   \Illuminate\Http\Request $request
     * 
     * @return  \Illuminate\Http\Response
     */
    public function store(Request $request, $company_slug) 
    {    
        $company = Company::where('slug', $company_slug)->first();

        if($request->hasFile('photo')) {
            $photoFullName = $request->file('photo')->getClientOriginalName(); 
            $photoname = pathinfo($photoFullName, PATHINFO_FILENAME);
            $photoPath = $request->file('photo')->path();
            $photoExtension = $request->file('photo')->getClientOriginalExtension();
            $photoNameToStore = time().'.'.$photoExtension;
            $path = $request->file('photo')->move(public_path('/uploads/driver_photos'), $photoNameToStore);  
            $photoNameToStore = 'uploads/driver_photos/'.$photoNameToStore;
        } else {
            $photoNameToStore = null;
        }

        $driver = new Driver($request->all());

        if($request->driver_license_date !== 'null')
            $driver->driver_license_date = Carbon::parse($request->driver_license_date);

        $driver->photo = $photoNameToStore;
        $driver->save(); 
        $company->drivers()->attach($driver->id);

        $driverAttachments = array();
        if($request->hasFile('attachments')) {            
            foreach($request->attachments as $file) {
                $fileFullName = $file->getClientOriginalName(); 
                $filename = pathinfo($fileFullName, PATHINFO_FILENAME);
                $filePath = $file->path();
                $fileExtension = $file->getClientOriginalExtension();
                $fileNameToStore = uniqid().'.'.$fileExtension;
                $path = $file->move(public_path('/uploads/attachments/drivers'), $fileNameToStore);  
                $fileNameToStore = 'uploads/attachments/drivers/'.$fileNameToStore;    
                
                array_push($driverAttachments, new DriverAttachment([
                    'attachment' => $fileNameToStore,
                    'driver_id' => $driver->id
                ]));
            }
        } else {
            $fileNameToStore = null;
        }        

        if($fileNameToStore !== null)
            $driver->attachments()->saveMany($driverAttachments);

        return response()->json([
            'success' => true,
            'message' => 'Водитель успешно создан.',
        ]);
    }
}
