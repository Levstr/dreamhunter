<?php
require_once dirname(__FILE__)."/functions-actions.php";
require_once dirname(__FILE__)."/functions-filters.php";
require_once dirname(__FILE__)."/functions-widgets.php";
require_once dirname(__FILE__)."/functions-shortcodes.php";
require_once dirname(__FILE__)."/functions-adminpages.php";

require_once dirname(__FILE__)."/class-price.php";

/* FUNCTIONS -------------------------------------------------------------------------------------------------------------- */

if(!function_exists('show_breadcrumb_nav')){
  function show_breadcrumb_nav() {
    if(is_home() || is_front_page()) return;
    $delim = " &rarr; ";

                                              echo "<a href='/'>Главная</a>$delim";
    if(wpsc_have_breadcrumbs())
      while(wpsc_have_breadcrumbs()){
        wpsc_the_breadcrumb();
        if(wpsc_breadcrumb_url())             echo "<a href='".wpsc_breadcrumb_url()."'>".trim(wpsc_breadcrumb_name())."</a>$delim";
        else                                  echo wpsc_breadcrumb_name();
      }
    elseif(is_category())                     single_cat_title();
    elseif(is_single())                       the_category(', ')._e($delim).the_title();
    elseif(is_page() && $post->post_parent)   echo get_the_title($post->post_parent).$delim.the_title();
    elseif(is_page())                         echo the_title();
    elseif(is_search())                       echo 'Поиск: '.the_search_query();
  }
}
function get_home_gallery_images($dir){
  $upload_dir = wp_upload_dir();
  foreach(get_directory_list($upload_dir['basedir']."/".$dir) as $img) {?>
    <li><a href="#" title=""><img src="<?php echo $upload_dir['baseurl']."/$dir/$img"?>" alt="" /></a></li>
<?php
  }
}
function get_directory_list($dir){
  $res = array();
  $h = opendir($dir); while($file=readdir($h)) if($file[0]!=".") $res[]=$file; closedir($h);
  return $res;
}

class _WP_TEMPLATE_Walker extends Walker_Nav_Menu
{
  function end_el(&$output, $category, $depth, $args) {
    $output = preg_replace("/(<\/a>)$/", "<div class='num'>".esc_html(with_zero($category->ID))."</div>$1", &$output);
  }
}
function with_zero($num){
  if($num<10) $num = "0".$num;
  return $num;
}
/* ADDITIONAL WPSC -------------------------------------------------------------------------------------------------------- */

function wpsc_category_image_url($category_id){
  global $wpdb;
  return htmlspecialchars("index.php?wpsc_request_image=true&category_id=".$category_id);
}

function wpsc_product_categories(){
  global $wpsc_query, $wpdb;
  $categories_all = $wpdb->get_results("SELECT * FROM `".WPSC_TABLE_PRODUCT_CATEGORIES."` WHERE `active` IN ('1') AND `group_id`=1 ORDER BY `name` ASC", ARRAY_A);
  $product_categories_ids =  $wpdb->get_results("SELECT `category_id` FROM `".WPSC_TABLE_ITEM_CATEGORY_ASSOC."` WHERE `product_id` IN ('{$wpsc_query->product['id']}')", ARRAY_A);
  $text = "";
  foreach($categories_all as $category)
    foreach($product_categories_ids as $category_id)
      if($category_id['category_id'] == $category['id']) {
        $text .= "<a href='/catalog/".$category['nice-name']."/'><small>".with_zero($category['id'])."</small>".$category['name']."</a>";
        break;
      }
  $text .= "</p>";
  return $text;
}

function gold_shpcrt_search_sql(){
}

function getlinks($id) {
	$link_obj = get_bookmark($id);
	
	return '<a href="'.$link_obj->link_url.'">'.$link_obj->link_name.'</a>';
}


// корзино---------------
function get_last_cart_tours() {
	global $current_user, $wpdb;
	
	$res = '<table>';
	$return = 0;
	if($current_user->ID) {
		$cart_inf = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wp_user_tours` WHERE cart_id=(select cart_id from wp_user_cart_tours where status=0 and user_id=".$current_user->ID.")"), ARRAY_A);
		if(count($cart_inf)) {
			$return = 1;
			foreach($cart_inf as $tour_r) {
				$res .= '<tr><td>'.$tour_r['tour_id'].'</td></tr>';
			}
		}
	}
	$res .= '</table>';
	echo $res;
	
	return $return;
}

function send_admin_cart_inf() {
	global $current_user, $wpdb;
	
	$return = 0;
	if($current_user->ID && isset($_POST['cart_send']) && $_POST['cart_send']) {
		// запрос одмину и смена статуса корзине
		$cart_num = $wpdb->get_var($wpdb->prepare("SELECT cart_id FROM `wp_user_cart_tours` WHERE status=0 and user_id=".$current_user->ID));
		if($cart_num) {
			$cart_inf = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wp_user_tours` WHERE cart_id='".$cart_num."'"), ARRAY_A);
			if(count($cart_inf)) {
				$res = '<table>';
				foreach($cart_inf as $tour_r) {
					$res .= '<tr><td>'.$tour_r['tour_id'].'</td></tr>';
				}
				$res .= '</table>';
			

				$admin_email = get_option('admin_email');
				$subject = 'Запрос на покупку туров';
				$message = 'Запрос на покупку туров, пользователь:'.$current_user->user_lastname.' '.$current_user->user_firstname.' ('.$current_user->display_name.')<br>'.
							'e-mail: '.$current_user->user_email.'<br>'.
							'Список туров:'.$res;
				if(wp_mail( $admin_email, $subject, $message)) {
					$wpdb->update( 'wp_user_cart_tours', array( 'status' => '1' ), array( 'cart_id' => $cart_num ), array( '%d' ), array( '%d' ) );
					$return = 1;
				} 
			}
		}
	}
	return $return;
}
// /- корзино---------------

function dreamhunter_comment ($comment, $args, $depth) {
	global $show_reply;
   $GLOBALS['comment'] = $comment; 
   ?>
   <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
     <div id="comment-<?php comment_ID(); ?>" class="single_comment">
	  <div class="comment_avatar">
		<?php echo get_avatar($comment,$size='24',$default='<path_to_url>' ); ?>
	  </div>
	  <div class="comment_inf">
		  <div class="comment-author vcard">
			 <?php //the_author_link($comment->comment_ID); 
				//echo get_the_author();
				$user_inf = get_userdata($comment->user_id);
				if($user_inf->user_url) {
					echo '<a href="'.$user_inf->user_url.'">'.$user_inf->user_nicename.'</a>';
				} else {
					echo $user_inf->user_nicename;
				}
			 ?>
			 <?php //printf(__('<span class="fn">%s</span>'), get_comment_author_link()) ?>
			 
			 <span class="comment-meta commentmetadata">
			<?php /*<a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>">*/ ?>
			<?php printf(__('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?>
			<?php edit_comment_link(__('(Edit)'),'  ','') ?></span>
		  </div>
		  <?php if ($comment->comment_approved == '0') : ?>
			 <em><?php _e('Your comment is awaiting moderation.') ?></em>
			 <br />
		  <?php endif; ?>

		  <?php comment_text() ?>

		  <?php if($show_reply) { ?>
		  <div class="reply">
			 <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
		  </div>
		  <?php } ?>
	  </div>
	  <div class="clear"></div>
     </div>
<?php
        }

function get_wants_list ($post_id) {
	global $current_user, $wpdb;
	
	$inf = $wpdb->get_results($wpdb->prepare("SELECT user_nicename, user_url FROM `wp_users` WHERE ID in(select post_author from wp_posts where post_type='request' and (select meta_value from wp_postmeta where meta_key='deal_DEALID' and post_id=wp_posts.ID)=".$post_id.")"), ARRAY_A);
	
	//echo "SELECT user_nicename, user_url FROM `wp_users` WHERE ID in(select post_author from wp_posts where post_type='request' and (select meta_value from wp_postmeta where meta_key='deal_DEALID' and post_id=wp_posts.ID)=".$post_id.")";
	
	$res_str = 'Уже хотят ехать: ';
	if(count($inf)) {
		foreach ($inf as $row){
			$res_str .= '<a href="'.$row['user_url'].'">'.$row['user_nicename'].'</a>,';
		}
		$res_str = substr($res_str,0, (strlen($res_str)-1));		
	} else {
		$res_str = 'Буду первым путешественником';
	}
	
	return $res_str;
}

function get_currentuserin_wants_list ($post_id) {
	global $current_user, $wpdb;
	
	get_currentuserinfo();
	if($current_user->ID) {
		$inf = $wpdb->get_var($wpdb->prepare("SELECT count(ID) FROM `wp_users` WHERE ID in(select post_author from wp_posts where post_type='request' and (select meta_value from wp_postmeta where meta_key='deal_DEALID' and post_id=wp_posts.ID)=".$post_id.") and ID = ".$current_user->ID));
		
		if($inf) {
			return true;
		}
	}
	return false;
}

function dh_show_admin_panel_filter () {
	global $user_ID;
	$user_info = get_userdata($user_ID);
	if($user_info->user_level!=10) {
		add_action( 'show_admin_bar', '__return_false' );
		echo '<style type="text/css">
				html { margin-top: 0px !important; }
				* html body { margin-top: 0px !important; }
			</style>';
	}
}

// добавлен ли уже данный тур в открытую корзину данного юзера
function is_tour_in_cart($tour_id){
	global $wpdb, $current_user;

    get_currentuserinfo();
	if($current_user->ID) {
		//---проверка----
		$can_add = true;
		$can_add_tour = true;
		$post_list = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_status='publish' and post_type='cart' and post_author=".$current_user->ID));
		if(count($post_list)) {
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
		return $can_add_tour;
	}
	return false;
}

function get_user_last_foto() {
	global $wpdb, $wpdb_data_table, $wpdb_fields_table, $current_user, $cuef_upload_path;
	
	get_currentuserinfo();
	
	$sql = "select * from wp_posts where post_type = 'attachment' and post_status = 'inherit' and post_parent in (select ID from wp_posts as wpp where post_type = 'profile' and post_status = 'publish' ) order by post_date desc limit 30";
	$attach_list = $wpdb->get_results($wpdb->prepare($sql), ARRAY_A);
	
	$text = '';
	if(count($attach_list)) {
		$text = '<table><tr>';
		$k=0;
		foreach ($attach_list as $image) {
			$img = '<a href="'. wp_get_attachment_url( $image['ID'] ).'" class="cboxElement">'.wp_get_attachment_image( $image['ID'], array(null, 59)).'</a>'; //colorbox-278
			if($img) {
				if($k==0) {
					$text .= '<td>'.$img;
					$k++;
				} elseif($k==1) {
					$text .= $img.'</td>';
					$k=0;
				}
			}
		}
		if($k==1) {
			$text .= '</td>';
		}
		$text .= '</tr></table>';
	}
	return $text;
}

function get_user_foto() {
	global $wpdb, $wpdb_data_table, $wpdb_fields_table, $current_user, $cuef_upload_path;
	
	get_currentuserinfo();
	
	$sql = "select * from wp_posts where post_type = 'attachment' and post_status = 'inherit' and post_parent in (select ID from wp_posts as wpp where post_type = 'profile' and post_status = 'publish' ) order by post_date";
	$attach_list = $wpdb->get_results($wpdb->prepare($sql), ARRAY_A);
	
	$text = '';
	if(count($attach_list)) {
		$text = '<table><tr>';
		foreach ($attach_list as $image) {
			$img = '<a href="#">'.wp_get_attachment_image( $image['ID'], array(null, 119)).'</a>'; 
			if($img)
				$text .= '<tr><td>'.$img.'</td></tr>';
		}
		if($k==1) {
			$text .= '</td>';
		}
		$text .= '</tr></table>';
	}
	return $text;
	/*
	*/
}

function set_cart_status_exec() {
	global $wpdb, $current_user;	
	get_currentuserinfo();
	
	if($current_user->ID) {
		$cart_id = (int)($_POST['cart_open']);
		
		$is_open_cart = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_status='publish' and post_type='cart' and ID='".$cart_id."' and post_author=".$current_user->ID));
		if($is_open_cart) {
				//-------------
				$post_terms =  wp_get_post_terms($is_open_cart, 'cart_status');
				if(!count($post_terms)) { // по идее, окрытая корзина не принадлежит ни одной категории
					//add_post_meta($is_open_cart, 'cart_status', 27);// 27 - id статуса корзины "на рассмотрении"
					wp_set_object_terms( $is_open_cart, array(27), 'cart_status', true );
					return true;
				}
				//-------------
				return false;
		}
		return false;
		/*
		$admin_email = get_option('admin_email');
				$subject = 'Запрос на покупку туров';
				$message = 'Запрос на покупку туров, пользователь:'.$current_user->user_lastname.' '.$current_user->user_firstname.' ('.$current_user->display_name.')<br>'.
							'e-mail: '.$current_user->user_email.'<br>'.
							'Список туров:'.$res;
				if(wp_mail( $admin_email, $subject, $message)) {
					$wpdb->update( 'wp_user_cart_tours', array( 'status' => '1' ), array( 'cart_id' => $cart_num ), array( '%d' ), array( '%d' ) );
					$return = 1;
				} 
		*/
	}
	return false;
}

function filepath_upload($v) {
	return  get_bloginfo('url').'/wp-content/uploads/'.basename($v['file']);
}
/*
function buttonDisabled ($html) {
	return '<span class="button_disabled">'.$html.'</span>';
}*/

function buttonClassDisabled () {
	$current_user = wp_get_current_user();
	$class_disabled = '';
	if(!$current_user->ID) {
		$class_disabled = ' button_disabled ';
	}
	return $class_disabled;
}
