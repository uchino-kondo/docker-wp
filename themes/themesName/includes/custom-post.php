<?php
function create_post_type_and_taxonomy() {

  // News投稿タイプの追加
  register_post_type('news',
    array(
      'labels' => array(
        'name' => __('News一覧'),
        'singular_name' => __('News')
      ),
      'public' => true,
      'has_archive' => true,
      'menu_icon' => 'dashicons-media-text',
      'rewrite' => array('slug' => 'news'),
      'supports' => array('title', 'editor', 'thumbnail'),
      'show_in_rest' => true,
    )
  );

  // カスタムタクソノミー('news-cat')の追加
  register_taxonomy(
    'news-cat',
    'news',
    array(
      'label' => __('Newsカテゴリ'),
      'rewrite' => array('slug' => 'news-cat'),
      'hierarchical' => true,
      'show_ui' => true,
      'show_in_rest' => true,
    )
  );
}
add_action('init', 'create_post_type_and_taxonomy');



// news-itemのデフォルトカテゴリーを設定
function set_default_category_for_news($post_id, $post, $update) {
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  if ($post->post_type == 'news') {
    $default_category_id = get_term_by('slug', 'news-item', 'news-cat');
    if ($default_category_id) {
      wp_set_object_terms($post_id, array($default_category_id->term_id), 'news-cat', false);
    }
  }
}
add_action('save_post', 'set_default_category_for_news', 10, 3);

?>
