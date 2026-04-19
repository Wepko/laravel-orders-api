# План: Запуск Laravel Orders API в Docker

## Статус проекта

### Сделано:
- [x] Проанализирована структура проекта
- [x] Изучен docker-compose.yml, Dockerfile, nginx config
- [x] Удален Laravel 13 из `src/`

### Нужно сделать:

## Шаг 1: Создать Laravel 12 проект

```bash
docker run --rm -v $(pwd):/app -w /app composer create-project laravel/laravel:^12.0 src
```

## Шаг 2: Запустить Docker контейнеры

```bash
docker compose up -d
```

## Шаг 3: Сгенерировать APP_KEY

```bash
docker compose exec app php artisan key:generate
```

## Шаг 4: Выполнить миграции БД

```bash
docker compose exec app php artisan migrate
```

## Шаг 5: Проверить работу

```bash
# Проверить статус контейнеров
docker compose ps

# Проверить health endpoint
curl http://localhost:8080/health

# Проверить главную страницу
curl http://localhost:8080
```

---

## Структура проекта

```
laravel-orders-api/
├── docker/
│   ├── php/
│   │   ├── Dockerfile          # PHP 8.4-FPM образ
│   │   └── docker-entrypoint.sh
│   └── nginx/
│       └── default.conf        # Nginx конфиг
├── src/                        # Laravel приложение (нужно создать)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── tests/
│   ├── .env
│   ├── composer.json
│   └── ...
├── docker-compose.yml          # 4 сервиса: app, nginx, db, redis
├── .env                        # Конфигурация
└── .env.testing
```

## Сервисы Docker

| Сервис | Образ | Описание |
|--------|-------|----------|
| app | php:8.4-fpm-alpine | PHP-FPM приложение |
| nginx | nginx:alpine | Веб-сервер |
| db | postgres:16-alpine | База данных PostgreSQL |
| redis | redis:7-alpine | Redis для кэша и очередей |
| queue-worker | php:8.4-fpm-alpine | Обработчик очередей |

## Порты

| Сервис | Порт |
|--------|------|
| Nginx | 8080 |
| PostgreSQL | 5432 |
| Redis | 6379 |

## Переменные окружения (.env)

```
APP_NAME="Order Management API"
APP_ENV=local
APP_KEY=                          # Сгенерировать через artisan
APP_DEBUG=true
APP_URL=http://localhost:8080

DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=order_api
DB_USERNAME=order_user
DB_PASSWORD=order_password

REDIS_HOST=redis
REDIS_PASSWORD=
REDIS_PORT=6379

QUEUE_CONNECTION=redis
CACHE_DRIVER=redis
```

---

## Команды для управления

```bash
# Запуск
docker compose up -d

# Остановка
docker compose down

# Просмотр логов
docker compose logs -f

# Войти в контейнер
docker compose exec app sh

# Выполнить artisan команду
docker compose exec app php artisan <command>

# Пересобрать образы
docker compose up --build -d
```

## Ожидаемый результат

1. Все 5 контейнеров в статусе `running`
2. http://localhost:8080 возвращает страницу Laravel
3. http://localhost:8080/health возвращает "OK"
4. Миграции выполнены успешно
