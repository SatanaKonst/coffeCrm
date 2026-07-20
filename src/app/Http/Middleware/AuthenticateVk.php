<?php

namespace App\Http\Middleware;

use App\Models\Client;
use App\Services\VkSign;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Проверка VK launch params и авторизация клиента.
 *
 * Стратегия:
 *  - Если в сессии уже есть client_id — используем его.
 *  - Иначе ищем vk_sign в запросе, проверяем, find-or-create Client.
 *  - При провале — 403.
 *
 * Client и isAdmin кладутся в request attributes для контроллеров.
 */
class AuthenticateVk
{
    public function __construct(private readonly VkSign $vkSign) {}

    public function handle(Request $request, Closure $next): Response
    {
        $clientId = $request->session()->get('vk.client_id');

        if (! $clientId && ($params = $this->vkSign->verify($request->query->all()))) {
            $client = Client::firstOrCreate(
                ['vk_user_id' => (int) $params['vk_user_id']],
                ['name' => 'Пользователь VK '.$params['vk_user_id']],
            );

            $request->session()->put('vk', [
                'client_id' => $client->id,
                'vk_user_id' => $client->vk_user_id,
            ]);

            $clientId = $client->id;
        }

        if (! $clientId) {
            abort(403, 'Требуется авторизация VK.');
        }

        /** @var Client $client */
        $client = Client::findOrFail($clientId);

        $request->attributes->set('client', $client);
        $request->attributes->set('isAdmin', $this->isAdmin($client));

        return $next($request);
    }

    private function isAdmin(Client $client): bool
    {
        $rootId = (int) config('services.vk.root_admin_id');

        return $rootId > 0 && $client->vk_user_id === $rootId;
    }
}
