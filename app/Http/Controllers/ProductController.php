<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();

        return response()->json($products);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {

        //Kita pindah validasi dibawah ini menjadi di function rules() di dalam class storeProductRequest yang berada di folder app/Http/Requests

        // $request->validate([
        //     'name' => 'required',
        //     'description' => 'required',
        //     'price' => 'required',
        //     'stock' => 'required',
        // ]);


        $product = new Product;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->favorite = false;
        $product->status = 'published';
        $product->save();

        if ($request->file('image')) {
            $image = $request->file('image');
            $image->storeAs('public/products', $product->id . '.png');
            $product->image = $product->id . '.png';
            $product->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Berhasil Create Product'
        ], 200);
    }

    /**
     * Display the specified resource.
     */

    //Jika kita GET api dengan endpoint /product/{id} maka
    // dia akan memanggil function/method show dengan parameter id yang kita terima
    // dari parameter yang ada di setelah endpoint  

    //function show untuk menampilkan product berdasarkan  id yang dimasukkan/ditaruh setleah endpoint product
    // contoh kita get API /product/2 maka maksudnya kita akan menampilkan product dengan id 2
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
                'status' => 'error'
            ]);
        }

        return response()->json($product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
                'status' => 'error'
            ]);
        }

        $product->name = $request->name;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->stock = $request->stock;
        $product->favorite = $request->favorite;
        $product->status = $request->status;
        $product->save();

        if ($request->file('image')) {
            $image = $request->file('image');
            $image->storeAs('public/products', $product->id . '.png');
            $product->image = $product->id . '.png';
            $product->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Berhasil Edit Product'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
                'status' => 'error'
            ]);
        }

        $product->delete();

        return response()->json([
            'status' => true,
            'message' => 'Berhasil Menghapus Product'
        ], 200);
    }
}
