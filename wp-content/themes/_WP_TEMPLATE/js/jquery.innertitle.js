// Inner title JQuery plugin - shows titles inside input boxes automatically.
// Requires JQuery 1.3+
// (C) 2009, Nikolay Karev, karev.n@gmail.com
// Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php

(function($){
  $.fn.observe = function(callback, period ){
    var element = this, value = this.val();
    element.focused = false;
    setInterval(function(){
      if ( element.val() != value ) callback.call( element );
      value = element.val();
    },
    period * 1000);
  };


  $.fn.innerTitle = function(options){
    var defaults = {
      inputClass: 'it-has-overlay',
      overlayClass: 'it-overlay'
    }
    options = $.extend(defaults, options);
    $(this).each(function(){
      if ($(this).hasClass(options.inputClass) || $(this).is(':hidden')){
        return;
      }
      $(this).addClass(options.inputClass);

      var title = '';
      if(options.innerTitle != null) title = options.innerTitle;
      else title = $(this).attr('title');
      if (title == '' || title == null){
        return;
      }

      var div = $('<div/>');
      var focused = false;
      var input = this;
      div.html(title);
      $(div).css({
        position: 'absolute',
        top: $(this).position().top + $(this).margin().top + "px",
        left: $(this).position().left + $(this).margin().left + "px",
        zIndex: $(this).css('zindex') + 100
      });
      $(div).attr('class', $(this).attr('class'));
      $(div).addClass(options.overlayClass);
      $(this).offsetParent().append(div);

      if($(this).val() != ''){
        $(div).hide();
      }
      $(div).click(function(){
        $(this).hide();
        $(input).focus();
      });
      $(this).blur(function(){
        focused = false;
        if ($(this).val() == ''){
          $(div).show();
        }
      });
      $(this).focus(function(){
        focused = true;
        $(div).hide();
      });
      $(this).observe(function(){
        if ($(this).val() == '' && !focused){
          $(div).show();
        } else {
          $(div).hide();
        }
      },
      0.25);
    });
  }


  $.fn.innerTitle.enableGlobally = function(){
    $('body :input:visible').innerTitle();
  }
})(jQuery);