<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

class HealthController extends Controller
{
    #[OA\Get(
        path: "/api/ping",
        tags: ["Health"],
        summary: "Health check",
        description: "Endpoint de verificação de saúde da API. Útil para monitoramento e testes de conectividade."
    )]
    #[OA\Response(
        response: 200,
        description: "API está online e funcionando",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "pong", type: "boolean", example: true, description: "Sempre retorna true quando a API está operacional")
            ]
        )
    )]
    public function ping()
    {
        return response()->json([
            'pong' => true
        ]);
    }
}
