<?php

namespace App\Http\Controllers;

use App\Models\Order;
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

    public function order_pdf(Order $order)
    {
        try {
            //code...
            $masterSql = "select o.id,
            convert(varchar, o.created_at, 34) [date],
            c.name [contact],
            c.address + ', ' + c.city + ', ' +
            c.district + ', ' + s.name + ' - ' + c.pincode [address],
            c.phone,
            o.remarks,
            u.name [user]
            from orders o
            inner join contacts c on c.id = o.contact_id
            inner join users u on u.id = o.user_id
            inner join states s on s.id = c.state_id
            where o.id = $order->id";

            $detailsSql = "select oi.s_no, b.name, b.style, b.size, oi.qty
            from order_items oi
            inner join brands b on oi.size_id = b.size_id
            where oi.order_id = $order->id
            order by oi.s_no";

            $master = DB::select($masterSql);
            $details = DB::select($detailsSql);

            return response()->json(compact('master', 'details'));
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }
}
