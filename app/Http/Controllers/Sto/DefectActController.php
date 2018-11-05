<?php

namespace App\Http\Controllers\Sto;

use App\Car;
use App\CarCard;
use App\DefectAct;
use App\Defect;
use App\DefectType;
use App\Comment;
use App\Attachment;
use App\EquipmentType;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Image;
use Mail;

class DefectActController extends Controller
{
    private $partialReport;
    private $fullReport;
    /**
     * Get all defect acts.
     */
    public function get($sto_slug, $card_id)
    {
        $defectActs = DefectAct::where('car_card_id', $card_id)->get();
        return response()->json($defectActs);
    }

    /**
     * Store a newly created defect act.
     * 
     * @param   string $sto_slug
     * @param   int $card_id
     * @param   \Illuminate\Http\Request $request
     * 
     * @return  \Illuminate\Http\Response
     */
    public function store(Request $request, $sto_slug, $card_id)
    {
        // Parse string data back to JSON
        $comment = $request->comment;
        $conditions = json_decode($request->detail_conditions);
        $detailInfo = json_decode($request->detail_info);
        $detailConclusions = json_decode($request->detail_conclusions);
        $servicePrices = json_decode($request->service_prices);
        $equipment = json_decode($request->equipment);
        // Name and HTML file for .pdf
        $partialReportFilename = $this->generateActFile($request->partialReport);
        $fullReportFilename = $this->generateActFile($request->fullReport);
        // Create a new defect act
        $defectAct = new DefectAct();
        $defectAct->car_card_id = $card_id;
        $defectAct->defect_act_date = Carbon::now();
        $defectAct->comment = $comment;
        $defectAct->user_id = $request->user_id;
        $defectAct->partial_file = $partialReportFilename;
        $defectAct->full_file = $fullReportFilename;
        $defectAct->save();

        // Loop through all defect details' conditions
        foreach($conditions as $detailId => $detailArray) {
            if($detailArray !== null) {
                $arrayOfValues = [];
                // Generate an array to bulk insert to the db
                array_push($arrayOfValues, [
                    'defect_id' => $detailId,
                    'defect_option_id' => $detailArray,
                    'defect_act_id' => $defectAct->id
                ]);
                // Save to the db
                DB::table('defect_defect_option')->insert($arrayOfValues);
            }
        }

        // Loop through all defect details' comments
        foreach($detailInfo as $key => $infoItem) { 
            if($infoItem !== null) {
                // Create new defect detail
                $defect = Defect::find($key);
                if($infoItem->comment !== '') {
                    $detailComments = [];
                    array_push($detailComments, new Comment([
                        'body' => $infoItem->comment,
                        'user_id' => null,
                        'defect_act_id' => $defectAct->id
                    ]));
                    $defect->comments()->saveMany($detailComments);
                }
                
                if($infoItem->toReport !== null) {
                    // Attach defect detail to the defect act 
                    $defectAct->defects()->attach($key, [
                        'to_report' => $infoItem->toReport
                    ]);
                }
            }
        }

        // Loop through all defect details' conclusions
        foreach($detailConclusions as $detailId => $detailArray) {
            if($detailArray !== null) {
                $arrayOfValues = [];
                // Generate an array to bulk insert to the db
                array_push($arrayOfValues, [
                    'defect_id' => $detailId,
                    'defect_conclusion_id' => $detailArray,
                    'defect_act_id' => $defectAct->id,
                    'service_price' => $servicePrices[$detailId]
                ]);
                // Save to the db
                DB::table('defect_defect_conclusion')->insert($arrayOfValues);
            }
        }

        // Save existed equipment
        if(count($equipment) > 0)
            $defectAct->equipment()->attach($equipment);

        $defectAttachments = [];
        // Loop through all defect act attachments
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
                $compressedImage->save(public_path('uploads/attachments/defect_acts/'.$fileNameToStore));
                
                array_push($defectAttachments, new Attachment([
                    'attachment' => $fileNameToStore,
                    'attachment_name' => $fileFullName,
                    'attachment_ext' => strtolower($fileExtension)
                ]));
            }
        } else {
            $fileNameToStore = null;
        }

        if($fileNameToStore !== null)
            $defectAct->attachments()->saveMany($defectAttachments);

        return response()->json([
            'success' => true,
            'message' => 'Дефектный акт успешно создан.',
            'act' => $defectAct,
            'detailInfo' => $detailInfo
        ]);
    }

    /**
     * Generate RT act .pdf file and save on the server.
     * 
     * @return  string $fileName
     */
    public function generateActFile($html)
    {   
        /* Mpdf */
        $fileName = uniqid().'.pdf';
        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->Output(public_path('uploads/defect_acts/'.$fileName), \Mpdf\Output\Destination::FILE);
        
        /* Dompdf */
        // $dompdf = new Dompdf();
        // $dompdf->loadHtml($this->html);
        // $dompdf->render();
        // file_put_contents(public_path('/uploads/defect_acts/'.$fileName), $dompdf->output());

        return $fileName;
    }

    /**
     * Send defect act .pdf file to email.
     */
    public function sendActFile($sto_slug, $actId)
    {
        $act = DefectAct::find($actId);
        $card = CarCard::where('id', $act->car_card_id)->first();
        $car = Car::where('id', $card->car_id)->with('drivers')->first();
        $fileName = $act->partial_file;

        $driver = [];
        foreach($car->drivers as $dr) {
            if($dr->pivot->active === 1) {
                $driver = $dr;
            }
        }

        if($driver !== null) {
            if($driver->email !== null) {
                Mail::send('welcome', [], function($message) use ($driver, $fileName) {
                    $message->subject('Дефектный акт');
                    $message->from('sto@the55group.com');
                    $message->to($driver->email);
                    $message->attach(public_path('/uploads/defect_acts/'.$fileName), [
                        'as' => 'Дефектный акт',
                        'mime' => 'application/pdf'
                    ]);
                });
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Дефектный акт успешно отправлен на почту.',
        ]);
    }

    /**
     * Download defect act .pdf file.
     * 
     * @param   \Illuminate\Http\Request $request
     * 
     * @return  \Illuminate\Http\Response
     */
    public function downloadFile(Request $request) 
    {
        $act = DefectAct::find($request->id);
        $file = '';
        $filename = '';
        $headers = [
            'Content-Type' => 'application/pdf',
        ];
        if($request->type === 'partial') {
            $file = public_path('/uploads/defect_acts/'.$act->partial_file);
            $filename = $act->partial_file;
        } else {
            $file = public_path('/uploads/defect_acts/'.$act->full_file);
            $filename = $act->full_file;
        }
        
        return response()->download($file, $filename, $headers);
    }

    /**
     * 
     */
    public function addMoreFiles($sto_slug, $id, Request $request)
    {
        $defectAct = DefectAct::find($id);
        $defectAttachments = [];
        // Loop through all defect act attachments
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
                $compressedImage->save(public_path('uploads/attachments/defect_acts/'.$fileNameToStore));
                
                array_push($defectAttachments, new Attachment([
                    'attachment' => $fileNameToStore,
                    'attachment_name' => $fileFullName,
                    'attachment_ext' => strtolower($fileExtension)
                ]));
            }
        } else {
            $fileNameToStore = null;
        }

        if($fileNameToStore !== null)
            $defectAct->attachments()->saveMany($defectAttachments);

        $defectAct->load('attachments');

        return response()->json([
            'success' => true,
            'message' => 'Файлы успешно добавлены.',
            'attachments' => $defectAct->attachments
        ]);
    }

    /**
     * 
     */
    public function getById($sto_slug, $id)
    {
        $defectAct = DefectAct::select('defect_acts.*', 'users.fullname as username')
            ->leftJoin('users', 'users.id', '=', 'defect_acts.user_id')
            ->where('defect_acts.id', $id)->with([
                'defects.defect_options',
                'equipment', 
                'defects.defect_conclusions',
                'attachments',
                'defects.comments' => function($query) use ($id) {
                    $query->where('defect_act_id', $id)->get();
                }
            ])->first();

        $card = CarCard::find($defectAct->car_card_id);
        $car = Car::select('cars.*', 'shape_name', 'brand_name', 'model_name', 'engine_type_name', 'transmission_name')
            ->join('car_shapes', 'car_shapes.id', '=', 'cars.shape_id')
            ->join('car_models', 'car_models.id', '=', 'cars.model_id')
            ->join('car_brands', 'car_brands.id', '=', 'cars.brand_id')
            ->join('engine_types', 'engine_types.id', '=', 'cars.engine_type_id')
            ->join('transmissions', 'transmissions.id', '=', 'cars.transmission_id')
            ->with('companies')
            ->where('sold', 0)
            ->where('cars.id', $card->car_id)->with([ 'drivers' => function($q) {
                $q->where('active', 1)->get();
            }])->first();
        
        $defectInfo = DefectType::with('defects.defect_conclusions', 'defects.defect_options')->get();
        $defaultDefectConditions = DB::table('defect_options')->select('*')->where('defect_id', null)->get();
        $defaultDefectConclusion = DB::table('defect_conclusions')->select('*')->where('defect_id', null)->get();        
        // Put default defect options into every defect
        foreach($defectInfo as $defectType) {
            foreach($defectType->defects as $defect) {
                foreach($defaultDefectConditions as $defectOption) {
                    $defect->defect_options->push($defectOption);
                }

                foreach($defaultDefectConclusion as $defectConclusion) {
                    $collection = collect([
                        'defect_id' => $defect->id,
                        'id' => $defectConclusion->id,
                        'conclusion_name' => $defectConclusion->conclusion_name,
                        'deleted' => $defectConclusion->deleted,
                        'price' => []
                    ]);
    
                    $defect->defect_conclusions->push($collection);
                }
            }
        }
        $equipment = EquipmentType::all();
        $chosenDefectConditions = DB::table('defect_defect_option')
            ->select('defects.defect_name', 'defects.id as defect_id', 'defect_options.id as id', 'defect_options.defect_option_name')
            ->join('defects', 'defect_id', '=', 'defects.id')
            ->join('defect_options', 'defect_option_id', '=', 'defect_options.id')
            ->where('defect_defect_option.defect_act_id', $id)
            ->get();
        $chosenDefectConclusions = DB::table('defect_defect_conclusion')
            ->select('defects.defect_name', 'defects.id as defect_id', 'defect_conclusions.id as id', 'defect_conclusions.conclusion_name', 'service_price')
            ->join('defects', 'defect_id', '=', 'defects.id')
            ->join('defect_conclusions', 'defect_conclusion_id', '=', 'defect_conclusions.id')
            ->where('defect_defect_conclusion.defect_act_id', $id)
            ->get();
        
        return response()->json([
            'success' => true,
            'act' => $defectAct,
            'car' => $car,
            'equipment' => $equipment,
            'defectsInfo' => $defectInfo,
            'actDefectConditions' => $chosenDefectConditions,
            'actDefectConclusions' => $chosenDefectConclusions
        ]);
    }
}
