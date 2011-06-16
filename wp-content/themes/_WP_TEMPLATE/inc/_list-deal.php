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
		<?php
			$deal_sold = false;
			$terms_arr = wp_get_post_terms( $post->ID, $post->post_type.'_status' );
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
			<h2><a href="<?php the_permalink() ?>"><?php the_title()?></a></h2>
			<?php the_content()?>
			
			<div class="clear"></div>
			
			<?php if(!$deal_sold && !get_currentuserin_wants_list($post->ID)) {	?>
				<a href=""	class="<?php echo buttonClassDisabled() ?>g_send_request">Оставить заявку</a>
				<input type="hidden" value="<?php echo $post->ID ?>" class="send_request_v"/>
				<div class="alert"></div>
			<?php } ?>
			
			<div class="g_people">
				<?php echo get_wants_list($post->ID); ?>
			</div>
			
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
	</div>
	<div class="clear"></div>