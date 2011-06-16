<?php

/* Simple HTML helpers library for Wordpress */
/* Version: 0.1 */
/* Author: Nikolay Karev */


if (!(function_exists('tag'))){
  
  /* HTML Generator functions */
  
  function tag($tag, $attributes = array()){
    tag_start($tag, $attributes);
  }
  
  function get_html_tag($tag, $attributes = array()){
    $attr_string = '';
    foreach($attributes as $key => $val) $attr_string .= " $key=\"" . esc_attr($val) . "\"";
    return "<$tag $attr_string>";
  }
  
  function tag_start($tag, $attributes = array()){
    echo get_html_tag($tag, $attributes);
  }

  function tag_end($tag){
    echo get_tag_end($tag);
  }

  function get_tag_end($tag){
    return "</$tag>\r\n";
  }
  
  function tag_around($tag, $content, $attributes = array()){
    tag_start($tag, $attributes);
    echo $content;
    tag_end($tag);
  }

  function get_tag_around($tag, $content, $attributes = array()){
    return get_tag_start($tag, $attributes) . $content . get_tag_end($tag);
  }
  
  function select_tag($name, $values = array(), $selected = null, $attributes = array()){
    $attributes['name'] = $name;
    tag_start('select', $attributes);
    foreach($values as $name => $value){
      $attrs = array('value' => is_string($name) ? $name : $value);
      if (is_string($name) && $selected == $name || is_numeric($name) && $selected == $value) $attrs ['selected']= 'selected';
      tag_around("option", $value, $attrs);
    }
    tag_end('select');
  }

  function text_field_tag($name, $value = null, $attributes = array()){
    $attributes['value'] = $value;
    $attributes['name'] = $name;
    $attributes['type'] = 'text';
    tag('input', $attributes);
  }
  
  function text_field($name, $value = null, $attributes = array()){
    text_field_tag($name, $value, $attributes);
  }
  
  function hidden_field($name, $value = null, $attributes = array()){
    $attributes['value'] = $value;
    $attributes['name'] = $name;
    $attributes['type'] = 'hidden';
    tag('input', $attributes);
  }

  function checkbox_tag($attributes, $checked = false){
    if ($checked)
      $attributes['checked'] = 'checked';
    $attributes['type'] = 'checkbox';
    tag('input', $attributes);
  }
  function radiobutton_tag($attributes, $checked = false){
    if ($checked)
      $attributes['checked'] = 'checked';
    $attributes['type'] = 'radio';
    tag('input', $attributes);
  }

  function textarea_tag($value, $attributes = array(), $escape = true){
    tag_start('textarea', $attributes);
    echo $escape ? esc_html($value) : $value;
    tag_end('textarea');
  }
  
  
  
  /* "The" functions */
  function get_the_post_meta($names, $options = array()){
    global $wp_query, $id;
    if (!isset($options['separator']) && is_array($names)) $options['separator'] = ' ';
    if (is_string($names)) $names = array($names);
    $values = array();
    foreach($names as $name) $values []= get_post_meta($id, $name, true);
    $values = join($options['separator'], $values);
    return apply_filters('the_post_meta', $values);
  }

  function the_post_meta($names, $options = array()){
    $meta = get_the_post_meta($names, $options);
    echo $meta;
    if ($options['break_after'] && !empty($meta)) echo "<br />";
  }


  function the_post_meta_with_break($name){
    the_post_meta($name, array('break_after' => true));
  }

  function the_post_meta_div($name){
    $meta = get_the_post_meta($name);
    if (!empty($meta)){
      tag_around('div', apply_filters('the_content', get_the_post_meta($name)));
    }
  }


  function the_post_meta_content($name){
    echo apply_filters('the_content', get_the_post_meta($name));
  }

  function has_the_post_meta($name){
    $meta = get_the_post_meta($name);
    return !empty($meta);
  }
  
  /* end of "the" functions */
  

  function wp_enqueue_conditional_style($id, $path, $condition) {
    global $wp_styles;
    wp_enqueue_style($id, $path);
    $wp_styles->add_data($id, "conditional", $condition);
  }

  function multiline($string, $before = null, $after = "<br>\n"){
    $strings = parse_multiline($string);
    $result = '';
    foreach($strings as $string){
      $result .= "$before$string$after";
    }
    return $result;
  }
  
  function link_or_text($title, $url, $options = array()){
    $options['href'] = $url;
    if (!empty($url))
      tag_around('a', $title, $options);
    else
      echo $title;
  }

  function parse_multiline($string){
    $strings = preg_split('/(\r\n|\r|\n)/', $string);
    $result = array();
    foreach($strings as $string){
      if (!preg_match('/^\s*$/', $string))
        $result []= $string;
    }
    return $result;
  }

}