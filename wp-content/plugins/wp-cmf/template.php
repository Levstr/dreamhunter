<?php


class Template {
  var $options;
  var $fields = array();
  
  function Template($type, $fields = array(), $options = array()){
    $this->options = $options;
		if (is_array($fields)) {
	    foreach($fields as $name => $options){
	      if (is_int($name)){
	        $name = $options;
	        $options = array();
	      }
	      $this->buildfield($name, $options);
	    }
		}
  }
  
  protected function getTitle(){
    return empty($this->options['title']) ? 'Fields' : $this->options['title'];
  }
  
  function buildField($name, $options = array()){
    if (!isset($options['type'])) $options['type'] = 'Text';
    $field_type = ucwords($options['type']);
    $field_class_name = $field_type . "Field";
    $this->addField(new $field_class_name($this, $name, $options));
  }
  
  function addField($field){
    $this->fields[$field->dirty_name] = $field;
  }
  
  function getFieldValue($field, $field_name){
  }
  
  function validate($values, &$validationResult = null){
    if (!$validationResult) $validationResult = new ValidationResult();
    foreach($this->fields as $field) $field->validate($values, $validationResult);
    return $validationResult;
  }
  
  function renderErrorCleanupJS(){
    $res = array();
    foreach($this->fields as $field){
      $res []= $field->renderErrorCleanupJS();
    }
    return $res;
  }
  
  function renderField($field, $post){
    $field->renderPrefix();
    $field->renderLabel();
    if (!empty($field->options['description']))
      $field->renderDescription();
    $field->renderInput($post);
    $field->renderSuffix();
  }
  
  function renderWpnonce($post){
    wp_nonce_field('cmf_meta_nonce', 'cmf_meta_nonce');
  }
  
  function renderFieldPrefix($field){
    tag_start('div', array('class' => 'cmf_field_wrap', 'id' => $field->wrapperID()));
  }
  
  function renderFieldSuffix($field){
    tag_end('div');
  }
  
  function renderFieldLabel($field){
      tag_around('label', $field->title(),  array('for' => $field->inputName(), 'class' => 'field_label'));
  }
  
  function renderFieldDescription($field){
    tag_around('div', $field->options['description'], array('class' => 'description'));
  }
  
  
}
?>