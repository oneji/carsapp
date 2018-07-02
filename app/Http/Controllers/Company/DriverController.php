<?php

namespace App\Http\Controllers\Company;

use App\Driver;
use App\Company;
use App\DriverQueue;
use App\DriverAttachment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class DriverController extends Controller
{
    /**
     * Get all drivers of a company. 
     * 
     * @param   string $company_slug
     * 
     * @return  \Illuminate\Http\JsonResponse
     */
    public function get($company_slug) 
    {
        $boundDrivers = DB::table('car_driver')->where('active', 1)->pluck('driver_id')->all();
        
        $allDrivers = Company::where('slug', $company_slug)->with([
            'drivers' => function($query) {
                $query->with('queue')->get();
            }
        ])->first();

        $company = Company::where('slug', $company_slug)->with([
            'drivers' => function($query) use ($boundDrivers) {
                $query->whereNotIn('id', $boundDrivers)->with('queue')->get();
            }
        ])->first();
        
        return response()->json([
            'company' => $company,
            'allDrivers' => $allDrivers 
        ]);
    }

    /**
     * Store a newly created driver to the database.
     * 
     * @param   \Illuminate\Http\Request $request
     * 
     * @return  \Illuminate\Http\JsonResponse
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

    /**
     * Add driver to queue.
     * 
     * @param   \Illuminate\Http\Request $request
     * @param   string $company_slug
     * @param   int $driver_id
     * 
     * @return  \Illuminate\Http\JsonResponse
     */
    public function addToQueue(Request $request, $company_slug, $driver_id)
    {
        $queue = DriverQueue::where('driver_id', $driver_id)->get();

        if(count($queue) > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Водитель уже добавлен в очередь.'
            ]);
        }

        $driverQueue = new DriverQueue();
        $driverQueue->driver_id = $driver_id;
        $driverQueue->save();

        return response()->json([
            'success' => true,
            'message' => 'Водитель успешно добавлен в очередь.',
            'queue' => $driverQueue
        ]);
    }

    /**
     * Remove driver from queue.
     * 
     * @param   \Illuminate\Http\Request $request
     * @param   string $company_slug
     * @param   int $driver_id
     * 
     * @return  \Illuminate\Http\JsonResponse
     */
    public function backFromQueue(Request $request, $company_slug, $driver_id)
    {
        $queue = DriverQueue::where('driver_id', $driver_id)->get();

        if(count($queue) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Водителя в очереди нет.'
            ]);
        }

        $driverQueue = DriverQueue::where('driver_id', $driver_id)->first();
        $driverQueue->delete();

        return response()->json([
            'success' => true,
            'message' => 'Водитель успешно удален из очереди.',
            'queue' => $driverQueue
        ]);
    }

    /**
     * Get drivers' queue.
     *  
     * @return  \Illuminate\Http\JsonResponse
     */
    public function getQueue()
    {
        $queue = DriverQueue::with('driver.companies')->get();
        return response()->json($queue);
    }

    /**
     * Edit driver form.
     * 
     * @param   string $company_slug
     * @param   int $id
     *  
     * @return  \Illuminate\Http\JsonResponse
     */
    public function edit($company_slug, $id)
    {
        $driver = Driver::find($id);
        return response()->json($driver);
    }

    public function update(Request $request, $company_slug, $id) 
    {
        // return response()->json($request->all());
        $driver = Driver::find($id);
        $driver->fullname = $request->fullname;
        if($request->address !== 'null')
            $driver->address = $request->address;

        if($request->email !== 'null')
            $driver->email = $request->email;            
            
        $driver->phone = $request->phone;
        if($request->driver_license_date !== 'null')
            $driver->driver_license_date = Carbon::parse($request->driver_license_date);      
            
        if($request->driver_license_category !== 'null')
            $driver->driver_license_category = $request->driver_license_category;

        if($request->hasFile('photo')) {
            $photoFullName = $request->file('photo')->getClientOriginalName(); 
            $photoname = pathinfo($photoFullName, PATHINFO_FILENAME);
            $photoPath = $request->file('photo')->path();
            $photoExtension = $request->file('photo')->getClientOriginalExtension();
            $photoNameToStore = time().'.'.$photoExtension;
            $path = $request->file('photo')->move(public_path('/uploads/driver_photos'), $photoNameToStore);  
            $photoNameToStore = 'uploads/driver_photos/'.$photoNameToStore;
            File::delete(public_path($driver->photo));
        } else {
            $photoNameToStore = null;
        }

        if($photoNameToStore !== null)
            $driver->photo = $photoNameToStore;

        $driver->save();        

        return response()->json([
            'success' => true,
            'message' => 'Водитель успешно изменен.'
        ]);        
    }
}
