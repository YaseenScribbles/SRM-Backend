<?php

namespace App\Http\Controllers;

use App\Jobs\SendOrderPDFMail;
use App\Models\Contact;
use App\Models\Distributor;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Scopes\UserScope;
use App\Models\User;
use App\Models\UserRight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class HelperController extends Controller
{

    public function managers()
    {
        $sql = "select id, name from users where role='manager' order by name";
        $managers = DB::select($sql);
        return response()->json(compact('managers'));
    }

    public function userRoleUsers()
    {
        $user_id = auth()->user()->id;
        $role = auth()->user()->role;
        if ($role == 'manager') {
            $sql = "select id, name from users where manager_id = $user_id  order by name";
        } elseif ($role == 'admin') {
            $sql = "select id, name from users order by name";
        } else {
            $sql = "select id, name from users where id = $user_id";
        }
        $users = DB::select($sql);
        return response()->json(compact('users'));
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

    public function order_pdf(int $orderId)
    {
        try {
            //code...

            $order = Order::withoutGlobalScope(UserScope::class)->find($orderId);

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

    public function sendMailOrderPDF(Request $request)
    {
        $request->validate([
            'pdf' => 'required|file|mimes:pdf',
            'id' => 'required|exists:orders,id'
        ]);

        try {
            //code...
            $order = Order::withoutGlobalScope(UserScope::class)->find($request->id);
            $qty = OrderItem::where('order_id', $order->id)->sum('qty');
            $contact = Contact::withoutGlobalScope(UserScope::class)->find($order->contact_id);
            $emails = [];

            array_push($emails, config('mail.company_email'));

            if ($contact->email) {
                array_push($emails, $contact->email);
            }

            if ($contact->distributor_id) {
                $distributor = Distributor::find($contact->distributor_id);
                array_push($emails, $distributor->email);
            }

            $file = $request->file('pdf');
            $path = $file->store('temp_pdfs');

            SendOrderPDFMail::dispatch($order, $qty, $emails, $path);

            return response()->json(['message' => 'Email sent successfully']);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()]);
        }
    }

    public function userRights(int $id)
    {
        $userRightsSql = "select m.id [menu_id], m.[name] [menu], ur.[create], ur.[view], ur.[update], ur.[delete], ur.[print]
        from menus m
        left join user_rights ur on m.id = ur.menu_id and ur.user_id = $id";

        $name = User::find($id)->name;

        $userRights = DB::select($userRightsSql);
        return response()->json(compact('userRights', 'name'));
    }

    public function updateUserRights($id, Request $request)
    {

        $request->validate([
            'userRights' => 'required|array',
            'userRights.*.menu_id' => 'required|exists:menus,id',
            'userRights.*.create' => 'required|boolean',
            'userRights.*.view' => 'required|boolean',
            'userRights.*.update' => 'required|boolean',
            'userRights.*.delete' => 'required|boolean',
            'userRights.*.print' => 'required|boolean',
        ]);

        try {
            //code...
            DB::beginTransaction();

            //remove old rights
            DB::statement("delete from user_rights where user_id = $id");

            $userRights = $request->userRights;

            foreach ($userRights as $key => $value) {
                UserRight::create([
                    'menu_id' => $value['menu_id'],
                    'user_id' => $id,
                    'create' => $value['create'],
                    'view' => $value['view'],
                    'update' => $value['update'],
                    'delete' => $value['delete'],
                    'print' => $value['print'],
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'User rights updated']);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()]);
        }
    }

    public function menus()
    {
        $menusSql = "select id, name from menus";
        $menus = DB::select($menusSql);
        return response()->json(compact('menus'));
    }
}
