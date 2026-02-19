<?php get_header(); ?>

<main class="category-page">

<?php get_template_part('includes/breadcrumb'); ?>

<section class="category">
<div class="inner">

<h1 class="category__title"><?php single_cat_title(); ?></h1>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<article class="card">
  <a href="<?php the_permalink(); ?>">
    <div class="card__img">
      <?php if (has_post_thumbnail()) : ?>
        <?php the_post_thumbnail('post_thumbnails', array('alt' => esc_attr(get_the_title()))); ?>
      <?php endif; ?>
    </div>
    <div class="card__body">
      <time class="card__date" datetime="<?php the_time('Y-m-d'); ?>"><?php the_time('Y.m.d'); ?></time>
      <h2 class="card__title"><?php the_title(); ?></h2>
    </div>
  </a>
</article>
<!-- /.card -->

<?php endwhile; endif; ?>

<?php get_template_part('includes/pagenation'); ?>

</div>
<!-- /.inner -->
</section>

</main>

<?php get_footer(); ?>
