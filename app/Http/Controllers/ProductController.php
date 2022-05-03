<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\File; 


class ProductController extends Controller
{
    public function index()
    {
        return response()->json(Product::all(),200);
    }

    public function flashsale()
    {
        $products = Product::where('is_flashsale', true)->get();

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