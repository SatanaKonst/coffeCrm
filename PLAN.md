# Coffee CRM — план реализации

CRM для продажи кофе через ВКонтакте (VK Mini App). Базовый путь приложения — `/crm`.

## Решения (зафиксировано)

- Роли: 2 — **Client** и **Admin** (по `VK_ROOT_ADMIN_ID`).
- Оплата/доставка: **нет**. Статус заказа меняется вручную.
- Каталог: **плоский список продуктов**. Без категорий, без остатков на старте.
- Фронт: **Blade + Bootstrap 5** (свои шаблоны).
- Авторизация: проверка подписи `vk_sign` → `vk_user_id` → find-or-create `Client`.

## Этапы (один этап = один атомарный коммит)

### Этап 1 — Миграции и модели  *(СТАРТ)*

Таблицы:

- `clients`: `id`, `vk_user_id` (unique bigint), `name`, `phone` (nullable), timestamps.
- `products`: `id`, `name`, `description` (nullable), `price` (unsignedDecimal 10,2), `is_active` (bool, default true), timestamps.
- `orders`: `id`, `client_id` (foreign cascade), `status` (enum: new/confirmed/delivered/cancelled, default new), `total` (decimal 10,2, default 0), `comment` (nullable), timestamps.
- `order_items`: `id`, `order_id` (foreign cascade), `product_id` (foreign restrict — историю не теряем), `name` (snapshot), `price` (snapshot), `qty` (unsigned smallint), timestamps.

Модели: `Client`, `Product`, `Order`, `OrderItem` — fillable, relations, `$casts`.

Связи:

- `Client hasMany Order`
- `Order belongsTo Client + hasMany OrderItem`
- `OrderItem belongsTo Order + belongsTo Product`

Фабрики + `DatabaseSeeder` для тестовых данных.

### Этап 2 — VK авторизация и middleware

- `config/services.php` → секция `vk` (`secret`, `group_id`, `root_admin_id` из env).
- `App\Services\VkSign` — проверка подписи `vk_sign`.
- `App\Http\Middleware\AuthenticateVk`: parse launch params → verify → find-or-create `Client` → в сессию.
- Группа `Route::prefix('/crm')->middleware('auth.vk')`.

### Этап 3 — Layout + роут `/crm`

- `resources/views/layouts/crm.blade.php`: Bootstrap 5 (CDN), навбар, `@yield('content')`.
- `HomeController@index` → `crm.dashboard`.
- Admin-gate: сравнение `vk_user_id` с `config('services.vk.root_admin_id')`.

### Этап 4 — Каталог продуктов (клиент)

- `ProductController@index` → список активных продуктов.
- Карточки, кнопка «Заказать» → форма.

### Этап 5 — Оформление заказа

- `OrderController@create/store`: товары + qty → `Order` + `OrderItem` (snapshot `name`/`price`).
- FormRequest на каждый mutation.
- `/crm/orders` — список своих заказов со статусом.

### Этап 6 — Админка заказов

- `Admin\OrderController` (gate admin) → список всех заказов.
- Смена статуса (PATCH).
- Таблица с фильтром по статусу.

### Этап 7 — Админка продуктов (CRUD)

- `Admin\ProductController`: index/create/store/edit/update/destroy.
- FormRequest на каждый mutation.
- Toggle `is_active`.

### Этап 8 — Полировка

- `OrderPolicy` (client видит только свои).
- Flash-сообщения + Bootstrap alerts.
- 403/404 страницы под стиль.

## Open вопросы (не блокируют старт)

- Корзина как сущность или просто форма? Сейчас — **просто форма** (MVP). При необходимости добавлю `carts`.
- Ассортимент: seeder для demo, затем ведётся через админку.
