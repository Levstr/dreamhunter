<?php

class TaxonomyTemplate extends Template {
  var $taxonomy;
  var $table_mode = false;
  
  function __construct($taxonomy, $fields = array(), $options = array()){
    parent::__construct($taxonomy, $fields, $options);
    $this->taxonomy = $taxonomy;
    add_action("{$taxonomy}_edit_form_fields", array(&$this, 'action_edit_form_fields'), 100, 2);
    add_action("{$taxonomy}_add_form_fields", array(&$this, 'action_add_form_fields'), 10, 2);
    add_action("edited_$taxonomy", array(&$this, 'action_edited_term'), 10,2);
  }
  
  function getMetaValue($object, $meta_name, $single = true){
    if (!$object)
      return null;
    return get_term_meta($object->term_id, $meta_name, $single);
  }
  
  function setMetaValue($object, $meta_name, $meta_value, $prev_value = ''){
    update_term_meta($object->term_id, $meta_name, $meta_value, $prev_value);
  }
  
  function renderFormFields($tag, $taxonomy){
    foreach($this->fields as $field) $this->renderField($field, $tag);
  }
  
  function action_edited_term($term_id, $tt){
    $term = get_term($term_id, $this->taxonomy);
    foreach($this->fields as $field) $field->save($term, stripslashes_deep($_REQUEST['meta']));
  }
  
  function action_edit_form_fields($tag, $taxonomy){
    $this->table_mode = true;
    $this->renderFormFields($tag, $taxonomy);
  }
  
  function action_add_form_fields($taxonomy){
    $this->renderFormFields(null, $taxonomy);
  }
  
  function renderField($field, $post){
    $field->renderPrefix();
    $field->renderLabel();
    if ($this->table_mode) tag_start('td');
    $field->renderInput($post);
    
    if (!empty($field->options['description']))
      $field->renderDescription();
    if ($this->table_mode)  tag_end('td');
    $field->renderSuffix();
  }
  
  function renderFieldPrefix($field){
    if ($this->table_mode)
      tag_start('tr', array('class' => 'form-field cmf_field_wrap'));
    else
      tag_start('div', array('class' => 'cmf_field_wrap form-field', 'id' => $field->wrapperID()));
  }
  
  function renderFieldSuffix($field){
    if ($this->table_mode)
      tag_end('tr');
    else
      parent::renderFieldSuffix($field);
  }
  
  function renderFieldLabel($field){
    if ($this->table_mode){
      tag_start('th', array('scope' => 'row', 'valign' => 'top'));
      parent::renderFieldLabel($field);
      tag_end('th');
    } else 
      parent::renderFieldLabel($field);
  }
  
  function renderFieldDescription($field){
    if ($this->table_mode){
      tag_around('span', $field->options['description'], array('class' => 'description'));
    }
    else
      tag_around('p', $field->options['description'], array('class' => 'description'));
  }
  
  
}
?>