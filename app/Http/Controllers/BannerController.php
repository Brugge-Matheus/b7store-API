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
        description: "Retorna lista de banners com imagens e links"
    )]
    #[OA\Response(
        response: 200,
        description: "Lista de banners"
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
