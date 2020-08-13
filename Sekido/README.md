# Sekido in Docker containers

---

## Laravel アプリケーションの取得とセットアップ

```sh
$ docker-compose run --rm --no-deps php-fpm git clone "https://github.com/YA-androidapp/Sekido-Laravel-AudioPlayer-" .;chmod 777 -R storage

$ sudo cp ../app/sekido/.env.example ../app/sekido/.env
$ sudo nano ../app/sekido/.env
```

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laraveldb
DB_USERNAME=laraveluser
DB_PASSWORD=laravelpw

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### パッケージのインストールとキー生成

```sh
$ docker-compose run --rm --no-deps php-fpm ls
$ docker-compose run --rm --no-deps php-fpm composer install
$ docker-compose run --rm --no-deps php-fpm php artisan key:generate
```

## コンテナを起動

MySQL のデータベースは自動的に作成される

```sh
$ docker-compose up --build -d
```

## データベースの内容を確認

```sh
$ docker exec -it  sekido_mysql_1 /bin/bash
# mysql -h localhost -uroot -plaravelpw
mysql> SHOW DATABASES;
mysql> use laraveldb;
mysql> SHOW TABLES;
mysql> \q
```

## マイグレーション

```sh
$ docker-compose run --rm --no-deps php-fpm php artisan migrate
Migration table created successfully.
```

## 自分に管理者権限を付与(サイト上でユーザーを作成してから)

[localhost:8000](http://localhost:8000) にアクセス

```
$ docker exec -it sekido_mysql_1 /bin/bash
# mysql -h localhost -uroot -plaravelpw
mysql> use laraveldb;
mysql> show tables;
mysql> select * from users;
mysql> update users set role = 1 where id = 1;
mysql> \q
```

## MinIO

http://localhost:9000 にアクセスし、バケット（laravelbucket）を作成しておく

```sh
$ docker-compose run --rm --no-deps php-fpm which composer
/usr/local/bin/composer
$ docker-compose run --rm --no-deps php-fpm php -d memory_limit=-1 /usr/local/bin/composer require league/flysystem-aws-s3-v3
# $ docker-compose run --rm --no-deps php-fpm composer require league/flysystem-aws-s3-v3

$ sudo nano ../app/sekido/.env

$ sudo nano ../app/sekido/config/filesystems.php
```

```
<?php

return [
...

    'default' => env('FILESYSTEM_DRIVER', 's3'),
...

    'disks' => [
...

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
        ],
...

```

---

## Backup

```sh
$ tar -zcvf SekidoContainer_`date '+%Y%m%d%H%M%S'`.tar.gz SekidoContainer
```
