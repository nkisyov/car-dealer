# Setup

```
docker-compose build
docker-compose up -d
```

Install dependencies:
```
docker-compose exec -T laravel-env php composer.phar install --prefer-dist
```

Create SQLite databbase:
```
docker-compose exec -T laravel-env php bin/console doctrine:database:create
docker-compose exec -T laravel-env php bin/console doctrine:schema:update --force
docker-compose exec -T laravel-env chmod 777 var/mobliebg.db
```
