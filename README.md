# Справочник Организаций, Зданий и Деятельностей

Проект представляет собой REST API для справочника организаций, зданий и видов деятельности. Взаимодействие с пользователем осуществляется через HTTP запросы к API серверу.

## Структура проекта

1. **Организация**:
    - Название
    - Номера телефонов (можно несколько)
    - Здание (одно)
    - Виды деятельности (несколько)

2. **Здание**:
    - Адрес
    - Географические координаты (широта и долгота)

3. **Деятельность**:
    - Название
    - Древовидная структура (например, «Еда → Мясная продукция → Молочная продукция»)

## Функционал приложения

1. Список организаций, находящихся в конкретном здании.
2. Список организаций по виду деятельности.
3. Список организаций, находящихся в радиусе/области относительно заданной точки.
4. Список всех зданий.
5. Получение информации об организации по ID.
6. Поиск организаций по виду деятельности с ограничением уровня вложенности (до 3 уровней).
7. Поиск организаций по названию.

## Требования

- PHP 8.2
- Laravel 11
- MySQL
- Docker (для контейнеризации)

## Установка

### Шаг 1: Клонирование репозитория


1. git clone https://github.com/Afzalshoh02/business-locator.git
2. cd business-locator

### Шаг 2: Запуск Docker контейнеров
sudo docker-compose up --build

### Шаг 3: Создание .env файла
cp .env.example .env

### Шаг 4: Настройка базы данных

1. DB_CONNECTION=mysql
2. DB_HOST=db
3. DB_PORT=3306
4. DB_DATABASE=laravel_docker_rest_api
5. DB_USERNAME=root
6. DB_PASSWORD=root

### Шаг 5: Настройка базы данных
composer install

### Шаг 6: Настройка базы данных

sudo docker exec -it laravel_app php artisan migrate --seed

### Шаг 7: Шаги для исправления при возникновении ошибки
sudo chown -R www-data:www-data /var/www/business-locator/storage
sudo chmod -R 775 /var/www/business-locator/storage
php artisan cache:clear
php artisan view:clear
php artisan config:clear

sudo docker-compose down
sudo docker-compose down --volumes --remove-orphans
