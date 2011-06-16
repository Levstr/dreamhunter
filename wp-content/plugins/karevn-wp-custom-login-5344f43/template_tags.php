<?php

/**
 * If the current page is login page?
 * @return bool|if the current page is login page
 */
function is_login(){
  global $wp_query;
  return $wp_query->query_vars['lc_login_action'] == 'login';
}
/**
 * If the current page is registration confirmation page.
 * @return bool|if the current page is registration confirmation page
 */
function is_registered(){
  global $wp_query;
  return $wp_query->query_vars['lc_login_action'] == 'registered';
}

/**
 * If the current page is registration page
 * @return bool|if the current page is registration page
 */
function is_register(){
  global $wp_query;
  return $wp_query->query_vars['lc_login_action'] == 'register';
}

function lc_get_lost_password_url(){
  return get_option('lost_password_url', '/user/lost-password');
}

function ls_lost_password_url(){
  echo lc_get_lost_password_url();
}

function ls_get_confirm_password_change_url(){
  return get_option('confirm_password_change_url', '/user/confirm-password-change');
}
function lc_confirm_password_change_url(){
  echo ls_get_confirm_password_change_url();
}

function lc_get_login_url($options = array()){
  $url = get_option('login_url', '/user/login');
  if (isset($options['redirect_to'])){
    $url = add_query_arg('redirect_to', $options['redirect_to'], $url);
  }
  return $url;
}

function lc_get_registered_url(){
  return get_option('registered_url', '/user/registered');
}

function lc_login_url($options = array()){
  echo lc_get_login_url();
}

function lc_redirect_to(){
  global $lc_redirect_to;
  echo esc_attr(stripslashes($lc_redirect_to));
}

function lc_get_registration_url(){
  return get_option('register_url', '/user/register');
}

function lc_register_url(){
  echo lc_get_registration_url();
}

function lc_user_login(){
  global $lc_user_login;
  echo esc_attr(stripslashes($lc_user_login));
}

function lc_user_email(){
  global $lc_user_email;
  echo esc_attr(stripslashes($lc_user_email));
}

function lc_interim_login(){
  return isset($_REQUEST['interim-login']);
}



function lc_login_hidden_fields(){
  global $lc_redirect_to;
  if ( lc_interim_login() ) { ?>
      <input type="hidden" name="interim-login" value="1" />
  <?php } else { ?>
  		<input type="hidden" name="redirect_to" value="<?php echo esc_attr($lc_redirect_to); ?>" />
  <?php } ?>
  <input type="hidden" name="testcookie" value="1" />
  <?php 
}


function lc_has_errors(){
  global $lc_errors;
  return $lc_errors != null && count($lc_errors) > 0;
}

function lc_formatted_errors(){
  global $lc_errors;
  if ($lc_errors == null)
    return;
  foreach($lc_errors->errors as $key => $error_array){
    if (!count($error_array)){
      continue;
    }
    echo '<ul class="errors">';
    foreach($error_array as $error){
      echo "<li class=\"error\">$error</li>";
    }
    echo '</ul>';
  }
}

function lc_has_messages(){
  global $lc_messages;
  return $lc_messages != null && count($lc_messages) > 0;
}

function lc_formatted_messages(){
  global $lc_messages;
  if(!lc_has_messages()) return;

  echo '<ul class="messages">';
  foreach($lc_messages as $lc_message) {
    echo "<li>$lc_message</li>";
  }
  echo '</ul>';
}

function lc_redirect_hidden_field(){
  $url = isset($_REQUEST['redirect_to']) && $_REQUEST['redirect_to'] ? $_REQUEST['redirect_to'] : get_bloginfo('url');
  ?><input type="hidden" name="redirect_to" value="<?php echo esc_attr($url) ?>"><?php
}

function lc_rememberme(){
  return $_REQUEST['rememberme'] != null;
}

function lc_rememberme_checkbox(){?>
  <label class="rememberme"><input type="checkbox" name="rememberme" value="1" <?php echo lc_rememberme() ? 'checked="checked"' : ''?>>remember me</label>
  <?php
}
