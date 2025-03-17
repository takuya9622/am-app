# coachtechフリマ[模擬案件]

<div id="top"></div>

## 使用技術一覧

<p style="display: inline">

  <img src="https://img.shields.io/badge/-Laravel-171923.svg?logo=laravel&style=for-the-badge">
  <img src="https://img.shields.io/badge/-Php-777BB4.svg?logo=php&logoColor=FFF&style=for-the-badge">
  <img src="https://img.shields.io/badge/-Nginx-269539.svg?logo=nginx&style=for-the-badge">
  <img src="https://img.shields.io/badge/-MySQL-4479A1.svg?logo=mysql&style=for-the-badge&logoColor=white">
  <img src="https://img.shields.io/badge/-phpmyadmin-6C78AF.svg?logo=phpmyadmin&style=for-the-badge&logoColor=white">
  <img src="https://img.shields.io/badge/-Docker-1488C6.svg?logo=docker&style=for-the-badge">
  <img src="https://img.shields.io/badge/-github-010409.svg?logo=github&style=for-the-badge">
  <img src="https://img.shields.io/badge/-MailHog-952225.svg?style=for-the-badge">

</p>

## 目次

1. [プロジェクト概要](#プロジェクト概要)
2. [環境](#環境)
3. [開発環境構築](#開発環境構築)
4. [URL](#URL)
5. [機能テスト](#機能テスト)
6. [主なコマンド一覧](#主なコマンド一覧)
7. [ER図](#ER図)

<br />

## プロジェクト概要


### Webサービス制作の概要・方針決定

| 項目           | 内容                              |
|----------------|-----------------------------------|
| サービス名      | coachtech勤怠管理アプリ                   |
| サービス概要    | ある企業が開発した独自の勤怠管理アプリ |
| 制作の背景と目的 | ユーザーの勤怠と管理を目的とする |
| 制作の目標      | 初年度でのユーザー数1000人達成   |
| 作業範囲        | 設計、コーディング、テスト       |
| 納品方法        | GitHubでのリポジトリ共有         |

---

### サイト要件一覧

| 項目                 | 内容                                      |
|----------------------|-------------------------------------------|
| ターゲットユーザー   | 社会人全般人                          |
| ターゲットブラウザ・OS | PC：Chrome/Firefox/Safari 最新バージョン |
---

### システム要件一覧

#### 機能要件一覧
- [機能要件]を参照

#### 非機能要件一覧
| 項目                 | 内容                                      |
|----------------------|-------------------------------------------|
| 運用・保守について   | クライアントが運用・保守を行う            |
| リリースについて     | 4ヶ月後を予定                            |
| セキュリティについて | アプリケーション内に限り考慮する          |
| SEOについて          | 考慮しない                                |
| コード品質について   | [コーディング規約]を参照してコーディングを行うこと<br>その他の要件については、[開発プロセス]を参照 |

#### デザイン要件
- UIデザインについて: [画面設計]を参照

#### テスト計画
- テスト項目について: [開発プロセス]を参照

<p align="right">(<a href="#top">トップへ</a>)</p>

## 環境


| 仕様技術               | バージョン  |
| --------------------- | ---------- |
| php                   | 8.3.11     |
| Laravel               | 11.37.0   |
| MySQL                 | 15.1     |
| phpMyAdmin            | 5.2.1      |
| nginx                 | 1.21.1     |
| MailHog               |            |


<p align="right">(<a href="#top">トップへ</a>)</p>

## 開発環境構築

必要に応じてdocker-compose.yml,Dockerfileは編集してください


### リポジトリの設定

以下のコマンドでリポジトリをクローン

```
git clone https://github.com/takuya9622/am-app.git
```

必要であれば以下のコマンドでリモートリポジトリに紐づけ

```
cd cfm
git remote set-url origin <作成したリポジトリのurl>
git add .
git commit -m "リモートリポジトリの変更"
git push origin main
```

エラーが出るようであれば以下のコマンドを実行後に再度コマンドを実行

```
sudo chmod -R 777 src/*
```

### Dockerコンテナの作成

以下のコマンドでdockerコンテナを作成

```
docker-compose up -d --build
```

### 環境変数の設定

以下を参考に.envを作成

```
cd src
cp .env.example .env
```

必要に応じてAPP_NAMEを変更
```
APP_NAME=COACHTECH-Attendance-Management

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=test@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

### パッケージのインストール

phpコンテナに移動し以下のコマンドを実行

```
composer install
php artisan key:generate
```

### マイグレーションの実行

以下の7つのテーブルに対応するファイルがsrc/database/migrationsにある事を確認

1.users<br />
2.attendance_records<br />
3.break_records<br />
4.attendance_corrections<br />
5.break_corrections<br />

確認出来たら以下のコマンドでマイグレーションを実行

```
php artisan migrate
```

もしエラーが出た場合は以下のコマンドでリトライ

```
php artisan migrate:fresh
```

必要に応じて以下のコマンドでシーディングを実行

```
php artisan db:seed
```

うまく行かない場合は以下のコマンドを実行後に再度マイグレーション

```
composer dump-autoload
```

<p align="right">(<a href="#top">トップへ</a>)</p>

## URL

・開発環境 : http://localhost<br />
・phpMyAdmin : http://localhost:8080<br />
・MailHog : http://localhost:8025<br />

<p align="right">(<a href="#top">トップへ</a>)</p>

## 機能テスト

以下を参考に.env.testingを作成
```
cp .env .env.testing
```
DBの部分を以下のように変更
```
-DB_CONNECTION=mysql
-DB_HOST=mysql
-DB_PORT=3306
-DB_DATABASE=laravel_db
-DB_USERNAME=laravel_user
-DB_PASSWORD=laravel_pass

+DB_CONNECTION=sqlite
+DB_DATABASE=:memory:
```
以下のコマンドでテストを実行
```
docker-compose exec php bash
php artisan test tests/Feature
```
期間内にテストメソッドを全て記述できませんでした

<p align="right">(<a href="#top">トップへ</a>)</p>

## 主なコマンド一覧

| コマンド                                                                               | 実行する処理                           |
| -------------------------------------------------------------------------------------- | -------------------------------------- |
| composer create-project --prefer-dist laravel/laravel                                  | Laravelをインストール                 |
| composer require laravel/fortify                                                       | Laravel Fortifyをインストール         |
| php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"         | Laravel Fortifyのカスタムの準備 |
| docker-compose up -d --build                                                           | コンテナの起動                         |
| docker-compose down                                                                    | コンテナの停止                         |
| docker-compose exec php bash                                                           | php コンテナに入る                     |
| php artisan key:generate                                                               | 暗号化キーを生成                     |
| php artisan make:migration create_items_table                                       | マイグレーションファイルを作成         |
| php artisan make:seeder ItemSeeder                                                 | シーダーファイルを作成                 |
| php artisan make:factory ItemFactory                                                | ファクトリーファイルを作成             |
| php artisan migrate                                                                    | マイグレーションを行う                 |
| php artisan db:seed                                                                    | シーディングを行う                     |
| php artisan make:model Item                                                         | モデルファイルを作成                   |
| php artisan make:controller AttendanceController                                          | コントローラーファイルを作成           |
| php artisan make:request CorrectionRequest                                                | リクエストファイルを作成               |
| php artisan test tests/Feature| 機能テストを実行|

<p align="right">(<a href="#top">トップへ</a>)</p>

## ER図
![alt](er.png)

<p align="right">(<a href="#top">トップへ</a>)</p>

[機能要件]:https://docs.google.com/spreadsheets/d/1IMh8n3YuwGmA15w9Xjn8_M6ceBFe9BbLXZPaSyEdVP4/edit?gid=1909938334#gid=1909938334

[コーディング規約]:https://estra-inc.notion.site/1263a94a2aab4e3ab81bad77db1cf186

[開発プロセス]:https://docs.google.com/spreadsheets/d/1IMh8n3YuwGmA15w9Xjn8_M6ceBFe9BbLXZPaSyEdVP4/edit?gid=950806051#gid=950806051

[画面設計]:https://docs.google.com/spreadsheets/d/1IMh8n3YuwGmA15w9Xjn8_M6ceBFe9BbLXZPaSyEdVP4/edit?gid=1998718085#gid=1998718085