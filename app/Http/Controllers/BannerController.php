<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use OpenApi\Attributes as OA;

class BannerController extends Controller
{
    #[OA\Get(
        path: "/api/banner",
        tags: ["Banner"],
        summary: "Listar todos os banners",
        description: "Retorna lista de banners promocionais com imagens e links de destino"
    )]
    #[OA\Response(
        response: 200,
        description: "Lista de banners retornada com sucesso",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "error", type: "null", example: null),
                new OA\Property(
                    property: "banners",
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "img", type: "string", example: "http://localhost:8000/storage/media/banners/banner1.jpg"),
                            new OA\Property(property: "link", type: "string", example: "https://example.com/promo")
                        ]
                    )
                )
            ]
        )
    )]
    public function index()
    {
        $banners = Banner::all();

        $response = $banners->map(function ($banner) {
            return [
                'img' => asset('storage/' . $banner->file_path),
                'link' => $banner->link
            ];
        });

        return response()->json([
            'error' => null,
            'banners' => $response
        ]);
    }
}
