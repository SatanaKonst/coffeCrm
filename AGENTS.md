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

## VK-интеграция
- Работаем через VK API. Токены и секреты — ТОЛЬКО в `.env`, через `config/services.php` (ключ `vk`).
- В код и коммиты секреты не класть ни при каких условиях.
- Confirmation token для webhook-ов VK — тоже в `.env`.

## Безопасность
- `.env` всегда в `.gitignore` (в Laravel по умолчанию).
- Webhook-маршруты VK проверять через подтверждение и подпись.
- Валидация на trust-boundary (контроллеры, webhook) — обязательна.

## Соглашения по коду
- Следуем конвенциям Laravel (Eloquent, Form Request, Resource).
- Имена таблиц на английском во мн. числе; модели в ед.
- Миграции с `foreignId()` и каскадами — явно.
- Blade + Bootstrap-классы, без кастомного CSS пока не понадобится.
