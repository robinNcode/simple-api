# Simple API

A simple CodeIgniter 4 application to demonstrate how to create a RESTful API with Redis.

## Requirements

- PHP 8.2+
- CodeIgniter 4
- Redis

## Installation

1. Clone this repository.
```
git clone https://github.com/robinncode/simple-api.git
```

2. If you don't have PHP 8.2+ installed. You can run the project with docker.

```
docker-compose up --build
```
3. To stop the docker container.
```
docker-compose down
```

4. If you don't have vendor folder in your project. You can install dependencies with composer.

```
composer install
```
with docker.
```
docker-compose exec app composer install
```

5. To run migrations.
```
php spark migrate
```
with docker.
```
docker-compose exec app php spark migrate
```