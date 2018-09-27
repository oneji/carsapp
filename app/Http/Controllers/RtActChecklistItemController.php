<?php

namespace App\Http\Controllers;

use App\RtActChecklistItem;
use Illuminate\Http\Request;

class RtActChecklistItemController extends Controller
{
    /**
     * Get all checklist items.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function get()
    {
        $checklistItems = RtActChecklistItem::select('rt_act_checklist_items.id', 'item_name', 'checklist_name')
            ->join('rt_act_checklists', 'rt_act_checklist_items.rt_act_checklist_id', 'rt_act_checklists.id')
            ->get();

        return response()->json($checklistItems);
    }

    /**
     * Store a newly created checklist item to a db.
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $checklistItem = new RtActChecklistItem($request->all());
        $checklistItem->save();

        $checklistItem = RtActChecklistItem::select('rt_act_checklist_items.*', 'item_name', 'checklist_name')
            ->join('rt_act_checklists', 'rt_act_checklist_items.rt_act_checklist_id', 'rt_act_checklists.id')
            ->where('rt_act_checklist_items.id', $checklistItem->id)
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Элемент чек листа успешно создан.',
            'checklist_item' => $checklistItem
        ]);
    }

    /**
     * 
     */
    public function update(Request $request, $id) {
        $checklistItem = RtActChecklistItem::where('id', $id)->update([
            'item_name' => $request->item_name,
            'rt_act_checklist_id' => $request->rt_act_checklist_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Элемент чек листа успешно изменен.'
        ]);
    }
}
