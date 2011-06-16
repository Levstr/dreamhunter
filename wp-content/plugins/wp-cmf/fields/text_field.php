<?php
class TextField extends Field {
  
  protected function quicktagsID(){
    return "quicktags_" . $this->inputID();
  }
  
  private function isWysiwyg(){
    return isset($this->options['wysiwyg']) && $this->options['wysiwyg'] && user_can_richedit();
  }
  
  private function isMultiline(){
    return isset($this->options['multiline']) && $this->options['multiline'];
  }
  
  public function renderInput($post){
    if ( $this->isWysiwyg() || $this->isMultiline()){
      $this->renderTextArea($post);
    } else 
      tag('input', array('id' => $this->inputID(), 
        'value' => $this->template->getMetaValue($post, $this->dirty_name),
        'name' => $this->inputName(),
        'type' => 'text',
        'size' => isset($this->options['size']) ? $this->options['size'] : '100',
        'class' => 'text')
      );
  }
  
  function validate($post, &$validationResult){
    parent::validate($post, $validationResult);
    $value = $post[$this->name()];
    if (isset($this->options['email']) && $this->options['email'] && 
      !empty($value) && !is_email($value)){
      $validationResult->addError($this, __('{name} : email is invalid'));
    }
  }
  
  function renderTextArea($post){
    $rows = get_option('default_post_edit_rows');
  	if (($rows < 3) || ($rows > 100)) $rows = 12;
  		
    if ($this->isWysiwyg()){
      tag_start('div', array('class' => 'wysiwyg-toolbar'));
      tag_around('a', 'HTML editor', array('class' => 'html-mode hide-if-no-js float-right', 'href' => '#'));
      tag_around('a', 'Visual editor', array('class' => 'wysiwyg-mode hidden float-right hide-if-no-js', 'href' => '#'));
      if ( current_user_can( 'upload_files' ) ) $this->renderMediaButtons($post);
      tag_end('div');
      tag_start('div', array('class' => 'quicktags'));
    }
    tag_start('div', array('id' => 'editorcontainer_' . $this->inputID()));
    textarea_tag($this->inputName(), apply_filters('the_editor_content', get_post_meta($post->ID, $this->dirty_name, true)),
      array('id' => $this->inputID(), 
        'class' => $this->textAreaClass(),
        'rows' => $rows), false);
    tag_end('div');
    if ($this->isWysiwyg()) tag_end('div');
    
  }
  
  function renderMediaButtons($post){
    tag_start('div', array('class' => 'media-buttons'));
    add_filter('image_upload_iframe_src', array(&$this, 'cmf_upload_src_filter'));
    add_filter('audio_upload_iframe_src', array(&$this, 'cmf_upload_src_filter'));
    add_filter('video_upload_iframe_src', array(&$this, 'cmf_upload_src_filter'));
    add_filter('media_upload_iframe_src', array(&$this, 'cmf_upload_src_filter'));
    do_action('media_buttons');
    remove_filter('image_upload_iframe_src', array(&$this, 'cmf_upload_src_filter'));
    remove_filter('video_upload_iframe_src', array(&$this, 'cmf_upload_src_filter'));
    remove_filter('video_upload_iframe_src', array(&$this, 'cmf_upload_src_filter'));
    remove_filter('media_image_upload_iframe_src', array(&$this, 'cmf_upload_src_filter'));
    tag_end('div');
    
  }
  
  function cmf_upload_src_filter($src){
    return preg_replace('/\?/', "?target=" . $this->inputID() . "&amp;",  $src);
  }
  
  function textAreaClass(){
    if ($this->options['wysiwyg']){
      return 'cmf-wysiwyg';
    } else return 'no-wysiwyg';
  }
}
