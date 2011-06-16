<?php
/*
Plugin Name: Wordpress CMF plugin
Plugin URI: http://jetdog.biz/projects/wp-cmf
Description: Wordpress Content Management Framework plugin.
Version: 0.1
Author: Nikolay Karev
Author URI: http://jetdog.biz
*/

/*
Copyright (C) 2010 Nikolay Karev, karev.n@gmail.com

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
?>
<?php
require_once(dirname(__FILE__) . "/vendor/html-helpers.php");
require_once(dirname(__FILE__) . "/term_meta.php");
require_once(dirname(__FILE__) . "/api.php");
require_once(dirname(__FILE__) . "/ajax-validation.php");
require_once(dirname(__FILE__) . "/media.php");
require_once(dirname(__FILE__) . "/validation_result.php");
require_once(dirname(__FILE__) . "/fields.php");
require_once(dirname(__FILE__) . "/template.php");
require_once(dirname(__FILE__) . "/post_template.php");
require_once(dirname(__FILE__) . "/taxonomy_template.php");
require_once(dirname(__FILE__) . "/init.php");

register_activation_hook(dirname(__FILE__). "/activation.php", 'cmf_activation_hook');

?>