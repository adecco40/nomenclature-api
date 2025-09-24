# Nomenclature API

REST API для работы с номенклатурой товаров. Реализован полный CRUD для товаров, категорий, поставщиков, а также история изменений. Все ответы возвращаются в JSON в едином формате.

---

## Запуск проекта (Docker)

### 1. Клонировать репозиторий
```bash
git clone https://github.com/adecco40/nomenclature_api.git
cd nomenclature-api
```

### 2. Запустить контейнеры
```bash
docker-compose up -d --build
```

По умолчанию API будет доступен по адресу:
```
http://localhost:8000
```

### 3. Установка зависимостей
Зайти внутрь контейнера приложения:
```bash
docker exec -it app bash
```

Установить зависимости:
```bash
composer install
```

### 4. Настроить .env
Скопировать файл окружения:
```bash
cp .env.example .env
```

Сгенерировать ключ приложения:
```bash
php artisan key:generate
```

---

## Миграции и сидеры

Выполнить миграции:
```bash
php artisan migrate
```

Заполнить тестовыми данными (сидеры):
```bash
php artisan db:seed
```

---

## Passport (OAuth2)

Сгенерировать ключи для Laravel Passport:
```bash
php artisan passport:install
```

В результате будут созданы:
- Personal Access Client
- Password Grant Client

Эти ключи используются для авторизации по Bearer Token.

---

## Формат ответа

### Успех
```json
{
  "success": true,
  "message": "Описание результата",
  "data": { },
  "timestamp": "2025-09-24T15:36:00+00:00"
}
```

### Ошибка
```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "field": ["Описание ошибки"]
  },
  "timestamp": "2025-09-24T15:36:00+00:00"
}
```

Все методы требуют авторизацию:

```
Authorization: Bearer {token}
```

---

## Модели данных

- Product — товар  
- Category — категория (иерархия через `parent_id`)  
- Supplier — поставщик  
- ChangeHistory — история изменений  

Все ID — UUID.

---

## API Методы

### Products
- `GET /api/products` — список товаров  
- `GET /api/products/{id}` — получить товар  
- `POST /api/products` — создать товар  
- `PUT /api/products/{id}` — обновить товар  
- `DELETE /api/products/{id}` — мягкое удаление  
- `POST /api/products/upload` — загрузка изображения (jpg, jpeg, png)  
- `POST /api/products/import` — импорт товаров из CSV через очередь  

### Categories
- `GET /api/categories` — список категорий  
- `GET /api/categories/{id}` — получить категорию  
- `POST /api/categories` — создать категорию  
- `PUT /api/categories/{id}` — обновить категорию  
- `DELETE /api/categories/{id}` — удалить категорию  

### Suppliers
- `GET /api/suppliers` — список поставщиков  
- `GET /api/suppliers/{id}` — получить поставщика  
- `POST /api/suppliers` — создать поставщика  
- `PUT /api/suppliers/{id}` — обновить поставщика  
- `DELETE /api/suppliers/{id}` — удалить поставщика  

### Change History
- `GET /api/changes` — история изменений  

---

## Upload & Import

### Загрузка изображения
`POST /api/products/upload`  
Принимает `form-data`:
- key: **file**  
- type: **File**  
- value: jpg/png/jpeg  

### Импорт CSV
`POST /api/products/import`  
Принимает `form-data`:
- key: **file**  
- type: **File**  
- value: CSV-файл  

Импорт выполняется через очередь RabbitMQ.

---

## Очереди и RabbitMQ

- Драйвер: rabbitmq (`vladimir-yuldashev/laravel-queue-rabbitmq`)  
- Запуск воркера:
  ```bash
  php artisan queue:work rabbitmq
  ```
- RabbitMQ Management UI:
  ```
  http://localhost:15672
  ```
  Логин/пароль: `guest / guest`  

Используется для фонового импорта товаров из CSV.

---

## Postman Collection

В папке `postman` лежит файл `api-collection.json`, который можно импортировать в Postman.  
Коллекция называется **Nomenclature API Collection** и содержит полный CRUD для всех сущностей.  

### Переменные коллекции
- `baseUrl` — адрес API (по умолчанию `http://localhost:8000`)  
- `token` — токен авторизации  
- `productId` — ID тестового товара  
- `categoryId` — ID тестовой категории  
- `supplierId` — ID тестового поставщика  
- `imageFile` — путь к изображению для `upload`  
- `csvFile` — путь к CSV для `import`  

### Тесты
Во всех запросах выполняется тест на формат ответа:
```javascript
pm.test("Формат ответа корректен", function () { 
    if (pm.response.headers.get('Content-Type')?.includes('application/json')) {
        const res = pm.response.json();
        pm.expect(res).to.have.keys(['message', 'data', 'timestamp', 'success']);
    } else {
        pm.expect.fail("Ответ не JSON или некорректный Content-Type");
    }
});
```

---

## Итоги

- Реализованы все CRUD-методы для продуктов, категорий и поставщиков.  
- Поддержка загрузки изображений и импорта CSV через очередь.  
- Единый JSON-формат ответа (включая ошибки).  
- Тесты в Postman Runner для проверки формата.  
- RabbitMQ используется для фоновых задач.  
- В папке `postman` есть готовая коллекция для импорта в Postman.  
