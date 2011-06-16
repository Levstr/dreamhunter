<?php
function plugin_file_url($plugin_name, $relative_path){
  return WP_PLUGIN_URL  . "/$plugin_name/" . $relative_path;
}