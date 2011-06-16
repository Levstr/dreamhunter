<?php
/*
Plugin Name: Login Style
Plugin URI: http://jetdog.biz/
Description: Allows you to style login and password retrieval pages easily.
Version: 1.0
Author: Nikolay Karev
Author URI: http://jetdog.biz
*/

$login_url = get_option('login_url', 'login');
add_action('setup_theme', 'ls_add_rewrite');

function ls_add_rewrite(){
  global $wp_rewrite;
//  var_dump($wp_rewrite);
  add_rewrite_rule('login', plugin);
}
