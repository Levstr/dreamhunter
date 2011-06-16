<?php

/*
Plugin Name: HTML Helpers
Plugin URI: http://jetdog.biz/projects/wp-html-helpers
Description: Simple HTML rendering library for WordPress
Version: 0.1.1
Author: Nikolay Karev
Author URI: http://jetdog.biz
*/

/*
Copyright (C) 2010 Nikolay Karev, karev.n@gmail.com

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/



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
  
  function select_tag($name, $values = array(), $selected = null, $attributes = array(), $options = array()){
    $attributes['name'] = $name;
    tag_start('select', $attributes);
    if (isset($options['empty']) && $options['empty']){
      tag_around('option', $options['empty'], array('value' => null));
    }
      
    foreach($values as $name => $value){
      if (!isset($options['key_property'])){
        $attrs = array('value' => $name);
        if ($name == $selected) $attrs ['selected']= 'selected'; 
        tag_around("option", $value, $attrs);
      } else {
        $key_property = $options['key_property'];
        $value_property = $options['value_property'];
        $attrs = array('value' => $value->$key_property);
        if ($value->$key_property == $selected) 
          $attrs['selected'] = 'selected';
        tag_around("option", $value->$value_property, $attrs);
      }
      
    }
    tag_end('select');
  }

  function text_field_tag($name, $value = null, $attributes = array()){
    $attributes['value'] = $value;
    $attributes['name'] = $name;
    $attributes['type'] = 'text';
    tag('input', $attributes);
  }

  function checkbox_tag($name, $value, $checked = false, $attributes = array()){
    if ($checked)
      $attributes['checked'] = 'checked';
    $attributes['type'] = 'checkbox';
    $attributes = array_merge($attributes, array('name' => $name, 'value' => $value));
    tag('input', $attributes);
  }
  function radiobutton_tag($name, $value, $checked = false, $attributes = array()){
    if ($checked)
      $attributes['checked'] = 'checked';
    $attributes['type'] = 'radio';
    $attributes = array_merge($attributes, array('name' => $name, 'value' => $value));
    tag('input', $attributes);
  }

  function textarea_tag($name, $value = null, $attributes = array(), $escape = true){
    tag_start('textarea', $attributes);
    echo $escape ? esc_html($value) : $value;
    tag_end('textarea');
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
  
  
  
  /* "The" functions */
  function get_the_post_meta($names, $options = array()){
    global $wp_query, $id;
    if (!isset($options['separator']) && is_array($names)) $options['separator'] = ' ';
    if (!is_array($names)) $options['separator'] = '';
    if (is_string($names)) $names = array($names);
    $values = array();
    foreach($names as $name) $values []= get_post_meta($id, $name, true);
    $values = join($options['separator'], $values);
    return apply_filters('the_post_meta', $values);
  }

  function the_post_meta($names, $options = array()){
    $meta = get_the_post_meta($names, $options);
    echo $meta;
    if (isset($options['break_after']) && $options['break_after'] && !empty($meta)) echo "<br />";
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
  
  function objects2array($array, $key, $value){
    $result = array();
    foreach($array as $object){
      $result[$object->$key] = $object->$value;
    }
    return $result;
  }

}