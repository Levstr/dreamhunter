<?php
/**
 * Plugin Name: Post From Site
 * Plugin URI: http://redradar.net/2010/07/post-from-site-v2-0-1/
 * Description: Add a new post directly from your website - no need to go to the admin side.
 * Author: Kelly Dwan
 * Version: 2.0.3
 * Date: 08.13.10
 * Author URI: http://www.redradar.net/
 */

/* We need the admin functions to use wp_create_category(). */
require_once(ABSPATH . 'wp-admin' . "/includes/admin.php");
require_once('pfs-admin.php');
require_once('pfs-form.php');
//require_once('pfs-widget.php');

/**
 * Add text domain to enable localization.
 */
function pfs_add_domain(){
    load_plugin_textdomain('pfs_domain');
    register_setting('pfs_options', 'pfs_options', 'pfs_validate');
} add_action('init','pfs_add_domain');

/**
 * Sanitize and validate input. 
 * @param array $input an array to sanitize
 * @return array a valid array.
 */
function pfs_validate($input) {
    $ok = Array('publish','pending','draft');
    $input['pfs_linktext'] = wp_filter_nohtml_kses($input['pfs_linktext']);
    $input['pfs_excats'] = wp_filter_nohtml_kses($input['pfs_excats']);;
    $input['pfs_allowimg'] = ($input['pfs_allowimg'] == 1 ? 1 : 0);
    $input['pfs_allowcat'] = ($input['pfs_allowcat'] == 1 ? 1 : 0);
    $input['pfs_allowtag'] = ($input['pfs_allowtag'] == 1 ? 1 : 0);
    $input['pfs_post_status'] = (in_array($input['pfs_post_status'],$ok) ? $input['pfs_post_status'] : 'pending');
    $input['pfs_comment_status'] = ($input['pfs_comment_status'] == 'open' ? 'open' : 'closed');
    $input['pfs_imgdir'] = (is_dir($input['pfs_imgdir']) ? $input['pfs_imgdir'] : dirname(__FILE__));
    $input['pfs_maxfilesize'] = intval($input['pfs_maxfilsize']);
    $input['pfs_bgcolor'] = wp_filter_nohtml_kses($input['pfs_bgcolor']);
    $input['pfs_bgimg'] = wp_filter_nohtml_kses($input['pfs_bgimg']);
    $input['pfs_titlecolor'] =  wp_filter_nohtml_kses($input['pfs_titlecolor']);
    $input['pfs_textcolor'] =  wp_filter_nohtml_kses($input['pfs_textcolor']);
    $input['pfs_customcss'] =  wp_filter_nohtml_kses($input['pfs_customcss']);
    return $input;
}

/**
 * Add javascript and css to header files.
 */
function pfs_includes(){
    wp_enqueue_script( 'jquery-multi-upload', plugins_url("jquery.MultiFile.pack.js",__FILE__), array('jquery','jquery-form') );
    wp_enqueue_script( 'pfs-script', plugins_url("pfs-script.js",__FILE__) );
    wp_enqueue_style( 'pfs-style',  plugins_url("pfs-style.php",__FILE__) );
} add_action('get_header','pfs_includes');


/**
 * Add options to databases with defaults
 */
function show_pfs_settings() {
    add_options_page('Post From Site', 'Post From Site', 'manage_options', 'pfs', 'pfs_settings');
    if (!get_option("pfs_options")) {
        $pfs_options['pfs_linktext'] = 'quick post';
        $pfs_options['pfs_excats'] = 0;
        $pfs_options['pfs_allowimg'] = 0;
        $pfs_options['pfs_allowcat'] = 1;
        $pfs_options['pfs_allowtag'] = 1;
        $pfs_options['pfs_post_status'] = 'publish';
        $pfs_options['pfs_comment_status'] = 'open';
        $pfs_options['pfs_imgdir'] = dirname(__FILE__);
        $pfs_options['pfs_maxfilesize'] = 2*1024*1024;
        $pfs_options['pfs_bgcolor'] = '#EDF0CF';
        $pfs_options['pfs_bgimg'] = 'pfs_title.png';
        $pfs_options['pfs_titlecolor'] = '';
        $pfs_options['pfs_textcolor'] = 'black';
        $pfs_options['pfs_customcss'] = '';
        add_option ("pfs_options", $pfs_options) ;
    }
} add_action('admin_menu','show_pfs_settings');

/* * *
 * Convert number in bytes into human readable format (KB, MB etc)
 * @param int $filesize number in bytes to be converted
 * @return string bytes in human readable form
 */
function display_filesize($filesize){
    if(is_numeric($filesize)) {
        $decr = 1024; $step = 0;
        $prefix = array('B','KB','MB','GB','TB','PB');
        while(($filesize / $decr) > 0.9){
            $filesize = $filesize / $decr;
            $step++;
        }
        return round($filesize,2).$prefix[$step];
    } else {
        return 'NaN';
    }
}

/**
 * Convert string filesize in KB (or MB etc) into integer bytes
 * @param string $filesize size to be converted
 * @return int filesize in bytes
 */
function filesize_bytes($filesize){
    $prefix = array('B'=>0,'KB'=>1,'MB'=>2,'GB'=>3,'TB'=>4);
    preg_match('/([0-9]*{\.[0-9]*}?)([KMGT]?B)/', strtoupper($filesize), $match);
    if ('' != $match[0]) {
        $size = $match[1];
        for ($i = 0; $i < $prefix[$match[2]]; $i++) $size *= 1024;
    }
    return $size;
}
