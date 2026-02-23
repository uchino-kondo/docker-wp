# docker-wp - WordPress テーマ開発テンプレート

wp-env（Docker）+ Vite による WordPress テーマ開発環境。
Docker で WordPress を動かし、Vite でビルドを行う。

---

## 前提条件

- **Docker Desktop** がインストール済み＆起動していること
- **Node.js** v18 以上がインストール済みであること

---

## 初回セットアップ

### 1. パッケージインストール

```bash
npm install
```

### 2. 環境変数の設定（画像圧縮を使う場合）

```bash
cp .env.example .env
```

`.env` を開いて TinyPNG の API キーを設定する。
API キーは https://tinypng.com/developers で無料取得できる（月500枚まで無料）。

```
TINYPNG_API_KEY=ここにAPIキーを貼る
```

### 3. WordPress を起動する

Docker Desktop が起動していることを確認してから実行。

```bash
npm start
```

初回は WordPress のダウンロード＋プラグインのインストールが行われるため数分かかる。
起動後に `scripts/setup-wp.sh` が自動実行され、以下が設定される:

- 言語: 日本語
- タイムゾーン: Asia/Tokyo
- 日付形式: Y年n月j日 / H:i
- パーマリンク: 投稿名
- テーマ: themesName を有効化
- 不要なデフォルトテーマを削除（Twenty Twenty-Five のみ残す）
- プラグインの日本語パックをインストール

**WordPress 管理画面:**
- URL: http://localhost:8888/wp-admin
- ユーザー名: `admin`
- パスワード: `password`

---

## 日常の開発フロー

### 開発サーバーの起動

毎回の開発作業は、以下の順番でターミナルを使う。

**ターミナル1: WordPress 起動**

```bash
npm start
```

（すでに起動中ならスキップ可。迷ったら毎回実行しても問題ない）

**ターミナル2: Vite 開発サーバー起動**

```bash
npm run dev
```

**ターミナル3: 画像圧縮（画像を追加・変更したとき）**

```bash
npm run img
```

画像圧縮は常時起動しておく必要はない。`src/img/` に画像を追加・変更したタイミングで実行する。

### ブラウザでのアクセス先（重要）

| URL | 用途 |
|---|---|
| **http://localhost:5173** | サイト表示（SCSS/JS の HMR が効く。開発中はこちらを使う） |
| **http://localhost:8888/wp-admin** | 管理画面（投稿・固定ページ・プラグイン設定など） |

- サイトの見た目を確認・編集するときは **5173**
- 管理画面で作業するときは **8888**
- 5173 から wp-admin にアクセスすると自動で 8888 に飛ぶので、そのまま使えばよい

### 開発中の動作

- SCSS を編集 → ブラウザに即反映（HMR）
- JS を編集 → ブラウザに即反映（HMR）
- PHP を編集 → ブラウザが自動リロード
- Vite を止めると `.vite-running` ファイルが自動削除され、本番モードに戻る

### 開発終了

```bash
# Vite: Ctrl + C で停止
# WordPress:
npm run stop
```

### Git でクローンしたとき

**前提:** Git はインストール済みであること。GitHub に SSH 鍵を登録済みであること。

1. **project フォルダにクローンする**

```bash
cd ~/MyDocumet/project
git clone git@github.com:uchino-kondo/docker-wp.git
cd docker-wp
```

2. **フォルダ名を変更して管理する（任意）**

クローン直後は `docker-wp` というフォルダ名になる。案件ごとに分けたい場合はリネームしてよい。

```bash
# 例: 案件名のフォルダにしたい場合
mv docker-wp 20260301_clientname
cd 20260301_clientname
```

3. **このあと** → [初回セットアップ](#初回セットアップ) の「パッケージインストール」から進める。

---

## コマンド一覧

| コマンド | 説明 |
|---|---|
| `npm start` | WordPress（Docker）を起動 + 初期設定 |
| `npm run stop` | WordPress（Docker）を停止 |
| `npm run dev` | Vite 開発サーバーを起動（SCSS/JS の HMR + PHP の自動リロード） |
| `npm run build` | 本番用ビルド（`themes/themesName/dist/` に出力） |
| `npm run img` | TinyPNG で PNG/JPG を圧縮（GIF/SVG/WebP はそのままコピー） |
| `npm run img:copy` | 全画像を圧縮せずにコピーのみ（API不要） |

---

## ディレクトリ構成

```
docker-wp/
├── .wp-env.json            ← wp-env 設定（プラグイン・PHP バージョン等）
├── .env                    ← TinyPNG API キー（git管理外）
├── package.json            ← npm 依存関係・スクリプト
├── vite.config.js          ← Vite 設定
├── postcss.config.js       ← PostCSS（Autoprefixer）
│
├── src/                    ← 【編集するファイル】ソースコード
│   ├── scss/
│   │   ├── style.scss      ← SCSS エントリーポイント
│   │   ├── base/           ← リセット・ベーススタイル
│   │   ├── global/         ← 変数($mainColor等)・関数(rem等)・mixin(mq等)
│   │   ├── module/         ← コンポーネント別スタイル
│   │   └── page/           ← ページ別スタイル
│   ├── js/
│   │   └── common.js       ← JS エントリーポイント（SCSS も import している）
│   └── img/                ← 圧縮前の画像原本を置く場所
│
├── themes/
│   └── themesName/         ← 【編集するファイル】WordPress テーマ
│       ├── style.css       ← テーマヘッダー（テーマ名の定義）
│       ├── functions.php   ← テーマ関数（Vite アセット読み込み含む）
│       ├── header.php / footer.php
│       ├── front-page.php  ← トップページ
│       ├── home.php        ← ブログ一覧
│       ├── single.php      ← 投稿詳細
│       ├── page.php        ← 固定ページ
│       ├── category.php    ← カテゴリー一覧
│       ├── 404.php
│       ├── includes/       ← 共通パーツ（head, breadcrumb, pagenation 等）
│       ├── img/            ← 圧縮済み画像（npm run img で自動生成）
│       └── dist/           ← Vite ビルド出力（git管理外）
│
└── scripts/
    ├── tinypng.js          ← TinyPNG 画像圧縮スクリプト
    └── setup-wp.sh         ← WordPress 初期設定スクリプト（npm start 時に自動実行）
```

---

## 画像の管理

### 基本フロー

1. `src/img/` に画像原本を配置する（サブフォルダ可）
2. `npm run img` を実行する
3. PNG/JPG は TinyPNG API で圧縮、GIF/SVG/WebP はそのままコピーされる
4. 出力先: `themes/themesName/img/`

### 再圧縮の防止

`src/img/.tinypng-sigs.json` にファイルのハッシュが記録される。
画像が変更されていなければスキップされるため、API の無駄遣いを防げる。

### TinyPNG を使わずにコピーだけしたい場合

```bash
npm run img:copy
```

### PHP テンプレートでの画像参照

```php
<img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/common/logo.svg" alt="">
```

---

## Vite の開発/本番の切り替え

`functions.php` が `.vite-running` ファイルの有無で自動判定する。

- **開発モード**（`npm run dev` 実行中）:
  Vite 開発サーバー（localhost:5173）から SCSS/JS を直接読み込む。HMR が効く。

- **本番モード**（`npm run build` 後）:
  `themes/themesName/dist/` のビルド済みファイルを `manifest.json` 経由で読み込む。

手動での切り替え作業は不要。

---

## SCSS の構成

既存テンプレートと同じ 4 層構造。

| フォルダ | 役割 | 例 |
|---|---|---|
| `base/` | リセット CSS・全体のベーススタイル | `_reset.scss`, `_base.scss` |
| `global/` | 変数・関数・mixin（他のファイルから `@use` で参照） | `_setting.scss`, `_function.scss` |
| `module/` | 再利用するコンポーネント | `_header.scss`, `_card.scss` |
| `page/` | ページ固有のスタイル | `_top.scss`, `_blog.scss` |

### よく使う変数・mixin

```scss
// global/_setting.scss で定義済み

// ブレイクポイント: @include mq(md) { ... }
// md = 768px, lg = 1024px, xl = 1280px

// rem 関数: rem(16) → 1.6rem

// フォント一括指定: @include f_around(16, $medium, 24, 50)
// → font-size, weight, line-height, letter-spacing をまとめて設定
```

---

## プラグインの管理

`.wp-env.json` の `plugins` 配列で管理される。
`npm start` 時に自動でインストールされる。

### プラグインを追加する場合

`.wp-env.json` の `plugins` に WordPress.org のダウンロード URL を追加し、再起動する。

```json
"plugins": [
  "https://downloads.wordpress.org/plugin/advanced-custom-fields.zip",
  "https://downloads.wordpress.org/plugin/contact-form-7.zip",
  "https://downloads.wordpress.org/plugin/新しいプラグインのスラッグ.zip"
]
```

```bash
npm run stop
npm start
```

※ URL は WordPress.org のプラグインページ → 「ダウンロード」リンクから取得できる。

---

## Local との違い・Docker の注意点

### Local と同じところ

- テーマの PHP / SCSS / JS を編集する作業は全く同じ
- 管理画面の操作も同じ
- 固定ページ作成、投稿、プラグイン設定なども同じ

### Local と違うところ

| 項目 | Local | Docker（wp-env） |
|---|---|---|
| WordPress ファイル | `~/Local Sites/` に実ファイルがある | Docker コンテナ内にある（直接見えない） |
| 起動方法 | Local アプリで「Start」ボタン | ターミナルで `npm start` |
| 停止方法 | Local アプリで「Stop」ボタン | ターミナルで `npm run stop` |
| DB 管理 | Adminer が内蔵 | `npx wp-env run cli -- wp db export` 等 |
| 複数サイト | Local アプリで切り替え | ポート番号を変えて複数起動可 |
| 環境の共有 | Blueprint で共有 | `.wp-env.json` + `setup-wp.sh` で共有 |

### Docker Desktop の管理画面について

Docker Desktop は基本的に **開いておくだけでOK**。操作は不要。

- **Containers タブ**: 実行中のコンテナが見える。`npm start` すると表示される
- 緑の丸 = 起動中 / グレー = 停止中
- 右側のゴミ箱アイコンで削除できるが、**通常は `npm run stop` で停止するだけでよい**
- コンテナを Docker Desktop から削除すると WordPress データが消えるので注意

### データの永続性（重要）

| 操作 | テーマファイル | DB（投稿・設定等） |
|---|---|---|
| `npm run stop` → `npm start` | 残る | 残る |
| Mac を再起動 | 残る | 残る |
| Docker Desktop を終了→再起動 | 残る | 残る |
| `npx wp-env destroy` | **残る（ローカルにある）** | **消える（要注意）** |
| Docker Desktop でコンテナ削除 | **残る** | **消える（要注意）** |

テーマのファイル（`themes/themesName/`）はローカルのフォルダにあるので何をしても消えない。
ただし **DB（投稿・固定ページ・プラグイン設定・メディア等）は Docker 内にある** ため、
`destroy` やコンテナ削除で消える。大事な DB は WPvivid でバックアップしておくこと。

### よく使う WP-CLI コマンド

wp-env では WP-CLI がターミナルから使える。管理画面でできる操作の多くをコマンドで実行可能。

```bash
# DB をエクスポート（バックアップ）
npx wp-env run cli -- wp db export /tmp/backup.sql

# プラグイン一覧を確認
npx wp-env run cli -- wp plugin list

# テーマ一覧を確認
npx wp-env run cli -- wp theme list

# キャッシュをクリア
npx wp-env run cli -- wp cache flush

# パーマリンクを再構築
npx wp-env run cli -- wp rewrite flush
```

### Docker を使う上での注意点

1. **Docker Desktop は先に起動しておく**: `npm start` の前に Docker Desktop が起動している必要がある
2. **止める順番**: 開発終了時は Vite（Ctrl+C）→ `npm run stop` の順で止める
3. **ディスク容量**: Docker イメージは数GB使う。不要になったら `npx wp-env destroy` で削除できる
4. **ポート競合**: Local や他のサーバーが 8888 を使っていると起動できない。Local を止めてから使う
5. **Mac スリープ**: スリープ中は Docker も止まるが、復帰後に自動で戻る。たまに戻らない場合は `npm run stop` → `npm start` で再起動

---

## 新しいプロジェクト（案件）を作るとき

このリポジトリは GitHub の **テンプレートリポジトリ** として運用する。
テンプレートリポジトリ: https://github.com/uchino-kondo/docker-wp

### 1. GitHub で新しいリポジトリを作成

1. https://github.com/uchino-kondo/docker-wp を開く
2. 「**Use this template**」→「**Create a new repository**」をクリック
3. リポジトリ名を案件名にする（例: `20260301_wp_testsite`）
4. 「Create repository」で作成

### 2. ローカルに clone

```bash
cd ~/MyDocumet/project
git clone git@github.com:uchino-kondo/20260301_wp_testsite.git
cd 20260301_wp_testsite
```

### 3. テーマ名を一括置換

Cursor で開き、「`themesName` を `testsite` に一括置換して」と依頼する。

変更対象:
- `themes/themesName` → フォルダ名リネーム
- `vite.config.js` → `const themeName`
- `package.json` → `"dev"` スクリプト内のパス
- `.wp-env.json` → `themes` のパス
- `.gitignore` → `themes/themesName/` のパス
- `themes/新テーマ名/style.css` → `Theme Name:`
- `scripts/tinypng.js` → `DEST_DIR` のパス
- `scripts/setup-wp.sh` → `wp theme activate` のテーマ名

### 4. セットアップ

```bash
cp .env.example .env    # TinyPNG API キーを設定
npm install
npm start               # WordPress 起動
npm run dev             # Vite 開発サーバー起動
```

---

## トラブルシューティング

### Docker が起動していないと言われる

Docker Desktop を起動してから `npm start` を実行する。

### ポート 8888 が使われている

`.wp-env.json` の `port` を変更し、`vite.config.js` の `proxy.target` も同じポートに合わせる。
Local アプリが動いている場合は先に止める。

### Vite の HMR が効かない

`http://localhost:5173` でアクセスしているか確認する（8888 ではなく 5173）。

### wp-env をリセットしたい（DB を初期化）

```bash
npx wp-env destroy
npm start
```

WordPress の DB がリセットされ、`setup-wp.sh` で再設定される。テーマファイルは残る。

### Docker のディスク容量が気になる

```bash
# 使用中のディスク容量を確認
docker system df

# 使っていないイメージ・キャッシュを削除（他のプロジェクトに影響しないか注意）
docker system prune
```

### TinyPNG の API エラー

`.env` の `TINYPNG_API_KEY` が正しいか確認する。
月500回の無料枠を超えている場合は翌月まで待つか、`npm run img:copy` でコピーのみ行う。
