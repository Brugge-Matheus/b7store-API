<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductIndexRequest;
use App\Http\Requests\ProductRelatedRequest;
use App\Http\Requests\ProductShowRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Exception;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ProductIndexRequest $request)
    {
        try {
            $query = Product::with('images');
            $params = $request->validated();

            if(isset($params['orderby'])) {
                $column = match($params['orderby']) {
                    'views' => 'views_count',
                    'selling' => 'sales_count',
                    'price' => 'price',
                    default => 'id'
                };

                $query->orderBy($column, 'desc');
            }

            if(isset($params['limit'])) {
                $query->limit((int) $params['limit']);
            }
            
            if(isset($params['metadata'])) {
                $rawMetadata = json_decode($request->get('metadata'), true);
                foreach($rawMetadata as $key => $metadata) {
                    $query->whereHas('metadata', fn($q) => 
                        $q->where('category_metadata_id', $key)
                        ->where('metadata_value_id', $metadata)
                    );
                }
            }
            
            $products = $query->get();
                
            $response = $products->map(function ($product) {
                return [
                    'id' =>  $product->id,
                    'label' => $product->label,
                    'price' => $product->price,
                    'formatted_price' => $product->formatted_price,
                    'image' => asset('storage/' . ($product->images->first()->url ?? 'default-imagem.jpg')),
                    'liked' => false
                ];
            });

            return response()->json([
                'error' => null,
                'products' => $response
            ]);

        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Erro ao listar produtos',
                'details' => $e->getMessage()
            ], 500);
        }
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
    public function show(ProductShowRequest $request)
    {
        try {
            $params = $request->validated();

            $product = Product::with('images')->find($params['id']);
            $product->increment('views_count');

            $images = $product->images->map(fn($image) => ['url' => asset('storage/' . $image->url)]);

            if ($images->isEmpty()) {
                $images[] = ['url' => asset('storage/' . 'default-imagem.jpg')];
            }

            return response()->json([
                'error' => null,
                'product' => [
                    'id' => $product->id,
                    'label' => $product->label,
                    'price' => $product->price,
                    'views_count' => $product->views_count,
                    'formatted_price' => $product->formatted_price,
                    'description' => $product->description,
                    'categoryId' => $product->category->id,
                    'images' => $images 
                ],
                'category' => [
                    'id' => $product->category->id,
                    'name' => $product->category->name,
                    'slug' => $product->category->slug
                ]
            ]);    

        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Erro ao listar produto',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function related(ProductRelatedRequest $request) 
    {
        try {
            $params = $request->validated();

            $product = Product::find($params['id']);
            $query = Product::query();

            $query->with('images')
            ->where('category_id', $product->category->id)
            ->whereNot('id', $product->id);

            if (isset($params['limit'])) {
                $limit = (int) $params['limit'];
                $query->limit($limit);
            }

            $relatedProducts = $query->get();

            $response = [];
            foreach($relatedProducts as $relatedProduct) {
                $images = $relatedProduct->images->map(fn($image) => ['url' => asset('storage/' . $image->url)]);

                if ($images->isEmpty()) {
                    $images[] = ['url' => asset('storage/' . 'default-imagem.jpg')];
                }

                $response[] = [
                    'product' => [
                        'id' => $relatedProduct->id,
                        'label' => $relatedProduct->label,
                        'price' => $relatedProduct->price,
                        'views_count' => $relatedProduct->views_count,
                        'formatted_price' => $relatedProduct->formatted_price,
                        'description' => $relatedProduct->description,
                        'categoryId' => $relatedProduct->category->id,
                        'images' => $images 
                    ],
                    'category' => [
                        'id' => $relatedProduct->category->id,
                        'name' => $relatedProduct->category->name,
                        'slug' => $relatedProduct->category->slug
                    ]
                ];
            };

            return response()->json([
                'error' => null,
                'products' => $response
            ]);

        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Erro ao listar produtos relacionados',
                'details' => $e->getMessage()
            ], 500);
        }
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
