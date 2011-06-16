jQuery(document).ready(function(){
    jQuery(".pfs-post-link").click(function(){
        jQuery(this).parent('#pfs-alert').hide();
        var id = '#pfs-post-box-' + jQuery(this).attr('id').replace('-link','');
        var distrl = (jQuery(window).width()-600)/2;
        var disttb = (jQuery(window).height()-400)/2
        jQuery('#pfs-post-box-shadow').show();
        jQuery(id).css({zIndex:'200',top:"50px",left:distrl+"px",width:'600px',position:'absolute'}).show();
    });
    jQuery("#closex").click(function(){
        jQuery(this).parent('.pfs-post-box').hide();
        jQuery(this).parent('#pfs-alert').hide();
        jQuery('#pfs-post-box-shadow').hide();
    });
    jQuery("#pfs-post-box-shadow").click(function(e){
      if(jQuery(e.target).is('#pfs-post-box-shadow')) jQuery("#closex").click();
    });
    jQuery("#return").live('click',function(){
        jQuery('.pfs-post-box').hide();
        location.reload();
    });
    jQuery("form.pfs").submit(function() {
        jQuery(this).ajaxSubmit({
            type: "POST",
            url: jQuery(this).attr('action'),
            dataType:'json',
            beforeSend: function(){
                jQuery('.pfs-post-box #post').val('Идет загрузка...');
            },
            complete: function(request,textStatus,error) {
                data = jQuery.parseJSON(String(request.responseText.match(/\{[^\}]+\}/i)));
                if(!data){
                    jQuery('#pfs-alert').addClass('error').html('Unknown error').show();
                    jQuery('.pfs-post-box #post').val('Загрузить фото');
                }else if(data.error){
                    jQuery('#pfs-alert').addClass('error').html(data.error).show();
                    jQuery('.pfs-post-box #post').val('Загрузить фото');
                }else{
                    jQuery('.pfs-post-box').children().not('.closex').remove();
                    jQuery('.pfs-post-box').append('\
                      <center>\
                        <br/><br/>\
                        <strong>Изображения загружены</strong><br/>\
                        <br/>\
                        <h3><a id="return" href="#">Вернуться</a></h3>\
                      </center>').show();
                }
            }
        });
        return false;
    });
});
