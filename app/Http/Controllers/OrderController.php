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
        summary: "Listar pedidos",
        description: "Retorna todos os pedidos do usuário autenticado",
        security: [["sanctum" => []]]
    )]
    #[OA\Response(response: 200, description: "Lista de pedidos")]
    #[OA\Response(response: 401, description: "Não autenticado")]
    #[OA\Response(response: 500, description: "Erro ao buscar pedidos")]
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
        summary: "Detalhes do pedido",
        description: "Retorna informações completas de um pedido específico",
        security: [["sanctum" => []]]
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer", example: 1))]
    #[OA\Response(response: 200, description: "Detalhes do pedido")]
    #[OA\Response(response: 401, description: "Não autenticado")]
    #[OA\Response(response: 404, description: "Pedido não encontrado")]
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
        path: "/api/order/{id}/session",
        tags: ["Order"],
        summary: "Obter sessão de pagamento Stripe",
        description: "Retorna informações da sessão de pagamento",
        security: [["sanctum" => []]]
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer", example: 1))]
    #[OA\Response(response: 200, description: "Sessão criada")]
    #[OA\Response(response: 401, description: "Não autenticado")]
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
