#!/bin/bash
# WordPress 初期設定スクリプト
# wp-env 起動後に自動実行される（lifecycleScripts.afterStart）

echo ""
echo "========================================="
echo " WordPress 初期設定"
echo "========================================="

# ── WordPress インストール確認 ──
# DB が空の場合（ボリューム削除後など）に自動インストールする
if ! npx wp-env run cli -- wp core is-installed 2>/dev/null; then
  echo "▶ WordPress をインストール中..."
  npx wp-env run cli -- wp core install \
    --url="http://localhost:8888" \
    --title="Development Site" \
    --admin_user=admin \
    --admin_password=password \
    --admin_email=admin@example.com \
    --skip-email
fi

# ── 言語設定 ──
echo "▶ 日本語化..."
npx wp-env run cli -- wp language core install ja 2>/dev/null
npx wp-env run cli -- wp site switch-language ja

# ── タイムゾーン・日付・時刻 ──
echo "▶ タイムゾーン・日付形式..."
npx wp-env run cli -- wp option update timezone_string 'Asia/Tokyo'
npx wp-env run cli -- wp option update date_format 'Y年n月j日'
npx wp-env run cli -- wp option update time_format 'H:i'

# ── パーマリンク ──
echo "▶ パーマリンク設定..."
npx wp-env run cli -- wp rewrite structure '/%postname%/'

# ── テーマ設定 ──
echo "▶ テーマ有効化・不要テーマ削除..."
npx wp-env run cli -- wp theme activate themesName

# themesName と twentytwentyfive 以外の全テーマを削除
INACTIVE_THEMES=$(npx wp-env run cli -- wp theme list --status=inactive --field=name 2>/dev/null | tr -d '\r')
for theme in $INACTIVE_THEMES; do
  if [ "$theme" != "twentytwentyfive" ]; then
    npx wp-env run cli -- wp theme delete "$theme" 2>/dev/null
  fi
done

# ── プラグイン日本語化 ──
echo "▶ プラグイン日本語パック..."
npx wp-env run cli -- wp language plugin install --all ja 2>/dev/null

echo ""
echo "========================================="
echo " 初期設定完了"
echo " WordPress: http://localhost:8888"
echo " 管理画面:  http://localhost:8888/wp-admin"
echo " ID: admin / PW: password"
echo "========================================="
echo ""
