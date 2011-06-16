<?php

$ex_query_filters = array();
$ex_query_filters []= new MetaAndQueryFilter();
$ex_query_filters []= new DateRangeQuery();
$ex_query_filters []= new TitleContainsQuery();

class QueryFilter{
  function __construct(){
    add_filter('posts_where', array(&$this, 'filter_posts_where'), 10, 2);
    add_filter('posts_where', array(&$this, 'filter_posts_where_compare'), 10, 2);
    add_filter('posts_join', array(&$this, 'filter_posts_join'), 10, 2);
    add_filter('parse_query', array(&$this, 'filter_parse_query'));
  }
  
  function filter_posts_where($where, &$query){return $where;}
	function filter_posts_where_compare($where, &$query){return $where;}
  function filter_posts_join($join, &$query){return $join;}
  function filter_parse_query_hook(&$query){
    
  }
  function filter_parse_query(&$query){}
}

class MetaAndQueryFilter extends QueryFilter{
  
  function filter_posts_where($where, &$query){
    global $wpdb;
    if (isset($query->query_vars['meta_and']) && is_array($query->query_vars['meta_and'])){
      $index = 0;
      foreach($query->query_vars['meta_and'] as $key => $value){
        if (!empty($value)){
          $where .= $wpdb->prepare(" AND meta_$index.meta_key = %s AND meta_$index.meta_value = %s",$key, $value);
          $index ++;
        }
      }
    }
    return $where;
  }
  
	function filter_posts_where_compare($where, &$query){
		/* Accepts:
		array(
			'meta name', 'operation' [> < = >= <=], 'value'
		)
		*/
		global $wpdb;
		if (isset($query->query_vars['meta_compare']) && is_array($query->query_vars['meta_compare'])) {
			$index = 0;
      foreach($query->query_vars['meta_compare'] as $var){
				if ($var['value'] && in_array($var['operation'], array('>', '<', '>=', '<=', '='))) {
					$op = $var['operation'];
					$val = $var['value'];
					if (in_array($op, array('>', '<', '>=', '<=', '='))) {
	        $where .= $wpdb->prepare(
						" AND meta_compare_$index.meta_key = %s AND meta_compare_$index.meta_value $op $val",$var['key']);
	        $index ++;
					}
				}
      }
    }
		return $where;
	}

  function filter_posts_join($join, &$query){
    global $wpdb;
    if (isset($query->query_vars['meta_and'])){
      $index = 0;
      foreach($query->query_vars['meta_and'] as $key => $value){
        if (!empty($value)){
          $join .= " INNER JOIN $wpdb->postmeta meta_$index ON {$wpdb->posts}.ID  = meta_$index.post_id ";
          $index ++;
        }
      }
		}
		if (isset($query->query_vars['meta_compare']) && is_array($query->query_vars['meta_compare'])) {
			$index = 0;
      foreach($query->query_vars['meta_compare'] as $var){
				if ($var['value'] && in_array($var['operation'], array('>', '<', '>=', '<=', '='))) {
	        $join .= " INNER JOIN $wpdb->postmeta meta_compare_$index ON {$wpdb->posts}.ID  = meta_compare_$index.post_id ";
	        $index ++;
					}
        }
      }
    return $join;
  }

}
function date2mysql($date){
  return date('Y-m-d', strtotime($date));
}

class DateRangeQuery extends QueryFilter {
  function filter_posts_where($where, &$query){
    global $wpdb;
    if (isset($query->query_vars['date_from']) && isset($query->query_vars['date_to'])){
      $where .= $wpdb->prepare(' AND post_date BETWEEN DATE(%s) AND DATE(%s) ', 
        date2mysql($query->query_vars['date_from'] . " 00:00:00"),
        date('Y-m-d', strtotime($query->query_vars['date_to']) + 86400));
    } else {
      if (isset($query->query_vars['date_from'])){
        $where .= $wpdb->prepare(' AND post_date >= DATE(%s) ', date2mysql($query->query_vars['date_from']));
      }
      if (isset($query->query_vars['date_to'])){
        $where .= $wpdb->prepare(' AND post_date <= DATE(%s) ', date2mysql($query->query_vars['date_from']));
      }
    }
    return $where;
  }
}

class TitleContainsQuery extends QueryFilter{
  function filter_posts_where($where, &$query){
    global $wpdb;
    if (isset($query->query_vars['title_contains'])){
      $where .= $wpdb->prepare(" AND $wpdb->posts.post_title LIKE (%s) ", "%" . $query->query_vars['title_contains'] . "%");
    }
    return $where;
  }
}

function map_objects_to_array($array, $property){
  $result = array();
  foreach ($array as $item)
    $result []= $item->$property;
  return $result;
}

function get_term_parents($term_id, $taxonomy, $parents = array()){
  $term = get_term($term_id, $taxonomy);
  $parents []= $term;
  if ($term->parent) return get_term_parents($term->parent, $taxonomy, $parents);
  array_shift($parents);
  return $parents;
}