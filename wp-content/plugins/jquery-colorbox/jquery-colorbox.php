<?php
/**
 * @package Techotronic
 * @subpackage jQuery Colorbox
 *
 * Plugin Name: jQuery Colorbox
 * Plugin URI: http://www.techotronic.de/plugins/jquery-colorbox/
 * Description: Used to overlay images on the current page. Images in one post are grouped automatically.
 * Version: 4.0
 * Author: Arne Franken
 * Author URI: http://www.techotronic.de/
 * License: GPL
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */
?>
<?php
//define constants
define('JQUERYCOLORBOX_VERSION', '4.0');
define('COLORBOXLIBRARY_VERSION', '1.3.16');

if (!defined('JQUERYCOLORBOX_PLUGIN_BASENAME')) {
    define('JQUERYCOLORBOX_PLUGIN_BASENAME', plugin_basename(__FILE__));
}
if (!defined('JQUERYCOLORBOX_PLUGIN_NAME')) {
    define('JQUERYCOLORBOX_PLUGIN_NAME', trim(dirname(JQUERYCOLORBOX_PLUGIN_BASENAME), '/'));
}
if (!defined('JQUERYCOLORBOX_NAME')) {
    define('JQUERYCOLORBOX_NAME', 'jQuery Colorbox');
}
if (!defined('JQUERYCOLORBOX_TEXTDOMAIN')) {
    define('JQUERYCOLORBOX_TEXTDOMAIN', 'jquery-colorbox');
}
if (!defined('JQUERYCOLORBOX_PLUGIN_DIR')) {
    define('JQUERYCOLORBOX_PLUGIN_DIR', dirname(__FILE__));
}
if (!defined('JQUERYCOLORBOX_PLUGIN_URL')) {
    define('JQUERYCOLORBOX_PLUGIN_URL', WP_PLUGIN_URL . '/' . JQUERYCOLORBOX_PLUGIN_NAME);
}
if (!defined('JQUERYCOLORBOX_PLUGIN_URL')) {
    define('JQUERYCOLORBOX_PLUGIN_URL', WP_PLUGIN_URL . '/' . JQUERYCOLORBOX_PLUGIN_NAME);
}
if (!defined('JQUERYCOLORBOX_PLUGIN_LOCALIZATION_DIR')) {
    define('JQUERYCOLORBOX_PLUGIN_LOCALIZATION_DIR', JQUERYCOLORBOX_PLUGIN_DIR . '/localization');
}
if (!defined('JQUERYCOLORBOX_SETTINGSNAME')) {
    define('JQUERYCOLORBOX_SETTINGSNAME', 'jquery-colorbox_settings');
}
if (!defined('JQUERYCOLORBOX_LATESTDONATEURL')) {
    define('JQUERYCOLORBOX_LATESTDONATEURL', 'http://colorbox.techotronic.de/latest-donations.php');
}
if (!defined('JQUERYCOLORBOX_TOPDONATEURL')) {
    define('JQUERYCOLORBOX_TOPDONATEURL', 'http://colorbox.techotronic.de/top-donations.php');
}

class jQueryColorbox {

    var $colorboxThemes = array();

    var $colorboxUnits = array();

    var $colorboxSettings = array();

    var $colorboxDefaultSettings = array();

    /**
     * Plugin initialization
     *
     * @since 1.0
     * @access public
     * @author Arne Franken
     */
    //public function jQueryColorbox() {
    function jQueryColorbox() {
        if (!function_exists('plugins_url')) {
            return;
        }

        load_plugin_textdomain(JQUERYCOLORBOX_TEXTDOMAIN, false, '/jquery-colorbox/localization/');

        add_action('wp_head', array(& $this, 'buildWordpressHeader'));
        add_action('admin_post_jQueryColorboxDeleteSettings', array(& $this, 'jQueryColorboxDeleteSettings'));
        add_action('admin_post_jQueryColorboxUpdateSettings', array(& $this, 'jQueryColorboxUpdateSettings'));
        // add options page
        add_action('admin_menu', array(& $this, 'registerAdminMenu'));
        add_action('admin_notices', array(& $this, 'registerAdminWarning'));
        //register method for uninstall
        if (function_exists('register_uninstall_hook')) {
            register_uninstall_hook(__FILE__, array('jQueryColorbox', 'deleteSettingsFromDatabase'));
        }

        // Create the settings array by merging the user's settings and the defaults
        $usersettings = (array) get_option(JQUERYCOLORBOX_SETTINGSNAME);
        $defaultArray = $this->jQueryColorboxDefaultSettings();
        $this->colorboxSettings = wp_parse_args($usersettings, $defaultArray);

        //only add link to meta box
        if(isset($this->colorboxSettings['removeLinkFromMetaBox']) && !$this->colorboxSettings['removeLinkFromMetaBox']){
            add_action('wp_meta',array(& $this, 'renderMetaLink'));
        }

        if(isset($this->colorboxSettings['autoColorbox']) && $this->colorboxSettings['autoColorbox']){
            //write "colorbox-postID" to "img"-tags class attribute.
            //Priority = 100, hopefully the preg_replace is then executed after other plugins messed with the_content
            add_filter('the_content', array(& $this, 'addColorboxGroupIdToImages'), 100);
            add_filter('the_excerpt', array(& $this, 'addColorboxGroupIdToImages'), 100);
        }
        if(isset($this->colorboxSettings['autoColorboxGalleries']) && $this->colorboxSettings['autoColorboxGalleries']) {
            add_filter('wp_get_attachment_image_attributes', array(& $this, 'wpPostThumbnailClassFilter'));
        }

        //add style selector dropdown to TinyMCE
        add_filter('mce_buttons_2', array(& $this, 'addStyleSelectorBox'), 100);
        //add Colorbox CSS class to TinyMCE dropdown box
        add_filter('tiny_mce_before_init', array(& $this, 'addColorboxLinkClass'), 100);

        // Create list of themes and their human readable names
        $this->colorboxThemes = array(
            'theme1' => __('Theme #1', JQUERYCOLORBOX_TEXTDOMAIN),
            'theme2' => __('Theme #2', JQUERYCOLORBOX_TEXTDOMAIN),
            'theme3' => __('Theme #3', JQUERYCOLORBOX_TEXTDOMAIN),
            'theme4' => __('Theme #4', JQUERYCOLORBOX_TEXTDOMAIN),
            'theme5' => __('Theme #5', JQUERYCOLORBOX_TEXTDOMAIN),
            'theme6' => __('Theme #6', JQUERYCOLORBOX_TEXTDOMAIN),
            'theme7' => __('Theme #7', JQUERYCOLORBOX_TEXTDOMAIN),
            'theme8' => __('Theme #8', JQUERYCOLORBOX_TEXTDOMAIN),
            'theme9' => __('Theme #9', JQUERYCOLORBOX_TEXTDOMAIN),
            'theme10' => __('Theme #10', JQUERYCOLORBOX_TEXTDOMAIN),
            'theme11' => __('Theme #11', JQUERYCOLORBOX_TEXTDOMAIN)
        );

//        $this->colorboxThemes = array_merge($this->getThemeDirs(),$this->colorboxThemes);

        $dummyThemeNumberArray = array(
            __('Theme #12', JQUERYCOLORBOX_TEXTDOMAIN),
            __('Theme #13', JQUERYCOLORBOX_TEXTDOMAIN),
            __('Theme #14', JQUERYCOLORBOX_TEXTDOMAIN),
            __('Theme #15', JQUERYCOLORBOX_TEXTDOMAIN)
        );

        // create list of units
        $this->colorboxUnits = array(
            '%' => __('percent', JQUERYCOLORBOX_TEXTDOMAIN),
            'px' => __('pixels', JQUERYCOLORBOX_TEXTDOMAIN)
        );

        // create list of units
        $this->colorboxTransitions = array(
            'elastic' => __('elastic', JQUERYCOLORBOX_TEXTDOMAIN),
            'fade' => __('fade', JQUERYCOLORBOX_TEXTDOMAIN),
            'none' => __('none', JQUERYCOLORBOX_TEXTDOMAIN)
        );

        if (!is_admin()) {
            // enqueue JavaScript and CSS files in wordpress
            wp_enqueue_script('jquery');
            wp_register_style('colorbox-' . $this->colorboxSettings['colorboxTheme'], plugins_url('themes/' . $this->colorboxSettings['colorboxTheme'] . '/colorbox.css', __FILE__), array(), JQUERYCOLORBOX_VERSION, 'screen');
            wp_enqueue_style('colorbox-' . $this->colorboxSettings['colorboxTheme']);
            if($this->colorboxSettings['debugMode']) {
                $jqueryColorboxJavaScriptName = "js/jquery.colorbox.js";
            }
            else {
                $jqueryColorboxJavaScriptName = "js/jquery.colorbox-min.js";
            }
            wp_enqueue_script('colorbox', plugins_url($jqueryColorboxJavaScriptName, __FILE__), array('jquery'), COLORBOXLIBRARY_VERSION, $this->colorboxSettings['javascriptInFooter']);
//            if($this->colorboxSettings['draggable']) {
//                ?!?wp_enqueue_script('jquery-ui-draggable');
//                wp_enqueue_script('colorbox-draggable', plugins_url('js/jquery-colorbox-draggable.js', __FILE__), array('jquery-ui-draggable'), JQUERYCOLORBOX_VERSION, $this->colorboxSettings['javascriptInFooter']);
//            }
            if ($this->colorboxSettings['autoColorbox']) {
//            if ($this->colorboxSettings['autoColorboxJavaScript']) {
                if($this->colorboxSettings['debugMode']) {
                    $jqueryColorboxAutoJavaScriptName = "js/jquery-colorbox-auto.js";
                }
                else {
                    $jqueryColorboxAutoJavaScriptName = "js/jquery-colorbox-auto-min.js";
                }
                wp_enqueue_script('colorbox-auto', plugins_url($jqueryColorboxAutoJavaScriptName, __FILE__), array('colorbox'), JQUERYCOLORBOX_VERSION, $this->colorboxSettings['javascriptInFooter']);
            }
            if ($this->colorboxSettings['autoHideFlash']) {
                if($this->colorboxSettings['debugMode']) {
                    $jqueryColorboxFlashJavaScriptName = "js/jquery-colorbox-hideFlash.js";
                }
                else {
                    $jqueryColorboxFlashJavaScriptName = "js/jquery-colorbox-hideFlash-min.js";
                }
                wp_enqueue_script('colorbox-hideflash', plugins_url($jqueryColorboxFlashJavaScriptName, __FILE__), array('colorbox'), JQUERYCOLORBOX_VERSION, $this->colorboxSettings['javascriptInFooter']);
            }
        }
    }

    // jQueryColorbox()

    /**
     * Renders plugin link in Meta widget
     *
     * @since 3.3
     * @access public
     * @author Arne Franken
     */
    //public function renderMetaLink() {
    function renderMetaLink() { ?>
        <li id="colorboxLink"><?php _e('Using',JQUERYCOLORBOX_TEXTDOMAIN);?> <a href="http://www.techotronic.de/plugins/jquery-colorbox/" target="_blank" title="<?php echo JQUERYCOLORBOX_NAME ?>"><?php echo JQUERYCOLORBOX_NAME ?></a></li>
    <?php }

    // renderMetaLink()

    /**
     * Add Colorbox group Id to images.
     * function is called for every page or post rendering.
     * 
     * ugly way to make the images Colorbox-ready by adding the necessary CSS class.
     * unfortunately, Wordpress does not offer a convenient way to get certain elements from the_content,
     * so I had to do this by regexp replacement...
     *
     * @since 1.0
     * @access public
     * @author Arne Franken
     *
     * @param  $content
     * @return replaced content or excerpt
     */
    //public function addColorboxGroupIdToImages($content) {
    function addColorboxGroupIdToImages($content) {
        global
        $post;
        // match all img tags with this pattern
        $imgPattern = "/<img([^\>]*?)>/i";
        if (preg_match_all($imgPattern, $content, $imgTags)) {
            foreach ($imgTags[0] as $imgTag) {
                // only work on imgTags that do not already contain the String "colorbox-"
                if(!preg_match('/colorbox-/i', $imgTag)){
                    if (!preg_match('/class/i', $imgTag)) {
                        // imgTag does not contain class-attribute
                        $pattern = $imgPattern;
                        $replacement = '<img class="colorbox-' . $post->ID . '" $1>';
                    }
                    else {
                        // imgTag already contains class-attribute
                        $pattern = "/<img(.*?)class=('|\")([A-Za-z0-9 \/_\.\~\:-]*?)('|\")([^\>]*?)>/i";
                        $replacement = '<img$1class=$2$3 colorbox-' . $post->ID . '$4$5>';
                    }
                    $replacedImgTag = preg_replace($pattern, $replacement, $imgTag);
                    $content = str_replace($imgTag, $replacedImgTag, $content);
                }
            }
        }
        return $content;
    }

    // addColorboxGroupIdToImages()

    /**
     * Add colorbox-CSS-Class to WP Galleries
     * 
     * If wp_get_attachment_image() is called, filters registered for the_content are not applied on the img-tag.
     * So we'll need to manipulate the class attribute separately.
     *
     * @since 2.0
     * @access public
     * @author Arne Franken
     *
     * @param  $attribute class attribute of the attachment link
     * @return repaced attributes
     */
    //public function wpPostThumbnailClassFilter($attribute) {
    function wpPostThumbnailClassFilter($attribute) {
        global $post;
        $attribute['class'] .= ' colorbox-' . $post->ID . ' ';
        return $attribute;
    }

    // wpPostThumbnailClassFilter()

    /**
     * Register the settings page in wordpress
     *
     * @since 1.0
     * @access private
     * @author Arne Franken
     */
    //private function registerSettingsPage() {
    function registerSettingsPage() {
        if (current_user_can('manage_options')) {
            add_filter('plugin_action_links_' . JQUERYCOLORBOX_PLUGIN_BASENAME, array(& $this, 'addPluginActionLinks'));
            add_options_page(JQUERYCOLORBOX_NAME, JQUERYCOLORBOX_NAME, 'manage_options', JQUERYCOLORBOX_PLUGIN_BASENAME, array(& $this, 'renderSettingsPage'));
        }
    }

    //registerSettingsPage()

    /**
     * Add settings link to plugin management page
     *
     * @since 1.0
     * @access public
     * @author Arne Franken
     *
     * @param  original action_links
     * @return action_links with link to settings page
     */
    //public function addPluginActionLinks($action_links) {
    function addPluginActionLinks($action_links) {
        $settings_link = '<a href="options-general.php?page=' . JQUERYCOLORBOX_PLUGIN_BASENAME . '">' . __('Settings', JQUERYCOLORBOX_TEXTDOMAIN) . '</a>';
        array_unshift($action_links, $settings_link);

        return $action_links;
    }

    //addPluginActionLinks()

    /**
     * Insert JavaScript and CSS for Colorbox into WP Header
     *
     * @since 1.0
     * @access public
     * @author Arne Franken
     *
     * @return wordpress header insert
     */
    //public function buildWordpressHeader() {
    function buildWordpressHeader() {
        ?>
        <!-- <?php echo JQUERYCOLORBOX_NAME ?> <?php echo JQUERYCOLORBOX_VERSION ?> | by Arne Franken, http://www.techotronic.de/ -->
        <?php
        // include CSS fixes for IE for certain themes
//        preg_match('/\d+$/i',$this->colorboxSettings['colorboxTheme'],$themeNumbers);
//        if(in_array($themeNumbers[0],array(1,4,6,7,9,11))){
//            require_once 'includes/iefix-theme'.$themeNumbers[0].'.php';
//        }
        // include Colorbox Javascript
            require_once 'includes/colorbox-javascript.php';
            require_once 'includes/colorbox-javascript-loader.php';
            ?>
        <!-- <?php echo JQUERYCOLORBOX_NAME ?> <?php echo JQUERYCOLORBOX_VERSION ?> | by Arne Franken, http://www.techotronic.de/ -->
        <?php

    }

    //buildWordpressHeader()

    /**
     * Render Settings page
     *
     * @since 1.0
     * @access public
     * @author Arne Franken
     */
    //public function renderSettingsPage() {
    function renderSettingsPage() {
        require_once 'includes/settings-page.php';
    }

    //renderSettingsPage()

    /**
     * Registers the Settings Page in the Admin Menu
     *
     * @since 1.3.3
     * @access public
     * @author Arne Franken
     */
    //public function registerAdminMenu() {
    function registerAdminMenu() {
        if (function_exists('add_management_page') && current_user_can('manage_options')) {

            // update, uninstall message
            if (strpos($_SERVER['REQUEST_URI'], 'jquery-colorbox.php') && isset($_GET['jQueryColorboxUpdateSettings'])) {
                $return_message = sprintf(__('Successfully updated %1$s settings.', JQUERYCOLORBOX_TEXTDOMAIN), JQUERYCOLORBOX_NAME);
            } elseif (strpos($_SERVER['REQUEST_URI'], 'jquery-colorbox.php') && isset($_GET['jQueryColorboxDeleteSettings'])) {
                $return_message = sprintf(__('%1$s settings were successfully deleted.', JQUERYCOLORBOX_TEXTDOMAIN), JQUERYCOLORBOX_NAME);
            } else {
                $return_message = '';
            }
        }
        $this->registerAdminNotice($return_message);

        $this->registerSettingsPage();
    }

    // registerAdminMenu()

    /**
     * Registers Admin Notices
     *
     * @since 2.0
     * @access private
     * @author Arne Franken
     */
    //private function registerAdminNotice($notice) {
    function registerAdminNotice($notice) {
        if ($notice != '') {
            $message = '<div class="updated fade"><p>' . $notice . '</p></div>';
            add_action('admin_notices', create_function('', "echo '$message';"));
        }
    }

    // registerAdminNotice()

    /**
     * Registers the warning for admins
     *
     * @since 2.5
     * @access public
     * @author Arne Franken
     */
    //public function registerAdminWarning() {
    function registerAdminWarning() {
        if ($this->colorboxSettings['colorboxWarningOff'] || $this->colorboxSettings['autoColorbox']) {
            return;
        }
        ?>

        <div class="updated" style="background-color:#f66;">
            <p>
                <a href="options-general.php?page=<?php echo JQUERYCOLORBOX_PLUGIN_BASENAME ?>"><?php echo JQUERYCOLORBOX_NAME ?></a> <?php _e('needs attention: the plugin is not activated to work for all images.', JQUERYCOLORBOX_TEXTDOMAIN)?>
            </p>
        </div>
        <?php
    }

    // registerAdminWarning()

    /**
     * Default array of jQuery Colorbox settings
     *
     * @since 2.0
     * @access private
     * @author Arne Franken
     */
    //private function jQueryColorboxDefaultSettings() {
    function jQueryColorboxDefaultSettings() {

        // Create and return array of default settings
        return array(
            'jQueryColorboxVersion' => JQUERYCOLORBOX_VERSION,
            'colorboxTheme' => 'theme1',
            'maxWidth' => 'false',
            'maxWidthValue' => '',
            'maxWidthUnit' => '%',
            'maxHeight' => 'false',
            'maxHeightValue' => '',
            'maxHeightUnit' => '%',
            'height' => 'false',
            'heightValue' => '',
            'heightUnit' => '%',
            'width' => 'false',
            'widthValue' => '',
            'widthUnit' => '%',
            'linkHeight' => 'false',
            'linkHeightValue' => '',
            'linkHeightUnit' => '%',
            'linkWidth' => 'false',
            'linkWidthValue' => '',
            'linkWidthUnit' => '%',
            'initialWidth' => '300',
            'initialHeight' => '100',
            'autoColorbox' => false,
            'autoColorboxGalleries' => false,
            'slideshow' => false,
            'slideshowAuto' => false,
            'scalePhotos' => false,
            'displayScrollbar' => false,
            'draggable' => false,
            'slideshowSpeed' => '2500',
            'opacity' => '0.85',
            'preloading' => false,
            'transition' => 'elastic',
            'speed' => '350',
            'overlayClose' => false,
            'disableLoop' => false,
            'disableKeys' => false,
            'autoHideFlash' => false,
            'colorboxWarningOff' => false,
            'colorboxMetaLinkOff' => false,
            'javascriptInFooter' => false,
            'debugMode' => false,
            'autoColorboxJavaScript' => false,
            'removeLinkFromMetaBox' => false
        );
    }

    // jQueryColorboxDefaultSettings()

    /**
     * Update jQuery Colorbox settings wrapper
     *
     * handles checks and redirect
     *
     * @since 1.3.3
     * @access public
     * @author Arne Franken
     */
    //public function jQueryColorboxUpdateSettings() {
    function jQueryColorboxUpdateSettings() {

        if (!current_user_can('manage_options'))
            wp_die(__('Did not update settings, you do not have the necessary rights.', JQUERYCOLORBOX_TEXTDOMAIN));

        //cross check the given referer for nonce set in settings form
        check_admin_referer('jquery-colorbox-settings-form');
        //get settings from plugins admin page
        $this->colorboxSettings = $_POST[JQUERYCOLORBOX_SETTINGSNAME];
        //have to add jQueryColorboxVersion here because it is not included in the HTML form 
        $this->colorboxSettings['jQueryColorboxVersion'] = JQUERYCOLORBOX_VERSION;
        $this->updateSettingsInDatabase();
        $referrer = str_replace(array('&jQueryColorboxUpdateSettings', '&jQueryColorboxDeleteSettings'), '', $_POST['_wp_http_referer']);
        wp_redirect($referrer . '&jQueryColorboxUpdateSettings');
    }

    // jQueryColorboxUpdateSettings()

    /**
     * Update jQuery Colorbox settings
     *
     * handles updating settings in the WordPress database
     *
     * @since 1.3.3
     * @access private
     * @author Arne Franken
     */
    //private function updateSettingsInDatabase() {
    function updateSettingsInDatabase() {
        update_option(JQUERYCOLORBOX_SETTINGSNAME, $this->colorboxSettings);
    }

    //updateSettings()

    /**
     * Delete jQuery Colorbox settings wrapper
     *
     * handles checks and redirect
     *
     * @since 1.3.3
     * @access public
     * @author Arne Franken
     */
    //public function jQueryColorboxDeleteSettings() {
    function jQueryColorboxDeleteSettings() {

        if (current_user_can('manage_options') && isset($_POST['delete_settings-true'])) {
            //cross check the given referer for nonce set in delete settings form
            check_admin_referer('jquery-delete_settings-form');
            $this->deleteSettingsFromDatabase();
            $this->colorboxSettings = $this->jQueryColorboxDefaultSettings();
        } else {
            wp_die(sprintf(__('Did not delete %1$s settings. Either you dont have the nececssary rights or you didnt check the checkbox.', JQUERYCOLORBOX_TEXTDOMAIN), JQUERYCOLORBOX_NAME));
        }
        //clean up referrer
        $referrer = str_replace(array('&jQueryColorboxUpdateSettings', '&jQueryColorboxDeleteSettings'), '', $_POST['_wp_http_referer']);
        wp_redirect($referrer . '&jQueryColorboxDeleteSettings');
    }

    // jQueryColorboxDeleteSettings()

    /**
     * Delete jQuery Colorbox settings
     *
     * handles deletion from WordPress database
     *
     * @since 1.3.3
     * @access private
     * @author Arne Franken
     */
    //private function deleteSettingsFromDatabase() {
    function deleteSettingsFromDatabase() {
        delete_option(JQUERYCOLORBOX_SETTINGSNAME);
    }

    // deleteSettings()

    /**
     * executed during activation.
     *
     * @since 2.0
     * @access public
     * @author Arne Franken
     */
    //public function activateJqueryColorbox() {
    function activateJqueryColorbox() {
        $jquery_colorbox_settings = get_option(JQUERYCOLORBOX_SETTINGSNAME);
        if ($jquery_colorbox_settings) {
            //if jQueryColorboxVersion does not exist, the plugin is a version prior to 2.0
            //settings are incompatible with 2.0, restore default settings.
            if (!array_key_exists('jQueryColorboxVersion', $jquery_colorbox_settings)) {
                if (!array_key_exists('scalePhotos', $jquery_colorbox_settings)) {
                    //in case future versions require resetting the settings
                    //if($jquery_colorbox_settings['jQueryColorboxVersion'] < JQUERYCOLORBOX_VERSION)
                    update_option(JQUERYCOLORBOX_SETTINGSNAME, $this->jQueryColorboxDefaultSettings());
                }
            }
        }
    }

    // activateJqueryColorbox()

    /**
     * Read HTML from a remote url
     *
     * @since 3.5
     * @access private
     * @author Arne Franken
     * 
     * @param string $url
     * @return the response
     */
    //private function getRemoteContent($url) {
    function getRemoteContent($url) {
        if ( function_exists('wp_remote_request') ) {

            $options = array();
            $options['headers'] = array(
                'User-Agent' => 'jQuery Colorbox V' . JQUERYCOLORBOX_VERSION . '; (' . get_bloginfo('url') .')'
             );

            $response = wp_remote_request($url, $options);

            if ( is_wp_error( $response ) )
                return false;

            if ( 200 != wp_remote_retrieve_response_code($response) )
                return false;

            return wp_remote_retrieve_body($response);
        }

        return false;
    }

    // getRemoteContent()

    /**
     * gets current URL to return to after donating
     *
     * @since 3.5
     * @access private
     * @author Arne Franken
     */
    //private function getReturnLocation(){
    function getReturnLocation(){
        $currentLocation = "http";
        $currentLocation .= ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') ? "s" : "")."://";
        $currentLocation .= $_SERVER['SERVER_NAME'];
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') {
            if($_SERVER['SERVER_PORT']!='443') {
                $currentLocation .= ":".$_SERVER['SERVER_PORT'];
            }
        }
        else {
            if($_SERVER['SERVER_PORT']!='80') {
                $currentLocation .= ":".$_SERVER['SERVER_PORT'];
            }
        }
        $currentLocation .= $_SERVER['REQUEST_URI'];
        echo $currentLocation;
    }

    // getReturnLocation()

    /**
     * adds Colorbox CSS class to TinyMCE style selector dropdown box
     *
     * @since 3.7
     * @access public
     * @author Arne Franken
     *
     * @param  $init_array
     * @return modified array
     */
    //public function addColorboxLinkClass($defaultCss) {
    function addColorboxLinkClass($init_array) {

        $init_array['theme_advanced_styles'] .= ';colorbox-link=colorbox-link;';
        //strip first and last character if it matches ";"
        $init_array['theme_advanced_styles'] = trim($init_array['theme_advanced_styles'], ';');
        return $init_array;
    }

    // addColorboxLinkClasses()

   /**
    * Adds style selector option to TinyMCE
    *
    * @since 4.0
    * @access public
    * @author Arne Franken
    *
    * @param $array
    * @return modified array
    */
    function addStyleSelectorBox($array) {
        if(!in_array('styleselect',$array)){
            array_push($array,'styleselect');
        }
        return $array;
    }

    // addStyleSelectorBox()

    /**
     *
     *
     */
//    function getThemeDirs() {
//        $themesDirPath = JQUERYCOLORBOX_PLUGIN_DIR.'/themes/';
//        if ($themesDir = opendir($themesDirPath)) {
//            while (false !== ($dir = readdir($themesDir))) {
//                if (substr("$dir", 0, 1) != "."){
//                    $themeDirs[$dir] = $dir;
//                }
//            }
//            closedir($themesDir);
//        }
//        asort($themeDirs);
//        return $themeDirs;
//    }

}

// class jQueryColorbox()
?><?php
/**
 * initialize plugin, call constructor
 *
 * @since 1.0
 * @access public
 * @author Arne Franken
 */
function jQueryColorbox() {
        global $jQueryColorbox;
        $jQueryColorbox = new jQueryColorbox();
    }

// jQueryColorbox()

// add jQueryColorbox() to WordPress initialization
add_action('init', 'jQueryColorbox', 7);

// register method for activation
register_activation_hook(__FILE__, array('jQueryColorbox', 'activateJqueryColorbox'));
?>