<?php get_header(); ?>

<main class="single-page">

<?php get_template_part('includes/breadcrumb'); ?>

<article class="single-content">
<div class="inner">

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

  <div class="single-content__header">
    <time class="single-content__date" datetime="<?php the_time('Y-m-d'); ?>"><?php the_time('Y.m.d'); ?></time>
    <h1 class="single-content__title"><?php the_title(); ?></h1>
  </div>

  <div class="single-content__body">
    <?php the_content(); ?>
  </div>

<?php endwhile; endif; ?>

</div>
<!-- /.inner -->
</article>

<?php get_template_part('includes/pagenation'); ?>

</main>

<?php get_footer(); ?>
