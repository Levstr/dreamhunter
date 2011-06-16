<?php get_header(); ?>

  <?php //get_sidebar('left'); ?>
  <?php //get_sidebar('right'); ?>
  <div id="content">
	
	<div class="entire_cont">
	
    <?php while(have_posts()):the_post()?>
      <h1><?php the_title()?></h1>
      <?php the_content()?>
    <?php endwhile?>
	
	<?php 
		if(count($_POST)) {
			echo '<pre>';
			print_r($_POST);
			echo '</pre>';
		}
	?>
	
	</div>
	<div class="clear"></div>
  </div>

<?php get_footer(); ?>
