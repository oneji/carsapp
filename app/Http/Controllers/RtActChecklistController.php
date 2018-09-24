<?php

namespace App\Http\Controllers;

use App\RtActChecklist;
use App\RtActChecklistItem;
use Illuminate\Http\Request;

class RtActChecklistController extends Controller
{
    /**
     * 
     */
    public function getChecklistsAndChecklistItems()
    {
        $checklists = RtActChecklist::with('checklist_items')->get();
        $checklistItems = RtActChecklistItem::select('rt_act_checklist_items.id', 'item_name', 'checklist_name')
            ->join('rt_act_checklists', 'rt_act_checklist_items.rt_act_checklist_id', 'rt_act_checklists.id')
            ->get();
        
        return response()->json([
            'checklists' => $checklists,
            'checklist_items' => $checklistItems
        ]);
    }   
    
    /**
     * Get all checklists.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function get()
    {
        $checklists = RtActChecklist::with('checklist_items')->get();

        return response()->json($checklists);
    }

    /**
     * Store a newly created checklist to a db.
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $checklist = new RtActChecklist();
        $checklist->checklist_name = $request->checklist_name;
        $checklist->save();

        return response()->json([
            'success' => true,
            'message' => 'Чек лист успешно создан.',
            'checklist' => $checklist
        ]);
    }
}
