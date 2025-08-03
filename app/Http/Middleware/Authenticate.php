<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Se o pedido for para a API (começando com 'api/'), nunca redirecione.
        // Em vez disso, retorne null. O Laravel vai automaticamente converter isto
        // num erro 401 com uma resposta JSON.
        if ($request->is('api/*')) {
            return null;
        }

        return $request->expectsJson() ? null : route('login');
    }
}
