<?php

namespace App\Console\Commands;

use App\Services\VkSign;
use Illuminate\Console\Command;

/**
 * Сгенерировать подписанный URL для локальной разработки.
 *
 * Без VK-iframe нельзя пройти middleware. Эта команда подписывает фейковые
 * launch params реальным VK_MINIAPP_SECRET, чтобы открыть /crm локально.
 *
 * Внимание: использует только для dev. Секрет читать из config — не выводится.
 *
 * Пример:
 *   docker exec coffeecrm-app php artisan vk:dev-url --user=123456 --admin
 */
class GenerateVkDevUrl extends Command
{
    protected $signature = 'vk:dev-url
                            {--user= : VK ID пользователя (по умолчанию VK_ROOT_ADMIN_ID)}
                            {--admin : Синоним --user=VK_ROOT_ADMIN_ID}
                            {--path=/crm : Путь}';

    protected $description = 'Сгенерировать подписанный launch params URL для локального входа';

    public function handle(VkSign $vkSign): int
    {
        $secret = (string) config('services.vk.secret');

        if ($secret === '') {
            $this->error('VK_MINIAPP_SECRET не задан в .env');
            $this->info('Добавьте VK_MINIAPP_SECRET=... в .env и повторите.');

            return self::FAILURE;
        }

        $vkUserId = (int) ($this->option('user')
            ?: ($this->option('admin') ? config('services.vk.root_admin_id') : 0));

        if ($vkUserId <= 0) {
            $this->error('Нужен --user=ID или флаг --admin с заданным VK_ROOT_ADMIN_ID.');

            return self::FAILURE;
        }

        $params = [
            'vk_user_id' => (string) $vkUserId,
            'vk_app_id' => (string) config('app.name'),
            'vk_group_id' => (string) config('services.vk.group_id'),
        ];

        $sign = $vkSign->sign($params);
        $params['vk_sign'] = $sign;

        $path = ltrim((string) $this->option('path'), '/');
        $url = config('app.url').'/'.ltrim($path.'?'.http_build_query($params), '?');

        $this->info('Открой в браузере:');
        $this->line($url);

        return self::SUCCESS;
    }
}
