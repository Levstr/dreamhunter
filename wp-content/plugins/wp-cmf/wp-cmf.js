
jQuery(function($){
  var validationInProgress = false;
  /*$('#post').submit(function(event){
    
    function showInvalidPostNotice(){
      if ($('#notice').length == 0){
        $('<div id="notice" class="error below-h2" />').append($('<p/>')).insertBefore('#post');
      }
      $('#notice').addClass('error');
      $('#notice p').html('The post is not valid - please check all the fields.');
      
    }
    
    function evalResponseArray(code){
      $.each(code, function(index, value){
        try{eval(value);}
        catch(error){alert(error);}
      });
    }
    
    function resetSubmitButtonState(){
      $('#publish').removeAttr('disabled');
      $('#publish').removeClass('button-primary-disabled');
      $('#ajax-loading').css('visibility', 'hidden');
    }
    if (!validationInProgress){
      validationInProgress = true;
      $('#publish').attr('disabled', 'disabled');
      var actionBackup = $('#hiddenaction').val();
      $('#hiddenaction').val('cmf_validate_post');
      var serialized = $(this).serialize();
      $('#hiddenaction').val(actionBackup);
      $.post(ajaxurl, serialized, function(response){
        data = $.parseJSON(response);
        if (data.errors.length > 0){
          showInvalidPostNotice();
          evalResponseArray(data.cleanup);
          evalResponseArray(data.errors);
          resetSubmitButtonState();
        } else {
          enableSubmission = true;
          $('#post').submit();
        }
       

        validationInProgress = false;
      });
      event.stopImmediatePropagation();
      return;
    } 
    if (!enableSubmission){
      event.stopImmediatePropagation();
    } else {
      enableSubmission = false;
    }
  });
  
  function showValidationErrors(data){
    
  }
  */
  
  if (typeof(tinyMCE) != 'undefined'){
    jQuery('.cmf-wysiwyg').each(function(index, element){
      enableTinyMCE(element.id);
    });
    
    jQuery('.html-mode').click(function(event){
      event.preventDefault();
      jQuery(this).parent().children('a.wysiwyg-mode, a.html-mode').toggleClass('hidden');
      var textareaId = jQuery(this).parent().next().find('textarea').attr('id');
      var elementId = '#qtags' + textareaId +'_toolbar';
      jQuery(elementId).show();

      disableTinyMCE(textareaId);
      return false;
    });
    jQuery('.wysiwyg-mode').click(function(event){
      event.preventDefault();
      jQuery(this).parent().children('a.wysiwyg-mode, a.html-mode').toggleClass('hidden');
      enableTinyMCE(jQuery(this).parent().next().find('textarea').attr('id'));
      return false;
    });
    jQuery('.wysiwyg-toolbar .cmf-upload-image').click(function(){
      var textarea = jQuery(this).parent().next().find('textarea').attr('id');
      focusTextArea(textarea);
      return thickbox(this);
    });
    jQuery('.image-field .cmf-upload-image').click(function(event){
      event.preventDefault();
      return thickbox(this);
    });
    jQuery('.cmf_field_wrap .quicktags').each(function(index, element){
      var textareaId = jQuery(element).find('textarea').attr('id');
      var elementId = 'editorcontainer_' + textareaId;
      window['qtags' + textareaId] = new QTags( "qtags" + textareaId, textareaId, elementId);
      jQuery('#qtags' + textareaId +'_toolbar').hide();
    });
  }
  
  
  
  function focusTextArea(id) {
		jQuery(document).ready(function() {
			if ( typeof tinyMCE != "undefined" ) {
				var elm = tinyMCE.get(id);
			}
			if ( ! elm || elm.isHidden() ) {
				elm = document.getElementById(id);
				isTinyMCE = false;
			} else isTinyMCE = true;
			tmpFocus = elm;
			elm.focus();
			if (elm.createTextRange) {
				var range = elm.createTextRange();
				range.move("character", elm.value.length);
				range.select();
			} else if (elm.setSelectionRange) {
				elm.setSelectionRange(elm.value.length, elm.value.length);
			}
		});
	}
  
  function enableTinyMCE(id){
    var ed = tinyMCE.get(id);
		if ( ! ed || ed.isHidden() ) {
			document.getElementById(id).value = switchEditors.wpautop(document.getElementById(id).value);
			jQuery('#editorcontainer_' + id).prev().hide();
			if (ed){
			  ed.show();
			} else {
			  tinyMCE.execCommand("mceAddControl", false, id);
			}
		}
  }
  
  function disableTinyMCE(id){
     var ed = tinyMCE.get(id);
    if (ed)
      ed.hide(); 
		jQuery('#editorcontainer_'+id).prev().show();
		document.getElementById(id).style.color="#000000";
  }
  function thickbox(link) {
		var t = link.title || link.name || null;
    var a = link.href || link.alt;
    var g = link.rel || false;
    alert(a);
    tb_show(t,a,g);
    link.blur();
    return false;
	}
});

function cmf_send_to_field(id, html, plain){
  if (plain || typeof(tinyMCE) == 'undefined'){
    jQuery('#' + id).val(html);
    tb_remove();
    return;
  }
  if ( typeof tinyMCE == "undefined" ) ed = document.getElementById(id);
  else { ed = tinyMCE.get(id); 
  if(ed) {if(!ed.isHidden()) isTinyMCE = true;} else isTinyMCE = false;}
  if ( typeof tinyMCE != "undefined" && isTinyMCE && !ed.isHidden() ) {
    ed.focus();//if (tinymce.isIE)
    if ( html.indexOf("[caption") != -1 )
      html = ed.plugins.wpeditimage._do_shcode(html);
    ed.execCommand("mceInsertContent", false, html);
  } else {
//    else edInsertContent(edCanvas, html);
  }
  tb_remove();
  
  
    
}


jQuery(function(){
autosave = function () {
    blockSave = true;
    var c = (typeof tinyMCE != "undefined") && tinyMCE.get('content') && !tinyMCE.get('content').isHidden(),
        d, f, b, e, a;
    autosave_disable_buttons();
    d = {
        action: "autosave",
        post_ID: jQuery("#post_ID").val() || 0,
        post_title: jQuery("#title").val() || "",
        autosavenonce: jQuery("#autosavenonce").val(),
        post_type: jQuery("#post_type").val() || "",
        autosave: 1
    };
    jQuery(".tags-input").each(function () {
        d[this.name] = this.value
    });
    f = true;
    if (jQuery("#TB_window").css("display") == "block") {
        f = false
    }
    if (c && f) {
        b = tinyMCE.activeEditor;
        if (b.plugins.spellchecker && b.plugins.spellchecker.active) {
            f = false
        } else {
            if ("mce_fullscreen" == b.id) {
                tinyMCE.get("content").setContent(b.getContent({
                    format: "raw"
                }), {
                    format: "raw"
                })
            }
            tinyMCE.get("content").save()
        }
    }
    d.content = jQuery("#content").val();
    if (jQuery("#post_name").val()) {
        d.post_name = jQuery("#post_name").val()
    }
    if ((d.post_title.length == 0 && d.content.length == 0) || d.post_title + d.content == autosaveLast) {
        f = false
    }
    e = jQuery("#original_post_status").val();
    goodcats = ([]);
    jQuery("[name='post_category[]']:checked").each(function (g) {
        goodcats.push(this.value)
    });
    d.catslist = goodcats.join(",");
    if (jQuery("#comment_status").attr("checked")) {
        d.comment_status = "open"
    }
    if (jQuery("#ping_status").attr("checked")) {
        d.ping_status = "open"
    }
    if (jQuery("#excerpt").size()) {
        d.excerpt = jQuery("#excerpt").val()
    }
    if (jQuery("#post_author").size()) {
        d.post_author = jQuery("#post_author").val()
    }
    if (jQuery("#parent_id").val()) {
        d.parent_id = jQuery("#parent_id").val()
    }
    d.user_ID = jQuery("#user-id").val();
    if (jQuery("#auto_draft").val() == "1") {
        d.auto_draft = "1"
    }
    if (f) {
        autosaveLast = jQuery("#title").val() + jQuery("#content").val()
    } else {
        d.autosave = 0
    }
    if (d.auto_draft == "1") {
        a = autosave_saved_new
    } else {
        a = autosave_saved
    }
    autosaveOldMessage = jQuery("#autosave").html();
    jQuery.ajax({
        data: d,
        beforeSend: f ? autosave_loading : null,
        type: "POST",
        url: autosaveL10n.requestFile,
        success: a
    })
};
});