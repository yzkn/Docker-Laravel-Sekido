# Laravel-Mp3Player-Sample

---

## clone

```ps
$ git clone https://github.com/YA-androidapp/Laravel-Mp3Player-Sample
```

## ライブラリのインストール

```ps
$ composer install
```

## 設定ファイル

```ps
$ cp .env.example .env
$ php artisan key:generate
```

## DB の準備

```ps
$ touch database/database.sqlite
$ php artisan migrate
```

## 動作確認

```ps
$ php artisan serve
```

[http://127.0.0.1:8000](http://127.0.0.1:8000) にアクセス

## ユーザー追加

ブラウザから実施

### システム管理者権限付与

システム管理者にしたいユーザーの `role` に `1` を設定

## Apache2でサブディレクトリに設置する場合

* `public/.htaccess`

```
#RewriteRule ^ %1 [L,R=301]
RewriteRule ^(.*)/$ /<SUBDIR>/$1 [L,R=301]
RewriteBase /<SUBDIR>/
```

* audio.js

```js
"player-graphics.gif"
// 追記
"audio/js/player-graphics.gif"
```

php.iniの以下の設定値も併せて確認

* file_uploads
* max_file_uploads
* upload_max_filesize
* memory_limit
* post_max_size
