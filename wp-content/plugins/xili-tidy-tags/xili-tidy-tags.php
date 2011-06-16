<?php
/*
Plugin Name: xili-tidy-tags
Plugin URI: http://dev.xiligroup.com/xili-tidy-tags/
Description: xili-tidy-tags is a tool for grouping tags by language or semantic group. Initially developed to enrich xili-language plugin and usable in all sites (CMS).
Author: dev.xiligroup.com - MS
Version: 1.5.4
Author URI: http://dev.xiligroup.com
*/

# 1.5.4 - 110320 - 2 new template tags, posts series of group tag and examples, support email metabox
# 1.5.3.1 - 110209 - add option to desactivate javascript list
# 1.5.3 - 101217 - add options to select unchecked tags only and to exclude one group and include unchecked.
# 1.5.2 - 101205 - some cache issues fixed
# 1.5.1 - 101128 - popup for groups in widget
# 1.5.0 - 101107 - add DOM datatables js library - widget as extends class - fixe cache pb with get_terms - contextual help
# 1.4.3 - 101007 - fixes add_action for admin taxonomies of custom post type
# 1.4.2 - 100930 - fixes "warning" when xili-language is not present and no groups created ar first activation. More comments in source
# 1.4.1 - 100728 - fixes before published as current version
# 1.4.0 - 100727 - some source lines rewritten, new messages window, capabilities setting added in settings
# 1.3.4 - 100424 - special add for wpmu as superadmin
# 1.3.3 - 100416 - Compatible with xili-language 1.5.2
# 1.3.2 - 100411 - Optimizations for WMPU 3.0
# 1.3.1 - 100407 - minor modifications for WPMU 3.0
# 1.3.0 - 100218 - add sub-selection by tags belonging to a group (suggestion of David) - Now uses Walker class to sort groups in UI.
# 1.2.1 - 091129 - fix quick-edit tag error (thanks to zarban)
# 1.2 - 091122 - fix subselection sort in get_terms_of_groups (thanks to zarban)
# 1.1 - 091012 - new xili_the_tags() for the loop
# 1.0.1 - 090718 - new icon in admin menu - some fixes in php code for some servers (Thanks to Giannis)
# 1.0   - 090611 - add shortcode to include a cloud of a group of tags inside a post - compatible with WP 2.8
# 0.9.6 - 090602 <- # 0.8.1 - 090331 - see history in readme.txt -
# first public release 090329 - 0.8.0 - beta version

# This plugin is free software; you can redistribute it and/or
# modify it under the terms of the GNU Lesser General Public
# License as published by the Free Software Foundation; either
# version 2.1 of the License, or (at your option) any later version.
#
# This plugin is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
# Lesser General Public License for more details.
#
# You should have received a copy of the GNU Lesser General Public
# License along with this plugin; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

define('XILITIDYTAGS_VER','1.5.4'); /* used in admin UI */

class xili_tidy_tags {
	
	//var $is_metabox = false; /* for tests of special box in post */
	//var $is_post_ajax = false; /* for tests using ajax in UI */
	var $langgroupid = 0; /* group of langs*/
	
	var $subselect = 0; /* selected parent group */
	var $fromgroupselect = 0; /* group in which belong tags */
	var $groupexclude = false; /* exclude group from the query */
	var $uncheckedtags = false; /* exclude uncheckedtags from the query if false */
	var $onlyuncheckedtags = false; /* for general query to see only unchecked */
	var $onlyparent = false; /* when a group is parent, show all tags of childs of the group */

	function xili_tidy_tags () {
				
		/* activated when first activation of plug or automatic upgrade */
		register_activation_hook(__FILE__,array(&$this,'xili_tidy_tags_activate'));
	
		/*get current settings - name of taxonomy - name of query-tag - 0.9.8 new taxonomy taxolangsgroup */
		$this->xili_tidy_tags_activate();
		if ($this->xili_settings['version'] < '0.5') { /* updating value by default 0.9.5 */
			$this->xili_settings['version'] = '0.5';
		}
		if ($this->xili_settings['version'] == '0.5') {
			$this->xili_settings['editor_caps'] = 'no_caps' ;
			$this->xili_settings['version'] = '0.6';
		}
		if ($this->xili_settings['version'] == '0.6') {
			$this->xili_settings['datatable_js'] = '' ; // 1.5.3.1
			$this->xili_settings['version'] = '0.7';
		}	
		update_option('xili_tidy_tags_settings', $this->xili_settings);	
			
		define('TAXOTIDYTAGS',$this->xili_settings['taxonomy']);
		define('LANGSTAGSGROUPSLUG',$this->xili_settings['tidylangsgroup']);
		define('LANGSTAGSGROUPNAME',$this->xili_settings['tidylangsgroupname']);
		//$res = is_term (LANGSTAGSGROUPNAME,TAXOTIDYTAGS);
		//if ($res) $this->langgroupid = $res ['term_id'];
		
		/* hooks */	
		/* admin settings UI*/
		add_action('plugins_loaded', array(&$this, 'init_plugin')); /* text domain and caps of admin*/
		add_filter('plugin_action_links',  array(&$this,'xili_filter_plugin_actions'), 10, 2);
		/* admin */
		add_action('admin_init', array(&$this,'admin_init') ); // 1.5.0
		add_action('admin_menu', array(&$this,'xili_add_pages'));
		/* new since 3.0 action name changed */
		add_action('post_tag_add_form', array(&$this,'xili_add_tag_form')); /* to choose a group for a new tag */
		add_action('post_tag_edit_form', array(&$this,'xili_edit_tag_form'));
		add_action( 'contextual_help', array(&$this,'add_help_text'), 10, 3 ); /* 1.5.0 */
		
		add_action('created_term', array(&$this,'xili_created_term'),10,2); /* a new term was created */
		add_action('edited_term', array(&$this,'xili_created_term'),10,2);
		
		/* actions for post and page admin UI */
		add_action('save_post', array(&$this,'xili_tags_grouping'),50); /* to affect tags to lang of saved post */
		


	}
	
	function xili_tidy_tags_activate() {
		
		$this->xili_settings = get_option('xili_tidy_tags_settings');
		if ( empty( $this->xili_settings ) ) {
			$this->xili_settings = array(
			    'taxonomy'			=> 'xili_tidy_tags',
			    'tidylangsgroup'	=> 'tidy-languages-group',
			    'tidylangsgroupname' => 'All lang.',
			    'editor_caps'		=> 'no_caps',
			    'datatable_js'		=> '',
			    'version' 			=> '0.7'
		    );
			update_option('xili_tidy_tags_settings', $this->xili_settings);			
		}	
	}	
	
	function init_plugin() { 
		/*multilingual for admin pages and menu*/
		load_plugin_textdomain('xili_tidy_tags',PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)),dirname(plugin_basename(__FILE__)));
		/* add new taxonomy in available taxonomies - move here for wpmu and wp 3.0*/
		register_taxonomy( TAXOTIDYTAGS, 'term',array('hierarchical' => true, 'label'=>false, 'rewrite' => false, 'update_count_callback' => '', 'show_ui' => false ) );
		$res = is_term (LANGSTAGSGROUPNAME,TAXOTIDYTAGS);
		if ($res) $this->langgroupid = $res ['term_id'];
		
		/* since 0.9.5 new default caps for admnistrator - updated 1.4.0 */
		if (is_admin()) {
			global $wp_roles;
			
			if (current_user_can ('activate_plugins')) {
				$wp_roles->add_cap ('administrator','xili_tidy_editor_set');
				$wp_roles->add_cap ('administrator','xili_tidy_editor_group');  
			} elseif (current_user_can ('edit_others_pages')) {
				switch ($this->xili_settings['editor_caps']) {
					case 'caps_grouping';
						$wp_roles->remove_cap ('editor','xili_tidy_editor_set');
						$wp_roles->add_cap ('editor','xili_tidy_editor_group');
						break;
					case 'caps_setting_grouping';
						$wp_roles->add_cap ('editor','xili_tidy_editor_set');
						$wp_roles->add_cap ('editor','xili_tidy_editor_group');  
						break;
					case 'no_caps';
						$wp_roles->remove_cap ('editor','xili_tidy_editor_set');
						$wp_roles->remove_cap ('editor','xili_tidy_editor_group');
						break;
				}
			}
		}
	}
	
	/**
	 * Add action link(s) to plugins page
	 * 
	 * @since 0.8.0
	 * @author MS
	 * @copyright Dion Hulse, http://dd32.id.au/wordpress-plugins/?configure-link and scripts@schloebe.de
	 */
	function xili_filter_plugin_actions($links, $file){
		static $this_plugin;

		if( !$this_plugin ) $this_plugin = plugin_basename(__FILE__);

		if( $file == $this_plugin ){
			$settings_link = '<a href="admin.php?page=xili_tidy_tags_settings">' . __('Settings') . '</a>';
			$links = array_merge( array($settings_link), $links); // before other links
		}
		return $links;
	}
	
	/**
	 * add in new tag form to choose a group for a new tag
	 *
	 * @since 0.8.0
	 * 
	 *
	 */
	function xili_add_tag_form() { 
		$listgroups = get_terms(TAXOTIDYTAGS, array('hide_empty' => false,'get'=>'all'));
		$checkline ='';
		foreach ($listgroups as $group) {
			$checkline .= '<input type="checkbox" id="group-'.$group->term_id.'" name="group-'.$group->term_id.'" value="'.$group->term_id.'" />'.$group->name.'&nbsp;-&nbsp;';
		}
		$checkline .='<br /><br /><small>'.__('© by xili-tidy-tags.','xili_tidy_tags').'</small>';
			echo '<div style="margin:2px; padding:3px; border:1px solid #ccc;"><label>'.__('Tags groups','xili_tidy_tags').':</label><br />'.$checkline.'</div>';	
	}
	
	/**
	 * add in edit tag form to choose a group for a edited tag
	 *
	 * @since 0.8.0
	 * 
	 *
	 */
	function xili_edit_tag_form($tag) { 
		$listgroups = get_terms(TAXOTIDYTAGS, array('hide_empty' => false,'get'=>'all'));
		$checkline ='';
		foreach ($listgroups as $group) {
			/* add checked="checked" */
			if (is_object_in_term($tag->term_id,TAXOTIDYTAGS,(int) $group->term_id)) {
					$checked = 'checked="checked"';
				} else {
					$checked = '';
				}
			$checkline .= '<input type="checkbox" name="group-'.$group->term_id.'" id="group-'.$group->term_id.'" value="'.$group->term_id.'" '.$checked.' />'.$group->name.'&nbsp;-&nbsp;';
		}
		$checkline .='<br /><br /><small>'.__('© by xili-tidy-tags.','xili_tidy_tags').'</small>';
			echo '<div style="margin:2px; padding:3px; border:1px solid #ccc;"><label>'.__('Tags groups','xili_tidy_tags').':<br /></label><br />'.$checkline.'</div>';	
	}
	
	/**
	 * a new term was created
	 *
	 * @since 0.8.0
	 * @updated 1.2.1
	 *
	 */
	function xili_created_term ($term_id, $tt_id) {
		/* check if it is a term from 'post_tag' */
		if (!isset($_POST['_inline_edit'])) { /* to avoid delete relationship when in quick_edit (edit-tags.php) */
			
			$term = get_term( (int) $term_id, 'post_tag' ); 
			if ( $term ) {
				$listgroups = get_terms(TAXOTIDYTAGS, array('hide_empty' => false,'get'=>'all'));
				$groupids = array();
				foreach ($listgroups as $group) {
					$idcheck = 'group-'.$group->term_id;
					if (isset($_POST[$idcheck])) {
						 	
							$groupids[]= (int) $group->term_id;
					}	
				}
				wp_set_object_terms( $term_id, $groupids, TAXOTIDYTAGS, false );
			}
		}	
	}	
	
	/**
	 * in post edit UI if new term was created - give it a group 
	 *
	 * @since 0.9.4
	 * 
	 *
	 */
	function xili_tags_grouping ($post_ID) {
		if (!class_exists('xili_language')) return ; /* only used if present */
			$list_tags = wp_get_object_terms($post_ID, 'post_tag');
			if ( !$list_tags )
				return ; /* no tag*/
			$post_curlang = get_cur_language($post_ID);
			//$res = is_term (LANGSTAGSGROUPNAME,TAXOTIDYTAGS);
			//if ($res) $langgroupid = $res ['term_id'];
			$listlanggroups = get_terms(TAXOTIDYTAGS, array('hide_empty' => false,'parent' => $this->langgroupid));
			if ($listlanggroups) {
				foreach ($listlanggroups as $curgroup) { 
					$langsgroup[] = $curgroup->term_id;
				}
				$langsgroup[] = $this->langgroupid; /* add group parent */
				foreach ( $list_tags as $tag ) { /* test if the tag is owned by a group */ 
					$nbchecked = false;
					foreach ($langsgroup as $onelanggroup) {
						if (is_object_in_term($tag->term_id,TAXOTIDYTAGS,$onelanggroup)) {
							$nbchecked = true ;
						}
					}
					if ($nbchecked == false) { 
						if ($post_curlang == false) { /* add to group parent */
						    wp_set_object_terms((int) $tag->term_id, (int) $this->langgroupid, TAXOTIDYTAGS,false);
						} else {
							$res = is_term ($post_curlang,TAXOTIDYTAGS);
							wp_set_object_terms((int) $tag->term_id, (int) $res ['term_id'], TAXOTIDYTAGS,false);
						}
					}
				}
			}
	}
	
	/**
	 * @since 1.5.0
	 */
	function admin_enqueue_scripts() {
		wp_enqueue_script( 'datatables', WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)).'/js/jquery.dataTables.min.js' , array( 'jquery' ), '1.7.4', true );
		wp_enqueue_script( 'datatablesnodes', WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)).'/js/jquery.dataTables-Nodes.js' , array( 'datatables' ), '1.7.4', true );
		//wp_enqueue_script( 'tohide', WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)).'/js/tohide.js' , array( 'datatables' ), '1.7.4', false );
		
	}
	
	function admin_enqueue_styles() {
		wp_enqueue_style('table_style');
        //wp_enqueue_style('paging_style');
	}
	
	function admin_init()
    {
        /* Register our script. */
        wp_register_script('datatables', WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)).'/js/jquery.dataTables.min.js' ); // 1.5.3.1 - min
        wp_register_style('table_style', WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)).'/css/xtt_table.css');
        //wp_register_style('paging_style', WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)).'/css/demo_page.css');
    }
	
	/**
	 * add admin menu and associated pages of admin UI tools page
	 *
	 * @since 0.8.0
	 * @updated 0.9.5 - menu without repeat of main title, levels with new caps set by plugin array(&$this,'top_tidy_menu_title')
	 * @updated 1.0.1 - favicon.ico for menu title - 1.3.4 Special window for Super User
	 */
	function xili_add_pages() {
			$this->thehook0 = add_object_page(__('Tags groups','xili_tidy_tags'), __('Tidy Tags','xili_tidy_tags'), -1, __FILE__,array(&$this,'top_tidy_menu_title'), WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)).'/xili-favicon.ico');	 	
		  	$this->thehooka = add_submenu_page(__FILE__, __('Tags groups','xili_tidy_tags'),__('Info for SuperAdmin','xili_tidy_tags'),-1, __FILE__, array(&$this,'top_tidy_menu_title'));
		  	$this->thehook = add_submenu_page(__FILE__, __('Tags groups','xili_tidy_tags'),__('Tidy tags settings','xili_tidy_tags'), 'xili_tidy_editor_set', 'xili_tidy_tags_settings', array(&$this,'xili_tidy_tags_settings'));
		  	add_action('load-'.$this->thehook, array(&$this,'on_load_page'));
		 	/* sub-page */
		 	$this->thehook2 = add_submenu_page(__FILE__, __('Tidy tags','xili_tidy_tags'), __('Tidy tags assign','xili_tidy_tags'), 'xili_tidy_editor_group', 'xili_tidy_tags_assign', array(&$this,'xili_tidy_tags_assign'));
		 	add_action('load-'.$this->thehook2, array(&$this,'on_load_page2'));
		 	
		 	add_action( 'admin_print_scripts-'.$this->thehook2, array(&$this,'admin_enqueue_scripts') );
		 	add_action( 'admin_print_styles-'.$this->thehook2, array(&$this,'admin_enqueue_styles') ); 
		 	// 1.5
 	}
 	
 	function top_tidy_menu_title () { // again with wp3.0 instead '' call v1.3.4
 		?>
 		<div class='wrap'>
		<h2><?php _e("Tidy tags settings","xili_tidy_tags"); ?></h2>
		<h4><?php _e("This window is reserved for future settings in multisite mode (wpmu) for administrator like SuperAdmin...","xili_tidy_tags"); ?></h4>
		<p><?php _e("Link to set tidy tags in current site","xili_tidy_tags"); ?>: <a href="<?php echo "admin.php?page=xili_tidy_tags_settings"; ?>" title="xili-tidy-tags settings" ><?php _e("To create groups of tags","xili_tidy_tags"); ?></a></p>
		<p><?php _e("Link to assign tidy tags in current site","xili_tidy_tags"); ?>: <a href="<?php echo "admin.php?page=xili_tidy_tags_assign"; ?>" title="xili-tidy-tags settings"><?php _e("To assign a group to tags","xili_tidy_tags"); ?></a></p>
		<h4><a href="http://dev.xiligroup.com/xili-tidy-tags" title="Plugin page and docs" target="_blank" style="text-decoration:none" ><img style="vertical-align:middle" src="<?php echo WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)).'/xilitidy-logo-32.png'; ?>" alt="xili-tidy-tags logo"/>  xili-tidy-tags</a> - © <a href="http://dev.xiligroup.com" target="_blank" title="<?php _e('Author'); ?>" >xiligroup.com</a>™ - msc 2009-11 - v. <?php echo XILITIDYTAGS_VER; ?></h4>
		</div>
		<?php
 	}

	
	
	function on_load_page() {
			wp_enqueue_script('common');
			wp_enqueue_script('wp-lists');
			wp_enqueue_script('postbox');
			add_meta_box('xili_tidy_tags-sidebox-1', __('Message','xili_tidy_tags'), array(&$this,'on_sidebox_1_content'), $this->thehook , 'side', 'core');
			add_meta_box('xili_tidy_tags-sidebox-3', __('Info','xili_tidy_tags'), array(&$this,'on_sidebox_2_content'), $this->thehook , 'side', 'core');
			add_meta_box('xili_tidy_tags-sidebox-mail', __('Mail & Support','xili_tidy_tags'), array(&$this,'on_sidebox_mail_content'), $this->thehook , 'normal', 'low');
			if (current_user_can( 'administrator'))
				add_meta_box('xili_tidy_tags-sidebox-4', __('Capabilities','xili_tidy_tags'), array(&$this,'on_sidebox_4_content'), $this->thehook , 'side', 'core');
			
	}
	
	function on_load_page2() {
			wp_enqueue_script('common');
			wp_enqueue_script('wp-lists');
			wp_enqueue_script('postbox');
			add_meta_box('xili_tidy_tags-sidebox-1', __('Message','xili_tidy_tags'), array(&$this,'on_sidebox_1_content'), $this->thehook2 , 'side', 'core');
			add_meta_box('xili_tidy_tags-sidebox-3', __('Info','xili_tidy_tags'), array(&$this,'on_sidebox_2_content'), $this->thehook2 , 'side', 'core');
	}
	
	
	/**
	 * private functions for dictionary_settings
	 *
	 * @since 0.8.0
	 *
	 * fill the content of the boxes (right side and normal)
	 * 
	 */
	
	function  on_sidebox_1_content($data=array()) { 
		extract($data);
		?>
	 	<h4><?php _e('Note:','xili_tidy_tags') ?></h4>
		<p><?php echo $message;?></p>
		<?php
	}
	
	function  on_sidebox_2_content($data=array()) {  
	 	extract($data);
	 	if ($xili_tidy_tags_page == 'settings') { 
	 		echo '<p style="margin:2px; padding:3px; border:1px solid #ccc;">'.__('On this page, the tags groups are defined. The special groups for xili-language plugin are importable.<br /> For debug, some technical infos are displayed in the tables or boxes.<br />','xili_tidy_tags').'</p>';
	 	} elseif ($xili_tidy_tags_page == 'assign') {	
	 		echo '<p style="margin:2px; padding:3px; border:1px solid #ccc;">'.__('On this page, in a oneshot way, it is possible to assign the tags to one or more groups defined on the other page of <i>xili-tidy-tags</i> plugin.','xili_tidy_tags').'</p>';	
	 	}	 ?>
		<p><?php _e('<b>xili-tidy-tags</b> is a tool for grouping tags by language or semantic group. Initially developed to enrich multilingual website powered by xili-language plugin.','xili_tidy_tags') ?></p>
		<?php
	}
	/*
	 * Admin capabilities setting box
	 * @since 0.9.5
	 *
	 * @updated 1.4.0
	 * Only visible if admin (cap : update_plugins)
	 */
	function  on_sidebox_4_content($data=array()) {
			$editor_set = $this->xili_settings['editor_caps'];
			
			if ( $editor_set == "caps_setting_grouping"  )  
	  		{ 
	  			$selected3 = ' selected = "selected"'; 
	  		} elseif ( $editor_set == "caps_grouping" ) {
	  			$selected2 = ' selected = "selected"';
			}
		  						
	 	?>
	 	<div style="margin:2px; padding:3px; border:1px solid #ccc;">
		<p><?php _e('Here, as admin, set capabilities of the editor:','xili_tidy_tags') ?></p>
		<select name="editor_caps" id="editor_caps" style="width:80%;">
  				<option value="no_caps" ><?php _e('no capability','xili_tidy_tags'); ?></option>
  				<option value="caps_grouping" <?php echo $selected2;?>><?php _e('Grouping','xili_tidy_tags');  ?></option>
  				<option value="caps_setting_grouping" <?php echo $selected3;?>><?php _e('Setting and grouping','xili_tidy_tags');?></option>
  		</select><br /><br />
  		<label for="datatable_js"><?php _e('Disable Datatable javascript','xili_tidy_tags') ?> : <input id="datatable_js" name="datatable_js" type="checkbox" value="disable" <?php if($this->xili_settings['datatable_js'] == 'disable') echo 'checked="checked"' ?> /></label>
  		
  		<?php
  		echo'<p class="submit"><input type="submit" name="editor_caps_submit" value="'.__('Set &raquo;','xili_tidy_tags').'" /></p></div>';	
	}
	/**
	 * Get_terms without annoying cache
	 *
	 * @since 1.5.0
	 */
	function no_cache_get_terms ($taxonomy, $args) {
		global $wpdb;
		$defaults = array('orderby' => 'name', 'order' => 'ASC',
		'hide_empty' => true, 'exclude' => array(), 'exclude_tree' => array(), 'include' => array(),
		'number' => '', 'fields' => 'all', 'slug' => '', 'parent' => '',
		'hierarchical' => true, 'child_of' => 0, 'get' => '', 'name__like' => '',
		'pad_counts' => false, 'offset' => '', 'search' => '');
		$args = wp_parse_args( $args, $defaults );
		extract($args, EXTR_SKIP);
		$orderby = 't.name';
		if ( !empty($orderby) )
			$orderby = "ORDER BY $orderby";
		else
			$order = '';
		
		if ( '' !== $parent ) {
			$parent = (int) $parent;
			$where .= " AND tt.parent = '$parent'";
		}	
			
		$selects = array();
		switch ( $fields ) {
	 		case 'all':
	 			$selects = array('t.*', 'tt.*');
	 			break;
	 		case 'ids':
			case 'id=>parent':
	 			$selects = array('t.term_id', 'tt.parent', 'tt.count');
	 			break;
	 		case 'names':
	 			$selects = array('t.term_id', 'tt.parent', 'tt.count', 't.name');
	 			break;
	 		case 'count':
				$orderby = '';
				$order = '';
	 			$selects = array('COUNT(*)');
	 	}
	 	$select_this = implode(', ', apply_filters( 'get_terms_fields', $selects, $args ));
    	$query = "SELECT $select_this FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ('$taxonomy') $where $orderby $order $limit";

//echo $query ;
		$terms = $wpdb->get_results($query);
		
		return $terms;
	}
	
	/*
	 * Action's box
	 */
	function  on_sidebox_3_content($data=array()) { 
	 	extract($data);
	 	echo '<div style="margin:2px; padding:3px; border:1px solid #ccc;">';
	 	echo '<p>'.__('Add a tag\'s group for a chosen category','xili_tidy_tags').'</p>';
	 	/* build the selector of available categories */
	 	$categories = get_categories(array('get'=>'all')); /* even if cat is empty */
	 	echo '<select name="catsforgroup" id="catsforgroup" style="width:100%;">';
		  				echo '<option value="no" >'.__('choose a category','xili_tidy_tags').'</option>';
	 	foreach ($categories as $cat) {
	 		$catinside = is_term ($cat->slug,TAXOTIDYTAGS);
			if ($catinside == 0 && $cat->term_id != 1)
				echo '<option value="'.$cat->term_id.'" >'.$cat->name.'</option>';
		}
	 	echo '</select>';
	 	echo '<p>'.__('Choose a parent tag\'s group','xili_tidy_tags').'</p>';
	 	$listterms = get_terms(TAXOTIDYTAGS, array('hide_empty' => false));
		?>
		<select name="tags_parent" id="tags_parent" style="width:100%;">
  				<option value="no_parent" ><?php _e('no parent','xili_tidy_tags'); ?></option>
		<?php
		//$res = is_term (LANGSTAGSGROUPNAME,TAXOTIDYTAGS);
		//if ($res) $langgroupid = $res ['term_id'];
		foreach ($listterms as $curterm) {
			if ($curterm->parent == 0 && $curterm->term_id != $this->langgroupid)
				echo '<option value="'.$curterm->term_id.'" >'.$curterm->name.'</option>';
		} ?>
		</select>
		<br />
	    <?php
	 	echo '<p class="submit"><input type="submit" name="importacat" value="'.__('Add &raquo;','xili_tidy_tags').'" /></p>';
	 	echo '<p>'.__('See docs to set xili_tidy_tag_cloud function or widget to implement in theme…','xili_tidy_tags').'</p>';
	 	echo '</div>';
	 	echo '<div style="margin:2px; padding:3px; border:1px solid #ccc;">'; 
	 	if ( !class_exists('xili_language') ) { ?>
	 		<p class="submit"><?php _e('xili-language plugin is not activated.','xili_tidy_tags') ?> </p>
	 		<?php
	 	} else {
	 		
	 		$res = is_term (LANGSTAGSGROUPNAME,TAXOTIDYTAGS); //print_r($res); echo (is_array($res)) ? "arr" : '?' ; 
	 		if ( is_array($res) ) $childterms = $this->no_cache_get_terms(TAXOTIDYTAGS, array('hide_empty' => false, 'fields'=> 'all', 'parent' => $res['term_id'])); 
	 		//echo 'nb'.count($childterms) ; 
	 		if ($res && !empty($childterms)) {
		 		?>
				<p><?php _e('The group of languages is set for use with xili-language plugin.','xili_tidy_tags') ?> </p> 
				<?php
				
				if (current_user_can( 'administrator')) {
					$langinxtt = array();
					foreach ( $childterms as $childterm ) {
			 			$langinxtt[] = $childterm -> name ;
					}
					
					$langinxl = array();
					$listlanguages = get_terms(TAXONAME, array('hide_empty' => false));
					foreach ( $listlanguages as $language ) {
			 			$langinxl[] = $language -> name ;
					}
					//print_r( array_diff($langinxl, $langinxtt) );
			 		if ( array_diff( $langinxl, $langinxtt ) != array() ) { // since 1.5.0
			 		$s = implode( ', ', array_diff( $langinxl, $langinxtt ) );
			 		?>
						<p><?php printf(__('The group of languages in xili-language plugin has been changed.','xili_tidy_tags').' ( %s )', $s ); ?> </p> 
					<?php
					echo '<p class="submit">'.__('It is possible to update the group of languages.','xili_tidy_tags').'</p>';
			 		echo '<p class="submit"><input type="submit" name="importxililanguages" value="'.__('Update &raquo;','xili_tidy_tags').'" /></p>';
			 		}
			 		if ( array_diff( $langinxtt, $langinxl ) != array() ) {// ( count($langinxtt) != count($langinxl) )
			 		$s = implode( ', ', array_diff( $langinxtt, $langinxl ) ); 
			 			echo '<p class="submit">'.sprintf(__('One or more language(s) here are not present in active xili-language list.','xili_tidy_tags').' ( %s )', $s ).'</p>';
			 		}
				}
				
		 	} else { /* since 0.9.5 */
		 		if (current_user_can( 'administrator')) {
			 		$count_xl = wp_count_terms(TAXONAME); /* count a minima one language */
			 		if ( $count_xl > 0 ) { 
			 			echo '<p class="submit">'.__('It is possible to import the group of languages.','xili_tidy_tags').'</p>';
			 			echo '<p class="submit"><input type="submit" name="importxililanguages" value="'.__('Import…','xili_tidy_tags').'" /></p>';
			 			
			 		} else {
			 			echo '<p class="submit">'.__('Go to settings of xili-language plugin and add languages','xili_tidy_tags').'</p>';
			 		}
		 		} else {
		 			echo '<p class="submit">'.__('See administrator for language settings.','xili_tidy_tags').'</p>';
		 		}	
		 	}
	 	}
	 	echo '</div>';
	 	
	}
	
	function  on_normal_1_content($data=array()) {
	 	extract($data); ?>
		
					<table class="widefat">
						<thead>
						<tr>
						<th scope="col" style="text-align: center"><?php _e('ID','xili_tidy_tags') ?></th>
	        			<th scope="col"><?php _e('Name','xili_tidy_tags') ?></th>
	        			<th scope="col"><?php _e('Description','xili_tidy_tags') ?></th>
	        			<th scope="col"><?php _e('Group slug','xili_tidy_tags') ?></th>
	        			<th scope="col"><?php _e('Group taxo ID','xili_tidy_tags') ?></th>
	        			<th scope="col"><?php _e('Parent','xili_tidy_tags') ?></th>
	        			<th scope="col" width="90" style="text-align: center"><?php _e('Tags') ?></th>
	        			<th colspan="2" style="text-align: center"><?php _e('Action') ?></th>
						</tr>
						</thead>
						<tbody id="the-list">
							<?php $this->xili_tags_group_row(); /* the lines */ ?>
						</tbody>
					</table>
					
		<?php
	}
	
	function on_normal_2_content( $data=array() ) { 
		extract( $data ); /* form to add or edit group */
		?>
		
		<h2 id="addgroup" <?php if ($action=='delete') echo 'style="color:#FF1111;"'; ?>><?php _e($formtitle,'xili_tidy_tags') ?></h2>
		<?php if ($action=='edit' || $action=='delete') :?>
			<input type="hidden" name="tagsgroup_term_id" value="<?php echo $tagsgroup->term_id ?>" />
			<input type="hidden" name="tagsgroup_parent" value="<?php echo $tagsgroup->parent ?>" />
		<?php endif; ?>
		<table class="editform" width="100%" cellspacing="2" cellpadding="5">
			<tr>
				<th width="33%" scope="row" valign="top" align="right"><label for="tagsgroup_name"><?php _e('Name','xili_tidy_tags') ?></label>:&nbsp;</th>
				<td width="67%"><input name="tagsgroup_name" id="tagsgroup_name" type="text" value="<?php if (isset($tagsgroup)) echo attribute_escape( $tagsgroup->name ); ?>" size="40" <?php if($action=='delete') echo 'disabled="disabled"' ?> /></td>
			</tr>
			<tr>
				<th scope="row" valign="top" align="right"><label for="tagsgroup_nicename"><?php _e('tags group slug','xili_tidy_tags') ?></label>:&nbsp;</th>
				<td><input name="tagsgroup_nicename" id="tagsgroup_nicename" type="text" value="<?php if (isset($tagsgroup)) echo attribute_escape($tagsgroup->slug); ?>" size="40" <?php if($action=='delete') echo 'disabled="disabled"' ?> /></td>
			</tr>
			<tr>
				<th scope="row" valign="top" align="right"><label for="tagsgroup_description"><?php _e('Description','xili_tidy_tags') ?></label>:&nbsp;</th>
				<td><input name="tagsgroup_description" id="tagsgroup_description" size="40" value="<?php if (isset($tagsgroup)) echo $tagsgroup->description; ?>" <?php if($action=='delete') echo 'disabled="disabled"' ?> /></td>
				
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top" align="right"><label for="tagsgroup_parent"><?php _e('kinship','xili_tidy_tags') ?></label> :&nbsp;</th>
				<td>
			  	<?php if (isset($tagsgroup)) {
			  			$this->xili_selectparent_row($tagsgroup->term_id, $tagsgroup, $action); 
			  			/* choice of parent line*/
			  	} else {
			  		$this->xili_selectparent_row(0, $tagsgroup, $action);
			  	}
			  			?>
             	</td>
			</tr>
			<tr>
			<th><p class="submit"><input type="submit" name="reset" value="<?php echo $cancel_text ?>" /></p></th>
			<td>
			<p class="submit"><input type="submit" name="submit" value="<?php echo $submit_text ?>" /></p>
			</td>
			</tr>
		</table>
	<?php
	}
	
	function xili_selectparent_row($term_id=0,$tagsgroup,$action) {
		if ($term_id == 0) {
				$listterms = get_terms(TAXOTIDYTAGS, array('hide_empty' => false));
				?>
				<select name="tagsgroup_parent" id="tagsgroup_parent" style="width:100%;">
		  				<option value="no_parent" ><?php _e('no parent','xili_tidy_tags'); ?></option>
				<?php
				foreach ($listterms as $curterm) {
					if ($curterm->parent == 0) {
						if (current_user_can( 'administrator')) {
							$possible = true;
						} elseif  ($curterm->term_id == $this->langgroupid) {
							$possible = false;
						} else {
							$possible = true;
						}		
						if ($possible)
							echo '<option value="'.$curterm->term_id.'" >'.$curterm->name.'</option>';
					}
				} ?>
				</select>
				<br />
	    		<?php _e('Select the parent if necessary','xili_tidy_tags');
	     	} else {
	     		if ($tagsgroup->parent == 0) {
	     			$listterms = get_terms(TAXOTIDYTAGS, array('hide_empty' => false,'parent' => $term_id));
					// display childs
					if (!empty($listterms)) {
						echo __('parent of: ','xili_tidy_tags');
						echo '<ul>';
						foreach ($listterms as $curterm) { 
							echo '<li value="'.$curterm->term_id.'" >'.$curterm->name.'</li>';
						}
						echo '</ul>';
					} else {
						echo __('no child now','xili_tidy_tags')."<br /><br />";	
					}
					/* if modify*/
					if($action=='edit') {
						$listterms = get_terms(TAXOTIDYTAGS, array('hide_empty' => false));
				?>
				<select name="tagsgroup_parent" id="tagsgroup_parent" style="width:100%;">
		  				<option value="no_parent" ><?php _e('no parent','xili_tidy_tags'); ?></option>
				<?php
					foreach ($listterms as $curterm) {
						if ($curterm->parent == 0 && $curterm->term_id != $term_id)
							echo '<option value="'.$curterm->term_id.'" >'.$curterm->name.'</option>';
					} ?>
				</select>
				<br />
	    		<?php _e('Select the parent if necessary','xili_tidy_tags');
						
					}	
						
	     		} else {
	     			/* if modify*/
	     			$parent_term = get_term( (int) $tagsgroup->parent,TAXOTIDYTAGS,OBJECT,'edit');
	     			if($action=='delete') {
	     				echo __('child of: ','xili_tidy_tags');
	     				echo $parent_term->name; ?>
	     					<input type="hidden" name="tagsgroup_parent" value="<?php echo $parent_term->term_id ?>" />	
	     		<?php } else {
	     					$listterms = get_terms(TAXOTIDYTAGS, array('hide_empty' => false));
	     			?>
							<select name="tagsgroup_parent" id="tagsgroup_parent" style="width:100%;">
		  					<option value="no_parent" ><?php _e('no parent','xili_tidy_tags'); ?></option>
				<?php
							foreach ($listterms as $curterm) {
								if ($curterm->parent == 0 && $curterm->term_id != $term_id) {
									$checked = ($parent_term->term_id == $curterm->term_id) ? 'selected="selected"' :'' ;
									echo '<option value="'.$curterm->term_id.'" '.$checked.' >'.$curterm->name.'</option>';
								}
							} ?>
							</select>
				<br />
	    		<?php _e('Modify the parent if necessary','xili_tidy_tags');
	     		}	
	     	}	
			}	
	}
	
	/* Dashboard - Manage - Tidy tags */
	function xili_tidy_tags_settings() { 
		global $wp_version ;
		$xili_tidy_tags_page = 'settings';
		$formtitle = 'Add a group'; /* translated in form */
		$submit_text = __('Add &raquo;','xili_tidy_tags');
		$cancel_text = __('Cancel');
		$action = '';
		$optionmessage = '';
		$tagsgroup = null;
		$term_id = 0;
		if (isset($_POST['reset'])) {
			$action=$_POST['reset'];
		} elseif (isset($_POST['updateoptions'])) {
			$action='updateoptions';
		} elseif (isset($_POST['importxililanguages'])) {
			$action='importxililanguages';
		} elseif (isset($_POST['importacat'])) {
			$action='importacat';
		} elseif (isset($_POST['editor_caps_submit'])) { /* 0.9.5 capabilities */
			$action='editor_caps_submit';
		} elseif ( isset($_POST['sendmail']) ) { //1.5.4
			$action = 'sendmail' ;	
		} elseif (isset($_POST['action'])) {
			$action=$_POST['action'];	
		}
		
		if (isset($_GET['action'])) :
			$action=$_GET['action'];
			$term_id = $_GET['term_id'];
		endif;
		$message = $action ;
		$msg = 0;
		switch($action) {
			case 'editor_caps_submit';
				check_admin_referer( 'xilitagsettings' );
				$new_cap = $_POST['editor_caps'];
				
				$this->xili_settings['editor_caps'] = $new_cap ;
				$this->xili_settings['datatable_js'] = $_POST['datatable_js']; // 1.5.3.1
				
				update_option('xili_tidy_tags_settings', $this->xili_settings);
					
				$actiontype = "add";
			    $message .= " - ".__('Editor Capabilities changed to: ','xili_tidy_tags')." (".$new_cap.") ";
			    $optionmessage = $message;
			    $msg = 6;
				break;
			case 'importacat';
				check_admin_referer( 'xilitagsettings' );
				$chosencatid = $_POST['catsforgroup'];
				$chosenparent = $_POST['tags_parent'];
				$chosencat = get_category($chosencatid);
				$desc = __('Group for: ','xili_tidy_tags').$chosencat->name .' '. __('category','xili_tidy_tags');
				$args = array( 'alias_of' => '', 'description' => $desc, 'parent' => (int) $_POST['tags_parent']);
			    $theids = wp_insert_term( $chosencat->name, TAXOTIDYTAGS, $args);
			    if ( !is_wp_error($theids) )
			    	wp_set_object_terms($theids['term_id'], (int)$_POST['tags_parent'], TAXOTIDYTAGS);
				
				$actiontype = "add";
			    $message .= " - ".__('This group was added: ','xili_tidy_tags')." (".$chosencatid.") parent = ".$chosenparent;
				break;
					
			case 'importxililanguages';
				check_admin_referer( 'xilitagsettings' );
				$this->xili_langs_import_terms ();
				$actiontype = "add";
			    $message .= " - ".__('The languages groups was added.','xili_tidy_tags');
			    $msg = 5;
				break;
				
			case 'add';
				check_admin_referer( 'xilitagsettings' );
				$term = $_POST['tagsgroup_name'];
				if ('' != $term) {
					$args = array( 'alias_of' => '', 'description' => $_POST['tagsgroup_description'], 'parent' => (int) $_POST['tagsgroup_parent'], 'slug' => $_POST['tagsgroup_nicename']);
				    $theids = wp_insert_term( $term, TAXOTIDYTAGS, $args);
				    if (!is_wp_error($theids))
				    	wp_set_object_terms($theids['term_id'], (int)$_POST['tagsgroup_parent'], TAXOTIDYTAGS);
					$actiontype = "add";
				    $message .= " - ".__('A new group was added.','xili_tidy_tags');
				    $msg = 1;
				} else {
					$actiontype = "add";
				    $message .= " - ".__('NO new group was added.','xili_tidy_tags');
				    $msg = 2;
				}
			    break;
			
			case 'edit';
				$actiontype = "edited";
			    $tagsgroup = get_term( (int) $term_id, TAXOTIDYTAGS,OBJECT,'edit');
			    $submit_text = __('Update &raquo;','xili_tidy_tags');
			    $formtitle =  'Edit Group';
			    $message .= " - ".__('Group to update.','xili_tidy_tags');
				break;
			
			case 'edited';
				check_admin_referer( 'xilitagsettings' );
			    $actiontype = "add";
			    $term = $_POST['tagsgroup_term_id'];
				$args = array( 'alias_of' => '', 'description' => $_POST['tagsgroup_description'], 'parent' => (int)$_POST['tagsgroup_parent'], 'slug' =>$_POST['tagsgroup_nicename']);
				$theids = wp_update_term( $term, TAXOTIDYTAGS, $args);	
				$message .= " - ".__('A group was updated.','xili_tidy_tags');
				$msg = 3;
			    break;
				
			case 'delete';
			    $actiontype = "deleting";
			    $submit_text = __('Delete &raquo;','xili_tidy_tags');
			    $formtitle = 'Delete group';
			    $tagsgroup = get_term( (int) $term_id, TAXOTIDYTAGS,OBJECT, 'edit');
			    $message .= " - ".__('A group to delete.','xili_tidy_tags');
			    break;
			    	
			case 'deleting';
				check_admin_referer( 'xilitagsettings' );
			    $actiontype = "add";
			    $term = $_POST['tagsgroup_term_id'];
			    wp_delete_term( $term, TAXOTIDYTAGS, $args);
			    $message .= " - ".__('A group was deleted.','xili_tidy_tags');
			    $msg = 4;
			    break;
			     
			case 'reset';    
			    $actiontype = "add";
			    break;
			    
			case 'sendmail'; // 1.5.4
				check_admin_referer( 'xilitagsettings' ); 
				$this->xili_settings['url'] = $_POST['urlenable'];
				$this->xili_settings['theme'] = $_POST['themeenable'];
				$this->xili_settings['wplang'] = $_POST['wplangenable'];
				$this->xili_settings['version'] = $_POST['versionenable'];
				$this->xili_settings['xiliplug'] = $_POST['xiliplugenable'];
				update_option('xili_language_settings', $this->xili_settings);
				$contextual_arr = array();
				if ( $this->xili_settings['url'] == 'enable' ) $contextual_arr[] = "url=[".get_bloginfo ('home')."]" ;
				if ( isset($_POST['onlocalhost']) ) $contextual_arr[] = "url=local" ;
				if ( $this->xili_settings['theme'] == 'enable' ) $contextual_arr[] = "theme=[".get_option ('stylesheet')."]" ;
				if ( $this->xili_settings['wplang'] == 'enable' ) $contextual_arr[] = "WPLANG=[".WPLANG."]" ;
				if ( $this->xili_settings['version'] == 'enable' ) $contextual_arr[] = "WP version=[".$wp_version."]" ;
				if ( $this->xili_settings['xiliplug'] == 'enable' ) $contextual_arr[] = "xiliplugins=[". $this->check_other_xili_plugins() ."]" ;
				$contextual_arr[] = $_POST['webmestre']; // 1.9.1
				
				$headers = 'From: xili-tidy-tags plugin page <' . get_bloginfo ('admin_email').'>' . "\r\n" ;
	   			if ( '' != $_POST['ccmail'] ) $headers .= 'Cc: <'.$_POST['ccmail'].'>' . "\r\n";
	   			$headers .= "\\";
	   			$message = "Message sent by: ".get_bloginfo ('admin_email')."\n\n" ;
	   			$message .= "Subject: ".$_POST['subject']."\n\n" ;
	   			$message .= "Topic: ".$_POST['thema']."\n\n" ;
	   			$message .= "Content: ".$_POST['mailcontent']."\n\n" ;
	   			$message .= "Checked contextual infos: ". implode ( ' ** ', $contextual_arr ) ."\n\n" ;
	   			$message .= "This message was sent by webmaster in xili-language plugin settings page.\n\n";
	   			$message .= "\n\n"; 
	   			$result = wp_mail('contact@xiligroup.com', $_POST['thema'].' from xili-tidy-tags plugin settings page.' , $message, $headers );
				$message = __('Email sent.','xili_tidy_tags');
				$msg = 7;
				$emessage = sprintf( __( 'Thanks for your email. A copy was sent to %s (%s)','xili_tidy_tags' ), $_POST['ccmail'], $result ) ;
				break;    
			    
			default :
			    $actiontype = "add";
			    $message .= __('Find the list of groups.','xili_tidy_tags');
		}	
		
		/* register the main boxes always available */
		
		add_meta_box('xili_tidy_tags-normal-1', __('Groups of Tags','xili_tidy_tags'), array(&$this,'on_normal_1_content'), $this->thehook , 'normal', 'core'); /* list of groups*/
		add_meta_box('xili_tidy_tags-normal-2', __('The group','xili_tidy_tags'), array(&$this,'on_normal_2_content'), $this->thehook , 'normal', 'core'); /* the group*/
		add_meta_box('xili_tidy_tags-sidebox-2', __('Actions','xili_tidy_tags'), array(&$this,'on_sidebox_3_content'), $this->thehook , 'side', 'core'); /* Actions */ 
		
		$themessages[1] = __('A new group was added.','xili_tidy_tags');
		$themessages[2] = __('NO new group was added.','xili_tidy_tags');
		$themessages[3] = __('A group was updated.','xili_tidy_tags');
		$themessages[4] = __('A group was deleted.','xili_tidy_tags');
		$themessages[5] = __('The languages groups was added.','xili_tidy_tags');
		$themessages[6] = $optionmessage ;
		$themessages[7] = __('Email sent.','xili_tidy_tags');
		
		/* form datas in array for do_meta_boxes() */
		$data = array('xili_tidy_tags_page' => $xili_tidy_tags_page,'message' => $message, 'action'=>$action, 'formtitle'=>$formtitle, 'tagsgroup'=>$tagsgroup, 'submit_text'=>$submit_text,'cancel_text'=>$cancel_text, 'term_id'=>$term_id, 'emessage'=>$emessage);
		?>
		<div id="xili-tidy-tags-settings" class="wrap" style="min-width:880px">
			<?php screen_icon('tools'); ?>
			<h2><?php _e('Tidy tags groups','xili_tidy_tags') ?></h2>
			<?php if (0!= $msg ) { ?>
			<div id="message" class="updated fade"><p><?php echo $themessages[$msg]; ?></p></div>
			<?php } ?>
			<form name="add" id="add" method="post" action="admin.php?page=xili_tidy_tags_settings">
				<input type="hidden" name="action" value="<?php echo $actiontype ?>" />
				<?php wp_nonce_field('xili-tidy-tags-settings'); ?>
				<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); 
				/* 0.9.3 add has-right-sidebar for next wp 2.8*/ ?>
				<div id="poststuff" class="metabox-holder has-right-sidebar">
					
					<div id="post-body" class="has-sidebar has-right-sidebar">
						<div id="post-body-content" class="has-sidebar-content" style="min-width:580px">
						<?php do_meta_boxes($this->thehook, 'normal', $data); ?>
						</div>
						 	
					<h4><a href="http://dev.xiligroup.com/xili-tidy-tags" title="Plugin page and docs" target="_blank" style="text-decoration:none" ><img style="vertical-align:middle" src="<?php echo WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)).'/xilitidy-logo-32.png'; ?>" alt="xili-tidy-tags logo"/>  xili-tidy-tags</a> - © <a href="http://dev.xiligroup.com" target="_blank" title="<?php _e('Author'); ?>" >xiligroup.com</a>™ - msc 2009-11 - v. <?php echo XILITIDYTAGS_VER; ?></h4>
							
					</div>
					<div id="side-info-column" class="inner-sidebar">
						<?php do_meta_boxes($this->thehook, 'side', $data); ?>
					</div>
					
					
				</div>
				<?php wp_nonce_field('xilitagsettings'); ?>
		</form>
		</div>
		<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready( function($) {
				
				// close postboxes that should be closed
				$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
				// postboxes setup
				postboxes.add_postbox_toggles('<?php echo $this->thehook; ?>');
			});
			//]]>
		</script> 
		
	<?php
	}
	
	/*
	 * Import or update the terms (languages) set by xili-language	 *
	 * @updated 1.5.0
	 */
	function xili_langs_import_terms () {
		
		$term = LANGSTAGSGROUPNAME; 
		$args = array( 'alias_of' => '', 'description' => 'default lang group', 'parent' => 0, 'slug' => LANGSTAGSGROUPSLUG );
		$theids = is_term (LANGSTAGSGROUPNAME,TAXOTIDYTAGS); // impossible to use get_term as formerly !!!
		if (!$theids) {
			$theids = wp_insert_term( $term, TAXOTIDYTAGS, $args);  //print_r($theids);
			$this->langgroupid = $theids['term_id']; 
			
		}
		$listlanguages = get_terms(TAXONAME, array('hide_empty' => false )); 
		foreach ($listlanguages as $language) {
			$args = array( 'alias_of' => '', 'description' => $language->description, 'parent' => $theids['term_id'], 'slug' =>$language->slug.'-'.LANGSTAGSGROUPSLUG ); // slug to be compatible with former release of WP
			
			$ifhere = is_term ( $language->name, TAXOTIDYTAGS );
			if (!$ifhere) {
				$res = wp_insert_term( $language->name, TAXOTIDYTAGS, $args );
			}
		}
	}
	
	
	/*
	 * Display the rows of group of tags
	 *
	 * @updated since 1.3.0 - use now walker class to sort Tag's groups
	 */
	function xili_tags_group_row() {
		$listtagsgroups = get_terms(TAXOTIDYTAGS, array('hide_empty' => false,'get'=>'all'));
		
		if ( $listtagsgroups == array() ) {
			/* import */
			if ( class_exists('xili_language') ) { /* xili-language is present */
				$this->xili_langs_import_terms ();
				
			} else {
				/*create a default line with the default group*/
				$term = 'tidy group';
				$args = array( 'alias_of' => '', 'description' => 'default xili tidy tags group', 'parent' => 0);
				$resgroup = wp_insert_term( $term, TAXOTIDYTAGS, $args);
				
			}	
			
			$listtagsgroups = $this->no_cache_get_terms(TAXOTIDYTAGS, array('hide_empty' => false,'get'=>'all'));
				
		}
		
		$listtagsgroupssorted = walk_TagGroupList_sorted( $listtagsgroups, 3 , null, null );
		$class = '';
		if ($listtagsgroupssorted) { // @since 1.4.2 - no warning if no groups at init
			foreach ($listtagsgroupssorted as $tagsgroup) {
				$class = ((defined('DOING_AJAX') && DOING_AJAX) || " class='alternate'" == $class ) ? '' : " class='alternate'";
		
				$tagsgroup->count = number_format_i18n( $tagsgroup->count );
				$posts_count = ( $tagsgroup->count > 0 ) ? "<a href='edit.php?lang=$tagsgroup->term_id'>$tagsgroup->count</a>" : $tagsgroup->count;	
			    /* since 0.9.5 */
			    if (current_user_can( 'administrator')) { /* all admin only */
			    	$possible = true;
			    } elseif (current_user_can( 'xili_tidy_editor_set')) { /* editor if set */
			    	if ($tagsgroup->term_id == $this->langgroupid || $tagsgroup->parent == $this->langgroupid) {
			    		$possible = false;
			    	} else {
			    		$possible = true;
			    	}	
			    } else {
			    	$possible = false;
			    }
			    	
		    	if (true === $possible ) {
					$edit = "<a href='?page=xili_tidy_tags_settings&amp;action=edit&amp;term_id=".$tagsgroup->term_id."' >".__( 'Edit' )."</a></td>";	
					/* delete link &amp;action=edit&amp;term_id=".$tagsgroup->term_id."*/
					$edit .= "<td><a href='?page=xili_tidy_tags_settings&amp;action=delete&amp;term_id=".$tagsgroup->term_id."' class='delete'>".__( 'Delete' )."</a>";	
		    	} else {
		    		$edit = __('no capability','xili_tidy_tags').'</td><td>';
		    	}		
				
				$line="<tr id='cat-$tagsgroup->term_id'$class>
				<th scope='row' style='text-align: center'>$tagsgroup->term_id</th>
				<td> ";
				$tabb = ($tagsgroup->parent != 0) ? " –" : "" ;
				$tagsgroupname = ($tagsgroup->parent == 0) ? "<strong>".$tagsgroup->name."</strong>": $tagsgroup->name;
				$line .= "$tabb $tagsgroupname</td>
				<td>$tagsgroup->description</td>
				<td>$tagsgroup->slug</td>
				<td>$tagsgroup->term_taxonomy_id</td>
				<td>$tagsgroup->parent</td>
				<td align='center'>$tagsgroup->count</td> 
				<td>$edit</td>\n\t</tr>\n"; /*to complete*/
				echo $line;
			}
		}
				
	}
	/**
	 * @updated 1.5.0 with datatables js (ex widefat)
	 *
	 */
	function on_sub_normal_1_content ($data=array()) {
	 	extract($data); ?>
		<?php /**/ ?>
		<div id="topbanner">
		</div>
		<?php if ( $this->xili_settings['datatable_js'] == '' ) { ?>
		<div id="tableupdating"><br /><br /><h1><?php _e('Creating table of tags !','xili_tidy_tags') ?></h1>
		</div>
		<table class="display" id="assigntable">
		<?php } else { ?>
		<table class="display" id="assigntable" style="visibility : visible; " >
		<?php }?>
					
						<thead>
						<tr>
							<th scope="col" class="center colid" ><?php _e('ID','xili_tidy_tags') ?></th>
	        				<th scope="col" class="colname" ><?php _e('Name','xili_tidy_tags') ?></th>
	        				<th scope="col" class="center colposts"><?php _e('Posts') ?></th>
	        				<th colspan="2" class="colgrouping"><?php _e('Group(s) to choose','xili_tidy_tags') ?></th>
						</tr>
						</thead>
						<tbody id="the-list">
							<?php $this->xili_tags_row($tagsnamelike,$tagsnamesearch); /* the lines */?>
						</tbody>
						<tfoot>
							<tr>
								<th><?php _e('ID','xili_tidy_tags') ?></th>
								<th><?php _e('Name','xili_tidy_tags') ?></th>
								<th><?php _e('Posts') ?></th>
								<th><?php _e('Group(s) to choose','xili_tidy_tags') ?></th>
							</tr>
						</tfoot>
					</table>
		<div id="bottombanner">
		</div>
			
					
		<?php
		
	}
	/**
	 *
	 * @updated 1.5.3
	 */
	function  on_sub_sidebox_3_content($data=array()) { 
	 	extract($data);?>
	 	<p><?php _e('After checking or unchecking do not forget to click update button !','xili_tidy_tags'); ?></p>
		<p class="submit"><input type="submit" class="button-primary" id="update" name="update" value="<?php echo $submit_text ?>" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="reset" value="<?php echo $cancel_text ?>" /></p>
		
		<fieldset style="margin:2px; padding:3px; border:1px solid #ccc;"><legend><?php _e('Sub list of tags','xili_tidy_tags'); ?></legend>
			<label for="tagsnamelike"><?php _e('Starting with:','xili_tidy_tags') ?></label> 
			<input name="tagsnamelike" id="tagsnamelike" type="text" value="<?php echo $tagsnamelike; ?>" /><br />
			<label for="tagsnamesearch"><?php _e('Containing:','xili_tidy_tags') ?></label> 
			<input name="tagsnamesearch" id="tagsnamesearch" type="text" value="<?php echo $tagsnamesearch; ?>" /><br /><br />
			<label for="tagsfromgroup"><?php _e('Choose:','xili_tidy_tags') ?></label>
			<?php $listterms = get_terms(TAXOTIDYTAGS, array('hide_empty' => false)); 
			$listtagsgroupssorted = walk_TagGroupList_sorted( $listterms, 3, null, null );
			?>
				<select name="tagsgroup_from_select" id="tagsgroup_from_select" style="width:45%;">
		  				<option value="no_select" ><?php _e('every','xili_tidy_tags'); ?></option>
						<option value="onlyuncheckedtags" <?php echo ( $this->onlyuncheckedtags ) ? 'selected="selected"' :'' ; ?>><?php _e('Unchecked only','xili_tidy_tags'); ?></option>
				<?php
				$show = false;
				foreach ($listtagsgroupssorted as $curterm) {
					$ttab = ($curterm->parent == 0) ? '' : '– ' ;
					if ($this->fromgroupselect == $curterm->term_id) { 
						$checked =  'selected="selected"';
						if ( $curterm->parent == 0 ) {
							$listlanggroups = get_terms( TAXOTIDYTAGS, array('hide_empty' => true, 'parent' => $curterm->term_id ) );
							if ( $listlanggroups ) $show = true ;
						}
					} else { 
						$checked = '' ;
					}
					echo '<option value="'.$curterm->term_id.'" '.$checked.' >'.$ttab.$curterm->name.'</option>';
						
				} ?>
				</select>
				<?php if( $this->onlyuncheckedtags == false &&  $this->fromgroupselect != 0 ) { ?>
				<br /><label for="xili_group_not_select"><?php _e('Exclude this group','xili_tidy_tags') ?> <input id="xili_group_not_select" name="xili_group_not_select" type="checkbox" value="not" <?php if($this->groupexclude == true) echo 'checked="checked"' ?> /></label>
				<?php if ($show) { ?>
				&nbsp;–&nbsp;<label for="xili_group_only_parent"><?php _e('No childs','xili_tidy_tags') ?> <input id="xili_group_only_parent" name="xili_group_only_parent" type="checkbox" value="onlyparent" <?php if($this->onlyparent == true) echo 'checked="checked"' ?> /></label>
				<?php } ?>
				<br />
				<?php if($this->groupexclude == true) { ?>
					<label for="xili_uncheckedtags"><?php _e('Include unchecked','xili_tidy_tags') ?> <input id="xili_uncheckedtags" name="xili_uncheckedtags" type="checkbox" value="include" <?php if($this->uncheckedtags == true) echo 'checked="checked"' ?> /></label>
				<?php } 
				}?>
			<p class="submit"><input type="submit" id="tagssublist" name="tagssublist" value="<?php _e('Sub select…','xili_tidy_tags'); ?>" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" id="notagssublist" name="notagssublist" value="<?php _e('No select…','xili_tidy_tags'); ?>" /></p>
		</fieldset>
		<?php /* only show one group to select */ ?>
		<fieldset style="margin:2px; padding:3px; border:1px solid #ccc;"><legend><?php _e('Columns: Group selection','xili_tidy_tags'); ?></legend>
			<?php //$listterms = get_terms(TAXOTIDYTAGS, array('hide_empty' => false)); ?>
				<select name="tagsgroup_parent_select" id="tagsgroup_parent_select" style="width:100%;">
		  				<option value="no_select" ><?php _e('No sub-selection','xili_tidy_tags'); ?></option>
				<?php
				foreach ($listterms as $curterm) {
					if ($curterm->parent == 0) {
						$checked = ($this->subselect == $curterm->term_id) ? 'selected="selected"' :'' ;
						echo '<option value="'.$curterm->term_id.'" '.$checked.' >'.$curterm->name.'</option>';
					}
				} ?>
				</select>
				<br /> <p class="submit"><input type="submit" id="subselection" name="subselection" value="<?php _e('Sub select…','xili_tidy_tags'); ?>" /></p></fieldset><?php
	}
	/**
	 * The rows of the tags and checkboxes to assign group(s)
	 *
	 * @since 0.8.0
	 * @updated 1.3.0 - Call walker instantiation
	 * @uses 
	 * @param 
	 * @return the rows for admin ui
	 */
	function xili_tags_row($tagsnamelike='',$tagsnamesearch='') {
		$listgroups = get_terms(TAXOTIDYTAGS, array('hide_empty' => false,'get'=>'all'));
		$hiddenline = array ();
		$edit =''; $i=0;
		$listgroupids = array();
		$sub_listgroups = array();
		$subselectgroups = array();
		if ($this->subselect > 0) {
			$subselectgroups[] = $this->subselect; /* the parent group and */
			/*childs of */
			$listterms = get_terms(TAXOTIDYTAGS, array('hide_empty' => false,'parent' => $this->subselect));
			if (!empty($listterms)) {
					foreach ($listterms as $curterm) { 
							$subselectgroups[] = $curterm->term_id;
					}
			}		
		}
		if (!empty($subselectgroups)) {	 /* columns sub-selection */
			foreach ($listgroups as $group) {
				$listgroupids[] = $group->term_id;
				if (in_array ($group->term_id,$subselectgroups)) {
					$sub_listgroups[] = $group;
				} else {
					 $hiddenline[] = $group->term_id ;	/* keep line values */
				}	
			}
			$editformat = walk_TagGroupList_tree_row( $sub_listgroups, 3, null );	
		} else {
			foreach ($listgroups as $group) {
				$listgroupids[] = $group->term_id;
			}
			$editformat = walk_TagGroupList_tree_row( $listgroups, 3, null );	
		}
				
		if ( $this->fromgroupselect == 0 && $this->onlyuncheckedtags === false ) {
			$listtags = get_terms('post_tag', array('hide_empty' => false,'get'=>'all','name__like'=>$tagsnamelike,'search'=>$tagsnamesearch ));
		} else { /* since 1.3.0 */
			if ( $this->onlyuncheckedtags === false ) { // one group
				$group_id[] = $this->fromgroupselect;
				if ( $this->onlyparent === false ) {
					$childterms = get_terms(TAXOTIDYTAGS, array('hide_empty' => false,'parent' => $this->fromgroupselect));
			 		if ( !empty($childterms) ) { 
			 			foreach ( $childterms as $childterm ) { /* if group is a parent, add all childs */
			 			 	$group_id[] = $childterm->term_id;
			 			}
			 		}
				}
			} else { // only unchecked
				$listgroups = get_terms( TAXOTIDYTAGS, array('hide_empty' => false, 'get'=>'all') );
				foreach ( $listgroups as $group ) {
					$group_id[] = $group->term_id;
				}
				$this->groupexclude = true;
				$this->uncheckedtags = true;
			}
			$listtags = get_terms_of_groups_new ( $group_id, TAXOTIDYTAGS, 'post_tag', array('hide_empty' => false, 'get'=>'all', 'name__like'=>$tagsnamelike, 'search'=>$tagsnamesearch, 'orderby'=>'name'), $this->groupexclude, $this->uncheckedtags );	
		}
		$class = '';
		foreach ( $listtags as $tag ) {
			$class = ((defined('DOING_AJAX') && DOING_AJAX) || " class='alternate'" == $class ) ? '' : " class='alternate'";	
			$tag->count = number_format_i18n( $tag->count );
			$posts_count = ( $tag->count > 0 ) ? "<a href='edit.php?tag=$tag->name'>$tag->count</a>" : $tag->count;				
			$edit = sprintf($editformat,$tag->term_id);
			$hiddenlines = "";
			foreach ($listgroupids as $groupid) {
				if (is_object_in_term($tag->term_id,TAXOTIDYTAGS,$groupid)) {
					$edit = str_replace('checked'.$groupid,'checked="checked"',$edit);
					if (in_array($groupid,$hiddenline))
						$hiddenlines .= '<input type="hidden" name="line-'.$tag->term_id.'-'.$groupid.'" value="'.$tag->term_id.'" />';				
				} else {
					$edit = str_replace('checked'.$groupid,'',$edit);
				}	
			}							
			$line="<tr id='cat-$tag->term_id'$class>
			<td class='termid' id='termid-{$tag->term_id}' scope='row' style='text-align: center'>$tag->term_id</td>
			<td> <a href='edit-tags.php?action=edit&amp;tag_ID=".$tag->term_id."'>".$tag->name."</a> </td>
			<td align='center'>$posts_count</td>
			<td>$edit\n$hiddenlines</td>\n\t</tr>\n"; /*to complete*/
			echo $line;
		}
	}
		
	/* page for tags assign to (a) group(s) */
	function xili_tidy_tags_assign () { 
		$action = "";
		$term_id = '';
		$tagsnamelike = '';
		$tagsnamesearch = '';
		$xili_tidy_tags_page = 'assign';
		$submit_text = __('Update','xili_tidy_tags');
		$cancel_text = __('Cancel');
		$tagsnamelike = $_POST['tagsnamelike'];
		$tagsnamesearch = $_POST['tagsnamesearch'];
		if (isset($_POST['update'])) {
			$action='update';
		}
		/* since 1.3.0 */
		if (isset($_POST['tagsgroup_from_select']) && $_POST['tagsgroup_from_select'] != 'no_select') {
				$this->fromgroupselect = (int) $_POST['tagsgroup_from_select'];
			} else {
				$this->fromgroupselect = 0;
			}
		$this->onlyuncheckedtags = (isset($_POST['tagsgroup_from_select']) && $_POST['tagsgroup_from_select'] == 'onlyuncheckedtags') ? true : false ;
		$this->onlyparent = (isset($_POST['xili_group_only_parent']) && $_POST['xili_group_only_parent'] == 'onlyparent') ? true : false ;	
		$this->groupexclude = (isset($_POST['xili_group_not_select']) && $_POST['xili_group_not_select'] == 'not') ? true : false ;
		$this->uncheckedtags = (isset($_POST['xili_uncheckedtags']) && $_POST['xili_uncheckedtags'] == 'include') ? true : false ;
		$subselectgroups = array();
		if (isset($_POST['tagsgroup_parent_select']) && $_POST['tagsgroup_parent_select'] != 'no_select') {
				$this->subselect = (int) $_POST['tagsgroup_parent_select'];
			} else {
				$this->subselect = 0;
			}	
		if (isset($_POST['subselection'])) {
			$action='subselection';
		}
		if (isset($_POST['notagssublist'])) {
			$action='notagssublist';
		}
		if (isset($_POST['tagssublist'])) {
			$action='tagssublist';
		}
		if (isset($_GET['action'])) :
			$action = $_GET['action'];
			$term_id = $_GET['term_id'];
		endif;
		$message = $action ;
		switch($action) {
			
			case 'notagssublist';
				$tagsnamelike = '';
				$tagsnamesearch = '';
				$this->fromgroupselect = 0; /* since 1.3.0 */
				$this->groupexclude = false;
				$message .= ' no sub list of tags';
				$actiontype = "add";
				break;
			
			case 'tagssublist';
				check_admin_referer( 'xilitagassign' );
				$message .= ' sub list of tags starting with '.$_POST['tagsnamelike'];
				$message .= '. From group '.$_POST['tagsgroup_from_select'];
				$actiontype = "add";
				break;
				
			case 'subselection';
				check_admin_referer( 'xilitagassign' );
				$tagsnamelike = $_POST['tagsnamelike'];
				$tagsnamesearch = $_POST['tagsnamesearch'];
				$message .= ' selection of '.$_POST['tagsgroup_parent_select'];
				$msg = 2 ;
				$actiontype = "add";
				break;
							
			case 'update';
				check_admin_referer( 'xilitagassign' );
				$message .= ' ok: datas are saved... ';
				$message .= $this->checkboxes_update_them($tagsnamelike,$tagsnamesearch); $msg=1;
				$actiontype = "add";
				break;
				
			case 'reset';    
			    $actiontype = "add";
			    break;
			    
			default :
			    $actiontype = "add";
			    $message .= __('Find the list of tags.','xili_tidy_tags');	
		}
		/* form datas in array for do_meta_boxes() */
		$data = array('xili_tidy_tags_page' => $xili_tidy_tags_page,'message'=>$message, 'action'=>$action,'submit_text'=>$submit_text,'cancel_text'=>$cancel_text,'term_id'=>$term_id, 'tagsnamesearch'=>$tagsnamesearch, 'tagsnamelike'=>$tagsnamelike);	
			
		/* register the main boxes always available */
		add_meta_box('xili_tidy_tags-sidebox-2', __('Actions','xili_tidy_tags'), array(&$this,'on_sub_sidebox_3_content'), $this->thehook2 , 'side', 'core'); /* Actions */ 
		add_meta_box('xili_tidy_tags-normal-1', __('Tidy Tags','xili_tidy_tags'), array(&$this,'on_sub_normal_1_content'), $this->thehook2 , 'normal', 'core'); /* list of tags*/
		
		$themessages[1] = __('List updated in database !','xili_tidy_tags');
		
		$themessages[2] = $message ;	
			?>
		<div id="xili-tidy-tags-assign" class="wrap" style="min-width:880px">
			<?php screen_icon('post'); ?>
			<h2><?php _e('Tags in group','xili_tidy_tags') ?></h2>
			<?php if (0!= $msg ) { ?>
			<div id="message" class="updated fade"><p><?php echo $themessages[$msg]; ?></p></div>
			<?php } ?>
			<form name="add" id="add" method="post" action="admin.php?page=xili_tidy_tags_assign">
				<input type="hidden" name="action" value="<?php echo $actiontype ?>" />
				<?php wp_nonce_field('xili-tidy-tags-settings'); ?>
				<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); 
				/* 0.9.3 add has-right-sidebar for next wp 2.8*/ ?>
				<div id="poststuff" class="metabox-holder has-right-sidebar">
					<div id="side-info-column" class="inner-sidebar">
						<?php do_meta_boxes($this->thehook2, 'side', $data); ?>
					</div>
					<div id="post-body" class="has-sidebar has-right-sidebar">
						<div id="post-body-content" class="has-sidebar-content" style="min-width:580px">
					
	   					<?php do_meta_boxes($this->thehook2, 'normal', $data); ?>
						</div>
						 	
					<h4><a href="http://dev.xiligroup.com/xili-tidy-tags" title="Plugin page and docs" target="_blank" style="text-decoration:none" ><img style="vertical-align:middle" src="<?php echo WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)).'/xilitidy-logo-32.png'; ?>" alt="xili-tidy-tags logo"/>  xili-tidy-tags</a> - © <a href="http://dev.xiligroup.com" target="_blank" title="<?php _e('Author'); ?>" >xiligroup.com</a>™ - msc 2009-11 - v. <?php echo XILITIDYTAGS_VER; ?></h4>
							
					</div>
				</div>
				<?php wp_nonce_field('xilitagassign'); ?>
		</form>
		</div>
		<script type="text/javascript">
			//<![CDATA[
		
				
			var assignTable;
			jQuery(document).ready( function($) {
				<?php if ( $this->xili_settings['datatable_js'] == '' ) { ?>
				$('#tableupdating').hide();
				$('#assigntable').css({ visibility:'visible' });
				var assignTable = $('#assigntable').dataTable( {
					"iDisplayLength": 30,
					"bStateSave": true,
					"sDom": '<"topbanner"ipf>rt<"bottombanner"lp><"clear">',
					"sPaginationType": "full_numbers",
					"aLengthMenu": [[15, 30, 60, -1], [15, 30, 60, "<?php _e('All lines','xili_tidy_tags') ?>"]],
					"oLanguage": {
						"oPaginate": {
							"sFirst": "<?php _e('First','xili_tidy_tags') ?>",
							"sLast": "<?php _e('Last page','xili_tidy_tags') ?>",
							"sNext": "<?php _e('Next','xili_tidy_tags') ?>",
							"sPrevious": "<?php _e('Previous','xili_tidy_tags') ?>"
						},
						"sInfo": "<?php _e('Showing (_START_ to _END_) of _TOTAL_ tags','xili_tidy_tags') ?>",
						"sInfoFiltered": "<?php _e('(filtered from _MAX_ total entries)','xili_tidy_tags') ?>",
						"sLengthMenu": "<?php _e('Show _MENU_ tags','xili_tidy_tags') ?>",
						"sSearch": "<?php _e('Filter tags:','xili_tidy_tags') ?>"
					},	
					"aaSorting": [[1,'asc']],
					"aoColumns": [ 
						{ "bSearchable": false },
						null,
						{ "bSortable": false, "bSearchable": false },
						{ "bSortable": false, "bSearchable": false }]
				} );
				
				
				
				$('#update').click( function () {
					
					$('#assigntable').hide();
					
					$('#tableupdating').html("<br /><br /><h1><?php _e('Updating table of tags !','xili_tidy_tags') ?></h1>");
					$('#tableupdating').show();
					assignTable.fnDestroy();
				} );
				<?php } ?>
				// close postboxes that should be closed
				$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
				// postboxes setup
				postboxes.add_postbox_toggles('<?php echo $this->thehook2; ?>');
			});
			
						//]]>
		</script>
		<?php	
	}	
	/*
	 * Update the relationships according CheckBoxes array "sDom": '<"top"i>rt<"bottom"flp><"clear">'
	 *
	 */
	function checkboxes_update_them($tagsnamelike='',$tagsnamesearch='') {
	//print_r($_POST);
		$listgroups = get_terms(TAXOTIDYTAGS, array('hide_empty' => false,'get'=>'all'));
		if ( $this->fromgroupselect == 0 && $this->onlyuncheckedtags === false ) {
			$listtags = get_terms('post_tag', array('hide_empty' => false,'get'=>'all','name__like'=>$tagsnamelike,'search'=>$tagsnamesearch));
		} else {/* since 1.3.0 */
			if ( $this->onlyuncheckedtags === false ) { // one group
				$group_id[] = $this->fromgroupselect;
				if ( $this->onlyparent === false ) {
					$childterms = get_terms(TAXOTIDYTAGS, array('hide_empty' => false,'parent' => $this->fromgroupselect));
			 		if ( !empty($childterms) ) { 
			 			foreach ( $childterms as $childterm ) { /* if group is a parent, add all childs */
			 			 	$group_id[] = $childterm->term_id;
			 			}
			 		}
				}
			} else { // only all unchecked
				$listgroups = get_terms( TAXOTIDYTAGS, array('hide_empty' => false, 'get'=>'all') );
				foreach ( $listgroups as $group ) {
					$group_id[] = $group->term_id;
				}
				$this->groupexclude = true;
				$this->uncheckedtags = true;
			}
			$listtags = get_terms_of_groups_new ($group_id,TAXOTIDYTAGS,'post_tag', array('hide_empty' => false,'get'=>'all','name__like'=>$tagsnamelike,'search'=>$tagsnamesearch, 'orderby'=>'name'), $this->groupexclude, $this->uncheckedtags);	
		}
		
		foreach ($listtags as $tag) {
			$groupids = array();
			foreach ($listgroups as $group){
				$idcheck = 'line-'.$tag->term_id.'-'.$group->term_id;
				//$hiddencheck = 'hidd-'.$tag->term_id.'-'.$group->term_id;
				/*if (isset($_POST[$hiddencheck])) {
					if (!isset($_POST[$idcheck])) {
						//$box2reset[$group->term_id][]=$tag->term_id;
					} else {
						$groupids[]= (int) $group->term_id;
					}	
				} else {*/
				
					if ( isset($_POST[$idcheck]) ) {
						//$box2update[$group->term_id][]=$tag->term_id;
						$groupids[]= (int) $group->term_id;
					}
				//}
				
			}
			$datavisible = 'hidden_termid-'.$tag->term_id;
			// if ( isset($_POST[$datavisible]) )
				wp_set_object_terms((int) $tag->term_id, $groupids, TAXOTIDYTAGS,false);
		}
		
		return ;//$box2update;
	}
	
	/**
	 * Contextual help
	 *
	 * @since 1.5.0
	 */
	 function add_help_text($contextual_help, $screen_id, $screen) { 
	  // $contextual_help = var_dump($screen); // use this to help determine $screen->id
	  //echo $contextual_help;
	  //echo $screen_id;
	  //print_r($screen);
	  if ('tidy-tags_page_xili_tidy_tags_settings' == $screen->id ) {
	    $contextual_help =
	      '<p>' . __('Things to remember to set xili-tidy-tags:','xili_tidy_tags') . '</p>' .
	      '<ul>' .
	      '<li>' . __('If you use it for multilingual website: verify that the xili-language trilogy is active.','xili_tidy_tags') . '</li>' .
	      '<li>' . __('Update the list of targeted languages. See Actions left box.','xili_tidy_tags') . '</li>' .
	      '<li>' . __('For current site with lot of tags, create group or sub-group and go in assign page.','xili_tidy_tags') . '</li>' .
	      '<li>' . __('Don’t forget to activate a tags cloud widget.','xili_tidy_tags') . '</li>' .
	      '<li>' . __('In widget: the group for displaying tags in current lang is "the_curlang".','xili_tidy_tags') . '</li>' .
	      '<li>' . __('In widget: the group to set is the slug - trademark for TradeMark group - if you create one.','xili_tidy_tags') . '</li>' .
	      '</ul>' .
	      
	      '<p><strong>' . __('For more information:') . '</strong></p>' .
	      '<p>' . __('<a href="http://dev.xiligroup.com/xili-tidy-tags" target="_blank">Xili-tidy-tags Plugin Documentation</a>','xili_tidy_tags') . '</p>' .
	      '<p>' . __('<a href="http://codex.wordpress.org/" target="_blank">WordPress Documentation</a>','xili_tidy_tags') . '</p>' .
	      '<p>' . __('<a href="http://forum2.dev.xiligroup.com/" target="_blank">Support Forums</a>','xili_tidy_tags') . '</p>' ;
	  } else if ( 'tidy-tags_page_xili_tidy_tags_assign' == $screen->id ) {
	  	$contextual_help =
	  	'<p>' . __('Things to remember to assign tags to groups:','xili_tidy_tags') . '</p>'.
	  	'<ul>' .
	      '<li>' . __('Use features of paginated table.','xili_tidy_tags') . '</li>' .
	      '<li>' . __('Don’t forget to update by clicking button in left - Actions - titled meta box.','xili_tidy_tags') . '</li>' .
	  	'</ul>' .
	  	'<p><strong>' . __('For more information:') . '</strong></p>' .
	    '<p>' . __('<a href="http://dev.xiligroup.com/xili-tidy-tags" target="_blank">Xili-tidy-tags Plugin Documentation</a>','xili_tidy_tags') . '</p>' .
	    '<p>' . __('<a href="http://codex.wordpress.org/" target="_blank">WordPress Documentation</a>','xili_tidy_tags') . '</p>' .
	    '<p>' . __('<a href="http://forum2.dev.xiligroup.com/" target="_blank">Support Forums</a>','xili_tidy_tags') . '</p>' ;
	  }
	  return $contextual_help;
	}
	
	function check_other_xili_plugins () {
		$list = array();
		//if ( class_exists( 'xili_language' ) ) $list[] = 'xili-language' ;
		if ( class_exists( 'xili_language' ) ) $list[] = 'xili-language' ;
		if ( class_exists( 'xili_dictionary' ) ) $list[] = 'xili-dictionary' ;
		if ( class_exists( 'xilithemeselector' ) ) $list[] = 'xilitheme-select' ;
		if ( function_exists( 'insert_a_floom' ) ) $list[] = 'xili-floom-slideshow' ;
		if ( class_exists( 'xili_postinpost' ) ) $list[] = 'xili-postinpost' ;
		return implode (', ',$list) ;
	}
	
	function on_sidebox_mail_content ( $data ) {
		extract( $data );
		global $wp_version ;
		if ( '' != $emessage ) { ?>
	 		<h4><?php _e('Note:','xili_tidy_tags') ?></h4>
			<p><strong><?php echo $emessage;?></strong></p>
		<?php } ?>
		<fieldset style="margin:2px; padding:12px 6px; border:1px solid #ccc;"><legend><?php echo _e('Mail to dev.xiligroup', 'xili_tidy_tags'); ?></legend>
		<label for="ccmail"><?php _e('Cc:','xili_tidy_tags'); ?>
		<input class="widefat" id="ccmail" name="ccmail" type="text" value="<?php bloginfo ('admin_email') ; ?>" /></label><br /><br />
		<?php if ( false === strpos( get_bloginfo ('home'), 'local' ) ){ ?>
			<label for="urlenable">
				<input type="checkbox" id="urlenable" name="urlenable" value="enable" <?php if( $this->xili_settings['url']=='enable') echo 'checked="checked"' ?> />&nbsp;<?php bloginfo ('home') ; ?>
			</label><br />
		<?php } else { ?>
			<input type="hidden" name="onlocalhost" id="onlocalhost" value="localhost" />
		<?php } ?>
		<label for="themeenable">
			<input type="checkbox" id="themeenable" name="themeenable" value="enable" <?php if( $this->xili_settings['theme']=='enable') echo 'checked="checked"' ?> />&nbsp;<?php echo "Theme name= ".get_option ('stylesheet') ; ?>
		</label><br />
		<?php if (''!= WPLANG ) {?>
		<label for="wplangenable">
			<input type="checkbox" id="wplangenable" name="wplangenable" value="enable" <?php if( $this->xili_settings['wplang']=='enable') echo 'checked="checked"' ?> />&nbsp;<?php echo "WPLANG= ".WPLANG ; ?>
		</label><br />
		<?php } ?>
		<label for="versionenable">
			<input type="checkbox" id="versionenable" name="versionenable" value="enable" <?php if( $this->xili_settings['version']=='enable') echo 'checked="checked"' ?> />&nbsp;<?php echo "WP version: ".$wp_version ; ?>
		</label><br /><br />
		<?php $list = $this->check_other_xili_plugins();
		if (''!= $list ) {?>
		<label for="xiliplugenable">
			<input type="checkbox" id="xiliplugenable" name="xiliplugenable" value="enable" <?php if( $this->xili_settings['xiliplug']=='enable') echo 'checked="checked"' ?> />&nbsp;<?php echo "Other xili plugins = ".$list ; ?>
		</label><br /><br />
		<?php } ?>
		<label for="webmestre"><?php _e('Type of webmaster:','xili_tidy_tags'); ?>
		<select name="webmestre" id="webmestre" style="width:100%;">
			<option value="?" ><?php _e('Define your experience as webmaster…','xili_tidy_tags'); ?></option>
			<option value="newbie" ><?php _e('Newbie in WP','xili_tidy_tags'); ?></option>
			<option value="wp-php" ><?php _e('Good knowledge in WP and few in php','xili_tidy_tags'); ?></option>
			<option value="wp-php-dev" ><?php _e('Good knowledge in WP, CMS and good in php','xili_tidy_tags'); ?></option>
			<option value="wp-plugin-theme" ><?php _e('WP theme and /or plugin developper','xili_tidy_tags'); ?></option>
		</select></label>
		<br /><br />
		<label for="subject"><?php _e('Subject:','xili_tidy_tags'); ?>
		<input class="widefat" id="subject" name="subject" type="text" value="" /></label>
		<select name="thema" id="thema" style="width:100%;">
			<option value="" ><?php _e('Choose topic...','xili_tidy_tags'); ?></option>
			<option value="Message" ><?php _e('Message','xili_tidy_tags'); ?></option>
			<option value="Question" ><?php _e('Question','xili_tidy_tags'); ?></option>
			<option value="Encouragement" ><?php _e('Encouragement','xili_tidy_tags'); ?></option>
			<option value="Support need" ><?php _e('Support need','xili_tidy_tags'); ?></option>
		</select>
		<textarea class="widefat" rows="5" cols="20" id="mailcontent" name="mailcontent"><?php _e('Your message here…','xili_tidy_tags'); ?></textarea>
		</fieldset>
		<p>
		<?php _e('Before send the mail, check the infos to be sent and complete textarea. A copy (Cc:) is sent to webmaster email (modify it if needed).','xili_tidy_tags'); ?>
		</p>
		<div class='submit'>
		<input id='sendmail' name='sendmail' type='submit' tabindex='6' value="<?php _e('Send email','xili_tidy_tags') ?>" /></div>
		
		<div style="clear:both; height:1px"></div>
		<?php
	}
	
	
} /* end class */

/**
 * Display tidy tag cloud. (adapted form wp_tag_cloud - category-template)
 *
 * The text size is set by the 'smallest' and 'largest' arguments, which will
 * use the 'unit' argument value for the CSS text size unit. The 'format'
 * argument can be 'flat' (default), 'list', or 'array'. The flat value for the
 * 'format' argument will separate tags with spaces. The list value for the
 * 'format' argument will format the tags in a UL HTML list. The array value for
 * the 'format' argument will return in PHP array type format.
 *
 * The 'orderby' argument will accept 'name' or 'count' and defaults to 'name'.
 * The 'order' is the direction to sort, defaults to 'ASC' and can be 'DESC'.
 *
 * The 'number' argument is how many tags to return. By default, the limit will
 * be to return the top 45 tags in the tag cloud list.
 *
 * The 'topic_count_text_callback' argument is a function, which, given the count
 * of the posts  with that tag, returns a text for the tooltip of the tag link.
 *
 * The 'exclude' and 'include' arguments are used for the {@link get_tags()}
 * function. Only one should be used, because only one will be used and the
 * other ignored, if they are both set.
 *
 * @since 0.8.0
 * @updated 0.8.2, 1.2
 *
 * @param array|string $args Optional. Override default arguments.
 * @return array Generated tag cloud, only if no failures and 'array' is set for the 'format' argument.
 */
function xili_tidy_tag_cloud( $args = '' ) {
	if ( is_array($args) )
		$r = &$args;
	else
		parse_str($args, $r); 
	$defaults = array(
		'smallest' => 8, 'largest' => 22, 'unit' => 'pt', 'number' => 45,
		'format' => 'flat', 'orderby' => 'name', 'order' => 'ASC',
		'exclude' => '', 'include' => '', 'link' => 'view', 'tagsgroup' => '', 'tagsallgroup' => ''
	);
	$r = array_merge($defaults, $r);
	extract($r); /* above changed because new args */
	if (($tagsgroup == '' && $tagsallgroup == '' ) || !function_exists('get_terms_of_groups_new')) {
		$tags = get_tags( array_merge( $r, array( 'orderby' => 'count', 'order' => 'DESC' ) ) ); // Always query top tags
	} else {
		if ($tagsgroup !='') {
			$groupterm = is_term($tagsgroup,TAXOTIDYTAGS);
			$group_id[] = $groupterm['term_id'];
		}
		if ($tagsallgroup !='') {
			$groupterm = is_term($tagsallgroup,TAXOTIDYTAGS);
			$group_id[] = $groupterm['term_id'];
		}

		$tags = get_terms_of_groups_new ($group_id, TAXOTIDYTAGS,'post_tag',array_merge( $r, array( 'orderby' => 'count', 'order' => 'DESC' ))); 
	}	

	if ( empty( $tags ) )
		return;

	foreach ( $tags as $key => $tag ) {
		if ( 'edit' == $r['link'] )
			$link = get_edit_tag_link( $tag->term_id );
		else
			$link = get_tag_link( $tag->term_id );
		if ( is_wp_error( $link ) )
			return false;

		$tags[ $key ]->link = $link;
		$tags[ $key ]->id = $tag->term_id;
	}

	$return = wp_generate_tag_cloud( $tags, $r ); // Here's where those top tags get sorted according to $args

	//$return = apply_filters( 'wp_tag_cloud', $return, $r );

	if ( 'array' == $r['format'] )
			{return $return;}
 	echo $return;
}

/**
 * the tags for each post in loop 
 * (not in class for general use)
 *
 * @since 1.1 - 
 * @same params as the default the_tags() and and array as fourth param (see [xili_] get_object_terms for details)
 */
function xili_the_tags( $before = null, $sep = ', ', $after = '',$args = array() ) {
	if ( null === $before )
		$before = __('Tags: ');
	if ($args == array()) {	
		echo get_the_tag_list($before, $sep, $after);
	} else {
		echo xili_get_the_term_list($before, $sep, $after, $args); /* no filter tag_list*/
	}
}
/**
 * get_the tag_list for each post in loop $xili_tidy_tags
 * (not in class for general use)
 *
 * @since 1.1 - 
 * @same params as the default the_tags() and and array as fourth param
 */
function xili_get_the_term_list($before, $sep, $after, $args) {
 	global $post;
 	$id = (int) $post->ID;
 	$taxonomy = 'post_tag';
 	/* args analysis */
 	$defaults = array(
		'sub_groups' => ''
	);
	$r = array_merge($defaults, $args);
	extract($r);
 	if ($sub_groups == '') {
		 $terms = get_the_terms( $id, $taxonomy );
 	} else {
 		if (!is_array($sub_groups)) $sub_groups = explode(',',$sub_groups);
 		/* xili - search terms in sub groups */
 		$terms = get_object_term_cache( $id, $taxonomy.implode('-',$sub_groups));
		if ( false === $terms )
 			$terms = get_subgroup_terms_in_post ( $id, $taxonomy, $sub_groups );
 		
 	}
 	if ( is_wp_error( $terms ) )
		return $terms;

	if ( empty( $terms ) )
		return false;

	foreach ( $terms as $term ) {
		$link = get_term_link( $term, $taxonomy );
		if ( is_wp_error( $link ) )
			return $link;
		$term_links[] = '<a href="' . $link . '" rel="tag">' . $term->name . '</a>';
	}

	$term_links = apply_filters( "term_links-$taxonomy", $term_links );

	return $before . join( $sep, $term_links ) . $after;
}

function get_subgroup_terms_in_post ( $id, $taxonomy, $sub_groups ) {
	return xili_get_object_terms ($id,$taxonomy,array('tidy_tags_group'=>TAXOTIDYTAGS, 'sub_groups' => $sub_groups));
}

/**** Functions that improve taxinomy.php ****/

/**
 * get the terms of subgroups of the series objects 
 * (not in class for general use)
 *
 * @since 1.1 - 
 *
 */

function xili_get_object_terms($object_ids, $taxonomies, $args = array()) {
	
	global $wpdb;

	if ( !is_array($taxonomies) )
		$taxonomies = array($taxonomies);

	foreach ( (array) $taxonomies as $taxonomy ) {
		if ( ! is_taxonomy($taxonomy) )
			return new WP_Error('invalid_taxonomy', __('Invalid Taxonomy'));
	}

	if ( !is_array($object_ids) )
		$object_ids = array($object_ids);
	$object_ids = array_map('intval', $object_ids);

	$defaults = array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'all','tidy_tags_group' => '');
	$args = array_merge ( $defaults, $args );
	extract ($args);
	//echo "--- "; print_r($sub_groups);
		
	if (!is_array($sub_groups)) $sub_groups = array($sub_groups);
	foreach ($sub_groups as $tagsgroup) {
		if ($tagsgroup !='') {
			$groupterm = is_term($tagsgroup, $tidy_tags_group); //echo '----'.$tagsgroup;
			$group_ids[] = $groupterm['term_id'];
		}
	}
	$group_ids = array_map('intval', $group_ids);
		$group_ids = implode(', ', $group_ids); /* the terms ID of subgroups are now in list */
		
	$terms = array();
	if ( count($taxonomies) > 1 ) {
		foreach ( $taxonomies as $index => $taxonomy ) {
			$t = get_taxonomy($taxonomy);
			if ( isset($t->args) && is_array($t->args) && $args != array_merge($args, $t->args) ) {
				unset($taxonomies[$index]);
				$terms = array_merge($terms, wp_get_object_terms($object_ids, $taxonomy, array_merge($args, $t->args)));
			}
		}
	} else {
		$t = get_taxonomy($taxonomies[0]);
		if ( isset($t->args) && is_array($t->args) )
			$args = array_merge($args, $t->args);
	}

	extract($args, EXTR_SKIP);

	if ( 'count' == $orderby )
		$orderby = 'tt.count';
	else if ( 'name' == $orderby )
		$orderby = 't.name';
	else if ( 'slug' == $orderby )
		$orderby = 't.slug';
	else if ( 'term_group' == $orderby )
		$orderby = 't.term_group';
	else if ( 'term_order' == $orderby )
		$orderby = 'tr.term_order';
	else if ( 'none' == $orderby ) {
		$orderby = '';
		$order = '';
	} else {
		$orderby = 't.term_id';
	}

	// tt_ids queries can only be none or tr.term_taxonomy_id
	if ( ('tt_ids' == $fields) && !empty($orderby) )
		$orderby = 'tr.term_taxonomy_id';

	if ( !empty($orderby) )
		$orderby = "ORDER BY $orderby";

	$taxonomies = "'" . implode("', '", $taxonomies) . "'";
	$object_ids = implode(', ', $object_ids);

	$select_this = '';
	if ( 'all' == $fields )
		$select_this = 't.*, tt.*';
	else if ( 'ids' == $fields )
		$select_this = 't.term_id';
	else if ( 'names' == $fields )
		$select_this = 't.name';
	else if ( 'all_with_object_id' == $fields )
		$select_this = 't.*, tt.*, tr.object_id';
		
	$subselect = "SELECT st.term_id FROM $wpdb->term_relationships AS str INNER JOIN $wpdb->term_taxonomy AS stt ON str.term_taxonomy_id = stt.term_taxonomy_id INNER JOIN $wpdb->terms AS st ON st.term_id = str.object_id INNER JOIN $wpdb->term_taxonomy AS stt2 ON stt2.term_id = str.object_id WHERE stt.taxonomy IN ('".TAXOTIDYTAGS."') AND stt2.taxonomy = ".$taxonomies." AND stt.term_id IN (".$group_ids.") ";
	//echo $subselect;
	$query = "SELECT $select_this FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON tt.term_id = t.term_id INNER JOIN $wpdb->term_relationships AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tt.taxonomy IN ($taxonomies) AND tr.object_id IN ($object_ids) AND t.term_id IN ($subselect) $orderby $order"; //echo $query;

	if ( 'all' == $fields || 'all_with_object_id' == $fields ) {
		$terms = array_merge($terms, $wpdb->get_results($query));
		update_term_cache($terms);
	} else if ( 'ids' == $fields || 'names' == $fields ) {
		$terms = array_merge($terms, $wpdb->get_col($query));
	} else if ( 'tt_ids' == $fields ) {
		$terms = $wpdb->get_col("SELECT tr.term_taxonomy_id FROM $wpdb->term_relationships AS tr INNER JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tr.object_id IN ($object_ids) AND tt.taxonomy IN ($taxonomies) $orderby $order");
	}

	if ( ! $terms )
		$terms = array();

	return $terms;
}

/**
 * 
 * @updated 1.5.3
 */
function get_terms_of_groups_new ( $group_ids, $taxonomy, $taxonomy_child, $order = '', $not = false, $uncheckedtags = false ) {
		global $wpdb;
		if ( !is_array($group_ids) )
			$group_ids = array($group_ids);
		$group_ids = array_map('intval', $group_ids);
		$group_ids = implode(', ', $group_ids);
		$theorderby = '';
		$where = '';
		$defaults = array('orderby' => 'term_order', 'order' => 'ASC',
		'hide_empty' => true, 'exclude' => '', 'exclude_tree' => '', 'include' => '',
		'number' => '', 'slug' => '', 'parent' => '',
		'name__like' => '',
		'pad_counts' => false, 'offset' => '', 'search' => '');
		
		if (is_array($order)) { // for back compatibility
			$r = &$order;
			$r = array_merge($defaults, $r);
			extract($r);
			
			if ($order == 'ASC' || $order == 'DESC') {
				if ('term_order'== $orderby) {
					$theorderby = ' ORDER BY tr.'.$orderby.' '.$order ;
				} elseif ('count'== $orderby || 'parent'== $orderby) {
					$theorderby = ' ORDER BY tt2.'.$orderby.' '.$order ;
				} elseif ('term_id'== $orderby || 'name'== $orderby) {
					$theorderby = ' ORDER BY t.'.$orderby.' '.$order ;
				}
			}
			
			if ( !empty($name__like) )
			$where .= " AND t.name LIKE '{$name__like}%'";
		
			if ( '' != $parent ) {
				$parent = (int) $parent;
				$where .= " AND tt2.parent = '$parent'";
			}
		
			if ( $hide_empty && !$hierarchical )
				$where .= ' AND tt2.count > 0'; 
			// don't limit the query results when we have to descend the family tree 
			if ( ! empty($number) && '' == $parent ) {
				if( $offset )
					$limit = ' LIMIT ' . $offset . ',' . $number;
				else
					$limit = ' LIMIT ' . $number;
		
			} else {
				$limit = '';
			}
		
			if ( !empty($search) ) {
				$search = like_escape($search);
				$where .= " AND (t.name LIKE '%$search%')";
			}
		
		} else { // for back compatibility
			if ($order == 'ASC' || $order == 'DESC') $theorderby = ' ORDER BY tr.term_order '.$order ;
		}
		//$not = true;
		//SELECT t.*, tt.* FROM wp2_terms AS t INNER JOIN wp2_term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ('post_tag') ORDER BY t.name ASC
		
		if ( $not === false ) {
		$query = "SELECT t.*, tt2.term_taxonomy_id, tt2.description,tt2.parent, tt2.count, tt2.taxonomy, tr.term_order FROM $wpdb->term_relationships AS tr INNER JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id INNER JOIN $wpdb->terms AS t ON t.term_id = tr.object_id INNER JOIN $wpdb->term_taxonomy AS tt2 ON tt2.term_id = tr.object_id WHERE tt.taxonomy IN ('".$taxonomy."') AND tt2.taxonomy = '".$taxonomy_child."' AND tt.term_id IN (".$group_ids.") ".$where.$theorderby.$limit;
		} else {
			if ( $uncheckedtags ) { // current query + not in
		 		$query = "SELECT t.*, tt.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ('".$taxonomy_child."') AND (t.term_ID) NOT IN ("."SELECT t.term_id FROM $wpdb->term_relationships AS tr INNER JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id INNER JOIN $wpdb->terms AS t ON t.term_id = tr.object_id INNER JOIN $wpdb->term_taxonomy AS tt2 ON tt2.term_id = tr.object_id WHERE tt.taxonomy IN ('".$taxonomy."') AND tt2.taxonomy = '".$taxonomy_child."' AND tt.term_id IN (".$group_ids.") ".") ".$where.$theorderby.$limit;	
			} else {
				$query = "SELECT DISTINCT t.*, tt2.term_taxonomy_id, tt2.description,tt2.parent, tt2.count, tt2.taxonomy, tr.term_order FROM $wpdb->term_relationships AS tr INNER JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id INNER JOIN $wpdb->terms AS t ON t.term_id = tr.object_id INNER JOIN $wpdb->term_taxonomy AS tt2 ON tt2.term_id = tr.object_id WHERE tt.taxonomy IN ('".$taxonomy."') AND tt2.taxonomy = '".$taxonomy_child."' AND (t.term_ID) NOT IN ("."SELECT t.term_id FROM $wpdb->term_relationships AS tr INNER JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id INNER JOIN $wpdb->terms AS t ON t.term_id = tr.object_id INNER JOIN $wpdb->term_taxonomy AS tt2 ON tt2.term_id = tr.object_id WHERE tt.taxonomy IN ('".$taxonomy."') AND tt2.taxonomy = '".$taxonomy_child."' AND tt.term_id IN (".$group_ids.") ".") ".$where.$theorderby.$limit;
			}
		}
		//echo $query;
		$listterms = $wpdb->get_results($query);
		if ( ! $listterms )
			return array();

		return $listterms;
	}


/**
 * Create HTML check row (select) content for Tidy Tag Group List.
 *
 * @package xili-tidy-tags
 * @since 1.3.0
 * @uses Walker
 */
class Walker_TagGroupList_row extends Walker {
	/**
	 * @see Walker::$tree_type
	 * @since 1.3.0
	 * @var string
	 */
	var $tree_type = 'tidytaggroup';

	/**
	 * @see Walker::$db_fields
	 * @since 1.3.0
	 * @todo Decouple this
	 * @var array
	 */
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id');

	/**
	 * @see Walker::start_el()
	 * @since 1.3.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $term term data object.
	 * @param int $depth Depth of category. Used for padding.
	 */
	function start_el(&$output, $term, $depth) {
		/*$pad = str_repeat('&nbsp;', $depth * 3);*/
		if ($depth > 0) {
			$pad = str_repeat('– ', $depth);
			$term_name = $term->name;
		} else {
			$pad = '';
			$term_name = '<strong>'.$term->name.'</strong>';
		}
		
		$output .= '<input type="checkbox" id="line-%1$s-'.$term->term_id.'" name="line-%1$s-'.$term->term_id.'" value="'.$term->term_id.'" checked'.$term->term_id.' />'.$pad.$term_name.'&nbsp;&nbsp;';
	}
	/**
	 * @see Walker::end_lvl()
	 * @since 1.3.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of category. Used for tab indentation.
	 */
	function end_lvl(&$output, $depth) {
		$output .= "<br />";
	}
}

/**
 * Retrieve HTML check row (select) content for Tag Group List.
 *
 * @uses Walker_TagGroupList_row to create HTML  content line.
 * @since 1.3.0
 * @see Walker_TagGroupList_row::walk() for parameters and return description.
 */
function walk_TagGroupList_tree_row() {
	$args = func_get_args();
	// the user's options are the third parameter
	if ( empty($args[2]['walker']) || !is_a($args[2]['walker'], 'Walker') ) {
		$walker = new Walker_TagGroupList_row;
	} else {
		$walker = $args[2]['walker'];
	}	
	return call_user_func_array(array( &$walker, 'walk' ), $args );
}

/**
 * Create Sorted array of Tags from Group List.
 * 
 * @since 1.3.0
 *
 */
class Walker_TagGroupList_sorted extends Walker {
	/**
	 * @see Walker::$tree_type
	 * @since 1.3.0
	 * @var string
	 */
	var $tree_type = 'tidytaggroup';

	/**
	 * @see Walker::$db_fields
	 * @since 1.3.0
	 * @todo Decouple this
	 * @var array
	 */
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id');

	/**
	 * @see Walker::start_el()
	 * @since 1.3.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $term term data object.
	 * @param int $depth Depth of category. Used for padding.
	 */
	function start_el(&$output, $term, $depth) {
		/*$pad = str_repeat('&nbsp;', $depth * 3);*/
		$output[] = $term;
	}
}
/**
 * Retrieve Sorted array of Tags from Group List.
 *
 * @uses Walker_TagGroupList_sorted to sort.
 * @since 1.3.0
 * @see Walker_TagGroupList_sorted::walk() for parameters and return description.
 */
function walk_TagGroupList_sorted() {
	$args = func_get_args();
	// the user's options are the third parameter
	if ( empty($args[2]['walker']) || !is_a($args[2]['walker'], 'Walker') ) {
		$walker = new Walker_TagGroupList_sorted;
	} else {
		$walker = $args[2]['walker'];
	}	
	return call_user_func_array(array( &$walker, 'walk' ), $args );
}

/**
 * class for multiple tidy tags cloud widgets
 * @since 1.3.3  
 * @updated 1.5.0 rewritten as extends
 */

class xili_tidy_tags_cloud_multiple_widgets extends WP_Widget {
	
	function xili_tidy_tags_cloud_multiple_widgets() {
		load_plugin_textdomain('xili_tidy_tags',PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)), dirname(plugin_basename(__FILE__)));
		$widget_ops = array('classname' => 'xili_tdtc_widget', 'description' => __( "Cloud of grouped tags by xili-tidy-tags plugin",'xili_tidy_tags' ) );
		$this->WP_Widget('xili_tidy_tags_cloud_widget', __("Tidy tags cloud", 'xili_tidy_tags'), $widget_ops);
		$this->alt_option_name = 'xili_tidy_tags_cloud_widgets_options';
	}
	
	function widget( $args, $instance ) {
		
		extract($args, EXTR_SKIP);
		
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget.$before_title.$title.$after_title;
		
		$cloudsargs = array();
		
		if ('the_curlang' == $instance['tagsgroup'] && class_exists( 'xili_language' ) ) { // if xl temporary desactivate
			$cloudsargs[] = 'tagsgroup='.the_curlang();
		} elseif ('the_category' == $instance['tagsgroup'])  {	
			$cloudsargs[] = 'tagsgroup='.single_cat_title('',false);
		} else {
			$cloudsargs[] = 'tagsgroup='.$instance['tagsgroup'];
		}
		$cloudsargs[] = 'tagsallgroup='.$instance['tagsallgroup'];
		
		if ( abs( (int) $instance['smallest'] ) > 0 ) $cloudsargs[] = 'smallest='.abs((int) $instance['smallest']);
		if ( abs( (int) $instance['largest'] ) > 0  ) $cloudsargs[] = 'largest='.abs((int) $instance['largest']);
		if ( abs( (int) $instance['quantity'] ) > 0 ) $cloudsargs[] = 'number='.abs((int) $instance['quantity']); // fixe number
		
		if ('no' != $instance['orderby'] ) $cloudsargs[] = 'orderby='.$instance['orderby'];
		if ('no' != $instance['order'] ) $cloudsargs[] = 'order='.$instance['order'];
		
		$cloudsargs[] = 'format='.$instance['displayas'];
		
				
		echo '<div class="xilitidytagscloud">';
			xili_tidy_tag_cloud(implode("&",$cloudsargs));
		echo '</div>';
		
		echo $after_widget;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		$instance['title'] = strip_tags($new_instance['title']);
		
		$instance['tagsgroup'] = strip_tags(stripslashes($new_instance['tagsgroup']));
		$instance['tagsallgroup'] = strip_tags(stripslashes($new_instance['tagsallgroup']));
		$instance['smallest'] = strip_tags(stripslashes($new_instance['smallest']));
		$instance['largest'] = strip_tags(stripslashes($new_instance['largest']));
		$instance['quantity'] = strip_tags(stripslashes($new_instance['quantity']));
		$instance['orderby'] = strip_tags(stripslashes($new_instance['orderby']));
		$instance['order'] = strip_tags(stripslashes($new_instance['order'])); 
		$instance['displayas'] = strip_tags(stripslashes($new_instance['displayas']));
		
		return $instance;
	}
	
	function form( $instance ) {
		
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$tagsgroup = attribute_escape($instance['tagsgroup']);
		$tagsallgroup = attribute_escape($instance['tagsallgroup']);
		$smallest = attribute_escape($instance['smallest']);
		$largest = attribute_escape($instance['largest']);
		$quantity = attribute_escape($instance['quantity']);
		$orderby = $instance['orderby'];
		$order = $instance['order'];
		$displayas = $instance['displayas'];
		$listterms = get_terms(TAXOTIDYTAGS, array('hide_empty' => false)); 
		$listtagsgroupssorted = walk_TagGroupList_sorted( $listterms, 3, null, null );
		
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		
		<label for="<?php echo $this->get_field_id('tagsgroup'); ?>" ><?php _e('Group','xili_tidy_tags') ?> : </label><br />
		<select name="<?php echo $this->get_field_name('tagsgroup'); ?>" id="<?php echo $this->get_field_id('tagsgroup'); ?>" style="width:90%;">
		<option value="" ><?php _e('Choose a group…','xili_tidy_tags'); ?></option>
		<?php /* group named as current language */
		if (class_exists('xili_language') ) { ?>
			<option value="the_curlang" <?php echo ($tagsgroup == 'the_curlang' ) ? 'selected="selected"':'' ; ?> ><?php _e('Current language','xili_tidy_tags');  ?></option>
		<?php } 
		/* group named as current category */ ?>
		
		<option value="the_category" <?php echo ($tagsgroup == 'the_category' ) ? 'selected="selected"':'' ; ?> ><?php _e('Current category','xili_tidy_tags');  ?></option>
		<?php
		foreach ($listtagsgroupssorted as $curterm) {
			$ttab = ($curterm->parent == 0) ? '' : '– ' ;
			$checked = ($tagsgroup == $curterm->slug) ? 'selected="selected"' :'' ;
			echo '<option value="'.$curterm->slug.'" '.$checked.' >'.$ttab.$curterm->name.'</option>';
						
		} ?>
		</select>
		
		<br />
		<label for="<?php echo $this->get_field_id('tagsallgroup'); ?>" ><?php _e('Group #2','xili_tidy_tags') ?> : </label><br />
		
		<select name="<?php echo $this->get_field_name('tagsallgroup'); ?>" id="<?php echo $this->get_field_id('tagsallgroup'); ?>" style="width:90%;">
		<option value="" ><?php _e('(Option) Choose a 2nd group…','xili_tidy_tags'); ?></option>
		
		<?php
		foreach ($listtagsgroupssorted as $curterm) {
			$ttab = ($curterm->parent == 0) ? '' : '– ' ;
			$checked = ($tagsallgroup == $curterm->slug) ? 'selected="selected"' :'' ;
			echo '<option value="'.$curterm->slug.'" '.$checked.' >'.$ttab.$curterm->name.'</option>';
						
		} ?>
		</select>
		
		<br />
		<label for="<?php echo $this->get_field_id('smallest'); ?>" ><?php _e('Smallest size','xili_tidy_tags') ?> : <input id="<?php echo $this->get_field_id('smallest'); ?>" name="<?php echo $this->get_field_name('smallest'); ?>" type="text" value="<?php echo $smallest ?>" /></label><br />
		<label for="<?php echo $this->get_field_id('largest'); ?>" ><?php _e('Largest size','xili_tidy_tags') ?> : <input id="<?php echo $this->get_field_id('largest'); ?>" name="<?php echo $this->get_field_name('largest'); ?>" type="text" value="<?php echo $largest ?>" /></label><br />
		<label for="<?php echo $this->get_field_id('quantity'); ?>" ><?php _e('Number','xili_tidy_tags') ?> : <input id="<?php echo $this->get_field_id('quantity'); ?>" name="<?php echo $this->get_field_name('quantity'); ?>" type="text" value="<?php echo $quantity ?>" /></label>
		<fieldset style="margin:2px; padding:3px; border:1px solid #ccc;"><legend><?php _e('Order and sorting infos','xili_tidy_tags') ?></legend>
		<select name="<?php echo $this->get_field_name('orderby'); ?>" id="<?php echo $this->get_field_id('orderby'); ?>" style="width:100%;"> 		<?php 
		echo '<option value="no" >'.__('no orderby','xili_tidy_tags').'</option>';
		echo '<option value="count" '.(($orderby == "count") ? 'selected="selected"' :'').' >'.__('count','xili_tidy_tags').'</option>';
		echo '<option value="name" '.(($orderby == "name") ? 'selected="selected"' :'').' >'.__('name','xili_tidy_tags').'</option>'; ?>
		</select>
		<select name="<?php echo $this->get_field_name('order'); ?>" id="<?php echo $this->get_field_id('order'); ?>" style="width:100%;">
		<?php
		echo '<option value="no" >'.__('no order','xili_tidy_tags').'</option>';
		echo '<option value="ASC" '.(($order == "ASC") ? 'selected="selected"' :'').' >'.__('ASC','xili_tidy_tags').'</option>';
		echo '<option value="DESC" '.(($order == "DESC") ? 'selected="selected"' :'').' >'.__('DESC','xili_tidy_tags').'</option>';
		?>
		</select>
		</fieldset>
		<fieldset style="margin:2px; padding:3px; border:1px solid #ccc;"><legend><?php _e('Display as','xili_tidy_tags') ?></legend>
		<select name="<?php echo $this->get_field_name('displayas'); ?>" id="<?php echo $this->get_field_id('displayas'); ?>" style="width:100%;"> <?php
		echo '<option value="flat" '.(($displayas == "flat") ? 'selected="selected"' :'').' >'.__('Cloud','xili_tidy_tags').'</option>';
		echo '<option value="list" '.(($displayas == "list") ? 'selected="selected"' :'').' >'.__('List','xili_tidy_tags').'</option></select>';
		?>
		</fieldset>
	<?php
	}
}

/**
 * Shortcode to insert a cloud of a group of tags inside a post.
 *
 * Example of shortcode : [xili-tidy-tags params="tagsgroup=trademark&largest=10&smallest=10" glue=" | "]
 *
 *@since 1.0
 */
function xili_tidy_tags_shortcode ($atts) {
	$arr_result = shortcode_atts(array('params'=>'', 'glue'=> ' ' ), $atts);
	extract($arr_result);
	return implode($glue, xili_tidy_tag_cloud(html_entity_decode($params)."&format=array")); /* don't use param echo only in 2.8 */
}
add_shortcode('xili-tidy-tags', 'xili_tidy_tags_shortcode');

/**
 * instantiation of xili_tidy_tags class
 *
 * @since 0.8.0 - 0.9.5 =& for instantiation
 *
 */
 
$xili_tidy_tags =& new xili_tidy_tags ();
 

if ( $wp_version >= '2.8.0') {
	function add_xtt_widgets() {
 		register_widget('xili_tidy_tags_cloud_multiple_widgets'); // since 1.5.0
	}
	// comment below lines if you don't use widget(s)
	add_action('widgets_init','add_xtt_widgets');
}

/**
 * example of selection of tags of a group as used in xili-tidy-tags dashboard
 * only for tests
 * @since 1.4.2
 */
 function xili_tags_from_group( $group_name, $mode = 'slug' ) {
	// from $group_name to ID
	$taxonomy = TAXOTIDYTAGS ;
	$taxonomy_child = 'post_tag' ;
	$groupterm = term_exists($group_name, $taxonomy); 
	$group_id  = $groupterm['term_id']; 
	// return array of tags as object
	$args = array('orderby' => 'name', 'order' => 'ASC', 'hide_empty' => false ); // even if no post attached to a tag - see doc inside source of xili-tidy-tags
	$thetags = get_terms_of_groups_new ( $group_id, $taxonomy, $taxonomy_child, $args );
	// example of array as expected by S.Y. but here with key - 
	$result_array = array();
	if ( $thetags ) {
		foreach ( $thetags as $onetag ) {
			if ( $mode == 'array' ) {
				$result_array[] = array('tag_name' => $onetag->name, 'tag_id' => $onetag->term_id);
			} else { // slug for link or $query
				$result_array[] = $onetag->slug ;
			}
			
		}
		
	return $result_array ;	
	
	}
	
}

/**
 *  return the link to show posts of a xili_tags_group
 *	can be used in template - used in tags group cloud
 *  example : echo '<a href="'.link_for_posts_of_xili_tags_group ('trademark').'" >Trademark</a>'
 *
 * @param: slug of target tags-group
 * @since 1.5.4
 */
 function link_for_posts_of_xili_tags_group ( $tags_group ) {
 	if ( $tags_group != "" ) {
		$thetags = xili_tags_from_group( $tags_group ) ;
		if ( $thetags ) {
			$list = implode (',',$thetags);
			return get_bloginfo('siteurl').'?tag='.$list;
		}
 	}
 }

/**
 * get tags-group as list with link to show Posts with tags belonging to each tags-group
 *
 * examples :
 * echo xili_tags_group_list (); // by default show only non languages group
 * echo xili_tags_group_list ( ', ', array ('tidy-languages-group','software') ); // show all tags group excluding langs and 'software'
 * echo xili_tags_group_list ( ', ', array ('tidy-languages-group') , 'Posts with tags belonging to %s tags-group') ;
 *
 * @param: $separator in list
 * @param: array of excluded slugs - 'tidy-languages-group' is for languages groups
 * @param: title format as in sprintf - %s = tagsgroup name
 *
 * @since 1.5.4
 *
 */
 function xili_tags_group_list ( $separator = ', ', $exclude = array ( 'tidy-languages-group' ), $title ='' ) {
	global $xili_tidy_tags;
	
	$result = array();
	$listgroups = get_terms(TAXOTIDYTAGS, array('hide_empty' => false,'get'=>'all'));
	
	if ( $listgroups ) {
		foreach ( $listgroups as $tagsgroup ) {
			if ( !in_array( $tagsgroup->slug , $exclude ) &&  !( in_array( 'tidy-languages-group' , $exclude ) && $tagsgroup->parent == $xili_tidy_tags->langgroupid ) ) {
				$thetitle = ( $title == '' ) ? '' :  'title="'.sprintf( $title, $tagsgroup->name ).'"' ;
				
				$result[] = '<a href="'.link_for_posts_of_xili_tags_group ($tagsgroup->slug).'" '.$thetitle.' >'.$tagsgroup->name.'</a>';
			}
		}
		return implode ( $separator, $result );
	}	
 }

/**
 * example to display ID of posts in a group tags
 *
 * @since 1.5.4
 *
 */
 function example_get_posts_of_xili_tags_group ( $tags_group ) {

	if ( $tags_group != "" ) {
		$thetags = xili_tags_from_group( $tags_group ) ;
		if ( $thetags ) {
			$list = implode (',',$thetags);
			$query = new WP_Query( 'tag='.$list );
			if ( $query->have_posts() )  {
				while ( $query->have_posts() ) : $query->the_post();
					// modify here
					echo '- '.get_the_ID().' -';
				endwhile;
			}
		} 
	}

 }

?>