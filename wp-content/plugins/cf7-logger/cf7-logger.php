<?php
/*
* Plugin Name: Contact Form 7 Logger
* Version: 0.1
* Plugin URI: http://ncsu.com
* Description: Save logs for contact forms
* Author: fotonstep
* Author URI: fotonstep@gmail.com
* Text Domain: 
*/
require_once(dirname(__FILE__) . "/model.php");
require_once(dirname(__FILE__) . "/logger.php");
require_once(dirname(__FILE__) . "/export.php");
require_once(dirname(__FILE__) . "/admin.php");


add_action('admin_init', 'cf7_logger_admin_init');
function cf7_logger_admin_init(){
  if($_GET['cf7_log_export'] == '1' && is_admin() && current_user_can('manage_options')) 
    cf7_logger_log_export($_REQUEST['post_type'], cf7_get_filter_from_request());
}

$cf_logging_hooks = array();
$cf_log_viewers = array();

add_action('admin_menu', 'cf7_logger_register_log_viewer');
if (isset($_REQUEST['page']) && preg_match('/cf7_logger_posts_menu_/', $_REQUEST['page']))
  add_action('admin_init', 'cf7_logger_action_admin_init');
add_action('wpcf7_mail_sent', 'cf7_logger_mail_sent');
