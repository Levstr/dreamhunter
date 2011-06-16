<?php
function cmf_activation_hook(){
  global $wpdb;
  if($wpdb->get_var("SHOW TABLES LIKE '$wpdb->termmeta'") != $wpdb->termmeta) {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $sql = "CREATE TABLE `$wpdb->termmeta` (
      `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      `term_id` bigint(20) unsigned NOT NULL DEFAULT '0',
      `meta_key` varchar(255) DEFAULT NULL,
      `meta_value` longtext,
      PRIMARY KEY (`meta_id`),
      KEY `term_id` (`term_id`),
      KEY `meta_key` (`meta_key`)
    );";
    dbDelta($sql);
}
