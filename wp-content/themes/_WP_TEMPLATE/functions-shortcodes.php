<?php

add_shortcode("contacts", "_WP_TEMPLATE_shortcode_contacts");
add_shortcode("subpages", "_WP_TEMPLATE_shortcode_subpages");

/* SYSTEM SHORTCODES ------------------------------------------------------------------------------------------------------------- */


/* OTHER SHORTCODES -------------------------------------------------------------------------------------------------------------- */

// [contacts]
function _WP_TEMPLATE_shortcode_contacts($atts, $content=""){
  return get_option("_WP_TEMPLATE_contacts");
}

// [subpages id="parent_id"]
function _WP_TEMPLATE_shortcode_subpages($atts, $content=""){
  extract(shortcode_atts(array(
    'id'=>get_the_ID(),
  ), $atts));

  foreach(get_pages(array('child_of'=>$id,'parent'=>$id,'sort_column'=>'post_date','sort_order'=>'ASC')) as $subpage){
    $content .= '<div class="subpage"><a class="link" href="'.$subpage->post_link.'">'.$subpage->post_title.'</a>';
    $content .= '<div class="content">'.$subpage->post_content.'</div>';
    $content .= '</div>';
  }
  return $content;
}
