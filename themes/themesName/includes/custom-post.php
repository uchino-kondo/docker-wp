<?php
function create_post_type_and_taxonomy() {

  // News投稿タイプの追加
  register_post_type('column',
    array(
      'labels' => array(
        'name' => __('コラム一覧'),
        'singular_name' => __('コラム')
      ),
      'public' => true,
      'has_archive' => true,
      'menu_icon' => 'dashicons-media-text',
      'rewrite' => array('slug' => 'column'),
      'supports' => array('title', 'editor', 'thumbnail'),
      'show_in_rest' => true,
    )
  );

  // カスタムタクソノミー('column-cat')の追加
  register_taxonomy(
    'column-cat',
    'column',
    array(
      'label' => __('コラムカテゴリ'),
      'rewrite' => array('slug' => 'column-cat'),
      'hierarchical' => true,
      'show_ui' => true,
      'show_in_rest' => true,
    )
  );
}
add_action('init', 'create_post_type_and_taxonomy');



// column-itemのデフォルトカテゴリーを設定
function set_default_category_for_column($post_id, $post, $update) {
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  if ($post->post_type == 'column') {
    $default_category_id = get_term_by('slug', 'column-item', 'column-cat');
    if ($default_category_id) {
      wp_set_object_terms($post_id, array($default_category_id->term_id), 'column-cat', false);
    }
  }
}
add_action('save_post', 'set_default_category_for_column', 10, 3);

?>
