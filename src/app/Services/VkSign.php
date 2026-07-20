<?php

namespace App\Services;

/**
 * Проверка подписи VK Mini App launch params.
 *
 * Алгоритм (https://dev.vk.com/ru/mini-apps/getting-started/launch-params):
 *  1. Из launch params взять все, начинающиеся с "vk_" (кроме самого "vk_sign").
 *  2. Отсортировать по ключу.
 *  3. Собрать query-строку (key=value&...).
 *  4. HMAC-SHA256 от строки с client_secret, base64url.
 *  5. Сравнить с параметром vk_sign.
 */
class VkSign
{
    public function __construct(private readonly string $secret) {}

    /**
     * Проверить подпись и вернуть launch params с vk_user_id, либо null.
     *
     * @param  array<string,string>  $params  Все launch params (вкл. vk_sign).
     * @return array<string,mixed>|null Параметры без vk_sign либо null при ошибке.
     */
    public function verify(array $params): ?array
    {
        if (empty($this->secret) || ! isset($params['vk_sign'])) {
            return null;
        }

        $vkParams = [];
        foreach ($params as $key => $value) {
            if (str_starts_with($key, 'vk_') && $key !== 'vk_sign') {
                $vkParams[$key] = $value;
            }
        }

        if (! isset($vkParams['vk_user_id'])) {
            return null;
        }

        ksort($vkParams);
        $paramsStr = http_build_query($vkParams);
        $computed = rtrim(strtr(base64_encode(hash_hmac('sha256', $paramsStr, $this->secret, true)), '+/', '-_'), '=');

        if (! hash_equals($computed, (string) $params['vk_sign'])) {
            return null;
        }

        return $vkParams;
    }

    /** Сгенерировать подпись (для dev-url и тестов). */
    public function sign(array $params): string
    {
        $vkParams = [];
        foreach ($params as $key => $value) {
            if (str_starts_with($key, 'vk_') && $key !== 'vk_sign') {
                $vkParams[$key] = $value;
            }
        }
        ksort($vkParams);
        $paramsStr = http_build_query($vkParams);

        return rtrim(strtr(base64_encode(hash_hmac('sha256', $paramsStr, $this->secret, true)), '+/', '-_'), '=');
    }
}
