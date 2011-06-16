/*
 * @package Techotronic
 * @subpackage jQuery Colorbox
 *
 * @since 4.1
 * @author Arne Franken
 *
 * makes photos draggable
 */
jQuery(document).ready(function($) {
    $(document).bind('cbox_complete', function(){
        $('#cboxPhoto').unbind().css({cursor:'inherit'});
        $("#cboxLoadedContent").draggable();
    });
});