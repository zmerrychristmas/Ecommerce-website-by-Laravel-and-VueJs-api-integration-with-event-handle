<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Jobs\ProcessFlashSaleOrder;
use Illuminate\Support\Facades\File; 
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Events\OrderShipmentStatusUpdated;


class ProductController extends Controller
{
    public function index()
    {
        // Redis::keys('*');

        // // Count by name
        // $queueName = 'flashsale';
        // echo Redis::llen('queues:' . $queueName) . '<br/ >';

        // // To count by status:
        // echo Redis::zcount('queues:' . $queueName . ':delayed', '-inf', '+inf'). '<br/ >';
        // echo Redis::zcount('queues:' . $queueName . ':reserved', '-inf', '+inf'). '<br/ >';
        // die;
        // $order = Order::find(54);
        // OrderShipmentStatusUpdated::dispatch($order);
        // broadcast(new OrderShipmentStatusUpdated($order))->toOthers();
        return response()->json(Product::all(),200);
    }

    public function flashsale()
    {
        $userids = DB::table('users')
        ->join('orders', 'users.id', '=', 'orders.user_id')
        ->pluck('orders.product_id')->toArray();
        $products = Product::whereNotIn('id', $userids)
                                                ->where('is_flashsale', true)
                                                ->get();
        return response()->json($products->toArray(), 200);
    }

    /**
     * @$request
     */
    public function store(Request $request)
    {
        if($request->image == null) {
            $request->image = asset("images/default-product-image.png");
        }
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'units' => $request->units,
            'price' => $request->price,
            'is_flashsale' => $request->is_flashsale,
            'image' => $request->image
        ]);

        return response()->json([
            'status' => (bool) $product,
            'data'   => $product,
            'message' => $product ? 'Product Created!' : 'Error Creating Product'
        ]);
    }

    public function show(Product $product)
    {
        return response()->json($product,200); 
    }

    public function uploadFile(Request $request)
    {
        if($request->hasFile('image')){
            $name = time()."_".$request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('images'), $name);

            return response()->json(asset("images/$name"),201);
        } else {
            return response()->json(asset("images/default-product-image.png"),201);
        } 
    }

    public function update(Request $request, Product $product)
    {
        if($request->get('image') != '') {            
            $image_names = explode('/', $product->image);
            if (end($image_names) != 'default-product-image.png') {
                $path = public_path('images') . '/' . end($image_names);
                if (File::exists($path)) {
                    File::delete($path);
                }
            }
        }
        $status = $product->update(
            $request->only(['name', 'description', 'units', 'price', 'is_flashsale','image'])
        );

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Product Updated!' : 'Error Updating Product'
        ]);
    }

    public function updateUnits(Request $request, Product $product)
    {
        $product->units = $product->units + $request->get('units');
        $status = $product->save();

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Units Added!' : 'Error Adding Product Units'
        ]);
    }

    public function updateFlashsale(Request $request, Product $product)
    {
        $product->is_flashsale = $request->get('is_flashsale');
        $status = $product->save();

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Flashsale!' : 'Error change flashsle product'
        ]);
    }

    public function destroy(Product $product)
    {
        $status = $product->delete();

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Product Deleted!' : 'Error Deleting Product'
        ]);
    }
}