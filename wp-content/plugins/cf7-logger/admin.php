<?php

function cf7_get_filter_from_request(){
  $filter = array();
  $filter['search'] = $_REQUEST['s'];
  $filter['date_from'] = isset($_REQUEST['date_from']) ? $_REQUEST['date_from'] : date('Y-m-d');
  $filter['date_to'] = isset($_REQUEST['date_to']) ? $_REQUEST['date_to'] : date('Y-m-d');
  $filter['post_status'] = $_REQUEST['post_status'];
  $filter['meta_and'] = array();
  $filter['title_contains'] = stripslashes($_REQUEST['title_contains']);
  foreach(cf7_logger_select_log_meta_keys($_REQUEST['post_type']) as $meta_key)
    if(isset($_REQUEST["meta_" . $meta_key['meta_key']]))
      $filter['meta_and'][$meta_key['meta_key']] = $_REQUEST["meta_".$meta_key['meta_key']];
  return $filter;
}



function cf7_logger_log_page() {
  global $cf_log_viewers;
  $post_type = $_REQUEST['post_type'];

  $posts = cf7_logger_get_records($post_type, cf7_get_filter_from_request());
  $meta_keys = cf7_logger_get_post_meta_superset($posts);
  $options = $cf_log_viewers[$post_type];
  if (!isset($options['title'])) $options['title'] = ucfirst($post_type) . " Log";
  include 'contact-form-7-admin.php';
}

function cf7_logger_register_log_viewer($post_type, $options = array()) {
  global $cf_log_viewers;
  $page = add_submenu_page("edit.php?post_type=$post_type", $options['title'], 
    'Log', 'export', "cf7_logger_posts_menu_$post_type", 
    'cf7_logger_log_page');
  $cf_log_viewers[$post_type] = $options;
}

function cf7_logger_action_admin_init(){
  wp_enqueue_style("jquery-datepicker", plugins_url('/css/jquery-ui-1.8.4.custom.css',__FILE__));
  wp_enqueue_script("jquery-ui", plugins_url("/js/jquery-ui-1.8.4.custom.min.js",__FILE__), array('jquery'));
}


