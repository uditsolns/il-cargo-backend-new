<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Checklist;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ChecklistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    // If the user is not an admin, fetch only the checklists associated with the user's group
    $checklists = Checklist::with('cargo')->latest()->get();
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
    $cargo_id = $request->input('cargo_id');
    $questions = $request->input('questions') ?? [];
    $answers = $request->file('answers') ?? [];
    $instructions = $request->input('instructions') ?? [];
    $answer_types = $request->input('answer_type') ?? [];

    // Use a database transaction for atomicity
    DB::beginTransaction();

    try {
        // Delete existing records with the given cargo_id
        Checklist::where('cargo_id', $cargo_id)->delete();

        // Prepare an array to store checklist items
        $checklistItems = [];

        // Iterate through the questions array
        foreach ($questions as $key => $question) {
            $answer = null;
            if (in_array($answer_types[$key], [1, 2]) && isset($answers[$key])) {
                $answer = $this->handleFileUpload($answers[$key], 'public/answers');
            } else {
                $answer = $request->input('answers')[$key] ?? null;
            }

            $checklistItems[] = [
                'cargo_id' => $cargo_id,
                'question' => $question,
                'answer_type' => $answer_types[$key],
                'answer' => $answer,
                'instruction' => $this->removeDoubleQuotes(json_encode($instructions[$key] ?? null)),
            ];
        }

        // Create new checklist items
        Checklist::insert($checklistItems);

        // Commit the transaction
        DB::commit();

        return response()->json(['message' => 'Checklist items saved or updated successfully'], 201);
    } catch (\Exception $e) {
        // Something went wrong, rollback the transaction
        DB::rollback();

        return response()->json(['error' => $e->getMessage()], 500);
    }
} 
    private function removeDoubleQuotes($text)
{
    return str_replace(['"', '\\"'], '', $text);
} 
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $checklist = Checklist::where('id', $id)->with('checklistPhotos')->first();
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
    $cargo_id = $request->input('cargo_id');
    $questions = $request->input('questions') ?? [];
    $answers = $request->file('answers') ?? [];
    $instructions = $request->input('instructions') ?? [];
    $answer_types = $request->input('answer_type') ?? [];

    // Use a database transaction for atomicity
    DB::beginTransaction();

    try {
        // Delete existing records with the given cargo_id
        Checklist::where('cargo_id', $cargo_id)->delete();

        // Prepare an array to store checklist items
        $checklistItems = [];

        // Iterate through the questions array
        foreach ($questions as $key => $question) {
            $answer = null;
            // if (in_array($answer_types[$key], [1, 2]) && isset($answers[$key])) {
            //     $answer = $this->handleFileUpload($answers[$key], 'public/answers');
            // } else {
            //     $answer = $request->input('answers')[$key] ?? null;
            // }
            
            if (in_array($answer_types[$key], [1, 2]) && isset($answers[$key])) {
            $file = $answers[$key];
            if ($file->isValid()) {
        // $answer = $file->store('public/answers');
        $filePath = $file->store('public/answers');
        
        // Extract the file name from the path
        $fileName = basename($filePath);
        
        $answer = $fileName; // Store only the file name
            
            }
            } else {
            $answer = $request->input('answers')[$key] ?? null;
            }

            $checklistItems[] = [
                'cargo_id' => $cargo_id,
                'question' => $question,
                'answer_type' => $answer_types[$key],
                'answer' => $answer,
                'instruction' => $this->removeDoubleQuotes(json_encode($instructions[$key] ?? null)),
            ];
        }

        // Create new checklist items
        Checklist::insert($checklistItems);

        // Commit the transaction
        DB::commit();

        return response()->json(['message' => 'Checklist items saved or updated successfully'], 201);
    } catch (\Exception $e) {
        // Something went wrong, rollback the transaction
        DB::rollback();

        return response()->json(['error' => $e->getMessage()], 500);
    }
}

// Helper method to handle file upload
private function handleFileUpload($file, $path)
{
    if ($file) {
        return $file->getClientOriginalName(); // Returns only the filename
    }
    return null;
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
