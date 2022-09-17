## Run docker

```
docker-compose up
```

## Configure

```
docker-compose exec app bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

## Fix code style

```
./vendor/bin/pint
```

View even more detail about changes:
```
./vendor/bin/pint -v
```

Simply inspect code for style errors without actually changing the files:
```
./vendor/bin/pint --test
```

## Telescope 

http://localhost:8080/telescope

## Website

http://localhost:8080
