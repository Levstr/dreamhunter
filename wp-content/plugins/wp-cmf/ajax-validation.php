<?php
add_action('wp_ajax_cmf_validate_post', 'cmf_ajax_validate_post');

function cmf_ajax_validate_post(){
  global $post;
  $post = get_post($_REQUEST['post_ID']);
  $values = stripslashes_deep($_POST['meta']);
  $template = get_template_for($post);
  if ($template){
    $validationResult = $template->validate($values);
    $cleanup = $template->renderErrorCleanupJS();
    echo json_encode(array('errors' => $validationResult->renderErrors(), 
      'cleanup' => $cleanup));
  } else {
    echo json_encode(array('errors' => array(), 'cleanup' => array()));
  }
  
  exit();
}
