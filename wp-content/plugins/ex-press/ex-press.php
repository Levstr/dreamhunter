<?php
/*
Plugin Name: Ex-Press
Plugin URI: mailto:karev.n@gmail.com
Description: Extends WordPress API with extended calls
Version: 0.1
Author: Nikolay Karev
Author URI: mailto:karev.n@gmail.com
*/
define("EX_PRESS", 'ex-press');
require(dirname(__FILE__). '/model.php');
require(dirname(__FILE__) . "/template.php");
require(dirname(__FILE__) . "/plugins.php");
require(dirname(__FILE__) . "/js.php");

add_action('widgets_init', 'express_action_init');
function express_action_init(){
  require(dirname(__FILE__) . "/widgets.php");
}
