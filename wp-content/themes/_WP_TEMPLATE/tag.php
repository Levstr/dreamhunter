
<?php get_header(); ?>

  <?php //get_sidebar('left'); ?>
  <?php //get_sidebar('right'); ?>
  <div id="content">
  
	<div id="d_hot">
		<div class="hot_l">
			<h2><?php	printf( __( 'Тег: %s'), '<span>' . single_tag_title( '', false ) . '</span>' );
				?></h2>
			<div class="clear"></div>

<?php
/* Run the loop for the tag archive to output the posts
 * If you want to overload this in a child theme then include a file
 * called loop-tag.php and that will be used instead.
 */
 //get_template_part( 'loop', 'deal' );
?>

	 <?php 
	global $query_string;
	parse_str( $query_string, $args );
	$args['post_type'] = array('post','deal');
	query_posts( $args );
	
	while(have_posts()):the_post();
		include("inc/_list-".$post->post_type.".php");
	endwhile;
	
	?>
	
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
