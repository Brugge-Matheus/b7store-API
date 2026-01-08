<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ProductRequest $request)
    {
        $query = Product::with('images');
        $params = $request->validated();

        if($request->filled('orderby')) {
            $column = match($params['orderby']) {
                'views' => 'views_count',
                'selling' => 'sales_count',
                'price' => 'price',
                default => 'id'
            };

            $query->orderBy($column);
        }

        if($request->filled('limit')) {
            $query->limit((int) $params['limit']);
        }
        $products = $query->get();

        // if(isset($params['metadata']) && !empty($params['metadata'])) {
        //     $products->orderBy($params['orderby']);
        // }
        
        $response = $products->map(function ($product) {
            return [
                'id' =>  $product->id,
                'label' => $product->label,
                'price' => $product->price,
                'formatted_price' => $product->formatted_price,
                'image' => asset('storage/' . ($product->images()->first()->url ?? 'default-imagem.jpg')),
                'liked' => false
            ];
        });

        return response()->json([
            'error' => null,
            'products' => $response
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
