<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Checklist;
use Illuminate\Support\Facades\Validator;

class ChecklistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $checklists = Checklist::all();
        return response()->json(['checklists' => $checklists]);
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
            'answer' => 'required|max:255',
            'instruction' => 'required|max:255',
            'trip_id' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 403);
        }
        $checklist = Checklist::create($request->all());
   
        return response()->json(['checklist' => $checklist], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $checklist = Checklist::findOrFail($id);
        return response()->json(['checklist' => $checklist]);
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
        $count = Checklist::count();

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
        $checklist = Checklist::findOrFail($id);

        // $validator = Validator::make($request->all(), [
        //     'question' => 'required|max:255',
        //     'answer' => 'required|max:255',
        //     'instruction' => 'required|max:255',
        //     'trip_id' => 'required',
        // ]);
    
        // if ($validator->fails()) {
        //     return response()->json(['error' => $validator->errors()], 403);
        // }

        $checklist->update($request->all());
        return response()->json(['checklist' => $checklist]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $checklist = Checklist::findOrFail($id);
        $checklist->delete();
        return response()->json(1, 204);
    }
}
