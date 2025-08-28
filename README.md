# フリマアプリ

## 環境構築

**Dockerビルド**

1. `git clone git@github.com:some-git-hub/flea-market.git`
2. `docker-compose up -d -build`

- MySQLはOSによって起動しない場合があるため、それぞれのPCに合わせてdocker-compose.ymlファイルを編集してください。

**Laravel環境構築**

1. `docker-compose exec php bash`
2. `composer install`
3. `cp .env.example .env`
4. 「.env」に以下の環境変数を追加する。

```text
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```

5. `php artisan key:generate`
6. `php artisan migrate --seed`
7. 「.env」に以下を追記する。

```text
STRIPE_KEY=pk_test_yourkey                      # 公開可能キー (Publishable Key)
STRIPE_SECRET=sk_test_yoursecret                # 秘密キー (Secret Key)
STRIPE_WEBHOOK_SECRET=whsec_yourwebhooksecret   # Webhookシークレット
```

- STRIPE_KEY と STRIPE_SECRET は Stripe ダッシュボードから取得する。
- STRIPE_WEBHOOK_SECRET は Webhook 作成時に Stripe から取得

8. Stripe CLI をインストールしてログイン後、以下のコマンドを実行する。

```bash
stripe listen --forward-to http://localhost/api/stripe/webhook
```

>* このコマンドを実行すると、`payment_intent.succeeded` などのイベントが
ローカル環境の `/api/stripe/webhook` に転送される。*

## 使用技術

- PHP 8.1.33
- Laravel 8.83.8
- mysql 8.0.26
- nginx 1.21.1


## ER図

* 以下が本システムのER図です。

![ER図](./docs/er-diagrams.png)


## URL

- 開発環境: http://localhost/
- phpMyAdmin: http://localhost:8080/