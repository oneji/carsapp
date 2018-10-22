<?php

namespace App\Http\Controllers\Sto;

use App\CardCard;
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

class DefectActController extends Controller
{
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
        $equipment = json_decode($request->equipment);
        // Create a new defect act
        $defectAct = new DefectAct();
        $defectAct->car_card_id = $card_id;
        $defectAct->defect_act_date = Carbon::now();
        $defectAct->comment = $comment;
        $defectAct->save();

        // Loop through all defect details' conditions
        foreach($conditions as $detailId => $detailArray) {
            if($detailArray !== null) {
                $arrayOfValues = [];
                foreach($detailArray as $key => $conditionId) {
                    // Generate an array to bulk insert to the db
                    array_push($arrayOfValues, [
                        'defect_id' => $detailId,
                        'defect_option_id' => $conditionId,
                        'defect_act_id' => $defectAct->id
                    ]);
                }
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
                foreach($detailArray as $key => $conclusionId) {
                    // Generate an array to bulk insert to the db
                    array_push($arrayOfValues, [
                        'defect_id' => $detailId,
                        'defect_conclusion_id' => $conclusionId,
                        'defect_act_id' => $defectAct->id
                    ]);
                }
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
            'comment' => $comment,
            'conditions' => $conditions,
            'detail_info' => $detailInfo,
            'detail_conclusions' => $detailConclusions,
            'equipment' => $equipment,
            'success' => true,
            'message' => 'Дефектный акт успешно создан.'
        ]);
    }

    /**
     * 
     */
    public function getById($sto_slug, $id)
    {
        $defectAct = DefectAct::where('id', $id)->with([
            'defects.defect_options',
            'equipment', 
            'defects.defect_conclusions',
            'attachments',
            'defects.comments' => function($query) use ($id) {
                $query->where('defect_act_id', $id)->get();
            }
        ])->first();
        
        $defectInfo = DefectType::with('defects.defect_conclusions', 'defects.defect_options')->get();
        $equipment = EquipmentType::all();
        $chosenDefectConditions = DB::table('defect_defect_option')
            ->select('defects.defect_name', 'defects.id as defect_id', 'defect_options.id as id', 'defect_options.defect_option_name')
            ->join('defects', 'defect_id', '=', 'defects.id')
            ->join('defect_options', 'defect_option_id', '=', 'defect_options.id')
            ->where('defect_defect_option.defect_act_id', $id)
            ->get();
        $chosenDefectConclusions = DB::table('defect_defect_conclusion')
        ->select('defects.defect_name', 'defects.id as defect_id', 'defect_conclusions.id as id', 'defect_conclusions.conclusion_name')
        ->join('defects', 'defect_id', '=', 'defects.id')
        ->join('defect_conclusions', 'defect_conclusion_id', '=', 'defect_conclusions.id')
        ->where('defect_defect_conclusion.defect_act_id', $id)
        ->get();
        
        return response()->json([
            'success' => true,
            'act' => $defectAct,
            'equipment' => $equipment,
            'defectsInfo' => $defectInfo,
            'actDefectConditions' => $chosenDefectConditions,
            'actDefectConclusions' => $chosenDefectConclusions
        ]);
    }
}
