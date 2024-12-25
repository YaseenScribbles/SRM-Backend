<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HelperController extends Controller
{

    public function managers()
    {
        $sql = "select id, name from users where role='manager' order by name";
        $managers = DB::select($sql);
        return response()->json(compact('managers'));
    }

    public function states()
    {
        $sql = "select id, name from states order by name";
        $states = DB::select($sql);
        return response()->json(compact('states'));
    }
}
