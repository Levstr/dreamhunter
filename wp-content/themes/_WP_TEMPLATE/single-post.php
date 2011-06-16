<?php get_header(); ?>

<link rel='stylesheet' href='<?php echo get_bloginfo('template_url') ?>/css/extstyles.css' type='text/css' media='all' />


  <?php //get_sidebar('left'); ?>
  <?php //get_sidebar('right'); ?>
  <div id="content">
  
  <div id="d_hot">
	
	<div class="">
		<a href="/cats/news/" class="href_hot">К списку новостей</a>
	</div>
	<div class="hot_l">


    <?php while(have_posts()):the_post()?>
	<div class="deal_inf deal_single">
		<div class="deal_text">
		
		<div class="deal_cont">
			<h2><?php the_title()?></h2>
			
			<?php
				if ( function_exists ("has_post_thumbnail") && has_post_thumbnail () ) { 
					the_post_thumbnail (array (221,147), array ("class" => "")); 
				}
			?>
			<?php the_content()?>
			
			
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
		
		<?php get_sidebar('way'); ?>
	</div>
	
	<div class="clear"></div>
  </div>
	
	
	
	<div class="clear"></div>
  </div>

<?php get_footer(); ?>