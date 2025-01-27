<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $sql = "select o.id, convert(date,o.created_at) [date], c.name [contact], o.remarks, u.name [user], sum(oi.qty) [quantity]
        from orders o
        inner join order_items oi on oi.order_id = o.id
        inner join contacts c on c.id = o.contact_id
        inner join users u on u.id = o.user_id
        group by o.id, c.name, o.remarks, u.name, o.created_at";

        $orders = DB::select($sql);
        return response()->json(compact('orders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $request->validated();
        try {
            $masterData = $request->except('order_items');
            $details = $request->only('order_items');
            DB::beginTransaction();
            $order = Order::create($masterData);
            foreach ($details['order_items'] as $key => $value) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'size_id' => $value['size_id'],
                    'qty' => $value['qty'],
                    's_no' => $key + 1
                ]);
            }
            DB::commit();

            return response()->json(['message' => 'Order created successfully', 'id' => $order->id]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $orderMasterSql = "select contact_id, remarks from orders where id = $order->id";
        $orderMaster = DB::select($orderMasterSql);

        $orderDetailsSql = "select oi.size_id, b.name [brand], b.style, b.size, oi.qty
                            from order_items oi
                            inner join brands b on oi.size_id = b.size_id and oi.order_id = $order->id";
        $orderDetails = DB::select($orderDetailsSql);

        return response()->json(compact('orderMaster', 'orderDetails'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $request->validated();
        try {
            $masterData = $request->except('order_items');
            $details = $request->only('order_items');
            DB::beginTransaction();
            $order->update($masterData);
            OrderItem::where('order_id', $order->id)->delete();

            foreach ($details['order_items'] as $key => $value) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'size_id' => $value['size_id'],
                    'qty' => $value['qty'],
                    's_no' => $key + 1
                ]);
            }
            DB::commit();

            return response()->json(['message' => 'Order updated successfully', 'id' => $order->id]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        try {
            DB::beginTransaction();
            OrderItem::where('order_id', $order->id)->delete();
            $order->delete();
            DB::commit();
            return response()->json(['message' => 'Order deleted successfully']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()]);
        }
    }
}
