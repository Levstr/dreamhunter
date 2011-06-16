<?php
class MediaField extends Field {
  function renderInput($post){
    tag_start('div', array('class' => 'clearfix image-field'));
    tag('input', array('id' => $this->inputID(), 
      'value' => get_post_meta($post->ID, $this->dirty_name, true),
      'name' => $this->inputName(),
      'type' => 'text',
      'class' => 'float-left',
      'size' => isset($this->options['size']) ? $this->options['size'] : '100'));
    $this->renderMediaButtons($post);
    tag_end('div');
  }
  
  function renderMediaButtons($post){
    tag_start('div', array('class' => 'media-buttons'));
    add_filter('image_upload_iframe_src', array(&$this, 'cmf_upload_src_filter'));
    add_filter('audio_upload_iframe_src', array(&$this, 'cmf_upload_src_filter'));
    add_filter('video_upload_iframe_src', array(&$this, 'cmf_upload_src_filter'));
    add_filter('media_upload_iframe_src', array(&$this, 'cmf_upload_src_filter'));
    do_action('media_buttons');
    remove_filter('image_upload_iframe_src', array(&$this, 'cmf_upload_src_filter'));
    remove_filter('audio_upload_iframe_src', array(&$this, 'cmf_upload_src_filter'));
    remove_filter('video_upload_iframe_src', array(&$this, 'cmf_upload_src_filter'));
    remove_filter('media_upload_iframe_src', array(&$this, 'cmf_upload_src_filter'));
    tag_end('div');
    
  }
  
  function cmf_upload_src_filter($src){
    return preg_replace('/\?/', "?target=" . $this->inputID() . "&amp;plain=true&amp;",  $src);
  }
}