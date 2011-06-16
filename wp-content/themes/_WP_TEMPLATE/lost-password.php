<?php get_header(); ?>

  <?php //get_sidebar('left'); ?>
  <?php //get_sidebar('right'); ?>
  <div id="content">
  
  <form name="lostpasswordform" id="lostpasswordform" action="<?php echo site_url('wp-login.php?action=lostpassword', 'login_post') ?>" method="post">
	<p>
		<label><?php _e('Username or E-mail:') ?><br />
		<input type="text" name="user_login" id="user_login" class="input" value="<?php echo esc_attr($user_login); ?>" size="20" tabindex="10" /></label>
	</p>
<?php do_action('lostpassword_form'); ?>
	<input type="hidden" name="redirect_to" value="<?php lc_login_url() ?>" />
	<p class="submit"><input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="<?php esc_attr_e('Get New Password'); ?>" tabindex="100" /></p>
</form>

	

  </div>

<?php get_footer(); ?>
