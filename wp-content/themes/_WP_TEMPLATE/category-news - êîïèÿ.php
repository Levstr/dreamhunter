<?php get_header(); ?>

<link rel='stylesheet' href='<?php echo get_bloginfo('template_url') ?>/css/extstyles.css' type='text/css' media='all' />


  <?php //get_sidebar('left'); ?>
  <?php //get_sidebar('right'); ?>
  <div id="content">
  
  <div id="d_hot">
	<div class="hot_l">
		<h2>Новости</h2>
		<div class="clear"></div>	
	
    <?php while(have_posts()):the_post()?>
	<div class="avatar_l">
		<div class="avatar_shadow"></div>
		<div class="avatar_img">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<?php echo get_avatar($post->post_author, 80); ?>
					</td>
				</tr>
			</table>
		</div>
		
		<div class="avatar_href">
			<?php the_author_link(); ?>
		</div>
	</div>
	<div class="deal_inf">
		<div class="deal_text">
		<div class="deal_cont">
			<h2><a href="<?php the_permalink() ?>"><?php the_title()?></a></h2>
			<?php the_content()?>
			
			<div class="clear"></div>
		
			<div class="tag_list"> 
			<?php echo get_the_tag_list('',' ','');?>
			</div>
		</div>
		
		<div class="bot_inf">
			<span><?php the_time('d F Y, H:i')?></span>
			
			<span class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="none" data-yashareLink="<?php the_permalink()?>" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki"></span> 
			<div class="clear"></div> 
		</div>
		<div class="clear"></div> 
		</div>
		
		<?php 
			$withcomments = "1";
			$per_page_comments = 3;
			$show_reply = false;
			comments_template();
			//if ( have_comments() ) {
			//wp_list_comments(array('per_page' =>3, 'callback' => 'dreamhunter_comment') ); 
			//}
		?> 
		<div class="clear"></div>
		<div class="comment_buts">
			<div class="com_but com_add">
				<a href="">Оставить комментарий</a>
			</div>
			<div class="com_but com_show">
				<a href="<?php the_permalink() ?>">Показать все комментарии</a>
			</div>
			
			<div class="clear"></div>
		</div>
		
		
		<?php // comment_form( array(), $post->ID ); ?> 
		
	</div>
	<div class="clear"></div>
    <?php endwhile?>
	
	
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