<?php

namespace App\Http\Controllers;

use App\Attachment;
use App\DoneAct;
use App\DefectType;
use App\DefectAct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Image;

class DoneActController extends Controller
{
    /**
     * Store a newly created done act in the db.
     */
    public function store(Request $request)
    {
        // Create a new done act
        $doneAct = new DoneAct();
        $doneAct->defect_act_id = $request->defect_act_id;
        $doneAct->car_card_id = $request->car_card_id;
        $doneAct->user_id = $request->user_id;
        $doneAct->save();

        $defectAct = DefectAct::find($request->defect_act_id);
        $defectAct->confirmed = 1;
        $defectAct->save();

        // Bind defects to a done act
        foreach($request->defects as $defect) {
            $doneAct->defects()->attach($defect);
        }

        return response()->json([
            'success' => true,
            'message' => 'Акт выполненных работ успешно создан.',
            'doneAct' => $doneAct
        ]);
    }

    /**
     * 
     */
    public function getById($id)
    {
        $doneAct = DoneAct::where('id', $id)->with([
            'defects.defect_options',
            'defects.defect_conclusions'
        ])->first();

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
        $chosenDefectConditions = DB::table('defect_defect_option')
            ->select('defects.defect_name', 'defects.id as defect_id', 'defect_options.id as id', 'defect_options.defect_option_name')
            ->join('defects', 'defect_id', '=', 'defects.id')
            ->join('defect_options', 'defect_option_id', '=', 'defect_options.id')
            ->where('defect_defect_option.defect_act_id', $doneAct->defect_act_id)
            ->get();
        $chosenDefectConclusions = DB::table('defect_defect_conclusion')
            ->select('defects.defect_name', 'defects.id as defect_id', 'defect_conclusions.id as id', 'defect_conclusions.conclusion_name', 'service_price')
            ->join('defects', 'defect_id', '=', 'defects.id')
            ->join('defect_conclusions', 'defect_conclusion_id', '=', 'defect_conclusions.id')
            ->where('defect_defect_conclusion.defect_act_id', $doneAct->defect_act_id)
            ->get();

        return response()->json([
            'success' => true,
            'act' => $doneAct,
            'defectsInfo' => $defectInfo,
            'actDefectConditions' => $chosenDefectConditions,
            'actDefectConclusions' => $chosenDefectConclusions
        ]);
    }

    /**
     * 
     */
    public function markDetailAsDone($id, Request $request)
    {
        DB::table('defect_done_act')->where([
            'defect_id' => $id,
            'done_act_id' => $request->done_act_id
        ])->update([ 'done' => $request->status ]);

        return response()->json([
            'success' => true,
            'message' => 'Ремонтные работы выполены.'
        ]);
    }

    /**
     * 
     */
    public function send($id, Request $request)
    {
        $doneAct = DoneAct::find($id);
        $doneAct->sent = 1;
        $doneAct->save();
        
        $doneActAttachments = [];
        // Fetch done act attachments
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
                $compressedImage->save(public_path('uploads/attachments/done_acts/'.$fileNameToStore));
                
                array_push($doneActAttachments, new Attachment([
                    'attachment' => $fileNameToStore,
                    'attachment_name' => $fileFullName,
                    'attachment_ext' => strtolower($fileExtension)
                ]));
            }
        } else {
            $fileNameToStore = null;
        }

        // Saving attachments to db
        if($fileNameToStore !== null)
            $doneAct->attachments()->saveMany($doneActAttachments);

        return response()->json([
            'success' => true,
            'message' => 'Акт успешно отправлен.',
            'data' => $request->all()
        ]);
    }

    /**
     * 
     */
    public function confirmAndClose($id)
    {
        $doneAct = DoneAct::where('id', $id)->update([
            'confirmed' => 1,
            'closed' => 1
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Акт подтвержден и закрыт.'
        ]);
    }
}
