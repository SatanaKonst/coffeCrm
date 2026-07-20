# Coffee CRM

CRM для продажи кофе через ВКонтакте.

## Рабочие правила
- Общение на русском.
- После каждой завершённой задачи — git-коммит (одна задача = один коммит, атомарно).
- Коммиты небольшие, без мусора (`.idea/`, `vendor/`, `.env` не трогаем).

## Стек
- Backend: Laravel (PHP ≥ 8.2)
- Frontend: Twitter Bootstrap (blade-шаблоны)
- БД: MySQL
- IDE: PhpStorm (PHP-интерпретатор уже настроен)

## Инициализация (репо пустое)
Laravel ещё не развёрнут. Первый запуск:
```
composer create-project laravel/laravel . --prefer-dist
```
Если ругается на непустую директорию — временно перенести `AGENTS.md`, поставить Laravel, вернуть обратно.

После установки:
- Прописать MySQL-доступы в `.env` (`DB_*`).
- `php artisan key:generate`.
- `php artisan migrate`.

## Типичные команды
- Сервер: `php artisan serve`
- Миграции: `php artisan migrate` (откат: `php artisan migrate:rollback`)
- Тинкер: `php artisan tinker`
- Тесты: `php artisan test` (один: `php artisan test --filter=TestName`)
- Логи: `storage/logs/laravel.log`

## VK Mini Apps
Архитектура: Laravel-бэкенд + фронт в iframe внутри VK. Дока: https://dev.vk.com/ru/mini-apps/getting-started

- Фронт использует **VK Bridge** (JS SDK, `@vkontakte/vk-bridge`) для общения с нативным клиентом VK.
- VK передаёт в URL iframe подписанные launch-параметры: `vk_user_id`, `vk_app_id`, `vk_group_id`, `vk_sign` и др.
- Авторизация на бэке: проверить подпись `vk_sign` по секрету приложения, далее доверять `vk_user_id` как идентификатору юзера.
- Секрет приложения (Mini App `client_secret` / `secure_key`) — ТОЛЬКО в `.env` (`VK_MINIAPP_SECRET`), через `config/services.php`.
- Секреты в код и коммиты не класть ни при каких условиях.

## Безопасность
- `.env` всегда в `.gitignore` (в Laravel по умолчанию).
- Все данные из launch-параметров VK — external input: валидировать на trust-boundary и проверять подпись перед использованием.
- API-эндпоинты, не относящиеся к iframe VK, закрывать отдельной авторизацией (Mini Apps публичный iframe =任何人 может открыть URL).

## Соглашения по коду
- Следуем конвенциям Laravel (Eloquent, Form Request, Resource).
- Имена таблиц на английском во мн. числе; модели в ед.
- Миграции с `foreignId()` и каскадами — явно.
- Blade + Bootstrap-классы, без кастомного CSS пока не понадобится.
