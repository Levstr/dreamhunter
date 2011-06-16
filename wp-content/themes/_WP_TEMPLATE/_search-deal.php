<?php
?>

	<div class="d_letter">
				<a href="http://www.rss2email.ru/default.asp?rss=http://dreamhunter.ru/rss2" target="_blank">Подписаться</a>
			</div>
			
			<div id="d_vk_m">
				<?php echo getlinks(9); ?> 
				<!--<a href="#">Читать предложения вконтакте</a>-->
			</div>
			
			<div class="clear"></div>
			<div class="d_form_search">
				<form id="search" name="searchform" method="get" action="<?php bloginfo("url"); ?>">
				<div class="form_search_inp">
					<input type="text" name="s" value="<?php echo $_GET['s'] ?>"/>
				</div>
				
				<input type="hidden" name="post_type" value="deal" />
				<input type="submit" class="but_search" value=""/>
				</form>
				<div class="clear"></div>
				
				<div class="search_exmp">Например: Spa-тур в Таиланд</div>
				
			</div>
