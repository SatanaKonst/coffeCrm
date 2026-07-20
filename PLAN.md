# Coffee CRM — план реализации

CRM для продажи кофе через ВКонтакте (VK Mini App). Базовый путь приложения — `/crm`.

## Решения (зафиксировано)

- Роли: 2 — **Client** и **Admin** (по `VK_ROOT_ADMIN_ID`).
- Оплата/доставка: **нет**. Статус заказа меняется вручную.
- Каталог: **плоский список продуктов**. Без категорий, без остатков на старте.
- Фронт: **Blade + Bootstrap 5** (свои шаблоны).
- Авторизация: проверка подписи `vk_sign` → `vk_user_id` → find-or-create `Client`.

## Этапы (один этап = один атомарный коммит)

### Этап 1 — Миграции и модели  *(ГОТОВО, commit `11aadbc`)*

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

### Этап 2 — VK авторизация и middleware  *(ГОТОВО, commit `da03e59`)*

- `config/services.php` → секция `vk` (`secret`, `group_id`, `root_admin_id` из env).
- `App\Services\VkSign` — проверка подписи `vk_sign`.
- `App\Http\Middleware\AuthenticateVk`: parse launch params → verify → find-or-create `Client` → в сессию.
- Группа `Route::prefix('/crm')->middleware('auth.vk')`.

### Этап 3 — Layout + роут `/crm`  *(ГОТОВО, commit `3749844`)*

- `resources/views/layouts/crm.blade.php`: Bootstrap 5 (CDN), навбар, `@yield('content')`.
- `HomeController@index` → `crm.dashboard`.
- Admin-gate: сравнение `vk_user_id` с `config('services.vk.root_admin_id')`.

### Этап 4 — Каталог продуктов (клиент)  *(ГОТОВО, commit `44cc9ca`)*

- `ProductController@index` → список активных продуктов.
- Карточки, кнопка «Заказать» → форма.

### Этап 5 — Оформление заказа  *(ГОТОВО, commit `47cc0ed`)*

- `OrderController@create/store`: товары + qty → `Order` + `OrderItem` (snapshot `name`/`price`).
- FormRequest на каждый mutation.
- `/crm/orders` — список своих заказов со статусом.

### Этап 6 — Админка заказов  *(ГОТОВО, commit `6210f4b`)*

- `Admin\OrderController` (gate admin) → список всех заказов.
- Смена статуса (PATCH).
- Таблица с фильтром по статусу.

### Этап 7 — Админка продуктов (CRUD)  *(ГОТОВО, commit `d715a91`)*

- `Admin\ProductController`: index/create/store/edit/update/destroy.
- FormRequest на каждый mutation.
- Toggle `is_active`.

### Этап 8 — Полировка  *(ГОТОВО)*

- Страницы ошибок под стиль CRM (403, 404, 419, 500, 503).
- Dashboard: карточки статистики (для админа — новые заказы/всего/активные товары; для клиента — свои заказы/товаров в каталоге).
- Flash-сообщения уже подключены в layout (Этап 3).

### Этап 9 — На будущее  *(не в MVP)*

- `OrderPolicy` для второй линии обороны (пока изоляция в контроллере достаточна).
- Корзина как отдельная сущность (сейчас заказ = один товар).
- Оплата (VK Pay / внешний шлюз) — сейчас статусы ручные.
- Категории товаров, учёт остатков (`stock`, списание при заказе).
- `OrderPolicy` (client видит только свои).

## Open вопросы (не блокируют старт)

- Корзина как сущность или просто форма? Сейчас — **просто форма** (MVP). При необходимости добавлю `carts`.
- Ассортимент: seeder для demo, затем ведётся через админку.
