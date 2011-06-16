<?php
/*
* Plugin Name: WP Finance Events Logger
* Version: 0.8
* Plugin URI: 
* Description: Give API to save any finance events and calculate checkin/checkout stats
* Author: fotonstep
* Author URI: fotonstep@gmail.com
* Text Domain: 
*/
include_once('helpers/sql.php');
define('WPFEL_TABLE', $wpdb->prefix.'wpfel_events');
define('WPFEL_SALT', 'q02ycre9iuhqw30riwqyrbv9q3ifuhw3n0rdiewuhcfb');

register_activation_hook(__FILE__, "wpfel_install");
register_deactivation_hook(__FILE__, "wpfel_uninstall");

function wpfel_install() {
  update_option("wpfel_plugin_data", get_plugin_data(__FILE__));

  global $wpdb;
  $_table=WPFEL_TABLE;
  $sql = "CREATE TABLE IF NOT EXISTS `$_table` (
    `ID` int(11) NOT NULL AUTO_INCREMENT,
    `type` varchar(128) NOT NULL DEFAULT '',
    `value` float(15,5) NOT NULL,
    `crypt` blob NOT NULL,
    `author_id` int(11) NOT NULL,
    `source_type` varchar(128) NOT NULL DEFAULT '',
    `source_id` int(11) NOT NULL DEFAULT '0',
    `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `date_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `parent_id` int(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (`ID`),
    KEY `type` (`type`),
    KEY `author_id` (`author_id`),
    KEY `source_type` (`source_type`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

  require_once(ABSPATH.'wp-admin/includes/upgrade.php');
  dbDelta($sql);
}
function wpfel_uninstall() {
}

class wpfel{
  var $_table;
  var $_validate;
  var $_salt;

  function wpfel($salt=null) {
    $this->_table = WPFEL_TABLE;
    $this->_validate = array('type','value','author_id','source_type','source_id');
    $this->_salt = isset($salt)? $salt: WPFEL_SALT;
  }


  function add_event($values){
    unset($values["crypt"]);
    if(!isset($values['author_id']) || $values['author_id']<=0){
      global $current_user;
      get_currentuserinfo();
      $values['author_id']=$current_user->ID;
    }
    if(!$this->validate_values($values)) return false;

    global $wpdb;
    $wpdb->insert($this->_table, $values);
    if(!$wpdb->insert_id) return false;

    $event_id = $wpdb->insert_id;
    $wpdb->query($wpdb->prepare("UPDATE `$this->_table` SET `crypt`= ENCODE(%s, %s) WHERE `ID`=%d", array(json_encode($values), $this->_salt, $event_id)));
    
    return $event_id;
  }
  function validate_values($values, $standart=null){
    foreach((array)$this->_validate as $k)
      if(!isset($values[$k]) || (isset($standart[$k]) && $values[$k]!=$standart[$k]))
        return false;

    return true;
  }


  function get_event($id, $withcrypt=false){
    $id=(int)$id;

    global $wpdb;
    if($withcrypt)// && is_admin())
      $event = $wpdb->get_row("SELECT *, DECODE(`crypt`, '$this->_salt') as `cryptd` FROM `$this->_table` WHERE `ID`=$id", ARRAY_A);
    else
      $event = $wpdb->get_row("SELECT * FROM `$this->_table` WHERE `ID`=$id", ARRAY_A);
    return $event;
  }
  function get_events($values){
    if(!count($values)) return false;
    return select_rows($this->_table, $values);
  }
  function validate_event($id){
    $event = $this->get_event($id, true);
    $values = json_decode($event['cryptd'], true);
    if(!$this->validate_values($event, $values))
      return false;

    return true;
  }


  function calc_events($values){
    if(!count($values)) return false;

    global $wpdb;
    return $wpdb->get_var("SELECT SUM(`value`) as `sum` FROM `$this->_table` WHERE ".prepare_ids_to_check_string($values)." GROUP BY ".prepare_ids_to_group_string($values));
  }
}

global $wpfel;
$wpfel = new wpfel();
$GLOBALS['wpfel'] = $wpfel;
?>