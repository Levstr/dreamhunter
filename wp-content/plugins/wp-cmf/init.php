<?php
define('WP_CMF', 'wp-cmf');

$cmf_templates = array();
$tax_templates = array();

global $wpdb;
$wpdb->termmeta = $wpdb->prefix . "termmeta";

add_action('admin_enqueue_scripts', 'cmf_admin_init');

function cmf_admin_init(){
  global $editing, $hook_suffix;
  if ($editing && in_array($hook_suffix, array('post.php', 'post-new.php'))){
    add_thickbox();
  	if ( user_can_richedit() )
  		wp_enqueue_script('editor');
  	wp_enqueue_script('word-count');
  	wp_enqueue_script('media-upload');
    wp_enqueue_script('wp-cmf', plugin_file_url(WP_CMF, 'wp-cmf.js'), array('jquery'));
    wp_enqueue_style('wp-cmf', plugin_file_url(WP_CMF, 'wp-cmf.css'));
  }
  
}
?>