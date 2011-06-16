<?php get_header(); ?>

  <?php //get_sidebar('left'); ?>
  <?php //get_sidebar('right'); ?>
 
	<?php 
    /*<ul class="aviaslider" id="home-gallery"><?php get_home_gallery_images("aviaslider") ?></ul>*/
	?>



	
	  <div id="content">
	  
	  <?php get_template_part( '_search', 'tour' ); ?>
	 <!-- <div class="clear"></div>
	  </div>-->
	
	
	
	<div class="d_foto_line">
		<div class="foto_line">
			<div class="foto_cont">
				<?php 
					echo get_user_last_foto();
				?>
			</div>
			<div class="foto_line_l"></div>
			<div class="foto_line_r"></div>
		</div>
		
		<div class="foto_line_inf">
			<h2>Фотоконкурс</h2>
			Получи главный приз или гарантированную
скидку на путешествие

			
			
			<?php 
				$current_user = wp_get_current_user();
				if($current_user->ID) {
					if (function_exists('post_from_site')) {post_from_site();} 
				} 
			?>
			<a href="#" class="<?php echo buttonClassDisabled(); ?>but_foto_upload" id="but_foto_upload">Загрузить свое фото</a>
			
			<!--<div id="foto_upload_form">
				<input type="file" name="foto_upl" id="foto_upl"/>
				<input type="button" id="but_foto_upl" value="Загрузить файл"/>
			</div>-->
			
		</div>
		
		<div class="clear"></div>
	</div>

	
	
	
	<div id="d_hot">
		
		<div class="hot_l">
			<h2>Горячие предложения</h2>
			
			<?php get_template_part( '_search', 'deal' ); ?>
			
			 <?php while(have_posts()):the_post()?>
		 
			<div class="d_hot_list">
				<?php
					if ( function_exists ("has_post_thumbnail") && has_post_thumbnail () ) { 
						the_post_thumbnail (array (221,147), array ("class" => "")); 
					}
				?>
				<!--<img src="images/test_image.jpg" width="221" height="147"/>-->
				
				<div class="hot_text">
					<h3><a href="<?php the_permalink() ?>"><?php the_title()?></a></h3>
					
					<?php the_content()?>
				</div>
				<div class="clear"></div>
			</div>
			
			<?php endwhile?>
			
			<a href="/cats/news/">смотреть все новости</a>
		</div>
		
		<div class="hot_r">
			
			 <?php get_sidebar('way'); ?>
			<!--			
			<div class="hot_tags">
				<h3>Летим из:</h3>
				
				<a href="#">Все города</a>
				<a href="#">Москва</a>
				<a href="#">Санкт-Петербург</a>
				<a href="#">Екатеринбург</a>
				<a href="#">Самара</a>
				
				<div class="clear"></div>
			</div>
			
			<div class="hot_tags">
				<h3>Страны</h3>
				
				<a href="#">Турция</a>
				<a href="#">Таиланд</a>
				<a href="#">Франция</a>
				<a href="#">Египет</a>
				
				
				<div class="clear"></div>
			</div>
			-->
			
		</div>
		
		<div class="clear"></div>
		
	</div>

	<div class="clear"></div>
  </div>
  
<?php get_footer(); ?>
