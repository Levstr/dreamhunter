    <?php lc_formatted_errors() ?>
    <?php if(isset($_GET['registered'])) { ?>
      <div class="notice">Please check your email for a new password. </div>
    <?php } ?>
	
	<div class="d_reg_href"><a href="<?php lc_register_url() ?>">Регистрация</a></div>
	<div class="clear"></div>
    <form action="<?php lc_login_url() ?>" method="post" id="form-login">
      <!--<label class="block">Login</label>-->
	  <div class="inp_style">
      <input type="text" class="text" name="log" value="<?php esc_attr($lc_user_login)?>">
	  </div>
 <!--     <label class="block">Password</label>-->
	<div class="inp_style">
      <input type="password" name="pwd" value="" class="text" >
	 </div>
      
        <div class="float-left href_passlost"><a href="<?php ls_lost_password_url() ?>">вспомнить пароль</a></div>
        <?php //lc_rememberme_checkbox() ?>
  
      <?php lc_login_hidden_fields() ?>
      <input type="hidden" name="redirect_to" value="<?php bloginfo('url')?>/cabinet">
      <input type="submit" value="Войти" class="button">
     
    </form>