<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Auth;
use App\Jobs\ProcessFlashSaleOrder;
use Illuminate\Support\Facades\Redis;

class OrderController extends Controller
{
    public function index()
    {
        return response()->json(Order::with(['product'])->get(),200);
    }

    public function deliverOrder(Order $order)
    {
        $order->is_delivered = true;
        $status = $order->save();

        return response()->json([
            'status'    => $status,
            'data'      => $order,
            'message'   => $status ? 'Order Delivered!' : 'Error Delivering Order'
        ]);
    }

    public function store(Request $request)
    {
        $order = Order::create([
            'product_id' => $request->product_id,
            'user_id' => Auth::id(),
            'quantity' => $request->quantity,
            'address' => $request->address,
            'status' => Order::ORDER_CREATED
        ]);
        for($i = 0; $i < 200; $i ++) {
            ProcessFlashSaleOrder::dispatch($order, $i)->onConnection('redis')->onQueue('flashsale');
        }
        return response()->json([
            'status' => (bool) $order,
            'data'   => $order,
            'message' => $order ? 'Order Created!' : 'Error Creating Order',
            'order' => Redis::llen('queues:' . 'flashsale')
        ]);
    }

    public function flashsaleStore(Request $request)
    {
        $order = Order::create([
            'product_id' => $request->product_id,
            'user_id' => Auth::id(),
            'quantity' => $request->quantity,
            'address' => $request->address,
            'status' => Order::ORDER_PROCESSING
        ]);
        ProcessFlashSaleOrder::dispatch($order)->onConnection('redis')->onQueue('flashsale');
        return response()->json([
            'status' => (bool) $order,
            'data'   => $order,
            'message' => $order ? 'Order Created!' : 'Error Creating Order'
        ]);
    }

    public function show(Order $order)
    {
        return response()->json($order,200);
    }

    public function update(Request $request, Order $order)
    {
        $status = $order->update(
            $request->only(['quantity'])
        );

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Order Updated!' : 'Error Updating Order'
        ]);
    }

    public function destroy(Order $order)
    {
        $status = $order->delete();

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Order Deleted!' : 'Error Deleting Order'
        ]);
    }
}