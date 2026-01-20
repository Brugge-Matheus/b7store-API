<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "B7Store API",
    description: "API completa para o sistema B7Store - E-commerce",
    contact: new OA\Contact(email: "suporte@b7store.com")
)]
#[OA\Server(
    url: "http://localhost:8000",
    description: "Servidor Local"
)]
#[OA\Server(
    url: "https://api.b7store.com",
    description: "Servidor de Produção"
)]
#[OA\SecurityScheme(
    securityScheme: "sanctum",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT",
    description: "Token de autenticação via Laravel Sanctum. Use: Bearer {seu-token}"
)]
#[OA\Tag(name: "Banner", description: "Gerenciamento de banners promocionais")]
#[OA\Tag(name: "Product", description: "Gerenciamento de produtos")]
#[OA\Tag(name: "Category", description: "Gerenciamento de categorias")]
#[OA\Tag(name: "Cart", description: "Carrinho de compras")]
#[OA\Tag(name: "User", description: "Gerenciamento de usuários e autenticação")]
#[OA\Tag(name: "Order", description: "Gerenciamento de pedidos")]
class OpenApiSpec
{
}
