<?php

class CheckboxField extends Field {
  
  function renderForm($post){
    $this->renderPrefix($post);
    if (isset($this->options['title']) && $this->options['title'])
      $this->renderLabel();
    if (!empty($this->options['description']))
      $this->renderDescription();
    $this->renderInput($post);
    $this->renderSuffix($post);
  }
  
  function renderInput($post){
    tag_start('label', array('for' => $this->inputName()));
    $fieldValue = get_post_meta($post->ID, $this->dirty_name, true);
    checkbox_tag($this->inputName(), $this->inputValue(), 
      $fieldValue && $fieldValue != 'false', array('id' => $this->inputID()));
    echo !empty($this->options['checkbox_text']) ? $this->options['checkbox_text'] : $this->title();
    tag_end('label');
  }
  
  function inputValue(){
    return isset($options['value']) ? $options['value'] : true; 
  }
  
}
