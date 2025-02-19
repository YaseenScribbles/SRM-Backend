<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {

        $user_id = $request->query('user_id');
        $from_date = $request->query('from_date');
        $to_date = $request->query('to_date');

        //browsing_user
        $role = auth()->user()->role;
        $user = auth()->user()->id;

        if ($role == 'manager') {
            $sql = "select id from users where manager_id = $user order by name";
        } elseif ($role == 'admin') {
            $sql = "select id from users order by name";
        } else {
            $sql = "select id from users where id = $user";
        }
        $user_ids = DB::select($sql);
        $idString = collect($user_ids)->pluck('id')->implode(',');

        $visitsSql = "select c.name [contact], p.name [purpose], u.name [user],v.created_at [time]
        from visits v
        inner join contacts c on c.id = v.contact_id
        inner join purposes p on p.id = v.purpose_id
        inner join users u on u.id = v.user_id where convert(date,v.created_at) between '$from_date' and '$to_date'";


        if ($user_id) {
            $visitsSql .= " and u.id = $user_id";
        } else {
            $visitsSql .= " and u.id in ($idString)";
        }

        $visitsSql .= " order by v.created_at desc";

        $ordersSql = "select c.name [contact], sum(oi.qty) [qty], u.name [user], o.created_at [time]
        from orders o
        inner join order_items oi on o.id = oi.order_id
        inner join contacts c on c.id = o.contact_id
        inner join users u on u.id  = o.user_id where convert(date,o.created_at) between '$from_date' and '$to_date'";

        if ($user_id) {
            $ordersSql .= " and u.id = $user_id";
        } else {
            $ordersSql .= " and u.id in ($idString)";
        }

        $ordersSql .= " group by c.name, u.name, o.created_at order by o.created_at desc";

        $countSql = "select u.name [user], count(distinct v.id) [visits], count(distinct o.id) [orders], sum(o.quantity) [quantity]
        from users u
        left join visits v on v.user_id = u.id
        left join (select o.id, o.user_id, o.created_at, sum(oi.qty) [quantity]
        from orders o
        inner join order_items oi on o.id = oi.order_id
        group by o.id, o.user_id, o.created_at) o on o.user_id = u.id where convert(date,v.created_at) between '$from_date' and '$to_date' and
        convert(date,o.created_at) between '$from_date' and '$to_date'";

        if ($user_id) {
            $countSql .= " and u.id = $user_id";
        } else {
            $countSql .= " and u.id in ($idString)";
        }

        $countSql .= " group by u.name order by [orders] desc";

        $orderItemsSql = "select b.name [brand], b.style, sum(oi.qty) [quantity], u.name [user]
        from order_items oi
        inner join orders o on o.id = oi.order_id
        inner join brands b on b.size_id = oi.size_id
        inner join users u on u.id = o.user_id where convert(date,o.created_at) between '$from_date' and '$to_date'";

        if ($user_id) {
            $orderItemsSql .= " and u.id = $user_id";
        } else {
            $orderItemsSql .= " and u.id in ($idString)";
        }

        $orderItemsSql .= " group by b.name, b.style, u.name, o.created_at order by o.created_at desc";

        $stateWiseSql = "select s.name, count(distinct o.id) orders, sum(oi.qty) [qty]
        from orders o
        inner join order_items oi on oi.order_id = o.id
        inner join contacts c on o.contact_id = c.id
        inner join states s on c.state_id = s.id where convert(date,o.created_at) between '$from_date' and '$to_date'";

        if ($user_id) {
            $stateWiseSql .= " and o.user_id = $user_id";
        } else {
            $stateWiseSql .= " and o.user_id in ($idString)";
        }

        $stateWiseSql .= " group by s.name order by [qty] desc";

        $districtWiseSql = "select c.district, count(distinct o.id) orders, sum(oi.qty) [qty]
        from orders o
        inner join order_items oi on oi.order_id = o.id
        inner join contacts c on o.contact_id = c.id
        where convert(date,o.created_at) between '$from_date' and '$to_date'";

        if ($user_id) {
            $districtWiseSql .= " and o.user_id = $user_id";
        } else {
            $districtWiseSql .= " and o.user_id in ($idString)";
        }

        $districtWiseSql .= " group by c.district order by [qty] desc";

        $visits = DB::select($visitsSql);
        $orders = DB::select($ordersSql);
        $count = DB::select($countSql);
        $orderItems = DB::select($orderItemsSql);
        $states = DB::select($stateWiseSql);
        $districts = DB::select($districtWiseSql);

        return response()->json(compact('visits', 'orders', 'count', 'orderItems', 'states', 'districts'));
    }
}
