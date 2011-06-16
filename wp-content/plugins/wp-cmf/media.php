<?php
add_filter('media_send_to_editor', 'cmf_filter_media_send_to_editor', 100, 3);
add_action('post-upload-ui', 'cmf_action_post_upload_ui');
add_filter('media_upload_form_url', 'cmf_filter_media_upload_form_url', 10, 2);

add_filter('get_media_item_args', 'cmf_filter_get_media_item_args');
add_filter('admin_url', 'cmf_filter_admin_url', 10, 3);

function cmf_filter_admin_url($url, $path, $blog_id){
  if ($path == 'async-upload.php'){
    $url = add_query_arg('target', $_REQUEST['target'], $url);
  }
  return $url;
}

function cmf_referer_query(){
  $referer = $_SERVER['HTTP_REFERER'];
  $referer = preg_replace('/^[^\?]+\?/', '', $referer);
  parse_str($referer, &$ref);
  return $ref;
}


function cmf_filter_get_media_item_args($args){
  $ref = cmf_referer_query();
  if (isset($_REQUEST['target']) && $_REQUEST['target'] || isset($ref['target'])){
    $args['send'] = true;
  }
  $args['send'] = true;
  return $args;
}


function cmf_filter_media_upload_form_url($form_action_url, $type){
  $plain = isset($_REQUEST['plain']) ? $_REQUEST['plain'] : null;
  $target = isset($_REQUEST['target']) ? $_REQUEST['target'] : null;
  return add_query_arg(array($plain, 'target' => $target), 
    $form_action_url);
}

function cmf_action_post_upload_ui(){
  $ref = cmf_referer_query();
  if (isset($_REQUEST['target']) && $_REQUEST['target'] || isset($ref['target'])){
    $target = isset($_REQUEST['target']) ? $_REQUEST['target'] : isset($ref['target']) ? $ref['target'] : '';
    $plain = isset($_REQUEST['plain']) ? $_REQUEST['plain'] : isset($ref['plain']) ? $ref['plain'] : '';
    echo "<input type='hidden' value='". esc_attr($target) . "' name='target'>";
    echo "<input type='hidden' value='". esc_attr($plain)  . "' name='plain'>";
  }
}



function cmf_filter_media_send_to_editor($html, $send_id, $attachment){
  $ref = cmf_referer_query();
  $target = isset($_REQUEST['target']) && $_REQUEST['target'] ? $_REQUEST['target'] : (isset($ref['target']) ? $ref['target'] : null);
  $plain = isset($_REQUEST['plain']) && $_REQUEST['plain'] ? $_REQUEST['plain'] : (isset($ref['plain']) ? $ref['plain'] : null);
  if ($target){
    if ( $plain == 'true' && $_GET['type'] == 'image'){
      $html = image_downsize($send_id, $attachment['image-size']);
      $html = $html[0];
    } elseif ($plain == 'true'){
      $html = $attachment['url'];
    }
    
      ?>
    <script type='text/javascript'>
    	/* <![CDATA[ */
    var win = window.dialogArguments || opener || parent || top;
    win.cmf_send_to_field('<?php echo $target ?>', 
      '<?php echo addslashes($html)?>', 
      <?php echo $plain ? 'true' : 'false' ?>);
    /* ]]> */
    </script><?php
    exit();
  }
  return $html;
}

?>