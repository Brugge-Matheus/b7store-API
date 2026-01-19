<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserAddressRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserAuthRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception;

use function Illuminate\Support\now;

class UserController extends Controller
{
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
