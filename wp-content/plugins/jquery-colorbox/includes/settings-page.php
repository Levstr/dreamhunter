<?php
/**
 * @package Techotronic
 * @subpackage jQuery Colorbox
 *
 * @since 3.1
 * @author Arne Franken
 *
 * HTML for settings page
 */
?>
<script type="text/javascript">
    //<![CDATA[
    jQuery(document).ready(function($) {

        //delete value from maxWidthValue if maxWidth radio button is selected
        $("input[name='<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[maxWidth]']").click(function() {
            if ("jquery-colorbox-maxWidth-custom-radio" != $(this).attr("id"))
                $("input[name='<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[maxWidthValue]']").val("");
        });
        
        //set maxWidth radio button if cursor is set into maxWidthValue
        $("input[name='<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[maxWidthValue]']").focus(function() {
            $("#jquery-colorbox-maxWidth-custom-radio").attr("checked", "checked");
        });

        //delete value from maxHeightValue if maxHeight radio button is selected
        $("input[name='<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[maxHeight]']").click(function() {
            if ("jquery-colorbox-maxHeight-custom-radio" != $(this).attr("id"))
                $("input[name='<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[maxHeightValue]']").val("");
        });
        //set maxHeight radio button if cursor is set into maxHeightValue
        $("input[name='<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[maxHeightValue]']").focus(function() {
            $("#jquery-colorbox-maxHeight-custom-radio").attr("checked", "checked");
        });

        //delete value from widthValue if width radio button is selected
        $("input[name='<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[width]']").click(function() {
            if ("jquery-colorbox-width-custom-radio" != $(this).attr("id"))
                $("input[name='<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[widthValue]']").val("");
        });
        //set width radio button if cursor is set into widthValue
        $("input[name='<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[widthValue]']").focus(function() {
            $("#jquery-colorbox-width-custom-radio").attr("checked", "checked");
        });

        //delete value from heightValue if height radio button is selected
        $("input[name='<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[height]']").click(function() {
            if ("jquery-colorbox-height-custom-radio" != $(this).attr("id"))
                $("input[name='<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[heightValue]']").val("");
        });
        //set height radio button if cursor is set into heightValue
        $("input[name='<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[heightValue]']").focus(function() {
            $("#jquery-colorbox-height-custom-radio").attr("checked", "checked");
        });

        //delete value from widthValue if width radio button is selected
        $("input[name='<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[linkWidth]']").click(function() {
            if ("jquery-colorbox-link-width-custom-radio" != $(this).attr("id"))
                $("input[name='<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[linkWidthValue]']").val("");
        });
        //set width radio button if cursor is set into widthValue
        $("input[name='<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[linkWidthValue]']").focus(function() {
            $("#jquery-colorbox-link-width-custom-radio").attr("checked", "checked");
        });

        //delete value from heightValue if height radio button is selected
        $("input[name='<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[linkHeight]']").click(function() {
            if ("jquery-colorbox-link-height-custom-radio" != $(this).attr("id"))
                $("input[name='<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[linkHeightValue]']").val("");
        });
        //set height radio button if cursor is set into heightValue
        $("input[name='<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[linkHeightValue]']").focus(function() {
            $("#jquery-colorbox-link-height-custom-radio").attr("checked", "checked");
        });

        //only one of the checkboxes is allowed to be selected.
        $("input[name='<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[autoColorbox]']").click(function() {
            if ($("input[name='<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[autoColorbox]']").is(':checked')) {
                $("#jquery-colorbox-autoColorboxGalleries").attr("checked", false);
            }
        });
        $("input[name='<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[autoColorboxGalleries]']").click(function() {
            if ($("input[name='<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[autoColorboxGalleries]']").is(':checked')) {
                $("#jquery-colorbox-autoColorbox").attr("checked", false);
            }
        });

        //deactivate warning if auto Colorbox is activated
        $("input[name='<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[autoColorbox]']").click(function() {
            if ($("input[name='<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[autoColorbox]']").is(':checked')) {
                $("#jquery-colorbox-colorboxWarningOff").attr("checked", true);
            }
        });

        //activate warning if auto Colorbox is deactivated
        $("input[name='<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[autoColorbox]']").click(function() {
            if (!$("input[name='<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[autoColorbox]']").is(':checked')) {
                $("#jquery-colorbox-colorboxWarningOff").attr("checked", false);
            }
        });

        //change screenshot if new theme is selected
        $("#jquery-colorbox-theme").change(function() {
            var src = $("option:selected", this).val().match(/\d+$/i);
            if ( src != "" ){
                var $imgTag = "<img src=\"" + "<?php echo JQUERYCOLORBOX_PLUGIN_URL; echo '/screenshot-' ; ?>" + src  + ".jpg\" />";
                $("#jquery-colorbox-theme_screenshot_image").empty().html($imgTag).fadeIn();
            }
        });
    });
    //]]>
</script>
<div class="wrap">
    <div>
    <?php screen_icon(); ?>
    <h2><?php printf(__('%1$s Settings', JQUERYCOLORBOX_TEXTDOMAIN), JQUERYCOLORBOX_NAME); ?></h2>
    <br class="clear"/>

    <?php settings_fields(JQUERYCOLORBOX_SETTINGSNAME); ?>

    <div class="postbox-container" style="width: 69%;">
    <form name="jquery-colorbox-settings-update" method="post" action="admin-post.php">
    <?php if (function_exists('wp_nonce_field') === true) wp_nonce_field('jquery-colorbox-settings-form'); ?>
    <div id="poststuff">
        <div id="jquery-colorbox-settings" class="postbox">
            <!--h3 id="colorbox-settings"><?php __('Settings', JQUERYCOLORBOX_TEXTDOMAIN); ?></h3-->
            <h3 id="colorbox-settings"><?php _e('Plugin settings', JQUERYCOLORBOX_TEXTDOMAIN); ?></h3>

            <div class="inside">

                <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="jquery-colorbox-autoColorbox"><?php printf(__('Automate %1$s for all images', JQUERYCOLORBOX_TEXTDOMAIN), JQUERYCOLORBOX_NAME); __('Automate %1$s for all images in pages, posts and galleries', JQUERYCOLORBOX_TEXTDOMAIN); ?>:</label>
                    </th>
                    <td>
                        <input type="checkbox" name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[autoColorbox]" id="jquery-colorbox-autoColorbox" value="true" <?php echo ($this->colorboxSettings['autoColorbox']) ? 'checked="checked"' : '';?>/>
                        <br/><?php _e('Automatically add colorbox-class to images in posts and pages. Also adds colorbox-class to galleries. Images in one page or post are grouped automatically.', JQUERYCOLORBOX_TEXTDOMAIN); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="jquery-colorbox-autoColorboxGalleries"><?php printf(__('Automate %1$s for images in WordPress galleries', JQUERYCOLORBOX_TEXTDOMAIN), JQUERYCOLORBOX_NAME); __('Automate %1$s for images in WordPress galleries only', JQUERYCOLORBOX_TEXTDOMAIN) ?>:</label>
                    </th>
                    <td>
                        <input type="checkbox" name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[autoColorboxGalleries]" id="jquery-colorbox-autoColorboxGalleries" value="true" <?php echo ($this->colorboxSettings['autoColorboxGalleries']) ? 'checked="checked"' : '';?>/>
                        <br/><?php _e('Automatically add colorbox-class to images in WordPress galleries, but nowhere else. Images in one page or post are grouped automatically.', JQUERYCOLORBOX_TEXTDOMAIN); ?>
                    </td>
                </tr>
                <!--tr>
                    <th scope="row">
                        <label for="jquery-colorbox-autoColorboxJavaScript"><?php __('Automate %1$s for all other images', JQUERYCOLORBOX_TEXTDOMAIN); //printf(__('Automate %1$s for all other images', JQUERYCOLORBOX_TEXTDOMAIN), JQUERYCOLORBOX_NAME); ?>:</label>
                    </th>
                    <td>
                        <input type="checkbox" name="<?php //echo JQUERYCOLORBOX_SETTINGSNAME ?>[autoColorboxJavaScript]" id="jquery-colorbox-autoColorboxJavaScript" value="true" <?php //echo ($this->colorboxSettings['autoColorboxJavaScript']) ? 'checked="checked"' : '';?>/>
                        <br/><?php __('Automatically add colorbox-class to all images that are not in posts and pages (e.g. the sidebar).', JQUERYCOLORBOX_TEXTDOMAIN); ?>
                    </td>
                </tr-->
                <tr>
                    <th scope="row">
                        <label for="jquery-colorbox-autoHideFlash"><?php _e('Automate hiding of flash objects', JQUERYCOLORBOX_TEXTDOMAIN); ?>:</label>
                    </th>
                    <td>
                        <input type="checkbox" name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[autoHideFlash]" id="jquery-colorbox-autoHideFlash" value="true" <?php echo ($this->colorboxSettings['autoHideFlash']) ? 'checked="checked"' : '';?>/>
                        <br/><?php _e('Automatically hide embeded flash objects behind the Colorbox layer.', JQUERYCOLORBOX_TEXTDOMAIN); ?>
                    </td>
                </tr>
                <!--tr>
                    <th scope="row">
                        <label for="jquery-colorbox-javascriptInFooter"><?php __('Add JavaScript to footer', JQUERYCOLORBOX_TEXTDOMAIN); ?>:</label>
                    </th>
                    <td>
                        <input type="checkbox" name="<?php //echo JQUERYCOLORBOX_SETTINGSNAME ?>[javascriptInFooter]" id="jquery-colorbox-javascriptInFooter" value="true" <?php //echo ($this->colorboxSettings['javascriptInFooter']) ? 'checked="checked"' : '';?>/>
                        <br/><?php __('Add JavaScript to footer instead of the header.', JQUERYCOLORBOX_TEXTDOMAIN); ?>
                    </td>
                </tr-->
                <!--tr>
                    <th scope="row">
                        <label for="jquery-colorbox-removeLinkFromMetaBox"><?php __('Remove link from Meta-box', JQUERYCOLORBOX_TEXTDOMAIN); ?>:</label>
                    </th>
                    <td>
                        <input type="checkbox" name="<?php //echo JQUERYCOLORBOX_SETTINGSNAME ?>[removeLinkFromMetaBox]" id="jquery-colorbox-removeLinkFromMetaBox" value="true" <?php //echo ($this->colorboxSettings['removeLinkFromMetaBox']) ? 'checked="checked"' : '';?>/>
                        <br/><?php __('Remove the link to the developers site from the WordPress meta-box.', JQUERYCOLORBOX_TEXTDOMAIN); ?>
                    </td>
                </tr-->
                <!--tr>
                    <th scope="row">
                        <label for="jquery-colorbox-debugMode"><?php __('Activate debug mode', JQUERYCOLORBOX_TEXTDOMAIN); ?>:</label>
                    </th>
                    <td>
                        <input type="checkbox" name="<?php //echo JQUERYCOLORBOX_SETTINGSNAME ?>[debugMode]" id="jquery-colorbox-debugMode" value="true" <?php //echo ($this->colorboxSettings['debugMode']) ? 'checked="checked"' : '';?>/>
                        <br/><?php __('Adds debug information and non-minified JavaScript to the page. Useful for troubleshooting.', JQUERYCOLORBOX_TEXTDOMAIN); ?>
                    </td>
                </tr-->
                <tr>
                    <th scope="row">
                        <label for="jquery-colorbox-colorboxWarningOff"><?php printf(__('Disable warning', JQUERYCOLORBOX_TEXTDOMAIN), JQUERYCOLORBOX_NAME); ?>:</label>
                    </th>
                    <td>
                        <input type="checkbox" name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[colorboxWarningOff]" id="jquery-colorbox-colorboxWarningOff" value="true" <?php echo ($this->colorboxSettings['colorboxWarningOff']) ? 'checked="checked"' : '';?>/>
                        <br/><?php _e('Disables the warning that is displayed if the plugin is activated but the auto-colorbox feature for all images is turned off.', JQUERYCOLORBOX_TEXTDOMAIN); ?>
                    </td>
                </tr>
                </table>
                <p class="submit">
                    <input type="hidden" name="action" value="jQueryColorboxUpdateSettings"/>
                    <input type="submit" name="jQueryColorboxUpdateSettings" class="button-primary" value="<?php _e('Save Changes') ?>"/>
                </p>
            </div>
        </div>
        <div id="jquery-colorbox-plugin-settings" class="postbox">
            <h3 id="plugin-settings"><?php _e('Colorbox settings', JQUERYCOLORBOX_TEXTDOMAIN); ?></h3>

            <div class="inside">

                <table class="form-table">
                <tr valign="top">
                    <th scope="row">
                        <label for="jquery-colorbox-theme"><?php _e('Theme', JQUERYCOLORBOX_TEXTDOMAIN); ?></label>
                    </th>
                    <td>
                        <select name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[colorboxTheme]" id="jquery-colorbox-theme" class="postform" style="margin:0">
                        <?php
                            foreach ($this->colorboxThemes as $theme => $name) {
                                echo '<option value="' . esc_attr($theme) . '"';
                                selected($this->colorboxSettings['colorboxTheme'], $theme);
                                echo '>' . htmlspecialchars($name) . "</option>\n";
                            }
                        ?>
                        </select>
                        <br/><?php _e('Select the theme you want to use on your blog.', JQUERYCOLORBOX_TEXTDOMAIN); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="jquery-colorbox-theme_screenshot_image"><?php _e('Theme screenshot', JQUERYCOLORBOX_TEXTDOMAIN); ?>:</label>
                    </th>
                    <td height="310px">
                        <div id="jquery-colorbox-theme_screenshot_image">
                            <img src="<?php echo JQUERYCOLORBOX_PLUGIN_URL; echo '/screenshot-' ; preg_match('/\d+$/i',$this->colorboxSettings['colorboxTheme'],$matches); echo $matches[0] ?>.jpg"/>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="jquery-colorbox-slideshow"><?php _e('Add Slideshow to groups', JQUERYCOLORBOX_TEXTDOMAIN); ?>:</label>
                    </th>
                    <td>
                        <input type="checkbox" name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[slideshow]" id="jquery-colorbox-slideshow" value="true" <?php echo ($this->colorboxSettings['slideshow']) ? 'checked="checked"' : '';?>/>
                        <br/><?php printf(__('Add Slideshow functionality for %1$s Groups', JQUERYCOLORBOX_TEXTDOMAIN), JQUERYCOLORBOX_NAME); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="jquery-colorbox-slideshowAuto"><?php _e('Start Slideshow automatically', JQUERYCOLORBOX_TEXTDOMAIN); ?>:</label>
                    </th>
                    <td>
                        <input type="checkbox" name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[slideshowAuto]" id="jquery-colorbox-slideshowAuto" value="true" <?php echo ($this->colorboxSettings['slideshowAuto']) ? 'checked="checked"' : '';?>/>
                        <br/><?php printf(__('Start Slideshow automatically if slideshow functionality is added to %1$s Groups', JQUERYCOLORBOX_TEXTDOMAIN), JQUERYCOLORBOX_NAME); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="jquery-colorbox-slideshowSpeed"><?php _e('Speed of the slideshow', JQUERYCOLORBOX_TEXTDOMAIN); ?>:</label>
                    </th>
                    <td>
                        <input type="text" name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[slideshowSpeed]" id="jquery-colorbox-slideshowSpeed" value="<?php echo $this->colorboxSettings['slideshowSpeed'] ?>" size="5" maxlength="5"/><?php _e('milliseconds',JQUERYCOLORBOX_TEXTDOMAIN) ?>
                        <br/><?php _e('Sets the speed of the slideshow, in milliseconds', JQUERYCOLORBOX_TEXTDOMAIN); ?>.
                    </td>
                </tr>
                <!--tr>
                    <th scope="row">
                        <label for="jquery-colorbox-disableLoop"><?php __('Disable loop', JQUERYCOLORBOX_TEXTDOMAIN); ?>:</label>
                    </th>
                    <td>
                        <input type="checkbox" name="<?php //echo JQUERYCOLORBOX_SETTINGSNAME ?>[disableLoop]" id="jquery-colorbox-disableLoop" value="true" <?php //echo ($this->colorboxSettings['disableLoop']) ? 'checked="checked"' : '';?>/>
                        <br/><?php __('Disable looping through image groups', JQUERYCOLORBOX_TEXTDOMAIN); ?>.
                    </td>
                </tr-->
                <!--tr>
                    <th scope="row">
                        <label for="jquery-colorbox-disableKeys"><?php __('Disable keys', JQUERYCOLORBOX_TEXTDOMAIN); ?>:</label>
                    </th>
                    <td>
                        <input type="checkbox" name="<?php //echo JQUERYCOLORBOX_SETTINGSNAME ?>[disableKeys]" id="jquery-colorbox-disableKeys" value="true" <?php //echo ($this->colorboxSettings['disableKeys']) ? 'checked="checked"' : '';?>/>
                        <br/><?php __('Disable ESC to close Colorbox and arrow keys to go to next and previous images', JQUERYCOLORBOX_TEXTDOMAIN); ?>.
                    </td>
                </tr-->
                <tr>
                    <th scope="row">
                        <label for="jquery-colorbox-maxWidthValue"><?php _e('Maximum width of an image', JQUERYCOLORBOX_TEXTDOMAIN); ?>:</label>
                    </th>
                    <td>
                        <input type="radio" name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[maxWidth]" id="jquery-colorbox-maxWidth-false-radio" value="false" <?php echo ($this->colorboxSettings['maxWidth']) == 'false' ? 'checked="checked"' : ''; ?>"/>
                        <label for="jquery-colorbox-maxWidth-false-radio"><?php _e('Do not set width', JQUERYCOLORBOX_TEXTDOMAIN); ?>.</label>
                        <br/>
                        <input type="radio" name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[maxWidth]" id="jquery-colorbox-maxWidth-custom-radio" value="custom" <?php echo ($this->colorboxSettings['maxWidth']) == 'custom' ? 'checked="checked"' : ''; ?>"/>
                        <label for="jquery-colorbox-maxWidth-custom-radio"><?php _e('Set maximum width of an image', JQUERYCOLORBOX_TEXTDOMAIN); ?>.</label>
                        <input type="text" name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[maxWidthValue]" id="jquery-colorbox-maxWidthValue" value="<?php echo $this->colorboxSettings['maxWidthValue'] ?>" size="3" maxlength="3"/>
                        <select name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[maxWidthUnit]" id="jquery-colorbox-maxWidth-unit" class="postform" style="margin:0">
                        <?php
                        foreach ($this->colorboxUnits as $unit => $name) {
                            echo '<option value="' . esc_attr($unit) . '"';
                            selected($this->colorboxSettings['maxWidthUnit'], $unit);
                            echo '>' . htmlspecialchars($name) . "</option>\n";
                        }
                        ?>
                        </select>
                        <br/><?php _e('Set the maximum width of the image in the Colorbox in relation to the browser window in percent or pixels. If maximum width is not set, image is as wide as the Colorbox', JQUERYCOLORBOX_TEXTDOMAIN); ?>.
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="jquery-colorbox-maxHeightValue"><?php _e('Maximum height of an image', JQUERYCOLORBOX_TEXTDOMAIN); ?>:</label>
                    </th>
                    <td>
                        <input type="radio" name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[maxHeight]" id="jquery-colorbox-maxHeight-false-radio" value="false" <?php echo ($this->colorboxSettings['maxHeight']) == 'false' ? 'checked="checked"' : ''; ?>"/>
                        <label for="jquery-colorbox-maxHeight-false-radio"><?php _e('Do not set height', JQUERYCOLORBOX_TEXTDOMAIN); ?>.</label>
                        <br/>
                        <input type="radio" name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[maxHeight]" id="jquery-colorbox-maxHeight-custom-radio" value="custom" <?php echo ($this->colorboxSettings['maxHeight']) == 'custom' ? 'checked="checked"' : ''; ?>"/>
                        <label for="jquery-colorbox-maxHeight-custom-radio"><?php _e('Set maximum height of an image', JQUERYCOLORBOX_TEXTDOMAIN); ?>.</label>
                        <input type="text" name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[maxHeightValue]" id="jquery-colorbox-maxHeightValue" value="<?php echo $this->colorboxSettings['maxHeightValue'] ?>" size="3" maxlength="3"/>
                        <select name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[maxHeightUnit]" id="jquery-colorbox-maxHeight-unit" class="postform" style="margin:0">
                        <?php
                        foreach ($this->colorboxUnits as $unit => $name) {
                            echo '<option value="' . esc_attr($unit) . '"';
                            selected($this->colorboxSettings['maxHeightUnit'], $unit);
                            echo '>' . htmlspecialchars($name) . "</option>\n";
                        }
                        ?>
                        </select>
                        <br/><?php _e('Set the maximum height of the image in the Colorbox in relation to the browser window to a value in percent or pixels. If maximum height is not set, the image is as high as the Colorbox', JQUERYCOLORBOX_TEXTDOMAIN); ?>.
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="jquery-colorbox-widthValue"><?php _e('Maximum width of the Colorbox', JQUERYCOLORBOX_TEXTDOMAIN); ?>:</label>
                    </th>
                    <td>
                        <input type="radio" name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[width]" id="jquery-colorbox-width-false-radio" value="false" <?php echo ($this->colorboxSettings['width']) == 'false' ? 'checked="checked"' : ''; ?>"/>
                        <label for="jquery-colorbox-width-false-radio"><?php _e('Do not set width', JQUERYCOLORBOX_TEXTDOMAIN); ?>.</label>
                        <br/>
                        <input type="radio" name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[width]" id="jquery-colorbox-width-custom-radio" value="custom" <?php echo ($this->colorboxSettings['width']) == 'custom' ? 'checked="checked"' : ''; ?>"/>
                        <label for="jquery-colorbox-width-custom-radio"><?php _e('Set width of the Colorbox', JQUERYCOLORBOX_TEXTDOMAIN); ?>.</label>
                        <input type="text" name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[widthValue]" id="jquery-colorbox-widthValue" value="<?php echo $this->colorboxSettings['widthValue'] ?>" size="3" maxlength="3"/>
                        <select name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[widthUnit]" id="jquery-colorbox-width-unit" class="postform" style="margin:0">
                        <?php
                        foreach ($this->colorboxUnits as $unit => $name) {
                            echo '<option value="' . esc_attr($unit) . '"';
                            selected($this->colorboxSettings['widthUnit'], $unit);
                            echo '>' . htmlspecialchars($name) . "</option>\n";
                        }
                        ?>
                        </select>
                        <br/><?php _e('Set the maximum width of the Colorbox itself in relation to the browser window to a value between in percent or pixels. If the image is bigger than the colorbox, scrollbars are displayed. If width is not set, the Colorbox will be as wide as the picture in it', JQUERYCOLORBOX_TEXTDOMAIN); ?>.
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="jquery-colorbox-heightValue"><?php _e('Maximum height of the Colorbox', JQUERYCOLORBOX_TEXTDOMAIN); ?>:</label>
                    </th>
                    <td>
                        <input type="radio" name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[height]" id="jquery-colorbox-height-false-radio" value="false" <?php echo ($this->colorboxSettings['height']) == 'false' ? 'checked="checked"' : ''; ?>"/>
                        <label for="jquery-colorbox-height-false-radio"><?php _e('Do not set height', JQUERYCOLORBOX_TEXTDOMAIN); ?>.</label>
                        <br/>
                        <input type="radio" name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[height]" id="jquery-colorbox-height-custom-radio" value="custom" <?php echo ($this->colorboxSettings['height']) == 'custom' ? 'checked="checked"' : ''; ?>"/>
                        <label for="jquery-colorbox-height-custom-radio"><?php _e('Set height of the Colorbox', JQUERYCOLORBOX_TEXTDOMAIN); ?>.</label>
                        <input type="text" name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[heightValue]" id="jquery-colorbox-heightValue" value="<?php echo $this->colorboxSettings['heightValue'] ?>" size="3" maxlength="3"/>
                        <select name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[heightUnit]" id="jquery-colorbox-height-unit" class="postform" style="margin:0">
                        <?php
                        foreach ($this->colorboxUnits as $unit => $name) {
                            echo '<option value="' . esc_attr($unit) . '"';
                            selected($this->colorboxSettings['heightUnit'], $unit);
                            echo '>' . htmlspecialchars($name) . "</option>\n";
                        }
                        ?>
                        </select>
                        <br/><?php _e('Set the maximum height of the Colorbox itself in relation to the browser window to a value between in percent or pixels. If the image is bigger than the colorbox, scrollbars are displayed. If height is not set, the Colorbox will be as high as the picture in it', JQUERYCOLORBOX_TEXTDOMAIN); ?>.
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="jquery-colorbox-linkWidthValue"><?php _e('Maximum width of the Colorbox used for links', JQUERYCOLORBOX_TEXTDOMAIN); ?>:</label>
                    </th>
                    <td>
                        <input type="radio" name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[linkWidth]" id="jquery-colorbox-link-width-false-radio" value="false" <?php echo ($this->colorboxSettings['linkWidth']) == 'false' ? 'checked="checked"' : ''; ?>"/>
                        <label for="jquery-colorbox-link-width-false-radio"><?php _e('Do not set width', JQUERYCOLORBOX_TEXTDOMAIN); ?>.</label>
                        <br/>
                        <input type="radio" name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[linkWidth]" id="jquery-colorbox-link-width-custom-radio" value="custom" <?php echo ($this->colorboxSettings['linkWidth']) == 'custom' ? 'checked="checked"' : ''; ?>"/>
                        <label for="jquery-colorbox-link-width-custom-radio"><?php _e('Set width of the Colorbox', JQUERYCOLORBOX_TEXTDOMAIN); ?>.</label>
                        <input type="text" name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[linkWidthValue]" id="jquery-colorbox-linkWidthValue" value="<?php echo $this->colorboxSettings['linkWidthValue'] ?>" size="3" maxlength="3"/>
                        <select name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[linkWidthUnit]" id="jquery-colorbox-width-unit" class="postform" style="margin:0">
                        <?php
                        foreach ($this->colorboxUnits as $unit => $name) {
                            echo '<option value="' . esc_attr($unit) . '"';
                            selected($this->colorboxSettings['linkWidthUnit'], $unit);
                            echo '>' . htmlspecialchars($name) . "</option>\n";
                        }
                        ?>
                        </select>
                        <br/><?php _e('Set the maximum width of the Colorbox itself in relation to the browser window to a value between in percent or pixels. If the image is bigger than the colorbox, scrollbars are displayed. If width is not set, the Colorbox will be as wide as the picture in it', JQUERYCOLORBOX_TEXTDOMAIN); ?>.
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="jquery-colorbox-linkHeightValue"><?php _e('Maximum height of the Colorbox used for links', JQUERYCOLORBOX_TEXTDOMAIN); ?>:</label>
                    </th>
                    <td>
                        <input type="radio" name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[linkHeight]" id="jquery-colorbox-link-height-false-radio" value="false" <?php echo ($this->colorboxSettings['linkHeight']) == 'false' ? 'checked="checked"' : ''; ?>"/>
                        <label for="jquery-colorbox-link-height-false-radio"><?php _e('Do not set height', JQUERYCOLORBOX_TEXTDOMAIN); ?>.</label>
                        <br/>
                        <input type="radio" name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[linkHeight]" id="jquery-colorbox-link-height-custom-radio" value="custom" <?php echo ($this->colorboxSettings['linkHeight']) == 'custom' ? 'checked="checked"' : ''; ?>"/>
                        <label for="jquery-colorbox-link-height-custom-radio"><?php _e('Set height of the Colorbox', JQUERYCOLORBOX_TEXTDOMAIN); ?>.</label>
                        <input type="text" name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[linkHeightValue]" id="jquery-colorbox-linkHeightValue" value="<?php echo $this->colorboxSettings['linkHeightValue'] ?>" size="3" maxlength="3"/>
                        <select name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[linkHeightUnit]" id="jquery-colorbox-link-height-unit" class="postform" style="margin:0">
                        <?php
                        foreach ($this->colorboxUnits as $unit => $name) {
                            echo '<option value="' . esc_attr($unit) . '"';
                            selected($this->colorboxSettings['linkHeightUnit'], $unit);
                            echo '>' . htmlspecialchars($name) . "</option>\n";
                        }
                        ?>
                        </select>
                        <br/><?php _e('Set the maximum height of the Colorbox itself in relation to the browser window to a value between in percent or pixels. If the image is bigger than the colorbox, scrollbars are displayed. If height is not set, the Colorbox will be as high as the picture in it', JQUERYCOLORBOX_TEXTDOMAIN); ?>.
                    </td>
                </tr>
<!--                <tr>-->
<!--                    <th scope="row">-->
<!--                        <label for="jquery-colorbox-initialWidth">--><?php __('Initial width of the Colorbox', JQUERYCOLORBOX_TEXTDOMAIN); ?><!--:</label>-->
<!--                    </th>-->
<!--                    <td>-->
<!--                        <input type="text" name="--><?php //echo JQUERYCOLORBOX_SETTINGSNAME ?><!--[initialWidth]" id="jquery-colorbox-initialWidth" value="--><?php //echo $this->colorboxSettings['initialWidth'] ?><!--" size="5" maxlength="5"/>--><?php __('pixels',JQUERYCOLORBOX_TEXTDOMAIN) ?>
<!--                        <br/>--><?php __('Set the maximum width of the Colorbox before the content is loaded', JQUERYCOLORBOX_TEXTDOMAIN); ?><!--.-->
<!--                    </td>-->
<!--                </tr>-->
<!--                <tr>-->
<!--                    <th scope="row">-->
<!--                        <label for="jquery-colorbox-initialHeight">--><?php __('Initial height of the Colorbox', JQUERYCOLORBOX_TEXTDOMAIN); ?><!--:</label>-->
<!--                    </th>-->
<!--                    <td>-->
<!--                        <input type="text" name="--><?php //echo JQUERYCOLORBOX_SETTINGSNAME ?><!--[initialHeight]" id="jquery-colorbox-initialHeight" value="--><?php //echo $this->colorboxSettings['initialHeight'] ?><!--" size="5" maxlength="5"/>--><?php __('pixels',JQUERYCOLORBOX_TEXTDOMAIN) ?>
<!--                        <br/>--><?php __('Set the maximum height of the Colorbox before the content is loaded', JQUERYCOLORBOX_TEXTDOMAIN); ?><!--.-->
<!--                    </td>-->
<!--                </tr>-->
                <tr>
                    <th scope="row">
                        <label for="jquery-colorbox-scalePhotos"><?php _e('Resize images', JQUERYCOLORBOX_TEXTDOMAIN); ?>:</label>
                    </th>
                    <td>
                        <input type="checkbox" name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[scalePhotos]" id="jquery-colorbox-scalePhotos" value="true" <?php echo ($this->colorboxSettings['scalePhotos']) ? 'checked="checked"' : '';?>/>
                        <br/><?php _e('If enabled and if maximum width of images, maximum height of images, width of the Colorbox, or height of the Colorbox have been defined, ColorBox will scale photos to fit within the those values', JQUERYCOLORBOX_TEXTDOMAIN); ?>.
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="jquery-colorbox-overlayClose"><?php _e('Close Colorbox on overlay click', JQUERYCOLORBOX_TEXTDOMAIN); ?>:</label>
                    </th>
                    <td>
                        <input type="checkbox" name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[overlayClose]" id="jquery-colorbox-overlayClose" value="true" <?php echo ($this->colorboxSettings['overlayClose']) ? 'checked="checked"' : '';?>/>
                        <br/><?php _e('If checked, enables closing ColorBox by clicking on the background overlay', JQUERYCOLORBOX_TEXTDOMAIN); ?>.
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="jquery-colorbox-preloading"><?php _e('Preload images', JQUERYCOLORBOX_TEXTDOMAIN); ?>:</label>
                    </th>
                    <td>
                        <input type="checkbox" name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[preloading]" id="jquery-colorbox-preloading" value="true" <?php echo ($this->colorboxSettings['preloading']) ? 'checked="checked"' : '';?>/>
                        <br/><?php _e('Allows for preloading of "next" and "previous" content in a group, after the current content has finished loading. Uncheck box to disable.', JQUERYCOLORBOX_TEXTDOMAIN); ?>.
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="jquery-colorbox-transition"><?php _e('Transition type', JQUERYCOLORBOX_TEXTDOMAIN); ?>:</label>
                    </th>
                    <td>
                        <select name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[transition]" id="jquery-colorbox-transition" class="postform" style="margin:0">
                        <?php
                        foreach ($this->colorboxTransitions as $unit => $name) {
                            echo '<option value="' . esc_attr($unit) . '"';
                            selected($this->colorboxSettings['transition'], $unit);
                            echo '>' . htmlspecialchars($name) . "</option>\n";
                        }
                        ?>
                        </select>
                        <br/><?php _e('The transition type of the Colorbox. Can be set to "elastic", "fade", or "none"', JQUERYCOLORBOX_TEXTDOMAIN); ?>.
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="jquery-colorbox-speed"><?php _e('Transition speed', JQUERYCOLORBOX_TEXTDOMAIN); ?>:</label>
                    </th>
                    <td>
                        <input type="text" name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[speed]" id="jquery-colorbox-speed" value="<?php echo $this->colorboxSettings['speed'] ?>" size="5" maxlength="5"/><?php _e('milliseconds',JQUERYCOLORBOX_TEXTDOMAIN) ?>
                        <br/><?php _e('Sets the speed of the "fade" and "elastic" transitions, in milliseconds', JQUERYCOLORBOX_TEXTDOMAIN); ?>.
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="jquery-colorbox-opacity"><?php _e('Opacity', JQUERYCOLORBOX_TEXTDOMAIN); ?>:</label>
                    </th>
                    <td>
                        <input type="text" name="<?php echo JQUERYCOLORBOX_SETTINGSNAME ?>[opacity]" id="jquery-colorbox-opacity" value="<?php echo $this->colorboxSettings['opacity'] ?>" size="4" maxlength="4"/>
                        <br/><?php _e('The overlay opacity level. Range: 0 to 1', JQUERYCOLORBOX_TEXTDOMAIN); ?>.
                    </td>
                </tr>
                <!--tr>
                    <th scope="row">
                        <label for="jquery-colorbox-draggable"><?php __('Make images draggable', JQUERYCOLORBOX_TEXTDOMAIN); ?>:</label>
                    </th>
                    <td>
                        <input type="checkbox" name="<?php //echo JQUERYCOLORBOX_SETTINGSNAME ?>[draggable]" id="jquery-colorbox-draggable" value="true" <?php //echo ($this->colorboxSettings['draggable']) ? 'checked="checked"' : '';?>/>
                        <br/><?php __('Make images draggable instead of scrollable', JQUERYCOLORBOX_TEXTDOMAIN); ?>.
                    </td>
                </tr-->
                </table>
                <p class="submit">
                    <input type="hidden" name="action" value="jQueryColorboxUpdateSettings"/>
                    <input type="submit" name="jQueryColorboxUpdateSettings" class="button-primary" value="<?php _e('Save Changes') ?>"/>
                </p>

            </div>
        </div>
    </div>
    </form>

    <div id="poststuff">
        <div id="jquery-colorbox-delete_settings" class="postbox">
            <h3 id="delete_options"><?php _e('Delete Settings', JQUERYCOLORBOX_TEXTDOMAIN) ?></h3>

            <div class="inside">
                <p><?php _e('Check the box and click this button to delete settings of this plugin.', JQUERYCOLORBOX_TEXTDOMAIN); ?></p>

                <form name="delete_settings" method="post" action="admin-post.php">
                <?php if (function_exists('wp_nonce_field') === true) wp_nonce_field('jquery-delete_settings-form'); ?>
                    <p id="submitbutton">
                    <input type="hidden" name="action" value="jQueryColorboxDeleteSettings"/>
                    <input type="submit" name="jQueryColorboxDeleteSettings" value="<?php _e('Delete Settings', JQUERYCOLORBOX_TEXTDOMAIN); ?> &raquo;" class="button-secondary"/>
                    <input type="checkbox" name="delete_settings-true"/>
                </p>
                </form>
            </div>
        </div>
    </div>
    </div>
    <div class="postbox-container" style="width: 29%;">
    <div id="poststuff">
        <div id="jquery-colorbox-topdonations" class="postbox">
            <h3 id="topdonations"><?php _e('Top donations', JQUERYCOLORBOX_TEXTDOMAIN) ?></h3>

            <div class="inside">
                <?php echo $this->getRemoteContent(JQUERYCOLORBOX_TOPDONATEURL); ?>
            </div>
        </div>
    </div>
    <div id="poststuff">
        <div id="jquery-colorbox-latestdonations" class="postbox">
            <h3 id="latestdonations"><?php _e('Latest donations', JQUERYCOLORBOX_TEXTDOMAIN) ?></h3>

            <div class="inside">
                <?php echo $this->getRemoteContent(JQUERYCOLORBOX_LATESTDONATEURL); ?>
            </div>
        </div>
    </div>
    <div id="poststuff">
        <div id="jquery-colorbox-donate" class="postbox">
            <h3 id="donate"><?php _e('Donate', JQUERYCOLORBOX_TEXTDOMAIN) ?></h3>

            <div class="inside">
                <p>
                    <?php _e('If you would like to make a small (or large) contribution towards future development please consider making a donation.', JQUERYCOLORBOX_TEXTDOMAIN) ?>
                </p>
<!--                <h5 id="donatePaypal">--><?php __('Donate using Paypal', JQUERYCOLORBOX_TEXTDOMAIN) ?><!--</h5>-->
                <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                    <input type="hidden" name="cmd" value="_xclick" />
                    <input type="hidden" name="business" value="G75G3Z6PQWXXQ" />
                    <input type="hidden" name="item_name" value="<?php _e('Techotronic Development Support' , JQUERYCOLORBOX_TEXTDOMAIN); ?>" />
                    <input type="hidden" name="item_number" value="jQuery Colorbox"/>
                    <input type="hidden" name="no_shipping" value="0"/>
                    <input type="hidden" name="no_note" value="0"/>
                    <input type="hidden" name="cn" value="<?php _e("Please enter the URL you'd like me to link to in the donors lists", JQUERYCOLORBOX_TEXTDOMAIN); ?>." />
                    <input type="hidden" name="return" value="<?php $this->getReturnLocation(); ?>" />
                    <input type="hidden" name="cbt" value="<?php _e('Return to Your Dashboard' , JQUERYCOLORBOX_TEXTDOMAIN); ?>" />
                    <input type="hidden" name="currency_code" value="USD"/>
                    <input type="hidden" name="lc" value="US"/>
                    <input type="hidden" name="bn" value="PP-DonationsBF"/>
                    <label for="preset-amounts"><?php _e('Select Preset Amount', JQUERYCOLORBOX_TEXTDOMAIN); echo ": "; ?></label>
                    <select name="amount" id="preset-amounts">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="30">30</option>
                        <option value="40">40</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select><span><?php _e('USD',JQUERYCOLORBOX_TEXTDOMAIN) ?></span>
                    <br /><br /><?php _e('Or', JQUERYCOLORBOX_TEXTDOMAIN); ?><br /><br />
                    <label for="custom-amounts"><?php _e('Enter Custom Amount', JQUERYCOLORBOX_TEXTDOMAIN); echo ": "; ?></label>
                    <input type="text" name="amount" size="4" id="custom-amounts"/>
                    <span><?php _e('USD',JQUERYCOLORBOX_TEXTDOMAIN) ?></span>
                    <br /><br />
                    <input type="submit" value="<?php _e('Submit',JQUERYCOLORBOX_TEXTDOMAIN) ?>" class="button-secondary"/>
                </form>
            </div>
        </div>
    </div>
    <div id="poststuff">
        <div id="jquery-colorbox-translation" class="postbox">
            <h3 id="translation"><?php _e('Translation', JQUERYCOLORBOX_TEXTDOMAIN) ?></h3>

            <div class="inside">
                <p><?php _e('The english translation was done by <a href="http://www.techotronic.de">Arne Franken</a>.', JQUERYCOLORBOX_TEXTDOMAIN); ?></p>
            </div>
        </div>
    </div>
    </div>
    </div>
    <div class="clear">
        <p>
            <br/>&copy; Copyright 2009 - <?php echo date("Y"); ?> <a href="http://www.techotronic.de">Arne Franken</a>
        </p>
    </div>
</div>