<?php

namespace App\Http\Controllers;

use App\Models\Distributor;
use App\Http\Requests\StoreDistributorRequest;
use App\Http\Requests\UpdateDistributorRequest;
use Illuminate\Support\Facades\DB;

class DistributorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sql = "select d.id, d.name, d.address, d.city, d.district, s.name [state], d.phone, d.email, d.pincode
        from distributors d
        inner join states s on s.id = d.state_id";

        $distributors = DB::select($sql);
        return response()->json(compact('distributors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDistributorRequest $request)
    {
        $data = $request->validated();
        try {
            Distributor::create($data);
            return response()->json(['message' => 'Distributor added successfully']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Distributor $distributor)
    {
        return $distributor;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDistributorRequest $request, Distributor $distributor)
    {
        $data = $request->validated();
        try {
            $distributor->update($data);
            return response()->json(['message' => 'Distributor updated successfully']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Distributor $distributor)
    {
        try {
            $distributor->delete();
            return response()->json(['message' => 'Distributor deleted successfully']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()]);
        }
    }
}
