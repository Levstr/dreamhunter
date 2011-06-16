<?php get_header(); ?>

  <?php //get_sidebar('left'); ?>
  <?php //get_sidebar('right'); ?>
  <div id="content">
	
	
	
	
    <?php while(have_posts()):the_post()?>
	<div>
		<?php echo get_avatar($post->post_author, 80); ?>
		
		<?php the_author_link(); ?>
	</div>
	<div> 
		<h1><?php the_title()?></h1>
		<?php the_content()?>
	</div>
    <?php endwhile?>
	
	
	
	<div class="clear"></div>
  </div>

<?php get_footer(); ?>