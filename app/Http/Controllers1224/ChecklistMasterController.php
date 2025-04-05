<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChecklistMaster;
use Illuminate\Support\Facades\Validator;

class ChecklistMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $checklistMasters = ChecklistMaster::with('group')->get();
        return response()->json(['checklist_masters' => $checklistMasters]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required|max:255',
            'instruction' => 'required|max:255',
            'group_id' => 'required|exists:groups,id',
            'answer' => 'required|max:20',
            // Add validation for other fields
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 403);
        }
        $checklistMaster = ChecklistMaster::create($request->all());
        return response()->json(['checklist_master' => $checklistMaster], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $checklistMaster = ChecklistMaster::findOrFail($id);
        return response()->json(['checklist_master' => $checklistMaster]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    public function count()
    {
        $count = ChecklistMaster::count();

        return response()->json(['count' => $count]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $checklistMaster = ChecklistMaster::findOrFail($id);

        // $request->validate([
        //     'question' => 'required|max:255',
        //     'instruction' => 'required|max:255',
        //     'group_id' => 'required|exists:groups,id',
        //     'answer' => 'required|max:20',
        //     // Add validation for other fields
        // ]);

        $checklistMaster->update($request->all());
        return response()->json(['checklist_master' => $checklistMaster]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $checklistMaster = ChecklistMaster::findOrFail($id);
        $checklistMaster->delete();
        return response()->json(1, 204);
    }
}
