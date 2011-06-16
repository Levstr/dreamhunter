<?php
add_filter('rewrite_rules_array', 'lc_add_rewrite_rules');
add_filter('query_vars', 'lc_add_query_vars');
add_action('wp', 'lc_wp');
add_action('template_include', 'lc_template_include');
add_filter('login_url', 'lc_login_url_hook');


function lc_add_rewrite_rules($rules){
  $urls = array(lc_get_login_url() => 'index.php?lc_login_action=login',
    lc_get_registration_url() => 'index.php?lc_login_action=register',
    lc_get_registered_url() => 'index.php?lc_login_action=registered',
    lc_get_lost_password_url() => 'index.php?lc_login_action=retrievepassword',
    ls_get_confirm_password_change_url() => 'index.php?lc_login_action=confirm_password_change');
  foreach($urls as $url => $rewrite){
    $url = preg_replace("/^\//" ,'', $url) . "\/?$";
    $additional_rules[$url] = $rewrite;
  }
  return $additional_rules + $rules;
}

function lc_login_url_hook($url){
  return site_url(lc_get_login_url());
}

function lc_add_query_vars($qvars){
  $qvars[]='lc_login_action';
  return $qvars;
}




function lc_wp(&$wp){
  global $wp_query;
  if ($wp_query->query_vars['lc_login_action']){
    $wp_query->is_home = false;
  }
}

function lc_template_include($template){
  global $wp_query;
  $action = $wp_query->query_vars['lc_login_action'];
  if (!$action){
    return $template;
  }
  $template_file_path = trailingslashit(TEMPLATEPATH).$action.'.php';
  if (file_exists($template_file_path)){
    $func_name = 'lc_pre_' . $action;
    if (function_exists($func_name))
      $func_name();
    return $template_file_path;
  }
  wp_redirect(site_url("wp-login.php?action=$action"));
  
}