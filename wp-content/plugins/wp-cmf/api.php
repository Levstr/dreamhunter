<?php 

function register_post_template($post_type, $fields = array(), $options = array()){
  global $cmf_templates;
  if (isset($cmf_templates[$post_type])){
    die('Template with this name is already registered');
  }
  $cmf_templates[$post_type] = new PostTemplate($post_type, $fields, $options);
}

function register_taxonomy_template($taxonomy, $fields = array(), $options = array()){
  global $tax_templates;
  if (isset($tax_templates[$taxonomy])){
    wp_die('A template is already registered for this taxonomy: ' . $taxonomy);
  }
  $tax_templates[$taxonomy] = new TaxonomyTemplate($taxonomy, $fields, $options);
}


function add_post_field($post_type, $name, $options){
  global $cmf_templates;
  $template = $cmf_templates[$post_type];
  if (!$template){
    wp_die("Post template is not registered for post type $post_type." );
  }
  $template->buildField($name, $options);
}

function register_template($name, $type, $options = array()){
  register_post_template($type);
}

function register_template_field($template_name, $field_name, $field_type, $options = array()){
  global $cmf_templates;
  if (!isset($cmf_templates[$template_name]))
    die('Unknown template: ' . $template_name);
  $template = $cmf_templates[$template_name];
  $options['type'] = $field_type;
  $template->buildField($field_name, $options);
}

function validate_post($post_type_or_object_or_id, $custom_fields){
  $template = get_template_for($post_type_or_object_or_id);
  return $template->validate($custom_fields);
}

function get_template_for($post_type_or_object_or_id){
  global $cmf_templates;
  if (is_string($post_type_or_object_or_id)){
    return $cmf_templates[$post_type_or_object_or_id];
  } else if (is_numeric($post_type_or_object_or_id)){
    $post = get_post($post_type_or_object_or_id);
    return $cmf_templates[$post->post_type];
  } else if (is_object($post_type_or_object_or_id)){
    return $cmf_templates[$post_type_or_object_or_id->post_type];
  }
  wp_die('cant find template for: ' . $post_type_or_object_or_id);
}