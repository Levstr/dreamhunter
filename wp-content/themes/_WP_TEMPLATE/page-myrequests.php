<?php get_header(); ?>

  <?php //get_sidebar('left'); ?>
  <?php //get_sidebar('right'); ?>
  <div id="content">

	
	<?php
		// список заявок юзера
		global $current_user;
		//get_currentuserinfo();
	
		if(!$current_user->ID) {
			echo 'Вы не авторизованы для просмотра этой страницы';
		} else {
			foreach(get_posts(array('post_type'=>'request','author'=>$current_user->ID)) as $post) {
				?>
				<div class="r_postlist">
				<h4><?php echo $post->post_title?></h4>
				<?php echo $post->post_content; ///post_excerpt; ?>
				</div>
				<?php 
			}
		

		}
	?>

	
	<div class="clear"></div>
  </div>

<?php get_footer(); ?>