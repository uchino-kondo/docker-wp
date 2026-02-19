<?php
function my_setup()
{
  add_theme_support('post-thumbnails');
  add_theme_support('automatic-feed-links');
  add_theme_support('title-tag');
  add_theme_support('html5', array(
    'search-form',
    'comment-form',
    'comment-list',
    'gallery',
    'caption',
  ));
}
add_action('after_setup_theme', 'my_setup');



/*
･add_image_size( 'サムネイル名', widthサイズ, heightサイズ, true(切り取る) or false(比率を維持してリサイズ));
<?php the_post_thumbnail('post_thumb', array('alt' => esc_attr(get_the_title()))); ?>
･既存の画像は反映されない。削除が必要
*/
add_image_size('post_thumbnails', 1280, 900, true);



//投稿名変更
function Change_menulabel()
{
  global $menu;
  global $submenu;
  $name = 'News';
  $menu[5][0] = $name;
  $submenu['edit.php'][5][0] = $name . '一覧';
  $submenu['edit.php'][10][0] = '新しい' . $name;
  $menu[10][0] = '画像・ファイル';
  $submenu['upload.php'][5][0] = '画像・ファイル一覧';
  $submenu['upload.php'][10][0] = '画像・ファイルを追加';
}
function Change_objectlabel()
{
  global $wp_post_types;
  $name = 'News';
  $labels = &$wp_post_types['post']->labels;
  $labels->name = $name;
  $labels->singular_name = $name;
  $labels->add_new = _x('追加', $name);
  $labels->add_new_item = $name . 'の新規追加';
  $labels->edit_item = $name . 'の編集';
  $labels->new_item = '新規' . $name;
  $labels->view_item = $name . 'を表示';
  $labels->search_items = $name . 'を検索';
  $labels->not_found = $name . 'が見つかりませんでした';
  $labels->not_found_in_trash = 'ゴミ箱に' . $name . 'は見つかりませんでした';
}
add_action('init', 'Change_objectlabel');
add_action('admin_menu', 'Change_menulabel');



//管理画面並べ替え
function sort_side_menu($menu_order)
{
  return array(
    "index.php",
    "edit.php",
    "separator1",
    "edit.php?post_type=page",
    "upload.php",
    "edit-comments.php",
    "separator2",
    "themes.php",
    "plugins.php",
    "users.php",
    "tools.php",
    "options-general.php",
    "separator-last"
  );
}
add_filter('custom_menu_order', '__return_true');
add_filter('menu_order', 'sort_side_menu');



// カスタム投稿呼び出し
require_once(dirname(__FILE__) . '/includes/custom-post.php');


// パンくずリスト
require_once(dirname(__FILE__) . '/includes/breadcrumb.php');


// ダッシュボード一部消去
function remove_dashboard_widget()
{
  remove_meta_box('dashboard_activity', 'dashboard', 'normal');
  remove_meta_box('dashboard_primary', 'dashboard', 'side');
}
add_action('wp_dashboard_setup', 'remove_dashboard_widget');



// ページネーションを使用するために必須
function my_parse_query($query)
{
  if (!isset($query->query_vars['paged']) && isset($query->query_vars['page']))
    $query->query_vars['paged'] = $query->query_vars['page'];
}
add_action('parse_query', 'my_parse_query');



// エディター自動保存延長
function change_autosave_interval($editor_settings)
{
  $editor_settings['autosaveInterval'] = 3600;
  return $editor_settings;
}
add_filter('block_editor_settings', 'change_autosave_interval');



// ブラウザタブの２ページ目表示を消す
function remove_title_pagenation($title)
{
  unset($title['page']);
  return $title;
};
add_filter('document_title_parts', 'remove_title_pagenation');



// セパレータ変更
function change_title_separator($sep)
{
  $sep = ' | ';
  return $sep;
}
add_filter('document_title_separator', 'change_title_separator');



// 自動成形阻止
add_action('init', function () {
  remove_filter('the_title', 'wptexturize');
  remove_filter('the_content', 'wptexturize');
  remove_filter('the_excerpt', 'wptexturize');
  remove_filter('the_title', 'wpautop');
  remove_filter('the_content', 'wpautop');
  remove_filter('the_excerpt', 'wpautop');
  remove_filter('widget_text_content', 'wpautop');
  remove_filter('the_editor_content', 'wp_richedit_pre');
});



/**
 * ========================================
 * Vite アセット読み込み
 * ========================================
 */

function is_vite_dev_mode()
{
  return file_exists(get_template_directory() . '/.vite-running');
}

function get_vite_manifest()
{
  $manifest_path = get_template_directory() . '/dist/.vite/manifest.json';
  if (file_exists($manifest_path)) {
    $manifest_content = file_get_contents($manifest_path);
    return json_decode($manifest_content, true);
  }
  return null;
}

function my_script_init()
{
  $latest_ver = '1.0.0';

  // GSAP（アニメーション用、必要な場合のみコメントを外す）
  // wp_enqueue_script('gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js', array(), '3.9.1');
  // wp_enqueue_script('scrollTrigger', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/ScrollTrigger.min.js', array('gsap'), '3.9.1');

  if (is_vite_dev_mode()) {
    // 開発モード: Vite開発サーバーから読み込み（プロキシ経由）
    wp_enqueue_script('vite-client', '/@vite/client', array(), null);
    wp_enqueue_script('common', '/src/js/common.js', array(), null);

    add_filter('script_loader_tag', function ($tag, $handle, $src) {
      if (in_array($handle, ['vite-client', 'common'])) {
        return '<script type="module" src="' . esc_url($src) . '"></script>';
      }
      return $tag;
    }, 10, 3);
  } else {
    // 本番モード: ビルド済みファイルを読み込み
    $manifest = get_vite_manifest();

    if ($manifest) {
      if (isset($manifest['src/scss/style.scss']['css'])) {
        foreach ($manifest['src/scss/style.scss']['css'] as $css_file) {
          wp_enqueue_style('style', get_template_directory_uri() . '/dist/' . $css_file, array(), $latest_ver);
        }
      }

      if (isset($manifest['src/js/common.js']['file'])) {
        wp_enqueue_script('common', get_template_directory_uri() . '/dist/' . $manifest['src/js/common.js']['file'], array(), $latest_ver, true);

        add_filter('script_loader_tag', function ($tag, $handle, $src) {
          if ($handle === 'common') {
            return '<script type="module" src="' . esc_url($src) . '"></script>';
          }
          return $tag;
        }, 10, 3);
      }
    } else {
      wp_enqueue_style('style', get_template_directory_uri() . '/dist/css/style.css', array(), $latest_ver);
      wp_enqueue_script('common', get_template_directory_uri() . '/dist/js/common.js', array(), $latest_ver, true);
    }
  }
}
add_action('wp_enqueue_scripts', 'my_script_init');



function block_theme_setup()
{
  add_theme_support('wp-block-styles');
  add_theme_support('editor-styles');
  add_editor_style('style.css');
}
add_action('after_setup_theme', 'block_theme_setup');



// Contact Form 7の自動pタグ無効
function wpcf7_autop_return_false()
{
  return false;
}
add_filter('wpcf7_autop_or_not', 'wpcf7_autop_return_false');



// 投稿ページのパーマリンクをカスタマイズ
function add_article_post_permalink($permalink)
{
  $permalink = '/news' . $permalink;
  return $permalink;
}
add_filter('pre_post_link', 'add_article_post_permalink');

function add_article_post_rewrite_rules($post_rewrite)
{
  $return_rule = array();
  foreach ($post_rewrite as $regex => $rewrite) {
    $return_rule['news/' . $regex] = $rewrite;
  }
  return $return_rule;
}
add_filter('post_rewrite_rules', 'add_article_post_rewrite_rules');



//bodyクラスにページスラッグと最上の親ページスラッグのクラスを追加
function add_page_slug_class_name($classes)
{
  if (is_page()) {
    $page = get_post(get_the_ID());
    $classes[] = $page->post_name;

    $parent_id = $page->post_parent;
    if (0 == $parent_id) {
      $classes[] = get_post($parent_id)->post_name;
    } else {
      $progenitor_id = array_pop(get_ancestors($page->ID, 'page', 'post_type'));
      $classes[] = get_post($progenitor_id)->post_name;
    }
  }
  return $classes;
}
add_filter('body_class', 'add_page_slug_class_name');

//カテゴリスラッグクラスをbodyクラスに追加
function add_category_slug_classes_to_body_classes($classes)
{
  global $post;
  if (is_single()) {
    foreach ((get_the_category($post->ID)) as $category)
      $classes[] = $category->category_nicename;
  } elseif (is_category()) {
    $catInfo = get_queried_object();
    $catSlug = $catInfo->slug;
    $catParent = $catInfo->parent;
    $thisCat =  $catInfo->cat_ID;
    if (!$catParent) {
      $classes[] =  $catSlug;
    } else {
      $ancestor = array_pop(get_ancestors($thisCat, 'category'));
      $classes[] =  get_category($ancestor)->slug;
    }
  }
  return $classes;
}
add_filter('body_class', 'add_category_slug_classes_to_body_classes');



// ショートコード テーマの「img」フォルダへのURL
// usage: [img]hoge.jpg
function my_images_dir()
{
  return get_template_directory_uri() . '/img/';
}
add_shortcode('img', 'my_images_dir');



//ショートコード get_template_partをショートコード化
//usage: [template slug="includes/header" name="nav" args="{"hoge":"fuga"}"]
function my_get_template_part($atts)
{
  extract(shortcode_atts(
    [
      'slug' => '',
      'name' => null,
      'args' => '',
    ],
    $atts,
    'template'
  ));

  ob_start();
  get_template_part($slug, $name, empty($args) ? [] : json_decode($args));
  $html = ob_get_contents();
  ob_end_clean();

  return $html;
}
add_shortcode('template', 'my_get_template_part');
