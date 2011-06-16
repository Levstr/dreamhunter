<?php
class ValidationResult{
  var $errors = array();
  var $fields = array();
  
  function addError(&$field, $error){
    if (!isset($this->errors[$field->dirty_name])){
      $this->errors[$field->dirty_name] = array();
    }
    $this->errors[$field->dirty_name] []= $error;
    $this->fields[$field->dirty_name] = $field;
  }
  
  function valid(){
    return empty($this->errors);
  }
  
  function getErrors(){
    return $this->errors;
  }
  
  function renderErrors(){
    if (is_admin())
      return $this->renderAdminErrors();
    else
      return $this->renderPublicErrors();
  }
  
  private function renderAdminErrors(){
    $errors = array();
    foreach($this->fields as $id => $field){
      $errors []= $field->renderErrors($this->errors[$id]);
    }
    return $errors;
  }
  
  private function renderPublicErrors(){
    $errors = '<ul>';
    foreach($this->fields as $id => $field){
      $error = $field->formatErrors($this->errors[$id]);
      $errors .= "<li>$error</li>\r\n";
    }
    $errors .= "</ul>";
    return $errors;
  }
  
  function clear(){
    $this->errors = array();
    $this->fields = array();
  }
}
?>