<?php
/**
 * @package Techotronic
 * @subpackage jQuery Colorbox
 *
 * @since 3.7
 * @author Arne Franken
 *
 * loads the main function of the Colorbox Javascript
 */
?>
<script type="text/javascript">
    // <![CDATA[
<?php
    /**
     * declare variables that are used in more than one function
     */
    ?>
    var COLORBOX_INTERNAL_LINK_PATTERN = /^#.*/;
    var COLORBOX_IMG_PATTERN = /\.(?:jpe?g|gif|png|bmp)/i;
    var COLORBOX_MANUAL = "colorbox-manual";
    var COLORBOX_OFF_CLASS = ".colorbox-off";
    var COLORBOX_LINK_CLASS = ".colorbox-link";
    var COLORBOX_OFF = "colorbox-off";
    var COLORBOX_CLASS_MATCH = "colorbox-[0-9]+";

    var colorboxInline = false;
    var colorboxIframe = false;
    var colorboxGroupId;
    var colorboxTitle;
    var colorboxWidth = false;
    var colorboxHeight = false;
    var colorboxMaxWidth = false;
    var colorboxMaxHeight = false;
    var colorboxSlideshow = <?php echo !$this->colorboxSettings['slideshow'] ? 'false' : 'true'; ?>;
    var colorboxSlideshowAuto = <?php echo $this->colorboxSettings['slideshowAuto'] ? 'true' : 'false';?>;
    var colorboxScalePhotos = <?php echo $this->colorboxSettings['scalePhotos'] ? 'true' : 'false';?>;
    var colorboxPreloading = <?php echo $this->colorboxSettings['preloading'] ? 'true' : 'false';?>;
    var colorboxOverlayClose = <?php echo $this->colorboxSettings['overlayClose'] ? 'true' : 'false';?>;
    var colorboxLoop = <?php echo !$this->colorboxSettings['disableLoop'] ? 'true' : 'false';?>;
    var colorboxEscKey = <?php echo !$this->colorboxSettings['disableKeys'] ? 'true' : 'false';?>;
    var colorboxArrowKey = <?php echo !$this->colorboxSettings['disableKeys'] ? 'true' : 'false';?>;
    var colorboxScrolling = <?php echo !$this->colorboxSettings['displayScrollbar'] || $this->colorboxSettings['draggable'] ? 'true' : 'false';?>;
    var colorboxOpacity = "<?php echo $this->colorboxSettings['opacity']; ?>";
    var colorboxTransition = "<?php echo $this->colorboxSettings['transition']; ?>";
    var colorboxSpeed = <?php echo $this->colorboxSettings['speed']; ?>;
    var colorboxSlideshowSpeed = <?php echo $this->colorboxSettings['slideshowSpeed']; ?>;
    var colorboxClose = "<?php _e('close', JQUERYCOLORBOX_TEXTDOMAIN); ?>";
    var colorboxNext = "<?php _e('next', JQUERYCOLORBOX_TEXTDOMAIN); ?>";
    var colorboxPrevious = "<?php _e('previous', JQUERYCOLORBOX_TEXTDOMAIN); ?>";
    var colorboxSlideshowStart = "<?php _e('start slideshow', JQUERYCOLORBOX_TEXTDOMAIN); ?>";
    var colorboxSlideshowStop = "<?php _e('stop slideshow', JQUERYCOLORBOX_TEXTDOMAIN); ?>";
    var colorboxCurrent = "<?php _e('{current} of {total} images', JQUERYCOLORBOX_TEXTDOMAIN); ?>";

    var colorboxImageMaxWidth = <?php echo $this->colorboxSettings['maxWidth'] == "false" ? 'false' : '"' . $this->colorboxSettings['maxWidthValue'] . $this->colorboxSettings['maxWidthUnit'] . '"'; ?>;
    var colorboxImageMaxHeight = <?php echo $this->colorboxSettings['maxHeight'] == "false" ? 'false' : '"' . $this->colorboxSettings['maxHeightValue'] . $this->colorboxSettings['maxHeightUnit'] . '"'; ?>;
    var colorboxImageHeight = <?php echo $this->colorboxSettings['height'] == "false" ? 'false' : '"' . $this->colorboxSettings['heightValue'] . $this->colorboxSettings['heightUnit'] . '"'; ?>;
    var colorboxImageWidth = <?php echo $this->colorboxSettings['width'] == "false" ? 'false' : '"' . $this->colorboxSettings['widthValue'] . $this->colorboxSettings['widthUnit'] . '"'; ?>;

    var colorboxLinkHeight = <?php echo $this->colorboxSettings['linkHeight'] == "false" ? 'false' : '"' . $this->colorboxSettings['linkHeightValue'] . $this->colorboxSettings['linkHeightUnit'] . '"'; ?>;
    var colorboxLinkWidth = <?php echo $this->colorboxSettings['linkWidth'] == "false" ? 'false' : '"' . $this->colorboxSettings['linkWidthValue'] . $this->colorboxSettings['linkWidthUnit'] . '"'; ?>;

    var colorboxInitialHeight = <?php echo $this->colorboxSettings['initialHeight']; ?>;
    var colorboxInitialWidth = <?php echo $this->colorboxSettings['initialWidth']; ?>;

<?php
    /**
     * call colorbox selector function.
     */
    ?>
    jQuery(document).ready(function() {
        colorboxSelector();
    });
    // ]]>
</script>
