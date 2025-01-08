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

    public function brands(Request $request)
    {
        if (!$request->query('brand')) return response()->json(['message' => "Please provide query for brand"], 400);;
        $brand = $request->query('brand');
        $sql = "select distinct id, name from brands where name like '%$brand%'";
        $brands = DB::select($sql);
        return response()->json(compact('brands'));
    }

    public function styles(Request $request)
    {
        if (!$request->query('brand')) return response()->json(['message' => "Please provide query for brand"], 400);;
        $brand = $request->query('brand');
        $sql = "select distinct style from brands where name = '$brand'";
        $styles = DB::select($sql);
        return response()->json(compact('styles'));
    }

    public function sizes(Request $request)
    {
        if (!$request->query('brand')) return response()->json(['message' => "Please provide query for brand"], 400);;
        if (!$request->query('style')) return response()->json(['message' => "Please provide query for style"], 400);;

        $brand = $request->query('brand');
        $style = $request->query('style');
        $sql = "select distinct size_id, size from brands where name = '$brand' and style = '$style'";
        $sizes = DB::select($sql);
        return response()->json(compact('sizes'));
    }
}
