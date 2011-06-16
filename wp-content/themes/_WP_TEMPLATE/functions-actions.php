<?php

add_action('after_setup_theme', '_WP_TEMPLATE_action_after_setup_theme');
add_action('widgets_init', '_WP_TEMPLATE_action_widgets_init');

add_action( 'init', 'create_post_type' );
add_action('wp_ajax_of_ajax_addmyrequest_action', 'func_add_request');
add_action('wp_ajax_of_ajax_sendrequest_action', 'func_send_request');
add_action('wp_ajax_of_ajax_cart_action', 'func_add_to_cart');
add_action('wp_ajax_of_ajax_picture_action', 'add_picture_to_user');

add_action( 'restrict_manage_posts', 'filter_requests' );

add_filter('manage_edit-request_columns', 'add_inf_column');
add_action('manage_request_posts_custom_column', 'manage_inf_column', 10, 2);

add_filter('manage_edit-cart_columns', 'add_cart_column');
add_action('manage_cart_posts_custom_column', 'manage_cart_column', 10, 2);

add_action('admin_menu', '_WP_TEMPLATE_action_add_control_panel');

//add_filter('manage_edit-cart_columns', 'add_inf_column');
//add_action('manage_cart_posts_custom_column', 'manage_inf_column', 10, 2);
/* SETUP ACTIONS -------------------------------------------------------------------------------------------------------------- */

function _WP_TEMPLATE_action_after_setup_theme() {
  add_theme_support('post-thumbnails');
  add_custom_background();
}

function _WP_TEMPLATE_action_widgets_init() {
  register_sidebar(array(
    'name' => 'Left Sidebar',
    'id' => 'left',
    'before_widget' => '<li class="widget %2$s" id="%1$s">',
    'after_widget' => '</li>',
    'before_title' => '<h3>',
    'after_title' => '</h3>'
    ));

  register_sidebar(array(
    'name' => 'Right Sidebar',
    'id' => 'right',
    'before_widget' => '<li class="widget %2$s" id="%1$s">',
    'after_widget' => '</li>',
    'before_title' => '<h3>',
    'after_title' => '</h3>'
    ));

  register_sidebar(array(
    'name' => 'Footer Sidebar',
    'id' => 'footer',
    'before_widget' => '<li class="widget %2$s" id="%1$s">',
    'after_widget' => '</li>',
    'before_title' => '<h3>',
    'after_title' => '</h3>'
    ));
}

function create_post_type() {
	$labels = array(
				'name' => __( 'Заказы' ),
				'singular_name' => __( 'Заказ' ),
				'add_new' => __( 'Создать заказ' ),
				'add_new_item' => __( 'Создать заказ' ),
				'edit_item' => __( 'Редактировать заказ' ),
				'new_item' => __( 'Новый заказ' ),
				'view_item' => __( 'Просмотреть заказ' ),
				'search_items' => __( 'Искать заказы' ),
				'not_found' => __( 'Ничего не найдено' ),
				'not_found_in_trash' => __( 'Корзина пуста' )
			);
	register_taxonomy('cart_status', array('cart'), array('labels'=>array('name'=>'Статус', 'singular_name'=>'Статус'), 'hierarchical'=>true)); 
	register_post_type( 'cart',
		array(
			'labels' => $labels,
			//'public' => true,
			'has_archive' => true,
			'exclude_from_search' => true,
			'show_ui' => true,
			'supports'=>array('title','author','custom-fields' ,'comments'),
			'taxonomies'=>array('cart_status')
		)
	);
	//------------------------------
	
	$labels = array(
				'name' => __( 'Предложения' ),
				'singular_name' => __( 'Предложение' ),
				'add_new' => __( 'Создать предложение' ),
				'add_new_item' => __( 'Создать предложение' ),
				'edit_item' => __( 'Редактировать предложение' ),
				'new_item' => __( 'Новое предложение' ),
				'view_item' => __( 'Просмотреть предложение' ),
				'search_items' => __( 'Искать предложение' ),
				'not_found' => __( 'Ничего не найдено' ),
				'not_found_in_trash' => __( 'Корзина пуста' )
			);
	register_taxonomy('deal_status', array('deal'), array('labels'=>array('name'=>'Статус', 'singular_name'=>'Статус'), 'hierarchical'=>true)); 
	register_post_type( 'deal',
		array(
			'labels' => $labels,
			'public' => true,
			'has_archive' => true,
			//'exclude_from_search' => true,
			'show_ui' => true,
			'supports'=>array('title','editor','author','custom-fields' ,'comments','revisions'),
			'taxonomies'=>array('deal_status','post_tag')
		)
	);
	
	//-------------------------------------
	
	$labels = array(
				'name' => __( 'Запросы' ),
				'singular_name' => __( 'Запрос' ),
				'add_new' => __( 'Создать запрос' ),
				'add_new_item' => __( 'Создать запрос' ),
				'edit_item' => __( 'Редактировать запрос' ),
				'new_item' => __( 'Новый запрос' ),
				'view_item' => __( 'Просмотреть запрос' ),
				'search_items' => __( 'Искать запрос' ),
				'not_found' => __( 'Ничего не найдено' ),
				'not_found_in_trash' => __( 'Корзина пуста' )
			);
	register_taxonomy('request_status', array('request'), array('labels'=>array('name'=>'Статус', 'singular_name'=>'Статус'), 'hierarchical'=>true)); 
	register_post_type( 'request',
		array(
			'labels' => $labels,
			//'public' => true,
			'has_archive' => true,
			'exclude_from_search' => true,
			'show_ui' => true,
			'supports'=>array('title','editor','author','custom-fields' ,'comments'),
			'taxonomies'=>array('request_status')
		)
	);
	
	//-------------------------------------
	
	$labels = array(
				'name' => __( 'Техподдержка' ),
				'singular_name' => __( 'Техподдержка' )
			);
	register_taxonomy('support_status', array('support'), array('labels'=>array('name'=>'Статус', 'singular_name'=>'Статус'), 'hierarchical'=>true)); 
	register_post_type( 'support',
		array(
			'labels' => $labels,
			'public' => true,
			'has_archive' => true,
			//'exclude_from_search' => true,
			'show_ui' => true,
			'supports'=>array('title','editor','author','custom-fields' ,'comments','revisions'),
			'taxonomies'=>array('support_status')
		)
	);
	//------------------------------------------
	$labels = array(
				'name' => __( 'Профиль' ),
				'singular_name' => __( 'Профиль' ),
				'add_new' => __( 'Создать профиль' ),
				'add_new_item' => __( 'Создать профиль' ),
				'edit_item' => __( 'Редактировать профиль' ),
				'new_item' => __( 'Новый профиль' ),
				'view_item' => __( 'Просмотреть профиль' ),
				'search_items' => __( 'Искать профиль' ),
				'not_found' => __( 'Ничего не найдено' ),
				'not_found_in_trash' => __( 'Корзина пуста' )
			);
	register_post_type( 'profile',
		array(
			'labels' => $labels,
			//'public' => true,
			'has_archive' => true,
			'exclude_from_search' => true,
			'show_ui' => true,
			'supports'=>array('title','editor','author','custom-fields')
		)
	);
	
	//------------------------------------------
}

//------добавление запроса (кнопка "добавить свой запрос" на странице горячих предложений)-----------------------------
function func_add_request(){
	global $wpdb, $current_user;
	
	$title = trim($_POST['request_title']);
	$text = trim($_POST['request_text']);
	
    get_currentuserinfo();
	
	if($current_user->ID) {
		$my_post = array(
		'post_status' => 'publish', 
		'post_type' => 'request',
		//'post_author' => $user_ID,
		'post_content' => $text,
		'post_title' => $title
		);
		
		// Insert the post into the database
		wp_insert_post( $my_post );
	}
	
	echo 1;
	return 1;
}

//---------кнопка "Оставить заявку" на странице предложений---------------------------------------
function func_send_request(){
	global $wpdb, $current_user;
	
	$title = 'Заявка на предложение';
	$deal = (int)$_POST['deal'];

    get_currentuserinfo();
	
	if($current_user->ID) {
		//---проверка----
		$can_add = true;
		$post_list = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_status='publish' and post_type='request' and post_author=".$current_user->ID));
		if(count($post_list)) {
			foreach($post_list as $v) {
				$deal_f = get_post_meta($v,'deal_DEALID', true);
				if($deal_f==$deal) {
					$can_add = false;
					break;
				}
			}
		}
		
		//-------------
		
		if($can_add) {
			//-----------------
			$deal_name = $wpdb->get_var($wpdb->prepare("SELECT post_title FROM $wpdb->posts WHERE post_status='publish' and post_type='deal' and ID=".$deal));
			$title .= ' "'.$deal_name.'"';
			//-----------------
			$my_post = array(
			'post_status' => 'publish', 
			'post_type' => 'request',
			//'post_author' => $user_ID,
			//'post_content' => $text,
			'post_title' => $title
			);
			
			// Insert the post into the database
			$post_id = wp_insert_post( $my_post );
			add_post_meta($post_id, 'deal_DEALID', $deal, true);
		} else {
			echo 2;
			return 2;
		}
	}
	echo 1;
	return 1;
}



// корзина
function func_add_to_cart(){
	global $wpdb, $current_user;
	
	$tour_id = $_POST['tour_id'];
	$tour_inf = trim($_POST['tour_inf']);
	
    get_currentuserinfo();
	
	/*
			wp_user_cart_tours (
				user_id
				cart_id
				status (0 - открыта, 1 - отправлено на обработку админу)
			)
			
			wp_user_tours (
				cart_id
				tour_id
			)
			*/
	
	/*if($current_user->ID) {
		// выясняем есть ли открытая корзина
		// если нет - создаем, пишем в нее товары
		$cart_num = $wpdb->get_var($wpdb->prepare("SELECT cart_id FROM `wp_user_cart_tours` WHERE status=0 and user_id=".$current_user->ID));
		if(!$cart_num) {
			$wpdb->insert( 'wp_user_cart_tours', array( 'user_id' => $current_user->ID), array( '%d' ) );
			$cart_num = $wpdb->insert_id;
		}
		$tour_num = $wpdb->get_var($wpdb->prepare("select count(id) from `wp_user_tours` where cart_id='".$cart_num."' and tour_id='".$tour_id."'"));
		
		if(!$tour_num) {
			$result = $wpdb->query($wpdb->prepare("INSERT INTO `wp_user_tours` (`cart_id`, `tour_id`) VALUES ('".$cart_num."', '".$tour_id."')"));
		}
	}*/
	
	if($current_user->ID) {
		//---проверка----
		$can_add = true;
		$can_add_tour = true;
		$post_list = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_status='publish' and post_type='cart' and post_author=".$current_user->ID));
		if(count($post_list)) {
			//$post_id = $post_list;
			//$can_add = false;
			foreach($post_list as $v) {
				//-------------
				$post_terms =  wp_get_post_terms($v, 'cart_status');
				if(!count($post_terms)) { // по идее, окрытая корзина не принадлежит ни одной категории
					$can_add = false;
					$post_id = $v;
				}
				//-------------
				if($post_id) {
					$tour_f = get_post_meta($post_id,'cart_TOURID');
					foreach ($tour_f as $t) {
						if($tour_id==$t) {
							$can_add_tour = false;
							break;
						}
					}
				}
			}
		}
		
		//-------------
		
		if($can_add) {
			$my_post = array(
			'post_status' => 'publish', 
			'post_type' => 'cart',
			//'post_author' => $user_ID,
			//'post_content' => $text,
			'post_title' => 'Корзина_'.$current_user->nickname
			);
			
			// Insert the post into the database
			$post_id = wp_insert_post($my_post);
		}
		
		if($can_add_tour && $post_id) {
			add_post_meta($post_id, 'cart_TOURID', $tour_id);
			
			$tour_inf = preg_replace('/<a class=\\\"add_to_cart\\\".+<\/a>/is', "", $tour_inf);
			add_post_meta($post_id, 'cart_TOURID_'.$tour_id, $tour_inf,true);
		}
	}
	
	//-------------------------------------------------

	/*$fp=fopen("file_data_test","w");
	fwrite($fp,$current_user->ID.'---'.$tour_id);
	fclose($fp);*/
	
	echo 1;
	return 1;//$current_user->ID.'---'.$tour_id;

}


/* OTHER ACTIONS -------------------------------------------------------------------------------------------------------------- */


function filter_requests() {
	global $typenow;
	if( $typenow == "request" || $typenow == "cart"){
		$taxonomy = $typenow.'_status';
		$filters = array($taxonomy);
		foreach ($filters as $tax_slug) {
			$tax_obj = get_taxonomy($tax_slug);
			$tax_name = $tax_obj->labels->name;
			$terms = get_terms($tax_slug); //,'hide_empty=0');
			echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
			echo "<option value=''>Все статусы</option>";
			foreach ($terms as $term) { echo '<option value='. $term->slug, $_GET[$tax_slug] == $term->slug ? ' selected="selected"' : '','>' . $term->name .' (' . $term->count .')</option>'; }
			echo "</select>";
		}
	}
}
// добавление колонок в админке--------------
function add_inf_column($posts_columns) {

    // Add a new column
	$posts_columns['user_phone'] = _x('Телефон', 'column name');
	$posts_columns['user_email'] = _x('Email', 'column name');
	$posts_columns['user_deal'] = _x('Предложение', 'column name');
 	return $posts_columns;
}

function manage_inf_column($column_name, $id) {
	global $wpdb, $post;
	//print_r($post);
	switch($column_name) {
	case 'user_email':
		$author_arr = get_userdata($post->post_author);
		echo '<a href="mailto:'.$author_arr->user_email.'">'.$author_arr->user_email.'</a>';
		break;
	case 'user_phone':
		echo get_cimyFieldValue($post->post_author, 'PHONE');
		break;
	case 'user_deal':
		if($deal_id = get_post_meta($post->ID, 'deal_DEALID', true)) {
			echo $wpdb->get_var($wpdb->prepare("SELECT post_title FROM $wpdb->posts WHERE post_type='deal' and ID={$deal_id}"));
		}
		break;
	default:
		break;
	}
}

function add_cart_column($posts_columns) {

    // Add a new column
	$posts_columns['user_phone'] = _x('Телефон', 'column name');
	$posts_columns['user_email'] = _x('Email', 'column name');
 	return $posts_columns;
}

function manage_cart_column($column_name, $id) {
	global $wpdb, $post;
	//print_r($post);
	switch($column_name) {
	case 'user_email':
		$author_arr = get_userdata($post->post_author);
		echo '<a href="mailto:'.$author_arr->user_email.'">'.$author_arr->user_email.'</a>';
		break;
	case 'user_phone':
		echo get_cimyFieldValue($post->post_author, 'PHONE');
		break;
	default:
		break;
	}
}
//--------------юзерские картинки----------------------------------
function add_picture_to_user($files) {
	global $wpdb, $current_user, $wpdb_data_table, $wpdb_fields_table, $result,  $cuef_upload_path,$cuef_upload_webpath;
		
    get_currentuserinfo();
	if($current_user->ID) {
		//============================================
		if(empty($files['image']['tmp_name'][0])){
		  $result['error'] = 'Please add at least one photo';
		  return $result;
		}

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

		$user_path = $cuef_upload_path.$current_user->user_login."/";
		$file_fordb = $cuef_upload_webpath.$current_user->user_login."/";

		for($i=0; $i<count($files['image']['tmp_name']); $i++) {
		  if(!empty($files['image']['tmp_name'][$i])){
			//$upload[$i] = wp_upload_bits($files['image']["name"][$i], null, file_get_contents($files['image']["tmp_name"][$i]));
			//$rules['equal_to'] = 600;
			//echo $upload[$i] = cimy_manage_upload($files['image']["name"][$i], $current_user->user_login, $rules);
			$file_full_path = $user_path.$files['image']["name"][$i];
			move_uploaded_file($files['image']['tmp_name'][$i], $file_full_path);
			$upload[$i] = $file_fordb.$files['image']["name"][$i];
			}
		}
		
		foreach($upload as $k=>$v) { // if(!$v['error']){
			//-----добавление фотки в профиль юзера-------
			// проверяем наличие свободного поля
			$sql = "SELECT NAME FROM $wpdb_fields_table WHERE NAME like 'FOTOFILE_%' and ID not in (select FIELD_ID from $wpdb_data_table where USER_ID=$current_user->ID and VALUE is not NULL and VALUE!='') limit 1";
			$field_exist = $wpdb->get_var($sql);
			if($field_exist) {
				$res = set_cimyFieldValue($current_user->ID, $field_exist,$v);
			}  else { // надо создать поле
				$number = $wpdb->get_var("select MAX(ID) from $wpdb_fields_table where NAME like 'FOTOFILE_%'");
				
				$number = ($number ? $number : 1);
				$field_exist = 1;
				while($field_exist) {
					$field_name = 'FOTOFILE_'.$number;
					$sql = "SELECT ID FROM $wpdb_fields_table WHERE NAME='$field_name'";
					$field_exist = $wpdb->get_var($sql);
					if(!$field_exist) break;
					$number++;
				}
				$field_name = 'FOTOFILE_'.$number;
				
				$order = ($wpdb->get_var("select MAX(F_ORDER) from $wpdb_fields_table"));
				//-------------
				$store_rule['email'] = false;
				$store_rule['can_be_empty'] = true;

				$store_rule['edit'] = $order;
				$store_rule['show_in_reg'] = false;
				$store_rule['show_in_profile'] = true;
				$store_rule['show_in_aeu'] = true;
				$store_rule['show_in_search'] = false;

				$store_rule['equal_to_case_sensitive'] = false;
				$store_rule['equal_to_regex'] = false;
				$store_rule['show_level'] = $order;
							
				$data = array();
				$data['name'] = $field_name;
				$data['value'] = '';
				$data['desc'] = '';
				$data['label'] = $field_name;
				$data['type'] = 'picture';
				$data['store_rule'] = $store_rule;
				$data['field_order'] = 0;
				$data['num_fields'] = $order;
				$data['fieldset'] = 0;
						
				cimy_save_field('add', $wpdb_fields_table, $data);
				//-------------
				
				$res = set_cimyFieldValue($current_user->ID, $field_name, $v);
			}
			//--------------------------------------------
		//}
		}
		//============================================
		// добавляем поле в профиль,если надо
		//$user_id = $_POST["user_id"];
		/*$i = 1;
		$field_name = 'FOTOFILE_'.$i;//$_POST["field_name"];
		//$new_value = $_POST["foto"];
		//$new_value = '';
		//print_r($new_value);
		
		$field_exist = true;
		while($field_exist) {
			$field_name = 'FOTOFILE_'.$i;
			$sql = "SELECT ID FROM $wpdb_fields_table WHERE NAME='$field_name'";
			$field_exist = $wpdb->get_var($sql);
			if(!$field_exist) break;
			$i++;
		}
		$field_name = 'FOTOFILE_'.$i;
		*/
		
		//echo $res.'============';
		//print_r($res);
		// сохраняем картинку 
	} else 
		return false;
}

//--------------заказы на оформлении в админке----------------------------------
function _WP_TEMPLATE_action_add_control_panel(){
	add_submenu_page("edit.php?post_type=cart", "На оформлении", "На оформлении", 10, "dh_cart_review", "dh_control_panel_cart_review_page");
}
function dh_control_panel_cart_review_page(){
	include('admin-cart-review.php');
}