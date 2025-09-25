# Nomenclature API

Документация по проекту и инструкции для запуска, миграций, сидеров и настройки Passport (OAuth2).

## Требования

- Docker и Docker Compose (версия docker-compose или `docker compose` поддерживается)
- Git
- (опционально) Composer, если будете запускать локально без контейнера

## Запуск проекта через Docker — пошаговая инструкция

Ниже приведён упорядоченный набор команд и действий для запуска проекта в контейнерах и подготовки базы данных и сервисов.

1. Клонировать репозиторий
```bash
git clone https://github.com/username/nomenclature-api.git
cd nomenclature-api/src/
```

2. Создать файл окружения
```bash
cp .env.example .env
```

3. Установить зависимости (в контейнере, если не установлены в образе)
```bash
composer install --no-interaction --prefer-dist --optimize-autoloader
```

4. Поднять контейнеры
```bash
docker compose up -d --build
```

5. Выполнить миграции и заполнить базу сидерами
```bash
php artisan migrate:fresh --seed
```

6. Создать ключь для Laravel Passport (если ещё не установлен)
```bash
php artisan passport:client --personal
```

7. Запустить очередь (в отдельном терминале/процессе)
```bash
# внутри контейнера app
php artisan queue:work rabbitmq --sleep=3 --tries=3 --timeout=60
```

## Примеры быстрого рабочего процесса (коротко)

1. Расположение файлов для импорта Postman
В папке `postman` есть коллекция `api-collection.json`. Ее можно импортировать в Postman: File → Import → выбрать файл `postman/api-collection.json`.
В коллекции присутствуют переменные:
- baseUrl
- token
- productId
- categoryId
- supplierId
- imageFile
- csvFile

Тесты: для всех запросов добавлен глобальный тест проверки формата ответа (`message`, `data`, `timestamp`, `success`).

## Структура и формат ответов

Все ответы API возвращаются в едином JSON-формате.

**Успех**
```json
{
  "success": true,
  "message": "Описание результата",
  "data": [],
  "timestamp": "2025-09-24T15:36:00+00:00"
}
```

**Ошибка**
```json
{
  "success": false,
  "message": "Ошибка валидации",
  "data": {
      "name": ["The name field is required."]
  },
  "timestamp": "2025-09-24T15:36:00+00:00"
}
```

Все методы требуют заголовок:
```
Authorization: Bearer {token}
```




