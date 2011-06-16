<?php

/* Extended Nav menu with minimal depth argument accepted */
function x_wp_nav_menu($args = array()){
  if (!isset($args['walker']))
    $args['walker'] = new X_Walker_Nav_Menu($args);
  return wp_nav_menu($args);
}



class X_Walker_Nav_Menu extends Walker_Nav_Menu {
  var $args = null;  
  var $prev_depth = null;
  var $stack = array();
  
  function __construct($args = array()){
    $this->args = $args;
  }
  
  private function depth_is_acceptable($depth){
    return !($this->args['min_depth'] && $depth < $this->args['min_depth']);
  }
  
  private function child_of_active($element){
    if (count($this->stack) == 1){
      return true;
    }
    $parent = $this->stack[count($this->stack) - 2];
    return $this->parent_of_active($parent);
  }
  
  private function parent_of_active($element){
    if (!isset($element->classes))
      return false;
    return in_array('current-menu-item', $element->classes) ||
      in_array('current-menu-ancestor', $element->classes) ||
      in_array('current-menu-parent', $element->classes); 
  }
  
  function child_of_active_invisible(){
    return $this->parent_of_active($this->stack[$this->args['min_depth'] - 1]);
  }
    
  function display_element($element, &$children_elements, $max_depth, $depth = 0, $args, &$output){
    if ($depth >= $this->args['min_depth'] && $this->child_of_active($element) || 
      ($depth > $this->args['min_depth'] && $this->args['show_children_of_inactive'] && $this->child_of_active_invisible())){
      array_push($this->stack, $element);
      parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
      array_pop($this->stack);
    } else if ($this->parent_of_active($element)){
      array_push($this->stack, $element);      
      $this->display_children($element, $children_elements, $max_depth, $depth, $args, &$output);
      array_pop($this->stack);
    }
  }

  function display_children(&$element, &$children_elements, $max_depth, $depth, $args, &$output){
    $id_field = $this->db_fields['id'];
    $id = $element->$id_field;
    // descend only when the depth is right and there are childrens for this element
		if ( ($max_depth == 0 || $max_depth > $depth + 1 ) && isset( $children_elements[$id]) ) {

			foreach( $children_elements[ $id ] as $child ){

				if ( !isset($newlevel) ) {
					$newlevel = true;
					//start the child delimiter
					$cb_args = array_merge( array(&$output, $depth), $args);
					call_user_func_array(array(&$this, 'start_lvl'), $cb_args);
				}
				$this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
			}
			unset( $children_elements[ $id ] );
		}

		if ( isset($newlevel) && $newlevel ){
			//end the child delimiter
			$cb_args = array_merge( array(&$output, $depth), $args);
			call_user_func_array(array(&$this, 'end_lvl'), $cb_args);
		}
  }
  
  
  function start_el(&$output, $item, $depth, $args){
    return $this->depth_is_acceptable($depth) ? 
      parent::start_el($output, $item, $depth, $args) : '';
  }
  
  function end_el(&$output, $item, $depth){
    return $this->depth_is_acceptable($depth) ? parent::end_el($output, $item, $depth) : '';
  }
  
}


function current_url_without_query(){
  return preg_replace('/\?.*/', '', $_SERVER['REQUEST_URI']);
}

function get_random_image($path, $pattern){
  $images = array();
  $h = opendir($path);
  while ($file = readdir($h))
    if ($file != "." && $file != ".." && preg_match($pattern, $file))
    $images[] = $file;
  closedir($h);

  if(count($images))
    return $images[rand(0,count($images)-1)];
  return null;
}

function last_class($array, $item){
  if (array_search($item, $array) == count($array) - 1)
    echo "last";
}

