<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $sql = "select c.id, c.name, c.address, c.city, c.district, s.name [state],
        // c.phone, c.pincode, c.email, d.name [distributor]
        // from contacts c
        // inner join states s on c.state_id = s.id
        // left join distributors d on d.id = c.distributor_id";

        $contacts = Contact::query()
        ->from('contacts as c')
        ->join('states as s', 'c.state_id', '=', 's.id')
        ->leftJoin('distributors as d', 'd.id', '=', 'c.distributor_id')
        ->select(
            'c.id',
            'c.name',
            'c.address',
            'c.city',
            'c.district',
            's.name as state',
            'c.phone',
            'c.pincode',
            'c.email',
            'd.name as distributor'
        )
        ->get();

        // $id = Auth::user()->id;
        // $role = Auth::user()->role;
        // if ($role === 'manager') {
        //     $user_ids = DB::select("select id from users where manager_id = ?", [$id]);
        //     // Extracting only the 'id' values
        //     $user_ids = array_column($user_ids, 'id');

        //     // Convert the array into a comma-separated string
        //     $user_ids_str = implode(',', $user_ids);

        //     if (!empty($user_ids_str)) {
        //         $sql .= " where c.user_id in ($user_ids_str)";
        //     }
        // } elseif ($role === 'user') {
        //     $sql .= " where c.user_id = $id";
        // }

        // $contacts = DB::select($sql);
        return response()->json(compact('contacts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactRequest $request)
    {
        $data = $request->validated();
        try {
            Contact::create($data);
            return response()->json(['message' => 'Contact added successfully']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        return $contact;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContactRequest $request, Contact $contact)
    {
        $data = $request->validated();
        try {
            $contact->update($data);
            return response()->json(['message' => 'Contact updated successfully']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        try {
            $contact->delete();
            return response()->json(['message' => 'Contact deleted successfully']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()]);
        }
    }
}
