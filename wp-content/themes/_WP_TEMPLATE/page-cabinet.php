<?php
global $current_user;
get_currentuserinfo();
if(!$current_user->ID) {
	header('Location:'.lc_get_login_url());
}

// отправка запроса на выполнение
$cart_status_review = set_cart_status_exec();
?>
<?php get_header(); ?>

  <?php //get_sidebar('left'); ?>
  <?php //get_sidebar('right'); ?>
  <div id="content">
  <div class="entire_cont cabinet_content">

  <style>
  </style>
	
	<?php
	
	//Нужно сделать вывод заявок/запросов/заказов пользователя, с фильтрацией по категориям (отложенные/активные/закрытые/отмененные/и тп), с комментариями и ссылками на предложения, с подробной информацией по турам. 
	
	/*
	 - список заявок юзера на предложения
	 - список заявок юзера просто 
	+ - список туров юзера
	 
	 разделить это все по категориям
	 + комментарии
	 + для туров  - описание
	*/
		// список заявок юзера
		
	
		if(!$current_user->ID) {
			//header('Location:'.lc_get_login_url());
			echo 'Вы не авторизованы для просмотра этой страницы';
		} else {
		
			if($cart_status_review) {
				?> 
				<div>Ваш запрос отправлен на выполнение!</div>
				<?php
			}
		
			// заказы (корзина)-------------------
			 $terms_cart = get_terms("cart_status");
			 //echo '<pre>';
			// print_r($terms_cart);
			 //echo '</pre>';
			 $cart_ids = '';
			 $cart_list_text = '';
			 if(count($terms_cart)) {
				?>
				<h2>Ваши заказы</h2>
				<?php
				foreach($terms_cart as $term) {
					
					$cart_list_text = '<h3>'.$term->name.'</h3>';
					$cart_post_list = query_posts(array('post_type'=>'cart','author'=>$current_user->ID, 'cart_status'=>$term->slug, 'order'  => 'ASC'));
					
					if(count($cart_post_list)) {
						foreach($cart_post_list as $post) {
							$cart_ids .= ','.$post->ID;
							
							// список туров ------
							$tour_f = get_post_meta($post->ID,'cart_TOURID');
							if(count($tour_f)) {
								$cart_list_text .= '<table class="cart_table">';
								foreach ($tour_f as $t) {
									$tour_text = get_post_meta($post->ID,'cart_TOURID_'.$t,true);
									
									$cart_list_text .= '<tr>'.$tour_text.'</tr>';
								}
								echo $cart_list_text .= '</table>';
								
								//-----комментарии----------
								?>
								<div class="comment_cabinet">
								<div class="comment_buts">
									<div class="com_but com_add">
										<a href="">Оставить комментарий</a>
									</div>
								
									<div class="clear"></div>
								</div>
								<?php
								$withcomments = "1";
								$per_page_comments = 10;
								$show_reply = false;
								comments_template();
								?>
								</div>
								<?php
								//---/--комментарии----------
							}
							//-------
						}
					}
					//echo '<pre>';
					//print_r($cart_post_list);
					//echo '</pre>';
				}
			 }
			 
			if(strlen($cart_ids)) {
				$cart_ids = substr($cart_ids,1);
			}
				
				// открытая корзина - не принадлежит ни одной категории
				$cart_post_list = get_posts(array('post_type'=>'cart','author'=>$current_user->ID,'exclude'=>$cart_ids, 'order'  => 'ASC'));
				if(count($cart_post_list)) {
					?>
					<h3>Открытая корзина</h3>
					<?php
						foreach($cart_post_list as $post) {
							// список туров ------
							$tour_f = get_post_meta($post->ID,'cart_TOURID');
							if(count($tour_f)) {
								?>
								<table class="cart_table">
								<?php
								foreach ($tour_f as $t) {
									$tour_text = get_post_meta($post->ID,'cart_TOURID_'.$t,true);
									?>
									<tr><?php echo $tour_text; ?></tr>
									<?php
								}
								?>
								</table>
								<form method="post">
								<input type="hidden" name="cart_open" value="<?php echo $post->ID ?>"/>
								<input type="submit" value="Отправить на выполнение" />
								</form>
								<?php
								//-----комментарии----------
								?>
								<div class="comment_cabinet">
								<div class="comment_buts">
									<div class="com_but com_add">
										<a href="">Оставить комментарий</a>
									</div>
								
									<div class="clear"></div>
								</div>
								<?php
								$withcomments = "1";
								$per_page_comments = 10;
								$show_reply = false;
								comments_template();
								?>
								</div>
								<?php
								//---/--комментарии----------
							}
						//-------
					}
					
				}
				
			 
			 //echo $cart_list_text;
			//------------------------------------
			//===========================================================================
			// заявки на предложения и просто-------------------
			 $terms_cart = get_terms("request_status");
			 $cart_ids = '';
			 $cart_list_text = '';
			 if(count($terms_cart)) {
				?>
				<h2>Ваши заявки</h2>
				<?php
				foreach($terms_cart as $term) {
					
					echo $cart_list_text = '<h3>'.$term->name.'</h3>';
					$cart_post_list = query_posts(array('post_type'=>'request','author'=>$current_user->ID, 'request_status'=>$term->slug, 'order'  => 'ASC'));
					
					if(count($cart_post_list)) {
						foreach($cart_post_list as $post) {
							$cart_ids .= ','.$post->ID;
							
							//  ------
							$deal_id = get_post_meta($post->ID,'deal_DEALID',true);
							if($deal_id) {
								$p_title = '<a href="'.get_permalink($deal_id).'">'.$post->post_title.'</a>';
							} else {
								$p_title = $post->post_title;
							}
															
								//-----комментарии----------
								?>
								<h5><?php echo $p_title ?></h5>
								<?php echo $post->post_content ?>
								
								<div class="comment_cabinet">
								<div class="comment_buts">
									<div class="com_but com_add">
										<a href="">Оставить комментарий</a>
									</div>
								
									<div class="clear"></div>
								</div>
								<?php
								$withcomments = "1";
								$per_page_comments = 10;
								$show_reply = false;
								comments_template();
								?>
								</div>
								<?php
								//---/--комментарии----------
							
							//-------
						}
					}
					//echo '<pre>';
					//print_r($cart_post_list);
					//echo '</pre>';
				}
			 }
			 
			if(strlen($cart_ids)) {
				$cart_ids = substr($cart_ids,1);
			}
				
				// открытая корзина - не принадлежит ни одной категории
				$cart_post_list = get_posts(array('post_type'=>'request','author'=>$current_user->ID,'exclude'=>$cart_ids, 'order'  => 'ASC'));
				if(count($cart_post_list)) {
					?>
					<h3>Открытые заявки</h3>
					<?php
						foreach($cart_post_list as $post) {
							// список туров ------
							$deal_id = get_post_meta($post->ID,'deal_DEALID',true);
							if($deal_id) {
								$p_title = '<a href="'.get_permalink($deal_id).'">'.$post->post_title.'</a>';
							} else {
								$p_title = $post->post_title;
							}
															
								//-----комментарии----------
								?>
								<h5><?php echo $p_title ?></h5>
								<?php echo $post->post_content ?>
								
								<div class="comment_cabinet">
								<div class="comment_buts">
									<div class="com_but com_add">
										<a href="">Оставить комментарий</a>
									</div>
								
									<div class="clear"></div>
								</div>
								<?php
								$withcomments = "1";
								$per_page_comments = 10;
								$show_reply = false;
								comments_template();
								?>
								</div>
								<?php
								//---/--комментарии----------
							
						//-------
					}
				}
				
			 
			//===========================================================================
			
			// картинки
			?>
			<h2>Фотоальбом</h2>
			<?php
			echo get_user_foto();

		}
	?>

	
	<div class="clear"></div>
	</div>
  </div>

<?php get_footer(); ?>