<?php
function insert_or_update_row($table, $data, $where){
  global $wpdb;
  $id=update_row($table, $data, $where);
  if(!$id){
    $wpdb->insert($table, $data);
    $id=$wpdb->insert_id;
  }
  return $id;
}

function update_row($table, $data, $where){
  global $wpdb;
  $id=0;
  $res = select_rows($table, $where);
  if(isset($res[0])){
    if(false===$wpdb->update($table, $data, $where)) echo 'failed to update';
    $id=$res[0]['id'];
  }
  return $id;
}

function select_rows($table, $where){
  global $wpdb;
  return $wpdb->get_results($wpdb->prepare("SELECT * FROM `$table` WHERE ".prepare_ids_to_check_string($where)), ARRAY_A);
}
function delete_rows($table, $where){
  global $wpdb;
  return $wpdb->query($wpdb->prepare("DELETE FROM `$table` WHERE ".prepare_ids_to_check_string($where)));
}
function prepare_ids_to_check_string($ids){
  $arr=array(); foreach((array)$ids as $id=>$v) $arr[]="`$id`='$v'";
  return implode(" AND ",$arr);
}
function prepare_ids_to_group_string($ids){
  $arr=array(); foreach((array)$ids as $id=>$v) $arr[]="`$id`";
  return implode(", ",$arr);
}
prepare_ids_to_check_string($values)
?>