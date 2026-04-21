# Orders API

REST API для управления заказами. Построен на Laravel 12, PostgreSQL и Redis.

## Стек

- **PHP 8.4** (Laravel 12)
- **PostgreSQL 16** — основная база данных
- **Redis 7** — кэш, очереди, сессии
- **Nginx** — web-сервер
- **Docker / Docker Compose** — оркестрация

## Структура данных

```
customers          — клиенты
products           — товары (SKU, название, категория, остаток, цена)
orders             — заказы (статус, сумма, timestamps)
order_items        — позиции заказа (связь товар-заказ, количество, цена)
order_exports      — журнал экспорта в стороннюю систему
```

## API (v1)

| Метод | Endpoint                     | Описание                      | Лимит          |
|-------|------------------------------|-------------------------------|----------------|
| GET   | `/api/v1/products`           | Список товаров с фильтрацией  | курсорная паг. |
| GET   | `/api/v1/orders`             | Список заказов с фильтрацией  | курсорная паг. |
| POST  | `/api/v1/orders`             | Создать заказ                 | 10/мин         |
| GET   | `/api/v1/orders/{id}`        | Детали заказа                 | —              |
| PATCH | `/api/v1/orders/{id}/status` | Изменить статус заказа        | —              |

### Статусы заказов и переходы

```
NEW → CONFIRMED → PROCESSING → SHIPPED → COMPLETED
  ↓        ↓           ↓
  └────────┴────────→ CANCELLED
```

Финальные статусы (COMPLETED, CANCELLED) не могут быть изменены.

### Фильтрация заказов

- `customer_id` — ID клиента
- `status` — статус (new, confirmed, processing, shipped, completed, cancelled)
- `from` / `to` — диапазон дат создания

## Архитектура

**Services** — вся бизнес-логика вынесена в сервисы:
- `OrderCreationService` — создание заказа с проверкой остатков и пессимистической блокировкой
- `OrderStatefulService` — управление статусом заказа и получение деталей
- `OrderPaginationService` — курсорная пагинация
- `ProductService` / `ProductPaginationService` — товары и их пагинация

**DTOs** — запросы преобразуются в DTO перед передачей в сервисы. Фильтры тоже DTO.

**State machine** — валидация переходов статусов через `OrderStatus` enum. При переходе NEW → CONFIRMED диспатчится event `OrderConfirmed`.

**Event-driven export** — на `OrderConfirmed` подписан `ExportOrderListener`, который ставит в очередь `ExportOrderJob`. Job отправляет данные заказа внешнему API (по умолчанию httpbin для тестов). Результат сохраняется в `order_exports` с тремя попытками и экспоненциальным backoff.

**Caching** — список продуктов кэшируется на 60 секунд через spatie/laravel-responsecache.

## Запуск

```bash
# Поднять все сервисы
docker compose up -d

# Миграции + сиды (внутри контейнера)
docker compose exec app php artisan migrate --seed
```

Приложение будет доступно на `http://localhost:8080`.

## Конфигурация

Все настройки через `.env`:

```
APP_ENV=local
APP_DEBUG=true
DB_HOST=db
REDIS_HOST=redis
QUEUE_CONNECTION=redis
EXPORT_API_URL=https://httpbin.org/post
```

## Очереди

Queue worker запущен как отдельный контейнер (`order-api_queue`). Обрабатывает экспорт заказов.

```bash
# Мониторинг
docker compose logs -f queue-worker

# Очередь jobs
docker compose exec app php artisan queue:work redis --sleep=3 --tries=3
```

## Тесты

```bash
docker compose exec app php artisan test
```

Postman-коллекция с запросами: `laravel-orders-api.postman_collection.json`

## Health check

```
GET /health → 200 OK
```