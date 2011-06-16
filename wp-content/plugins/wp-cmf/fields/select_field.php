<?php
class SelectField extends Field{
  function renderInput($post){
    if (!$this->options['options']){
      echo('Please give some options');
      return;
    }
    $values = array();
    $values = array_merge($values, $this->options['options']);
    $select_options = array();
    if (isset($this->options['key_property']) && $this->options['key_property'])
      $select_options['key_property'] = $this->options['key_property'];
    if (isset($this->options['value_property']) && $this->options['value_property'])
      $select_options['value_property'] = $this->options['value_property'];
    if (isset($this->options['empty']))
      $select_options['empty'] = $this->options['empty'];
    select_tag($this->inputName(), $values, 
      get_post_meta($post->ID, $this->dirty_name, true), array('id' => $this->inputID()), $select_options);

  }
}