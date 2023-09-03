<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\ProductStoreRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $products = Product::all();

        return response()->json([
            'products'=> $products
        ],200);
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
    public function store(ProductStoreRequest $request)
    {
        try {
            $imageName = Str::random(32) . "." . $request->image->getClientOriginalExtension();

            Product::create([
                'name' => $request->name,
                'image' => $imageName,
                'description' => $request->description,
            ]);

            Storage::disk('public')->put($imageName, file_get_contents($request->image));

            return response()->json([
                'message' => 'berhasil menambahkan data'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'ada yang salah'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);

        if(!$product){
            return response()->json([
                'massage' => 'file tidak ditemukan'
            ], 404);
        }
        return response()->json([
            'product'=> $product
        ],200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductStoreRequest $request, $id)
    {
        try {
            // Find product
            $product = Product::find($id);
            if(!$product){
              return response()->json([
                'message'=>'Product Not Found.'
              ],404);
            }
      
            //echo "request : $request->image";
            $product->name = $request->name;
            $product->description = $request->description;
      
            if($request->image) {
 
                // Public storage
                $storage = Storage::disk('public');
      
                // Old iamge delete
                if($storage->exists($product->image))
                    $storage->delete($product->image);
      
                // Image name
                $imageName = Str::random(32).".".$request->image->getClientOriginalExtension();
                $product->image = $imageName;
      
                // Image save in public folder
                $storage->put($imageName, file_get_contents($request->image));
            }
      
            // Update Product
            $product->save();
      
            // Return Json Response
            return response()->json([
                'message' => "Product successfully updated."
            ],200);
        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'message' => "Something went really wrong!"
            ],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
          // Find product
          $product = Product::find($id);
          if(!$product){
            return response()->json([
              'message'=>'Product Not Found.'
            ],404);
          }

             // Public storage
             $storage = Storage::disk('public');
      
             // Old iamge delete
             if($storage->exists($product->image))
                 $storage->delete($product->image);

            $product->delete();

            return response()->json([
                'message' => "berhasil menghapus"
            ],200);
    }
}
