<?php get_header(); ?>
  <div id="content">
  
  <div class="entire_cont">
<h2>Вспомнить пароль</h2>
<?php //lc_formatted_errors() ?>
<form name="lostpasswordform" id="lostpasswordform" action="<?php lc_get_lost_password_url() ?>" method="post">
	<p>
		<label class="block"><?php _e('Username or E-mail:') ?>
			<br>
			<input type="text" name="user_login" id="user_login" class="text" value="<?php lc_user_login(); ?>" size="20" tabindex="10" />
		</label>
	</p>
	<?php do_action('lostpassword_form'); ?>
	<input type="hidden" name="redirect_to" value="<?php lc_redirect_to() ?>" />
	<input type="submit" name="wp-submit" id="wp-submit" class="button" value="<?php esc_attr_e('Get New Password'); ?>" tabindex="100" />
</form>
</div>
</div>
<?php get_footer(); ?>
