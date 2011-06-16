<?php

function lc_activate(){
  global $wp_rewrite;
  $wp_rewrite->flush_rules();
}

function lc_deactivate(){
  global $wp_rewrite;
  $wp_rewrite->flush_rules();
}
?>