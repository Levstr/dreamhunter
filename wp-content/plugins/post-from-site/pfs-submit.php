<?php 
/* * *
 * Processed form data into a proper post array, uses wp_insert_post() to add post. 
 * 
 * @param array $pfs_data POSTed array of data from the form
 */
require('../../../wp-load.php');

/**
 * Create post from form data, including uploading images
 * @param array $post
 * @param array $files
 * @return string success or error message.
 */
function pfs_submit($post,$files){
	$pfs_options = get_option('pfs_options');
	$result = array('image'=>"",'error'=>"",'success'=>"",'post'=>"");

	global $current_user;
	get_currentuserinfo();
	
	if(!$current_user->ID) {
		$result['error'] = 'Пользователь не авторизован';
		return $result;
	}
	
	foreach($post as $key=>$value) ${$key} = $value;
	//add_picture_to_user($files);
	
	if(empty($files['image']['tmp_name'][0])){
	  $result['error'] = 'Please add at least one photo';
	  return $result;
	}
	/*if(empty($title)){
	  $result['error'] = 'Please set your girl name';
	  return $result;
	}*/


	$result['image'] = "multiple";
	foreach($files['image']['tmp_name'] as $tmp_file_name){
	  if(!getimagesize($tmp_file_name)){
	    $result['error'] = "Wrong filetype - only .gif, .png, .jpg, .jpeg allowed";
	    return $result;
	  }
	  if(filesize($tmp_file_name)>2*1024*1024){
	    $result['error'] = "Image size too big - max 2Mb per image allowed";
	    return $result;
	  }
	}


	for($i=0; $i<count($files['image']['tmp_name']); $i++)
	  if(!empty($files['image']['tmp_name'][$i])){
  		$upload[$i] = wp_upload_bits($files['image']["name"][$i], null, file_get_contents($files['image']["tmp_name"][$i]));
  	}
	/*$categories = $cats;
	$newcats = explode(',',$newcats);
	foreach ($newcats as $cat) $categories[] = wp_insert_category(array('cat_name' => trim($cat), 'category_parent' => 0));
	$newtags = explode(',',$newtags);
	foreach ($newtags as $tag) {
		wp_create_tag(trim($tag));
		$tags[] = trim($tag);
	}*/
	
	$posts = get_posts(array( 'post_type' => 'profile','author' => $current_user->ID));
	if(count($posts)) {
		$postarr = $posts[0];
		$post_id = $postarr->ID;
	} else {
	
		$postarr = array();
		$postarr['post_title'] = strip_tags($current_user->user_login);
		$postarr['post_name'] = sanitize_title($current_user->user_login);
		//$postarr['post_content'] = strip_tags($postcontent);
		$postarr['comment_status'] = $pfs_options['pfs_comment_status'];
		$postarr['post_status'] = 'publish';//$pfs_options['pfs_post_status'];
		$postarr['post_author'] = $current_user->ID;
		//$postarr['post_category'] = $categories;
		//$postarr['tags_input'] = implode(',',$tags);
		$postarr['post_type'] = 'profile';
		$post_id = wp_insert_post($postarr);
	}
	if(!$post_id){
		$result['error'] = 'Undefined error. Photos was not been uploaded';
		return $result;
  }


	//$result['post'] = $post_id;
	foreach($upload as $k=>$v) if(!$v['error']){
    $wp_filetype = wp_check_filetype(basename($v['file']), null);
    $attachment = array(
     'post_mime_type' => $wp_filetype['type'],
     'post_title' => preg_replace('/\.[^.]+$/', '', basename($v['file'])),
     'post_content' => '',
     'post_status' => 'draft'//'inherit'
    );
    $attach_id=wp_insert_attachment($attachment, $v['url'], $post_id);
    require_once(ABSPATH.'wp-admin/includes/image.php');
    $attach_data=wp_generate_attachment_metadata($attach_id, $v['file']);
    wp_update_attachment_metadata($attach_id, $attach_data);
	
  }

	$result['success'] = get_bloginfo('url')."/photo/$post_id.html";
	//wp_mail("fotonstep@gmail.com", "YAVD - New photo #$post_id", $result['success']);
	return $result;
}




if (!empty($_POST)){
  echo json_encode(pfs_submit($_POST,$_FILES));
}
?>