<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "B7Store API",
    description: <<<'DESCRIPTION'
API REST completa para o sistema B7Store E-commerce.

## 🚀 Recursos Principais

- **Autenticação**: Sistema de registro e login com tokens Bearer (Sanctum)
- **Produtos**: Catálogo completo com filtros, metadados e produtos relacionados
- **Carrinho**: Gerenciamento de itens e cálculo de frete
- **Pedidos**: Criação e acompanhamento de pedidos com integração Stripe
- **Categorias**: Organização hierárquica com metadados dinâmicos
- **Webhooks**: Integração com Stripe para atualização automática de status

## 🔐 Autenticação

Endpoints protegidos requerem token Bearer no header:

```
Authorization: Bearer {seu-token}
```

Obtenha seu token através do endpoint `/api/user/login`.

## 🔔 Webhooks Stripe

O sistema processa webhooks do Stripe no endpoint `/webhook/stripe`:

- `checkout.session.completed` → Marca pedido como **paid**
- `checkout.session.expired` → Marca pedido como **expired**
- `payment_intent.payment_failed` → Marca pedido como **failed**

**Configuração**: Stripe Dashboard → Developers → Webhooks

## 📦 Status de Pedidos

| Status | Descrição |
|--------|-----------|
| `pending` | Aguardando pagamento |
| `paid` | Pagamento confirmado |
| `expired` | Sessão expirada |
| `failed` | Pagamento falhou |

DESCRIPTION,
    contact: new OA\Contact(
        name: "Equipe B7Store",
        email: "suporte@brugge.com.br"
    )
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
#[OA\Tag(name: "Health", description: "Endpoints de monitoramento e saúde da API")]
#[OA\Tag(name: "Banner", description: "Gerenciamento de banners promocionais")]
#[OA\Tag(name: "Product", description: "Gerenciamento de produtos")]
#[OA\Tag(name: "Category", description: "Gerenciamento de categorias e metadados")]
#[OA\Tag(name: "Cart", description: "Operações do carrinho de compras e finalização")]
#[OA\Tag(name: "User", description: "Autenticação e gerenciamento de usuários")]
#[OA\Tag(name: "Order", description: "Consulta e gerenciamento de pedidos")]
#[OA\Tag(name: "Webhook", description: "Webhooks para integração com serviços externos")]
class OpenApiSpec
{
}
