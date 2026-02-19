<?php get_header(); ?>

<main class="page-<?php echo esc_attr(get_post_field('post_name')); ?>">

<?php get_template_part('includes/breadcrumb'); ?>

<div class="inner">

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

  <h1 class="page__title"><?php the_title(); ?></h1>

  <div class="page__content">
    <?php the_content(); ?>
  </div>

<?php endwhile; endif; ?>

</div>
<!-- /.inner -->

</main>

<?php get_footer(); ?>
