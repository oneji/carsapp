<?php

namespace App\Http\Controllers;

use App\RtAct;
use App\Driver;
use App\Sto;
use App\Car;
use App\CarCard;
use App\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mail;
use Dompdf\Dompdf;
use Spipu\Html2Pdf\Html2Pdf;
use Image;

class RtActController extends Controller
{
    private $htmlToPdf = '';

    /**
     * Get RT act by id.
     * @param   int $id
     * 
     * @return  \Illuminate\Http\JsonResponse
     */
    public function getById($id)
    {
        $act = RtAct::select('rt_acts.*', 'drivers.fullname as driver_name', 'users.fullname as created_by_name')
            ->leftJoin('drivers', 'rt_acts.driver_id', '=', 'drivers.id')
            ->leftJoin('users', 'rt_acts.created_by', '=', 'users.id')
            ->where('rt_acts.id', $id)->with('checklist_items.rt_act_checklist')->first();
        $card = CarCard::where('id', $act->car_card_id)->with(['car' => function($query) {
            $query->select('cars.*', 'shape_name', 'brand_name', 'model_name', 'engine_type_name', 'transmission_name')
                ->join('car_shapes', 'car_shapes.id', '=', 'cars.shape_id')
                ->join('car_models', 'car_models.id', '=', 'cars.model_id')
                ->join('car_brands', 'car_brands.id', '=', 'cars.brand_id')
                ->join('engine_types', 'engine_types.id', '=', 'cars.engine_type_id')
                ->join('transmissions', 'transmissions.id', '=', 'cars.transmission_id')
                ->get();
        }])->first();

        return response()->json([
            'act' => $act,
            'car' => $card->car
        ]);
    }

    /**
     * Store a newly created RT act in the db.
     * 
     * @param   \Illuminate\Http\Request $request
     * 
     * @return  \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Determine a company's type
        if($request->company_to_type === 'inner') {
            $company = Company::where('company_name', $request->company_to)->first();
            $card = CarCard::find($request->car_card_id);
            $carId = $card->car_id;
            DB::table('car_company')->where('car_id', $card->car_id)->update([ 'company_id' => $company->id ]);
        } else if($request->company_to_type === 'outer') {
            $card = CarCard::find($request->car_card_id);
            $car = Car::find($card->car_id);
            $car->sold = 1;
            $car->save();
        }
        // RT act files
        $files = [];
        if($request->hasFile('attachments')) {
            foreach($request->attachments as $file) {
                $fileFullName = $file->getClientOriginalName();
                $fileExtension = $file->getClientOriginalExtension();
                $fileDetails = getimagesize($file); 
                // Compressing image
                $compressedImage = Image::make($file->getRealPath());

                if($fileDetails[0] > 1290) {
                    $compressedImage->resize(1290, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
                
                $fileNameToStore = uniqid().'.'.$fileExtension;
                $compressedImage->save(public_path('uploads/rt_act_files/'.$fileNameToStore));
                
                array_push($files, [
                    'file' => $fileNameToStore,
                    'name' => $fileFullName
                ]);
            }
        } else {
            $files = null;
        }
        // The name and the HTML data for a .pdf file
        $this->htmlToPdf = $request->htmlToPdf;
        $fileName = $this->generateActFile();
        // Creating RT act
        $act = new RtAct($request->all());
        $act->files = json_encode($files, JSON_UNESCAPED_UNICODE);
        $act->rt_act_file = $fileName;
        $act->draft = 0;
        $act->save();
        // RT act values
        $values = json_decode($request->values);
        for($i = 0; $i < count($values); $i++) {
            if($values[$i] !== null) {
                $act->checklist_items()->attach($values[$i]->id, [
                    'status' => $values[$i]->status,
                    'comment' => $values[$i]->comment
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Акт приема передачи успешно создан. Отправляем на почту...',
            'act' => $act
        ]);
    }

    /**
     * Save RT act to draft
     */
    public function saveToDraft(Request $request)
    {
        // RT act files
        $files = [];
        if($request->hasFile('attachments')) {
            foreach($request->attachments as $file) {
                $fileFullName = $file->getClientOriginalName();                 
                $fileExtension = $file->getClientOriginalExtension();
                $fileNameToStore = uniqid().'.'.$fileExtension;
                $path = $file->move(public_path('/uploads/rt_act_files'), $fileNameToStore);
                
                array_push($files, [
                    'file' => $fileNameToStore,
                    'name' => $fileFullName
                ]);
            }
        } else {
            $files = null;
        }
        // Creating RT act
        $act = new RtAct($request->all());
        $act->files = json_encode($files, JSON_UNESCAPED_UNICODE);
        $act->draft = 1;
        $act->save();
        // Get and decode RT act values
        $values = json_decode($request->values);
        // Save RT act values
        for($i = 0; $i < count($values); $i++) {
            if($values[$i] !== null) {
                $act->checklist_items()->attach($values[$i]->id, [
                    'status' => $values[$i]->status,
                    'comment' => $values[$i]->comment
                ]);
            }
        }
         
        return response()->json([
            'success' => true,
            'message' => 'Акт сохранен в черновик.'
        ]);
    }

    /**
     * Store a newly created RT act in the db.
     * 
     * @param   \Illuminate\Http\Request $request
     * 
     * @return  \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, $id)
    {
        // RT act files
        $files = [];
        if($request->hasFile('attachments')) {
            foreach($request->attachments as $file) {
                $fileFullName = $file->getClientOriginalName();                 
                $fileExtension = $file->getClientOriginalExtension();
                $fileNameToStore = uniqid().'.'.$fileExtension;
                $path = $file->move(public_path('/uploads/rt_act_files'), $fileNameToStore);
                
                array_push($files, [
                    'file' => $fileNameToStore,
                    'name' => $fileFullName
                ]);
            }
        } else {
            $files = null;
        }
        // The name and the HTML data for a .pdf file
        $this->htmlToPdf = $request->htmlToPdf;
        $fileName = $this->generateActFile();
        // Editing RT act
        $act = RtAct::where('id', $id)->first();
        $act->company_from = $request->company_from;
        $act->responsible_employee = $request->responsible_employee;
        $act->company_to = $request->company_to;
        $act->rt_act_file = $fileName;
        $act->files = json_encode($files, JSON_UNESCAPED_UNICODE);
        $act->draft = 0;
        $act->save();
        // RT act values
        $values = json_decode($request->values);
        for($i = 0; $i < count($values); $i++) {
            if($values[$i] !== null) {
                $act->checklist_items()->updateExistingPivot($values[$i]->id, [
                    'status' => $values[$i]->status,
                    'comment' => $values[$i]->comment
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Акт приема передачи успешно изменен. Отправляем на почту...',
            'act' => $act
        ]);
    }

    /**
     * Get all needed info for RT act.
     * 
     * @param   \Illuminate\Http\Request $request
     * 
     * @return  \Illuminate\Http\JsonResponse
     */
    public function getFullInfo(Request $request)
    {
        $sto = Sto::where('slug', $request->sto_slug)->with('companies')->first();
        $companies = $sto->companies;
        $car = Car::select('cars.*', 'shape_name', 'brand_name', 'model_name', 'engine_type_name', 'transmission_name')
                        ->join('car_shapes', 'car_shapes.id', '=', 'cars.shape_id')
                        ->join('car_models', 'car_models.id', '=', 'cars.model_id')
                        ->join('car_brands', 'car_brands.id', '=', 'cars.brand_id')
                        ->join('engine_types', 'engine_types.id', '=', 'cars.engine_type_id')
                        ->join('transmissions', 'transmissions.id', '=', 'cars.transmission_id')
                        ->with('attachments', 'card.comments.user', 'card.defect_acts.defect_options.defect', 'card.defect_acts.equipment')
                        ->where('sold', 0)
                        ->where('cars.id', $request->car_id)->with([ 'companies', 'drivers' => function($q) {
                            $q->where('active', 1)->get();
                        }])->first(); 

        return response()->json([
            'car' => $car,
            'companies' => $companies
        ]);
    }

    /**
     * Download RT act .pdf file.
     * 
     * @param   \Illuminate\Http\Request $request
     * 
     * @return  \Illuminate\Http\Response
     */
    public function downloadFile(Request $request) 
    {
        $act = RtAct::find($request->id);
        $file = public_path('/uploads/rt_acts/'.$act->rt_act_file);
        $headers = [
            'Content-Type' => 'application/pdf',
        ];

        return response()->download($file, $act->rt_act_file, $headers);
    }

    /**
     * Send RT act .pdf file to email.
     */
    public function sendActFile($actId)
    {
        $act = RtAct::find($actId);
        $driver = Driver::find($act->driver_id);
        $fileName = $act->rt_act_file;

        if($driver !== null) {
            if($driver->email !== null) {
                Mail::send('welcome', [], function($message) use ($driver, $fileName) {
                    $message->subject('Акт приема передачи автомобиля');
                    $message->from('sto@the55group.com');
                    $message->to($driver->email);
                    $message->attach(public_path('/uploads/rt_acts/'.$fileName), [
                        'as' => 'Акт приема передачи',
                        'mime' => 'application/pdf'
                    ]);
                });
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Акт приёма передачи успешно отправлен на почту.'
        ]);
    }

    /**
     * Generate RT act .pdf file and save on the server.
     * 
     * @return  string $fileName
     */
    public function generateActFile()
    {   
        /* Mpdf */
        $fileName = uniqid().'.pdf';
        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($this->htmlToPdf);
        $mpdf->Output(public_path('uploads/rt_acts/'.$fileName), \Mpdf\Output\Destination::FILE);
        
        /* Dompdf */
        // $dompdf = new Dompdf();
        // $dompdf->loadHtml($this->htmlToPdf);
        // $dompdf->render();
        // file_put_contents(public_path('/uploads/rt_acts/'.$fileName), $dompdf->output());

        return $fileName;
    }

    /**
     * 
     */
    public function addMoreFiles(Request $request, $id)
    {
        $act = RtAct::find($id);
        $currentFiles = []; 
        if($act->files !== null && $act->files !== 'null') {
            $currentFiles = json_decode($act->files, true);
        }
        // RT act files
        $newFiles = [];
        if($request->hasFile('attachments')) {
            foreach($request->attachments as $file) {
                $fileFullName = $file->getClientOriginalName();
                $fileExtension = $file->getClientOriginalExtension();
                $fileDetails = getimagesize($file); 
                // Compressing image
                $compressedImage = Image::make($file->getRealPath());

                if($fileDetails[0] > 1290) {
                    $compressedImage->resize(1290, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
                
                $fileNameToStore = uniqid().'.'.$fileExtension;
                $compressedImage->save(public_path('uploads/rt_act_files/'.$fileNameToStore));
                
                array_push($newFiles, [
                    'file' => $fileNameToStore,
                    'name' => $fileFullName
                ]);
            }
        } else {
            $newFiles = null;
        }

        if($newFiles !== null) {
            $mergedFilesArray = array_merge($currentFiles, $newFiles);
            $act->files = json_encode($mergedFilesArray, JSON_UNESCAPED_UNICODE);
            $act->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Файлы успешно добавлены.',
            'files' => json_decode($act->files)
        ]);
    }
}
