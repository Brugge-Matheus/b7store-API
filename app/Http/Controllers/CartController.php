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
        description: "Retorna informações detalhadas dos produtos baseado nos IDs fornecidos. Usado para recuperar dados completos dos produtos no carrinho."
    )]
    #[OA\Parameter(
        name: "ids",
        in: "query",
        description: "IDs dos produtos separados por vírgula (ex: 1,2,3)",
        required: true,
        schema: new OA\Schema(type: "string", example: "1,2,3")
    )]
    #[OA\Response(
        response: 200,
        description: "Produtos do carrinho retornados com sucesso",
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
                            new OA\Property(property: "image", type: "string", example: "http://localhost:8000/storage/media/products/iphone14.jpg")
                        ]
                    )
                )
            ]
        )
    )]
    #[OA\Response(
        response: 500,
        description: "Erro ao buscar produtos",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "error", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Erro ao buscar informaçãoes sobre os produtos"),
                new OA\Property(property: "details", type: "string")
            ]
        )
    )]
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
        description: "Calcula o valor do frete e prazo de entrega em dias baseado no CEP de destino"
    )]
    #[OA\Parameter(
        name: "zipcode",
        in: "query",
        description: "CEP de destino no formato 12345-678 ou 12345678",
        required: true,
        schema: new OA\Schema(type: "string", example: "12345-678")
    )]
    #[OA\Response(
        response: 200,
        description: "Dados de frete calculados com sucesso",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "error", type: "null", example: null),
                new OA\Property(property: "zipcode", type: "string", example: "12345-678"),
                new OA\Property(property: "cost", type: "integer", example: 7, description: "Custo do frete em reais"),
                new OA\Property(property: "days", type: "integer", example: 3, description: "Prazo de entrega em dias úteis")
            ]
        )
    )]
    #[OA\Response(
        response: 500,
        description: "Erro ao calcular frete",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "error", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Erro ao buscar informaçãoes sobre o CEP"),
                new OA\Property(property: "details", type: "string")
            ]
        )
    )]
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
        description: "Cria um pedido com os itens do carrinho e retorna URL para pagamento via Stripe. Requer autenticação.",
        security: [["sanctum" => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["cart", "addressId"],
            properties: [
                new OA\Property(
                    property: "cart",
                    type: "array",
                    description: "Array de itens do carrinho",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "productId", type: "integer", example: 1),
                            new OA\Property(property: "quantity", type: "integer", example: 2)
                        ]
                    )
                ),
                new OA\Property(property: "addressId", type: "integer", example: 1, description: "ID do endereço de entrega")
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Pedido criado com sucesso e sessão de pagamento iniciada",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "error", type: "null", example: null),
                new OA\Property(property: "url", type: "string", example: "https://checkout.stripe.com/c/pay/cs_test_...", description: "URL de checkout do Stripe")
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
        description: "Erro ao criar pedido",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "error", type: "boolean", example: true),
                new OA\Property(property: "message", type: "string", example: "Erro ao criar pedido"),
                new OA\Property(property: "details", type: "string")
            ]
        )
    )]
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
