<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatusEnum;
use App\Http\Requests\CartFinishRequest;
use App\Http\Requests\CartMountRequest;
use App\Http\Requests\CartShippingRequest;
use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class CartController extends Controller
{
    #[OA\Get(
        path: "/api/cart/mount",
        tags: ["Cart"],
        summary: "Montar carrinho",
        description: "Retorna informações dos produtos baseado nos IDs fornecidos"
    )]
    #[OA\Parameter(name: "ids", in: "query", required: true, description: "IDs dos produtos separados por vírgula", schema: new OA\Schema(type: "string", example: "1,2,3"))]
    #[OA\Response(response: 200, description: "Produtos do carrinho")]
    #[OA\Response(response: 500, description: "Erro ao buscar produtos")]
    public function mount(CartMountRequest $request)
    {
        try {
            $params = $request->validated();

            $productIds = array_map('intval', explode(',', $params['ids']));

            $rawProducts = Product::select(['id', 'label', 'price'])
            // ->with('images')
            // ->with('images:id,url,product_id')
            ->with(['images' => fn($image) => $image->select(['id', 'url', 'product_id'])->limit(1)])
            ->whereIn('id', $productIds)
            ->get();

            $formattedProducts = $rawProducts->map(function($product) {
                return [
                    'id' => $product->id,
                    'label' => $product->label,
                    'price' => $product->price,
                    'formatted_price' => $product->formatted_price,
                    'image' => asset('storage/' . ($product->images->first()?->url ?? 'default-imagem.jpg'))
                ];
            });

            return response()->json([
                'error' => null,
                'products' => $formattedProducts
            ]);    
        
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Erro ao buscar informaçãoes sobre os produtos',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    #[OA\Get(
        path: "/api/cart/shipping",
        tags: ["Cart"],
        summary: "Calcular frete",
        description: "Retorna valor e prazo de entrega baseado no CEP"
    )]
    #[OA\Parameter(name: "zipcode", in: "query", required: true, schema: new OA\Schema(type: "string", example: "12345-678"))]
    #[OA\Response(response: 200, description: "Dados de frete")]
    #[OA\Response(response: 500, description: "Erro ao buscar frete")]
    public function shipping(CartShippingRequest $request)
    {
        try {
            $params = $request->validated();

            // $shippingData = Zipcode::getShippingData($params['zipcode']);
            $shippingData = [
                'zipcode' => $params['zipcode'],
                'cost' => random_int(3, 40),
                'days' => random_int(1, 4)
            ];

            return response()->json([
                'error' => null,
                'zipcode' => $shippingData['zipcode'],
                'cost' => $shippingData['cost'],
                'days' => $shippingData['days']
            ]);
        
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Erro ao buscar informaçãoes sobre o CEP',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    #[OA\Post(
        path: "/api/cart/finish",
        tags: ["Cart"],
        summary: "Finalizar compra",
        description: "Cria um pedido com os itens do carrinho",
        security: [["sanctum" => []]]
    )]
    #[OA\Response(response: 200, description: "Pedido criado")]
    #[OA\Response(response: 401, description: "Não autenticado")]
    #[OA\Response(response: 500, description: "Erro ao criar pedido")]
    public function finish(CartFinishRequest $request)
    {
        try {
            $params = $request->validated();

            $cart = $params['cart'];
            
            $products = Product::whereIn('id', array_column($cart, 'productId'))->get();
            $address = Address::where('id', $params['addressId'])->first();
            $user = Auth::user();

            $total = 0;
            foreach($cart as $item) {
                $product = $products->find($item['productId']);
                $total += ($product->price * $item['quantity']) ?? 0;
            }

            $order = Order::create([
                'status' => OrderStatusEnum::PENDING,
                'total' => $total,
                'shippingCoast' => 10,
                'shippingDays' => 10,
                'shippingZipcode'=> $address->zipcode,
                'shippingStreet' => $address->street,
                'shippingNumber' => $address->number,
                'shippingCity' => $address->city,
                'shippingState' => $address->state,
                'shippingCountry' => $address->country,
                'shippingComplement' => $address->complement,
                'user_id' => $user->id
            ]);

            foreach($cart as $item) {
                $product = $products->find($item['productId']);
                $order->orderItems()->create([
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'product_id' => $product->id
                ]);
            }

            return response()->json([
                'error' => null,
                'url' => 'https://checkout.stripe.com/...'
            ]);
                    
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Erro ao criar pedido',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
