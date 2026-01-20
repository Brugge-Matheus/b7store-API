<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryMetadataRequest;
use App\Models\Category;
use Exception;
use OpenApi\Attributes as OA;

class CategoryController extends Controller
{
    #[OA\Get(
        path: "/api/category/{slug}/metadata",
        tags: ["Category"],
        summary: "Obter metadados de uma categoria",
        description: "Retorna informações da categoria e seus metadados com valores"
    )]
    #[OA\Parameter(
        name: "slug",
        in: "path",
        description: "Slug da categoria",
        required: true,
        schema: new OA\Schema(type: "string", example: "smartphones")
    )]
    #[OA\Response(response: 200, description: "Metadados da categoria")]
    #[OA\Response(response: 500, description: "Erro ao listar metadados")]
    public function metadata(CategoryMetadataRequest $request) 
    {
        try {
            $params = $request->validated();

            $category = Category::select(['id', 'name', 'slug'])
            ->with(['categoryMetadata:id,name,category_id' => ['values:id,label,category_metadata_id']])
            ->where('slug', $params['slug'])
            ->first();

            $category = Category::with(['categoryMetadata' => ['values']])
            ->where('slug', $params['slug'])
            ->first();

            $metadatas = $category->categoryMetadata->map(function ($metadata) {
                $values = $metadata->values->map(function ($value) {
                    return [
                        'id' => $value->id,
                        'label' => $value->label
                    ];
                });
                return [
                    'id' => $metadata->id,
                    'name' => $metadata->name,
                    'values' => $values
                ];
            });

            return response()->json([
                'error' => null,
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug
                ],
                'metadata' => $metadatas
            ]);

        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Erro ao listar metadatas',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
