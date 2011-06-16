<?php

add_filter('body_class','dreamhunter_filter_body_class');
add_filter('wp_page_menu_args', 'dreamhunter_filter_wp_page_menu_args');
add_filter('excerpt_length', 'dreamhunter_filter_excerpt_length');
add_filter('excerpt_more', 'dreamhunter_filter_excerpt_more');
add_filter('get_the_excerpt', 'dreamhunter_filter_get_the_excerpt');
add_filter('search_tours_results', 'dreamhunter_search_tours_results');
add_filter('request', 'dreamhunterfeed_request');
add_filter('get_comment_link', 'dh_redirect_after_comment_filter');



/* SYSTEM FILTERS -------------------------------------------------------------------------------------------------------------- */


function dreamhunter_filter_body_class($classes) {
  global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

  if($is_lynx) $classes[] = 'lynx';
  elseif($is_gecko) $classes[] = 'gecko';
  elseif($is_opera) $classes[] = 'opera';
  elseif($is_NS4) $classes[] = 'ns4';
  elseif($is_safari) $classes[] = 'safari';
  elseif($is_chrome) $classes[] = 'chrome';
  elseif($is_IE) $classes[] = 'ie';
  else $classes[] = 'unknown';

  if($is_iphone) $classes[] = 'iphone';
  return $classes;
}


function dreamhunter_filter_wp_page_menu_args($args) {
  $args['show_home'] = true;
  return $args;
}

function dreamhunter_filter_excerpt_length($length) {
  return 60;
}
function dreamhunter_filter_excerpt_more( $more ) {
  return ' &hellip; <a href="'.get_permalink().'">Далее &rarr;</a>';
}
function dreamhunter_filter_get_the_excerpt($output) {
  if(has_excerpt() && !is_attachment())
    $output .= '<a href="'.get_permalink().'">Далее &rarr;</a>';

  return $output;
}

/* OTHER FILTERS --------------------------------------------------------------------------------------------------------------- */

function dreamhunter_search_tours_results($content){
	return "123";
}

function dreamhunterfeed_request($qv) { // добавление новостей и горячих предложений в rss
    if (isset($qv['feed']) && !isset($qv['post_type']))
        $qv['post_type'] = array('post', 'deal');
    return $qv;
}

function dh_redirect_after_comment_filter($loc) {
    if(isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'cabinet')) $loc='/cabinet/';
    return $loc;
}
