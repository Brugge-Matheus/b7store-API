<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductIndexRequest;
use App\Http\Requests\ProductRelatedRequest;
use App\Http\Requests\ProductShowRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Exception;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    #[OA\Get(
        path: "/api/product",
        tags: ["Product"],
        summary: "Listar produtos",
        description: "Retorna lista de produtos com filtros opcionais de ordenação, limite e metadados"
    )]
    #[OA\Parameter(
        name: "orderby",
        in: "query",
        description: "Ordenar produtos por: 'views' (mais vistos), 'selling' (mais vendidos), 'price' (preço)",
        required: false,
        schema: new OA\Schema(type: "string", enum: ["views", "selling", "price"], example: "views")
    )]
    #[OA\Parameter(
        name: "limit",
        in: "query",
        description: "Número máximo de produtos a retornar",
        required: false,
        schema: new OA\Schema(type: "integer", example: 10)
    )]
    #[OA\Parameter(
        name: "metadata",
        in: "query",
        description: "Filtro JSON de metadados no formato: {\"metadata_id\": \"value_id\"}",
        required: false,
        schema: new OA\Schema(type: "string", example: '{"1": "5"}')
    )]
    #[OA\Response(
        response: 200,
        description: "Lista de produtos retornada com sucesso",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "error", type: "null", example: null),
                new OA\Property(
                    property: "products",
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "label", type: "string", example: "iPhone 14 Pro"),
                            new OA\Property(property: "price", type: "number", format: "float", example: 5999.90),
                            new OA\Property(property: "formatted_price", type: "string", example: "R$ 5.999,90"),
                            new OA\Property(property: "image", type: "string", example: "http://localhost:8000/storage/media/products/iphone14.jpg"),
                            new OA\Property(property: "liked", type: "boolean", example: false)
                        ]
                    )
                )
            ]
        )
    )]
    #[OA\Response(
        response: 500,
        description: "Erro ao listar produtos",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "error", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Erro ao listar produtos"),
                new OA\Property(property: "details", type: "string", example: "Database connection failed")
            ]
        )
    )]
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

    #[OA\Get(
        path: "/api/product/{id}",
        tags: ["Product"],
        summary: "Obter detalhes do produto",
        description: "Retorna informações completas de um produto específico incluindo categoria e todas as imagens. Incrementa o contador de visualizações."
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        description: "ID do produto",
        required: true,
        schema: new OA\Schema(type: "integer", example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: "Detalhes do produto retornados com sucesso",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "error", type: "null", example: null),
                new OA\Property(
                    property: "product",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "label", type: "string", example: "iPhone 14 Pro"),
                        new OA\Property(property: "price", type: "number", format: "float", example: 5999.90),
                        new OA\Property(property: "description", type: "string", example: "Smartphone Apple com chip A16 Bionic"),
                        new OA\Property(property: "categoryId", type: "integer", example: 1),
                        new OA\Property(
                            property: "images",
                            type: "array",
                            items: new OA\Items(type: "string", example: "http://localhost:8000/storage/media/products/iphone14-1.jpg")
                        )
                    ],
                    type: "object"
                ),
                new OA\Property(
                    property: "category",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Smartphones"),
                        new OA\Property(property: "slug", type: "string", example: "smartphones")
                    ],
                    type: "object"
                )
            ]
        )
    )]
    #[OA\Response(
        response: 500,
        description: "Erro ao buscar produto",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "error", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Erro ao listar produto"),
                new OA\Property(property: "details", type: "string")
            ]
        )
    )]
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

    #[OA\Get(
        path: "/api/product/{id}/related",
        tags: ["Product"],
        summary: "Listar produtos relacionados",
        description: "Retorna produtos da mesma categoria do produto especificado, excluindo o próprio produto"
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        description: "ID do produto de referência",
        required: true,
        schema: new OA\Schema(type: "integer", example: 1)
    )]
    #[OA\Parameter(
        name: "limit",
        in: "query",
        description: "Número máximo de produtos relacionados a retornar",
        required: false,
        schema: new OA\Schema(type: "integer", example: 4)
    )]
    #[OA\Response(
        response: 200,
        description: "Lista de produtos relacionados",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "error", type: "null", example: null),
                new OA\Property(
                    property: "products",
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 2),
                            new OA\Property(property: "label", type: "string", example: "iPhone 13"),
                            new OA\Property(property: "price", type: "number", format: "float", example: 4999.90),
                            new OA\Property(property: "formatted_price", type: "string", example: "R$ 4.999,90"),
                            new OA\Property(property: "image", type: "string", example: "http://localhost:8000/storage/media/products/iphone13.jpg"),
                            new OA\Property(property: "liked", type: "boolean", example: false)
                        ]
                    )
                )
            ]
        )
    )]
    #[OA\Response(
        response: 500,
        description: "Erro ao listar produtos relacionados",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "error", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Erro ao listar produtos relacionados"),
                new OA\Property(property: "details", type: "string")
            ]
        )
    )]
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
