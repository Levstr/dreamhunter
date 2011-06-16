<?php get_header(); ?>

  <?php //get_sidebar('left'); ?>
  <?php //get_sidebar('right'); ?>
  <div id="content">
	
	
	<?php
	/*
    <?php while(have_posts()):the_post()?>
      <h1><?php the_title()?></h1>
      <?php the_content()?>
    <?php endwhile?>
	*/
	
	?>
	
	
	<?php
		echo '--------------------';
		// корзина
		global $current_user;
		get_currentuserinfo();
		print_r($current_user);
		
		if(count($_POST)) {
			if(send_admin_cart_inf()) {
				echo 'Ваш запрос отправлен.';
			}
		}
	
		if(!$current_user->ID) {
			echo 'Вы не авторизованы для просмотра этой страницы';
		} else {
			// список туров юзера
			
			/*
			wp_user_cart_tours (
				user_id
				cart_id
				status (0 - открыта, 1 - отправлено на обработку админу)
			)
			
			wp_user_tours (
				cart_id
				tour_id
			)
			*/
			
			if(get_last_cart_tours()) {
			?>
				<form method="post" action="">
					<input type="hidden" name="cart_send" value="cart_send"/>
					<input type="submit" value="Отправить запрос" />
				</form>
			<?php
			}
		}
	?>

	
	<div class="clear"></div>
  </div>

<?php get_footer(); ?>