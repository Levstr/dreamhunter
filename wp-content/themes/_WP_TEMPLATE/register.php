<?php get_header(); ?>

  <?php //get_sidebar('left'); ?>
  <?php //get_sidebar('right'); ?>
  
  <?php
	
  ?>
  
  
  <div id="content">
  
  <div class="entire_cont">
	
	<form name="registerform" id="registerform" action="<?php echo site_url('wp-login.php?action=register', 'login_post') ?>" method="post">
	<p>
		<label><?php _e('Username') ?>
		<input type="text" name="user_login" id="user_login" class="input" value="<?php echo esc_attr(stripslashes($user_login)); ?>" size="20" tabindex="10" /></label>
	</p>
	<p>
		<label><?php _e('E-mail') ?>
		<input type="text" name="user_email" id="user_email" class="input" value="<?php echo esc_attr(stripslashes($user_email)); ?>" size="25" tabindex="20" /></label>
	</p>
<?php do_action('register_form'); ?>
	<p id="reg_passmail"><?php _e('A password will be e-mailed to you.') ?></p>
	<br class="clear" />
	<input type="hidden" name="redirect_to" value="<?php echo lc_get_registered_url(); ?>" />
	<p class="submit"><input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="<?php esc_attr_e('Register'); ?>" tabindex="100" /></p>
</form>
	

  </div>
  </div>

<?php get_footer(); ?>
