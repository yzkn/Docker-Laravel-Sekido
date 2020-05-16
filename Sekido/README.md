# Sekido in Docker containers

---

## Laravelアプリケーションの取得とセットアップ

```sh
$ docker-compose run --rm --no-deps php-fpm git clone "https://github.com/YA-androidapp/Sekido-Laravel-AudioPlayer-" sekido;chown www-data:www-data -R sekido

$ sudo cp ../app/sekido/.env.example ../app/sekido/.env
$ sudo nano ../app/sekido/.env
```

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laraveldb
DB_USERNAME=laraveluser
DB_PASSWORD=${MYSQL_ROOT_PASSWORD}

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### キーを生成

```sh
$ docker-compose run --rm --no-deps php-fpm cd sekido;composer install;php artisan key:generate
```

## コンテナを起動

MySQLのデータベースは自動的に作成される

```sh
$ docker-compose up --build -d
```

## データベースの内容を確認

```sh
$ docker exec -it  laravelapp_mysql_1 /bin/bash
# mysql -h localhost -uroot -p'${MYSQL_ROOT_PASSWORD}'
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

```
$ docker exec -it laravelapp_mysql_1 /bin/bash
# mysql -h localhost -uroot -p'${MYSQL_ROOT_PASSWORD}'
mysql> use laraveldb;
mysql> show tables;
mysql> select * from users;
mysql> update users set role = 1 where id = 1;
mysql> \q
```

## MinIO

http://localhost:9000 にアクセスし、バケットを作成しておく

```sh
$ docker-compose exec php-fpm composer require league/flysystem-aws-s3-v3
```

```sh
$ docker-compose run --rm --no-deps php-fpm composer require league/flysystem-aws-s3-v3

$ sudo nano ../app/sekido/.env

$ sudo nano ../app/sekido/config/filesystems.php

```

---

## Backup

```sh
$ tar -zcvf SekidoContainer_`date '+%Y%m%d%H%M%S'`.tar.gz SekidoContainer
```
