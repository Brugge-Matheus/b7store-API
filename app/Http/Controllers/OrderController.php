<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderShowRequest;
use App\Http\Requests\OrderStripeSessionRequest;
use App\Models\Order;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class OrderController extends Controller
{
    #[OA\Get(
        path: "/api/order",
        tags: ["Order"],
        summary: "Listar pedidos do usuário",
        description: "Retorna todos os pedidos do usuário autenticado com informações básicas",
        security: [["sanctum" => []]]
    )]
    #[OA\Response(
        response: 200,
        description: "Lista de pedidos retornada com sucesso",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "error", type: "null", example: null),
                new OA\Property(
                    property: "orders",
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "status", type: "string", enum: ["pending", "paid", "expired", "failed"], example: "pending"),
                            new OA\Property(property: "total", type: "number", format: "float", example: 199.99),
                            new OA\Property(property: "createdAt", type: "string", format: "date-time", example: "2024-07-24T18:49:43.000Z")
                        ]
                    )
                )
            ]
        )
    )]
    #[OA\Response(
        response: 401,
        description: "Não autenticado",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string", example: "Unauthenticated.")
            ]
        )
    )]
    #[OA\Response(
        response: 500,
        description: "Erro ao buscar pedidos",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "error", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Erro ao buscar pedidos"),
                new OA\Property(property: "details", type: "string")
            ]
        )
    )]
    public function index()
    {
        try {
            $rawOrders = Auth::user()->orders()->get();
            $orders = $rawOrders->map(fn($order) => 
                [
                    'id' => $order->id,
                    'status' => $order->status,
                    'total' => $order->total,
                    'createdAt' => $order->created_at
                ]
            );

            return response()->json([
                'error' => null,
                'orders' => $orders
            ]);

        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Erro ao buscar pedidos',
                'details' => $e->getMessage()
            ], 500);
        }
        
    }

    #[OA\Get(
        path: "/api/order/{id}",
        tags: ["Order"],
        summary: "Obter detalhes do pedido",
        description: "Retorna informações completas de um pedido específico incluindo itens, endereço de entrega e dados do usuário",
        security: [["sanctum" => []]]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        description: "ID do pedido",
        required: true,
        schema: new OA\Schema(type: "integer", example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: "Detalhes do pedido retornados com sucesso",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "error", type: "null", example: null),
                new OA\Property(
                    property: "order",
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "pending"),
                        new OA\Property(property: "total", type: "number", format: "float", example: 199.99),
                        new OA\Property(property: "shippingCoast", type: "number", format: "float", example: 7),
                        new OA\Property(property: "shippingDays", type: "integer", example: 3),
                        new OA\Property(property: "shippingZipcode", type: "string", example: "12345-678"),
                        new OA\Property(property: "shippingStreet", type: "string", example: "Rua das Flores"),
                        new OA\Property(property: "shippingNumber", type: "string", example: "123"),
                        new OA\Property(property: "shippingCity", type: "string", example: "São Paulo"),
                        new OA\Property(property: "shippingState", type: "string", example: "SP"),
                        new OA\Property(property: "shippingCountry", type: "string", example: "Brasil"),
                        new OA\Property(property: "shippingComplement", type: "string", example: "Apto 42"),
                        new OA\Property(
                            property: "user",
                            properties: [
                                new OA\Property(property: "name", type: "string", example: "João Silva"),
                                new OA\Property(property: "email", type: "string", example: "joao@example.com")
                            ],
                            type: "object"
                        ),
                        new OA\Property(
                            property: "orderItems",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "quantity", type: "integer", example: 2),
                                    new OA\Property(property: "price", type: "number", format: "float", example: 99.99),
                                    new OA\Property(
                                        property: "product",
                                        properties: [
                                            new OA\Property(property: "id", type: "integer", example: 1),
                                            new OA\Property(property: "label", type: "string", example: "iPhone 14 Pro"),
                                            new OA\Property(property: "price", type: "number", format: "float", example: 99.99),
                                            new OA\Property(property: "image", type: "string", example: "http://localhost:8000/storage/media/products/iphone14.jpg")
                                        ],
                                        type: "object"
                                    )
                                ]
                            )
                        )
                    ],
                    type: "object"
                )
            ]
        )
    )]
    #[OA\Response(
        response: 401,
        description: "Não autenticado",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string", example: "Unauthenticated.")
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: "Pedido não encontrado ou não pertence ao usuário",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "error", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Pedido não encontrado")
            ]
        )
    )]
    public function show(OrderShowRequest $request)
    {
        try {
            $params = $request->validated();

            $user = Auth::user();

            $order = Order::with([
                'orderItems:id,order_id,quantity,price,product_id',
                'orderItems.product:id,label,price',
                'orderItems.product.images:id,product_id,url'
            ])
            ->where('user_id', $user->id)
            ->findOrFail($params['id']);

            $orderItems = $order->orderItems->map(fn($item) => [
                'id' => $item->id,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'product' => [
                    'id' => $item->product->id,
                    'label' => $item->product->label,
                    'price' => $item->product->price,
                    'image' => asset('storage/' . $item->product->images->first()->url ?? 'default-image.jpg')
                ]
            ]);

            return response()->json([
                'error' => null,
                'order' => [
                    'status' => $order->status,
                    'total' => $order->total,
                    'shippingCoast' => $order->shippingCoast,
                    'shippingDays' => $order->shippingDays,
                    'shippingZipcode'=> $order->shippingZipcode,
                    'shippingStreet' => $order->shippingStreet,
                    'shippingNumber' => $order->shippingNumber,
                    'shippingCity' => $order->shippingCity,
                    'shippingState' => $order->shippingState,
                    'shippingCountry' => $order->shippingCountry,
                    'shippingComplement' => $order->shippingComplement,
                    'user' => [
                        'name' => $user->name,
                        'email' => $user->email
                    ],
                    'orderItems' => $orderItems
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Erro ao buscar pedido',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    #[OA\Get(
        path: "/api/order/session",
        tags: ["Order"],
        summary: "Obter pedido por sessão Stripe",
        description: "Retorna o ID do pedido baseado no session_id do checkout Stripe. Usado após pagamento."
    )]
    #[OA\Parameter(
        name: "session_id",
        in: "query",
        description: "ID da sessão de checkout do Stripe",
        required: true,
        schema: new OA\Schema(type: "string", example: "cs_test_a1b2c3d4e5f6g7h8i9j0...")
    )]
    #[OA\Response(
        response: 200,
        description: "ID do pedido retornado com sucesso",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "error", type: "null", example: null),
                new OA\Property(property: "orderId", type: "integer", example: 123, description: "ID do pedido associado à sessão")
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: "Pedido não encontrado para a sessão fornecida",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "error", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Pedido não encontrado")
            ]
        )
    )]
    public function stripeSession(OrderStripeSessionRequest $request)
    {
        try {
            $params = $request->validated();

            return response()->json([
                'error' => null,
                'orderId' => 1 // Mock data
            ]);

        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Erro ao buscar pedido',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
