<?php get_header(); ?>

<link rel='stylesheet' href='<?php echo get_bloginfo('template_url') ?>/css/extstyles.css' type='text/css' media='all' />


  <?php //get_sidebar('left'); ?>
  <?php //get_sidebar('right'); ?>
  <div id="content">
  
  <div id="d_hot">
	
	<div class="">
		<a href="<?php //$cat = get_the_category(); print_r($cat); $cat = $cat[0]; echo get_category_link($cat->cat_ID); ?>/deal/" class="href_hot">К списку горячих предложения</a>
	</div>
	<div class="hot_l">


    <?php while(have_posts()):the_post()?>
	<div class="deal_inf deal_single">
		<div class="deal_text">
		<?php
			$deal_sold = false;
			$terms_arr = wp_get_post_terms( $post->ID, $post->post_type.'_status' );
			//print_r($terms_arr);
			foreach($terms_arr as $term) {
				if($term->slug=='sold') {
					$deal_sold = true;
		?>
				<div class="sold">продано</div>
		<?php
				}
			}
		?>
		
		<div class="deal_cont">
			<h2><?php the_title()?></h2>
			<?php the_content()?>
			
			<div class="clear"></div>
			
			<?php if(!$deal_sold  && !get_currentuserin_wants_list($post->ID)) { ?>
			<a href=""	class="<?php echo buttonClassDisabled()?>g_send_request">Оставить заявку</a>
			<input type="hidden" value="<?php echo $post->ID ?>" class="send_request_v"/>
			<div class="alert"></div>
			<?php } ?>
			
			<div class="g_people">
				<?php echo get_wants_list($post->ID); ?> <!--Уже хотят ехать: <a href="#">Chainik</a>, <a href="#">chudishko</a>, <a href="#">cheaptrip lab</a>,<a href="#">Masha</a>, <a href="#">Sasha</a>, <a href="#">Petya</a>-->
			</div>
			
			<div class="clear"></div>
			
			<div class="tag_list"> 
			<?php echo get_the_tag_list('',' ','');?>
			</div>
		</div>
		
		<div class="bot_inf">
			<span><?php the_time('d F Y,H:i')?></span>
			
			<span class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="none" data-yashareLink="<?php the_permalink()?>" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki"></span> 
			
			<span class="g_avatar">
				<?php echo get_avatar($post->post_author, 24); ?>
				
				<span class="avatar_href">
					<?php the_author_link(); ?>
				</span>
			</span>
			<div class="clear"></div> 
		</div>
		<div class="clear"></div> 
		</div>
		<?php 
			$withcomments = "1";
			$per_page_comments = 0;
			$show_reply  = true;
			comments_template();
			//if ( have_comments() ) {
			//wp_list_comments(array('per_page' =>3, 'callback' => 'dreamhunter_comment') ); 
			//}
			
			
			if(isset($_SERVER['QUERY_STRING']) &&  strstr($_SERVER['QUERY_STRING'], 'replytocom')) {
			?>
				<script>
				$('.respond_form').show();
				</script>
			<?php
			}
		?> 
		<div class="clear"></div>
		<div class="comment_buts">
			<div class="com_but com_add">
				<a href="">Оставить комментарий</a>
			</div>
			
			<div class="clear"></div>
		</div>
		
		
		<?php // comment_form( array(), $post->ID ); ?> 
		
	</div>
	<div class="clear"></div>
    <?php endwhile?>
	
	</div>
	<div class="hot_r">
		<div class="l_add_request">
			<div class="top_link"><?php wp_nav_menu(array('echo' => true,'container' => '','menu' => 'nav-my-request','menu_id' => 'nav-my-request','depth' => 1))?></div>
			<a href="" class="<?php echo buttonClassDisabled()?>but_add_request" id="but_add_request">Добавить свой запрос</a>
			<div class="req_text">
				если не нашли подходящее<br>
				предложение в ленте
			</div>
			
			<!-- -->
			<div class="form_add_request" id="form_add_request">
				<div class="alert"></div>
				<label>Заголовок</label>
				<input type="text" name="request_title" class="inp_text"/>
				
				<label>Текст</label>
				<textarea name="request_text"></textarea>
				
				<input type="button" value="Отправить запрос" class="inp_but submit" />
				<input type="button" value="Скрыть форму" class="inp_but form_close" />
			</div>
			<!-- -->
			<div class="add_request_bot"></div>
		</div>
		
		<?php get_sidebar('way'); ?>
	</div>
	
	<div class="clear"></div>
  </div>
	
	
	
	<div class="clear"></div>
  </div>

<?php get_footer(); ?>