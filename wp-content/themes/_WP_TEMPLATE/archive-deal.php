<?php get_header(); ?>

<link rel='stylesheet' href='<?php echo get_bloginfo('template_url') ?>/css/extstyles.css' type='text/css' media='all' />


  <?php //get_sidebar('left'); ?>
  <?php //get_sidebar('right'); ?>
  <div id="content">
  
  <div id="d_hot">
	<div class="hot_l">
		<h2>Горячие предложения</h2>
			
			<?php get_template_part( '_search', 'deal' ); ?>
	
    <?php while(have_posts()):the_post();
		include("inc/_list-".$post->post_type.".php");
    endwhile?>
	
	
	<div class="page_navigation_arch">
		<span class="post_prev"><?php previous_posts_link('Предыдущая страница') ?></span>
		<span class="post_next"><?php next_posts_link('Следующая страница') ?></span>		
		<?php if(function_exists('wp_paginate')) {
			wp_paginate();
		} ?>
	</div>
	
	</div>
	<div class="hot_r">
		<div class="l_add_request">
			<div class="top_link"><?php wp_nav_menu(array('echo' => true,'container' => '','menu' => 'nav-my-request','menu_id' => 'nav-my-request','depth' => 1))?></div>
			<a href="" class="<?php echo buttonClassDisabled() ?>but_add_request" id="but_add_request">Добавить свой запрос</a>
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
		
		<?php
			foreach(get_posts(array('post_type'=>'deal','showposts'=>3)) as $post) {
				?>
				<div class="r_postlist">
				<h4><a href="<?php the_permalink()?>"><?php echo $post->post_title?> </a></h4>
				<?php echo $post->post_excerpt; ///post_excerpt; ?>
				</div>
				
				<?php
			}
		?>
		<?php get_sidebar('way'); ?>
	</div>
	
	<div class="clear"></div>
  </div>
	
	
	
	<div class="clear"></div>
  </div>

<?php get_footer(); ?>