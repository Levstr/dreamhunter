<?php

class Field{
  var $dirty_name;
  var $options;
  var $index;
  var $template;
  
  public function __construct($template, $name, $options, $index = 0){
    $this->template = $template;
    $this->dirty_name = $name;
    $this->options = $options;
    $this->index = $index;
  }
  
  function renderPrefix(){
    if (isset($this->options['before'])) echo $this->options['before'];
    $this->template->renderFieldPrefix($this);
  }
  
  function renderSuffix(){
    $this->template->renderFieldSuffix($this);
    if (isset($this->options['after'])) echo $this->options['after'];
  }
  
  
  public function name(){
    return sanitize_title_with_dashes($this->dirty_name);
  }
  
  function renderLabel(){
    $this->template->renderFieldLabel($this);
  }
  
  function renderDescription(){
    $this->template->renderFieldDescription($this);
  }
  
  public function inputID(){
    return "meta_" . $this->name();
  }
  
  function wrapperID(){
     return $this->inputID() . "_wrapper";
  }
  
    
  public function inputName(){
    return "meta[" . $this->name(). "]";
  }
  
  public function title(){
    return empty($this->options['title']) ? $this->dirty_name : $this->options['title'];
  }
  
  
  
  function formatErrors($errors){
    if (is_array($errors)) $errors = join("<br>\r\n", $errors);
    return str_replace("{name}", $this->title(), $errors);
  }
  
  
  function renderErrors($errors){
    $wrapperID = $this->wrapperID();
    $errors = $this->formatErrors($errors);
    if (is_array($errors)){
      $errors = join("<br>\r\n", $errors);
    }
    ob_start();
    ?>
    jQuery('#<?php echo $this->wrapperID()?>').append(jQuery('<div class="errors" />').html('<?php echo addslashes($errors)?>'));
    <?php $js = ob_get_clean();
    return $js;
  }
  
  
  public function getDefaultValue($post){
    return isset($this->options['default']) ? $this->options['default'] : null;
  }
  
  function save($post, $meta){
    $this->template->setMetaValue($post, $this->dirty_name, $meta[$this->name()]);
  }
  
  function validate($values, &$validationResult){
    if (isset($this->options['required']) && $this->options['required'] && empty($values[$this->name()])){
      $validationResult->addError($this, __('{name} is a required field'));
    }
    if (isset($this->options['extra_validation']) && $this->options['extra_validation']){
      $func = $this->options['extra_validation'];
      //TODO: add template reference
      $errors = $func(new StdClass(), &$this, $values);
      if (is_array($errors) && count($errors)){
        foreach($errors as $error)
          $validationResult->addError($this, $error);
      }
    }
    $filtered_errors = apply_filters('validate_field', array(), &$this, $values);
    if (is_array($filtered_errors))
      foreach($filtered_errors as $error)
        $validationResult->addError($this, $error);
  }
  
  
  function renderErrorCleanupJS(){
    ob_start(); ?>
    jQuery('#<?php echo $this->wrapperID()?> div.errors').remove();
    <?php return ob_get_clean();
  }
  
}