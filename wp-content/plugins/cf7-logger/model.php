<?php
function cf7_logger_get_records($post_type, $filter = array()) {
  $args = array(
    'post_type' => $post_type,
    'orderby' => 'date',
    'order' => 'ASC',
    'numberposts' => -1,
    'posts_per_page' => -1
    );
  $filter = array_merge($args, $filter);
  $query = new WP_Query();
  
  if (!empty($filter['meta_and'])){
    $query->set('meta_and', $filter['meta_and']);
  }
  $posts = $query->query($filter);
  foreach($posts as $post) {
    $post->meta = get_post_custom($post->ID);
    $post->meta['post_date'] = array($post->post_date);
    $post->meta['ID'] = array($post->ID);
  }
  return $posts;
}



function cf7_logger_select_log_meta_keys($post_type){
  global $wpdb;
  $sql = "SELECT DISTINCT pm.`meta_key` FROM `wp_postmeta` pm LEFT JOIN `wp_posts` p ON pm.`post_id`=p.`ID`";
  $sql .= " WHERE p.`post_type`='$post_type' AND pm.`meta_key` NOT LIKE '!_%' ESCAPE '!'";
  $sql .= " ORDER BY `meta_key` DESC";
  return $wpdb->get_results($sql, ARRAY_A);
}

function cf7_logger_select_log_meta_values($post_type, $key){
  global $wpdb;
  $sql = "SELECT DISTINCT pm.`meta_value` FROM `wp_postmeta` pm LEFT JOIN `wp_posts` p ON pm.`post_id`=p.`ID`";
  $sql .= " WHERE p.`post_type`='$post_type' AND pm.`meta_key`='$key'";
  $sql .= " ORDER BY `meta_value` DESC";
  return $wpdb->get_results($sql, ARRAY_A);
}

function cf7_logger_get_post_meta_superset($posts){
  $result = array();
  foreach($posts as $post){
    foreach($post->meta as $key => $value){
      if (!preg_match('/^_/', $key)) $result[$key] = true;
    }
  }
  $result['post_date'] = true;
  return array_keys($result);
}

?>