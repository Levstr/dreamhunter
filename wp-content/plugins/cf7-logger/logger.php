<?php
class cf7_Logger {
  var $_form_id;
  var $_post_type;
  var $_options = array();

  function cf7_Logger($form_id, $post_type, $options = array()){
    $this->_form_id = $form_id;
    $this->_post_type = $post_type;
    $this->_options = $options;
  }

  function addEntry(&$data){
    global $current_user;
    wp_get_current_user();

    $post = new StdClass();
    $post->post_title = $this->formatPostTextFromData($data, $this->_options["title"]);
    $post->content = $this->formatPostTextFromData($data, $this->_options["content"]);
    $post->post_type = $this->_post_type;
    $post->post_status = 'publish';
    $post->post_author = $current_user->ID;
    $post->ID = wp_insert_post($post);
    $this->storeCustomFields($post->ID, $data);
    do_action_ref_array('cf7_post_created', array(&$post));
    return $post;
  }

  function formatPostTextFromData($data, $text){
    if(!isset($text)) return '';
    preg_match_all("/%([^%]+)%/i", $text, $matches);
    foreach($matches[1] as $match) {
      $text = preg_replace("/%$match%/i", $data[$match], $text);
    }
    return $text;
  }
  
  function storeCustomFields($post_id, &$data){
    foreach($data as $key => $value)
      if ($this->shouldStoreField($key))
        add_post_meta($post_id, $key, $value, true);
  }
  
  function shouldStoreField($key){
    return !preg_match('/^_/', $key) && $key != 'confirm'; 
  }
}

function cf7_logger_register_logging_hook($form_id, $post_type, $options = array()) {
  global $cf_logging_hooks;
  // Что если логгер уже есть? Что если $form_id == null? Что если нет такого типа поста?
  $logger_class = isset($options['logger_class']) ? $options['logger_class'] : 'cf7_Logger';
  $cf_logging_hooks[$form_id] = new $logger_class($form_id, $post_type, $options);
}


function cf7_logger_mail_sent(&$form) {
  global $cf_logging_hooks;
  if(isset($cf_logging_hooks[$form->id])) $cf_logging_hooks[$form->id]->addEntry($form->posted_data);
  return $form;
}

