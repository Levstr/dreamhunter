<?php
function cf7_logger_log_export($post_type, $filter) {
  $posts = cf7_logger_get_records($post_type, $filter);
  $fields = cf7_logger_get_post_meta_superset($posts);
  $rows = array();
  array_unique($fields);
  $rows []=  apply_filters('log_format_first_row_'.$post_type,$fields);

  foreach($posts as $post){
    $current_row = array();
    foreach($fields as $field){
      $current_row [$field]= apply_filters('log_format_column_'.$post_type, get_post_meta($post->ID, $field, true));
    }
    $current_row['post_date'] = $post->post_date;
    $rows []= apply_filters('log_format_row_'.$post_type,$current_row);
  }
  get_download($rows);
  die();
}

function csv_export($data, $options = array()){
  foreach($data as $row){
    $str = '';
    foreach($row as $value){
      if (preg_match('/[",]/', $value)){
        $value = str_replace('"', '""', $value);
        $value = "\"$value\"";
      }
      $str .= "$value,";
    }
    $str = preg_replace('/,$/', '',$str);
    echo("$str\r\n");
  }
}


function get_download($data) {
    header('Content-Description: File Transfer');
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename=Appointment Log.csv');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    csv_export($data);
}
