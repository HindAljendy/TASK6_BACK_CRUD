<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreProduct;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        return $this->customeRespone(ProductResource::collection($products), "All Retrieve Products Success", 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProduct $request)
    {
        try {
            DB::beginTransaction();
            $product = Product::create([
                'name'        => $request->name,
                'price'       => $request->price,
                'quantity'    => $request->quantity
            ]);
            DB::commit();

            return $this->customeRespone(new ProductResource($product),'the Product created successfully',201);

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return $this->customeRespone('',' the Product  not created',500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return $this->customeRespone(new ProductResource($product),'ok',200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'          => 'nullable|string|max:50',
            'price'         => 'nullable|integer',
            'quantity'      => 'nullable|integer'
        ]);

        $newData=[];
        if(isset($request->name)){
            $newData['name'] = $request->name;
        }
        if(isset($request->price)){
            $newData['price'] = $request->price;
        }
        if(isset($request->quantity)){
            $newData['quantity'] = $request->quantity;
        }

        $product->update($newData);

        return $this->customeRespone(new ProductResource($product),'Product Updated',200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return $this->customeRespone("",'the Product deleted',200);
    }
}
