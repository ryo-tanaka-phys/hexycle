# Hexycle

Hexycle は、文化祭での苗販売・栽培管理・イベント運営を支援する Laravel 製 Web アプリケーションです。

垂直農法装置 Hexycle で育成する植物について、商品の予約、在庫管理、栽培株の割当、センサーログ確認、イベントカフェの時間帯予約、入退場管理までを一つのシステムで扱います。

## 主な機能

### 一般ユーザー向け

- ユーザー登録・ログイン
- イベント情報の閲覧
- 苗商品の閲覧
- 苗の予約
- 自分の注文履歴の確認
- 栽培中の苗・育成位置の確認
- イベントカフェの時間帯予約
- 入場予約の確認
- 入場予約のキャンセル

### 管理者向け

- 注文一覧・注文状態管理
- 商品の登録・編集
- 栽培株の管理
- 栽培株と注文の割当
- Hexycle本体・スロットの確認
- センサーログの確認
- イベントカフェ予約の確認
- 来場者の入退場管理
- 入場予約からのチェックイン
- 現在の滞在人数・空き人数の確認

## 注文・栽培管理

商品の予約時には、予約受付期間、在庫、ユーザーごとの予約上限を確認します。

また、同時予約による在庫超過を防ぐため、データベースの行ロックを利用しています。

キャンセル時には在庫を復元し、二重キャンセルによって在庫が二重に戻らないようにしています。

注文された商品には実際の `Cultivation`（栽培株）を割り当てます。

注文数量と割当済み栽培株数を比較し、未割当株数を管理画面から確認できます。

必要な栽培株がすべて割り当てられ、かつすべて `ready` 状態になった場合のみ、注文を受け渡し準備完了に変更できます。

## イベント予約・入退場管理

イベントカフェでは30分単位の時間帯予約を受け付けます。

予約には以下の情報を記録します。

- 予約時間
- 人数
- 予約者名
- 予約コード
- 予約状態

管理者は予約情報からチェックインを行い、来場記録 `Visit` を生成できます。

複数人予約にも対応しており、現在の滞在人数は各来場グループの人数を合計して計算します。

イベントの定員を超えるチェックインはできません。

## Device / SensorLog

Hexycle本体を `Device`、各育成位置を `Slot` として管理しています。

各 Device では、以下のセンサー情報を扱います。

- 気温
- 湿度
- 水温
- EC
- pH
- 照度
- 水位

最新のセンサー値と、直近24時間のログを確認できます。

現在は Seeder によるデモデータを利用していますが、将来的には ESP32 から HTTPS API 経由でセンサーデータを送信する構成を想定しています。

## 主なデータ構造

```text
User
 ├─ Order
 │   └─ OrderItem
 │       └─ Cultivation
 │
 └─ AdmissionReservation

Event
 ├─ EventProduct
 │   └─ Product
 │
 ├─ Order
 │
 └─ EventProgram
     ├─ AdmissionReservation
     └─ Visit

Device
 ├─ Slot
 │   └─ Cultivation
 │
 └─ SensorLog
```

## Laratter で学んだ内容の応用

本制作では、Laratter の開発を通して学んだ Laravel の基本機能を、Hexycle の運用システムとして応用しました。

主に以下の内容を利用しています。

- Routing
- Controller
- Blade
- Migration
- Eloquent Model
- CRUD
- Validation
- Authentication
- Middleware
- Model Relation

Laratter では投稿管理を題材として Laravel の基本構造を学びました。

Hexycle ではその内容を、商品予約、栽培管理、Device 管理、イベント予約、入退場管理など複数の機能へ展開し、それぞれのデータをリレーションによって関連付けています。

## 使用技術

- PHP
- Laravel 13
- Laravel Breeze
- Blade
- Tailwind CSS
- MySQL
- Docker
- Laravel Sail
- Vite
- PHPUnit
- Git / GitHub

## 開発環境

- Windows
- WSL2 / Ubuntu
- Docker
- Laravel Sail

## セットアップ

リポジトリを取得後、依存関係をインストールします。

```bash
composer install
npm install
```

`.env` を作成します。

```bash
cp .env.example .env
```

Laravel Sail を起動します。

```bash
./vendor/bin/sail up -d
```

アプリケーションキーを生成します。

```bash
./vendor/bin/sail php artisan key:generate
```

データベースを構築します。

```bash
./vendor/bin/sail php artisan migrate
```

必要に応じて Seeder を実行します。

```bash
./vendor/bin/sail php artisan db:seed
```

別ターミナルで Vite を起動します。

```bash
./vendor/bin/sail npm run dev
```

ブラウザで以下へアクセスします。

```text
http://localhost
```

## テスト

Feature Test を中心に、注文、在庫、栽培、イベント予約、入退場、Device、SensorLog などの動作を確認しています。

全テストは以下で実行できます。

```bash
./vendor/bin/sail php artisan test
```

現在のテスト結果：

```text
84 passed
233 assertions
```

## 権限

ユーザーには `role` を持たせています。

```text
customer
admin
```

管理者向け画面には認証と管理者 Middleware を適用し、一般ユーザーからのアクセスを制限しています。

## 今後の拡張

- ESP32 から SensorLog を送信する API
- 実運用向け通知機能
- 栽培履歴の長期保存・分析
- クラウド上での複数 Hexycle 管理
- 管理画面の可視化強化