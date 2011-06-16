<?php get_header(); ?>

<link rel='stylesheet' href='<?php echo get_bloginfo('template_url') ?>/css/extstyles.css' type='text/css' media='all' />


  <?php //get_sidebar('left'); ?>
  <?php //get_sidebar('right'); ?>
  <div id="content">
  
  <div id="d_hot">
	<div class="hot_l">
		<h2>Новости</h2>
		<div class="clear"></div>	
	
    <?php while(have_posts()):the_post();
		include("inc/_list-".$post->post_type.".php");
    endwhile?>
		
	<div class="page_navigation_arch">
		<span class="post_prev"><?php previous_posts_link('Предыдущая страница') ?></span>
		<span class="post_next"><?php next_posts_link('Следующая страница') ?></span>		
		<?php if(function_exists('wp_paginate')) {
			wp_paginate();
		} ?>
	</div>
	
	</div>
	<div class="hot_r">
		<?php get_sidebar('way'); ?>
	</div>
	
	<div class="clear"></div>
  </div>
	
	
	
	<div class="clear"></div>
  </div>

<?php get_footer(); ?>