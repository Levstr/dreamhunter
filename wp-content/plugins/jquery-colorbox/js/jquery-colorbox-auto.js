/*
 * @package Techotronic
 * @subpackage jQuery Colorbox
 *
 * @since 3.1
 * @author Arne Franken
 * @author jrevillini
 *
 * adds colorbox-manual to ALL img tags that are found in the HTML output
 */
jQuery(document).ready( function(jQuery) {
    jQuery("img").each( function(index,obj){
        if(!jQuery(obj).attr("class").match('colorbox')) {
            jQuery(obj).addClass('colorbox-manual');
        }
    });
});