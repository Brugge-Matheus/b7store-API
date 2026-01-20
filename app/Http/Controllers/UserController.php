<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserAddressRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserAuthRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception;
use OpenApi\Attributes as OA;

use function Illuminate\Support\now;

class UserController extends Controller
{
    #[OA\Post(
        path: "/api/user/register",
        tags: ["User"],
        summary: "Registrar novo usuário",
        description: "Cria um novo usuário no sistema"
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name", "email", "password"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "João Silva"),
                new OA\Property(property: "email", type: "string", format: "email", example: "joao@example.com"),
                new OA\Property(property: "password", type: "string", format: "password", example: "senha123")
            ]
        )
    )]
    #[OA\Response(response: 201, description: "Usuário criado com sucesso")]
    #[OA\Response(response: 500, description: "Erro ao criar usuário")]
    public function register(UserRegisterRequest $request) {
        try {
            $params = $request->validated();

            $user = User::create([
                'name' => $params['name'],
                'email' => $params['email'],
                'password' => $params['password']
            ]);

            return response()->json([
                'error' => null,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ], 201);

        } catch(Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Erro ao criar usuário',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    #[OA\Post(
        path: "/api/user/login",
        tags: ["User"],
        summary: "Login de usuário",
        description: "Autentica usuário e retorna token Bearer"
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["email", "password"],
            properties: [
                new OA\Property(property: "email", type: "string", format: "email", example: "joao@example.com"),
                new OA\Property(property: "password", type: "string", format: "password", example: "senha123")
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Login realizado com sucesso")]
    #[OA\Response(response: 401, description: "Credenciais inválidas")]
    public function login(UserAuthRequest $request)
    {
        try {
            $params = $request->validated();

            if (!Auth::attempt($params)) {
                return response()->json([
                    'error' => true,
                    'message' => 'E-mail ou senha inválidos'
                ]);
            }

            $user = Auth::user();

            $user->tokens()->delete();
            $expiresAt = now()->addDays(7);
            $token = $user->createToken(name: 'auth_token', expiresAt: $expiresAt)->plainTextToken;

            return response()->json([
                'error' => null,
                'token' => $token
            ]);

        } catch(Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Erro ao efetuar login',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    #[OA\Post(
        path: "/api/user/addresses",
        tags: ["User"],
        summary: "Adicionar endereço",
        description: "Cadastra um novo endereço para o usuário autenticado",
        security: [["sanctum" => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["zipcode", "street", "city", "state", "country"],
            properties: [
                new OA\Property(property: "zipcode", type: "string", example: "12345-678"),
                new OA\Property(property: "street", type: "string", example: "Rua das Flores, 123"),
                new OA\Property(property: "city", type: "string", example: "São Paulo"),
                new OA\Property(property: "state", type: "string", example: "SP"),
                new OA\Property(property: "country", type: "string", example: "Brasil"),
                new OA\Property(property: "complement", type: "string", example: "Apto 42")
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Endereço cadastrado")]
    #[OA\Response(response: 401, description: "Não autenticado")]
    public function addresses(UserAddressRequest $request)
    {
        try {
            $params = $request->validated();
            $user = Auth::user();

            $address = $user->addresses()->create($params);

            return response()->json([
                'error' => null,
                'address' => [
                    'id' => $address->id,
                    'zipcode' => $address->zipcode,
                    'street' => $address->street,
                    'city' => $address->city,
                    'state' => $address->state,
                    'country' => $address->country,
                    'complement' => $address->complement
                ]
            ]);

        } catch(Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Erro ao efetuar login',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    #[OA\Get(
        path: "/api/user/addresses",
        tags: ["User"],
        summary: "Listar endereços",
        description: "Retorna todos os endereços do usuário autenticado",
        security: [["sanctum" => []]]
    )]
    #[OA\Response(response: 200, description: "Lista de endereços")]
    #[OA\Response(response: 401, description: "Não autenticado")]
    public function getAddresses()
    {
        try {
            $user = Auth::user();
            $rawAddresses = $user->addresses()->get();

            $addresses = $rawAddresses->map(fn($address) => [
                'id' => $address->id,
                'zipcode' => $address->zipcode,
                'street' => $address->street,
                'city' => $address->city,
                'state' => $address->state,
                'country' => $address->country,
                'complement' => $address->complement
            ]);

            return response()->json([
                'error' => null,
                'addresses' => $addresses ?? []
            ]);

        } catch(Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Erro ao efetuar login',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
