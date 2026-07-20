<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Допуск только админа. Ставится после auth.vk.
 * Проверяет request attribute 'isAdmin', установленный AuthenticateVk.
 */
class RequireAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->attributes->get('isAdmin')) {
            abort(403, 'Доступ только для администратора.');
        }

        return $next($request);
    }
}
