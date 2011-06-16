<?php
class PostTemplate extends Template {
  var $post_type;
  function __construct($post_type, $fields = array(), $options = array()){
    parent::__construct($post_type, $fields, $options);
    $this->post_type = $post_type;
    
    add_action('save_post', array(&$this, 'action_save_post'), 10, 2);
    add_action("add_meta_boxes_$this->post_type", array(&$this, 'action_add_meta_box'), 10);
  }
  
  function action_add_meta_box($post){
    add_meta_box('cmf_fields', $this->getTitle(), array(&$this, 'renderMetaBox'), $post->post_type, 'normal', 'high', &$this);
    if (!isset($this->options['leave_custom_fields']) || !$this->options['leave_custom_fields'])
      remove_meta_box('postcustom', $post->post_type, 'normal');
  }
  
  function action_save_post($id, $post){
    if ($post->post_type != $this->post_type) return;
    if( !isset( $id ) && $_REQUEST[ 'post_ID' ] )
			$id = $_REQUEST[ 'post_ID' ];
		if (wp_is_post_revision($post))
			$id = wp_is_post_revision($post);
		if( !current_user_can('edit_post', $id) )
			return $id;
		if( !isset($_REQUEST['cmf_meta_nonce']) || !wp_verify_nonce($_REQUEST['cmf_meta_nonce'], 'cmf_meta_nonce') )
			return $id;	
	  foreach($this->fields as $field){
      $field->save($post, $_REQUEST['meta']);
    }
  }
  
  function getMetaValue($object, $meta_name, $single = true){
    if (!$object)
      return null;
    return get_post_meta($object->ID, $meta_name, $single);
  }
  
  function setMetaValue($object, $meta_name, $meta_value, $old_value = ''){
		update_post_meta($object->ID, $meta_name, $meta_value, $old_value);
  }
  
  
  function reformatMeta($meta){
    $result = array();
    foreach($meta as $row)
      $result[$row['meta_key']] []= $row;
    return $result;
  }
  
  function renderMetaBox($post){
    $meta = $this->reformatMeta(has_meta($post->ID));
    $this->renderWpnonce($post);
    foreach($this->fields as $field){
      $this->renderField($field, $post);
    }
  }
}
?>