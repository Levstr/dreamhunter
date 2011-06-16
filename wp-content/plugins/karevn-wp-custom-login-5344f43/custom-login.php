<?php
/*
Plugin Name: Custom Login
Plugin URI: http://jetdog.biz/
Description: Allows you to style login and password retrieval pages easily.
Version: 1.0
Author: Nikolay Karev
Author URI: http://jetdog.biz
*/
require dirname(__FILE__) . '/model.php';
require dirname(__FILE__) . '/template_tags.php';
require dirname(__FILE__) . '/controller.php';
require dirname(__FILE__) . '/hooks.php';

register_activation_hook(dirname(__FILE__) . "/activation.php", 'lc_activate');
register_deactivation_hook(dirname(__FILE__) . "/activation.php", 'lc_deactivate');

