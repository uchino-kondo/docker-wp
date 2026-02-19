
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="format-detection" content="telephone=no">
<meta name="description" content="<?php bloginfo('description'); ?>">
<meta name="keywords" content="">
<link rel="canonical" href="<?php echo esc_url(home_url(add_query_arg(array(), ''))); ?>">
<meta property="og:url" content="<?php echo esc_url(home_url(add_query_arg(array(), ''))); ?>">
<meta property="og:type" content="<?php if(is_front_page()): ?><?php echo 'website';?><?php else: ?><?php echo 'article';?><?php endif; ?>">
<meta property="og:title" content="<?php bloginfo('name'); ?>">
<meta property="og:site_name" content="<?php bloginfo('name'); ?>">
<meta property="og:description" content="<?php bloginfo('description'); ?>">
<meta property="og:image" content="<?php echo esc_url(get_template_directory_uri()); ?>/img/common/ogp.png">
<meta name="twitter:card" content="summary_large_image">
<!-- icon -->
<link rel="icon" href="<?php echo esc_url(get_template_directory_uri()); ?>/favicon.ico">
<link rel="android-chrome" sizes="512x512" href="<?php echo esc_url(get_template_directory_uri()); ?>/img/common/android-chrome-512x512.png">
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url(get_template_directory_uri()); ?>/img/common/apple-touch-icon.png">
<!-- font -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preload" as="style" fetchpriority="high" href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap" media="print" onload="this.media='all'">
<!-- 404 to toppage -->
<?php if (is_404()) : ?>
  <meta http-equiv="refresh" content=" 3; url=<?php echo esc_url(home_url("/")); ?>">
<?php endif; ?>
