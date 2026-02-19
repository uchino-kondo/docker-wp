

<?php if (is_single()): ?>

<div class="previous-post"><?php previous_post_link(' %link', '<span class="arrow _prev">前の記事へ</span><br> <span class="title">%title</span>'); ?></div>
<div class="back-list-wrap">
  <a class="back-list" href="<?php echo esc_url(home_url('/')); ?>news">News一覧へ</a>
</div>
<div class="next-post"><?php next_post_link('%link ', '<span class="arrow _next">次の記事へ</span><br> <span class="title">%title</span>'); ?></div>

<?php else: ?>

  <!-- 一覧ページ用ページナビ -->
  <div class="pagination">
    <?php
      if ($query->max_num_pages > 1) {
        $big = 999999999;
        $current_page = max(1, $paged);

      if ($current_page > 1) {
          echo '<a href="' . get_pagenum_link(1) . '" class="first"><<</a>';
      }

      echo paginate_links(array(
          'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
          'format' => 'page=%#%',
          'current' => $current_page,
          'end_size'  => 1,
          'mid_size' => 2,
          'prev_next' => true,
          'prev_text' => '<',
          'next_text' => '>',
          'type'      => 'list',
          'total' => $query->max_num_pages,
      ));

      if ($current_page < $query->max_num_pages) {
          echo '<a href="' . get_pagenum_link($query->max_num_pages) . '" class="last">>></a>';
        }
      }
    ?>
  </div>

<?php endif; ?>
