# Coffee CRM

CRM для продажи кофе через ВКонтакте (VK Mini App).

## Рабочие правила
- Общение на русском.
- После каждой завершённой задачи — git-коммит (одна задача = один коммит, атомарно).
- Коммиты небольшие, без мусора (`.idea/`, `vendor/`, `.env` не трогаем).

## Стек
- Backend: Laravel 13 (PHP 8.4)
- Frontend: Twitter Bootstrap (blade-шаблоны)
- БД: MySQL 8.4
- Инфра: Docker (compose), nginx + php-fpm

## Окружение (всё в Docker)

Контейнеры (см. `docker-compose.yml`):
- `app` — php-fpm, код смонтирован в `/var/www/html`
- `nginx` — раздаёт `public/`, порт **8080** хоста
- `mysql` — БД `coffe_crm`, root/secret, порт 3306 хоста

### Запуск
```
docker compose up -d
```
Приложение: http://localhost:8080

### Артизан / composer / npm — только через `docker exec`
```
docker exec coffeecrm-app php artisan <cmd>
docker exec coffeecrm-app composer <cmd>
docker exec coffeecrm-app npm <cmd>
```
Пересобрать образ после изменения `Dockerfile` или `composer.json`:
```
docker compose build app && docker compose up -d
```

### Типичные команды (всё через контейнер `coffeecrm-app`)
- Миграции: `docker exec coffeecrm-app php artisan migrate` (откат: `migrate:rollback`)
- Тинкер: `docker exec -it coffeecrm-app php artisan tinker`
- Тесты: `docker exec coffeecrm-app php artisan test` (один: `--filter=TestName`)
- Логи: `storage/logs/laravel.log`
- Очистить кэш: `docker exec coffeecrm-app php artisan optimize:clear`

### Переменные `.env` (важные)
- `DB_HOST=mysql` (имя сервиса), `DB_DATABASE=coffe_crm`, `DB_USERNAME=root`, `DB_PASSWORD=secret`
- `VK_MINIAPP_SECRET`, `VK_GROUP_ID`, `VK_ROOT_ADMIN_ID` — заполняются пользователем

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
- API-эндпоинты, не относящиеся к iframe VK, закрывать отдельной авторизацией (Mini Apps публичный iframe — URL может открыть кто угодно).

## Соглашения по коду
- Следуем конвенциям Laravel (Eloquent, Form Request, Resource).
- Имена таблиц на английском во мн. числе; модели в ед.
- Миграции с `foreignId()` и каскадами — явно.
- Blade + Bootstrap-классы, без кастомного CSS пока не понадобится.
