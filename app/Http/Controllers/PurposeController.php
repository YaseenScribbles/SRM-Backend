<?php

namespace App\Http\Controllers;

use App\Models\Purpose;
use App\Http\Requests\StorePurposeRequest;
use App\Http\Requests\UpdatePurposeRequest;
use Illuminate\Support\Facades\DB;

class PurposeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sql = "select p.id, p.name, p.active, u.name [user]
                from purposes p
                inner join users u on u.id = p.user_id";
        $purposes = DB::select($sql);
        return response()->json(compact('purposes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePurposeRequest $request)
    {
        $data = $request->validated();
        try {
            Purpose::create($data);
            return response()->json(['message' => 'Purpose added successfully']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Purpose $purpose)
    {
        return $purpose;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePurposeRequest $request, Purpose $purpose)
    {
        $data = $request->validated();
        try {
            $purpose->update($data);
            return response()->json(['message' => 'Purpose updated successfully']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purpose $purpose)
    {
        try {
            $purpose->delete();
            return response()->json(['message' => 'Purpose deleted successfully']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()]);
        }
    }
}
