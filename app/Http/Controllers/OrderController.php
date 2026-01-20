<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderShowRequest;
use App\Http\Requests\OrderStripeSessionRequest;
use App\Models\Order;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
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
