	//------------------------------
	function parseDate(mydate) {
		// parse date 24.12.2009, а надо ,чтоб было '12/24/2009';
		var str = mydate.split(".");
		var mydate1 = str[1]+'/'+str[0]+'/'+str[2];
		
		//return new Date(mydate1).getTime();
		return new Date(mydate1);
	}
	function addDays(date, n) {
		// может отличаться на час, если произошло событие перевода времени
		var d = new Date();	
		d.setTime(date.getTime() + n * 24 * 60 * 60 * 1000);
		//d.setTime(date + n * 24 * 60 * 60 * 1000);
		//alert(date.getTime());
		//d.setDate(parseInt(date.getDate()) + parseInt(n));
		return d; //.getTime();
	}
	//------------------------------
	
jQuery(function($){
	//$( "#datepicker" ).datepicker();
	$("#datepicker1").datepicker({
				showOtherMonths: true, 
				selectOtherMonths: true,
				onSelect: function(dateText, inst) {
					$('#date_start').text(dateText);
					$('#date_df').val(dateText);
					
					//ДН > ДК-МКД,
					var date_start = parseDate(dateText);
					var date_end = addDays(parseDate($("#datepicker2").val()),-$('#select_nf').val());
					if(date_start.getTime() > date_end.getTime()) {
						$("#datepicker2").datepicker( "setDate" , addDays(date_start,'+'+$('#select_nf').val()));
						//$("#datepicker2").datepicker( "setDate" , addDays(parseDate($("#datepicker2").val()),'+'+$('#select_nf').val()));
						$('#date_end').text($("#datepicker2").val());
						$('#date_dt').val($("#datepicker2").val());
					}
				}
	});
	
	$("#datepicker2").datepicker({
				showOtherMonths: true, 
				selectOtherMonths: true,
				onSelect: function(dateText, inst) {
					if(!$('#date_expl').text()) {
						$('#date_expl').text('по');
					}
					$('#date_end').text(dateText);
					$('#date_dt').val(dateText);
					
					//ДК < ДН+МКД
					var date_start = parseDate(dateText);
					var date_end = addDays(parseDate($("#datepicker1").val()),$('#select_nf').val());
					if(date_start.getTime() < date_end.getTime()) {
						//$("#datepicker1").datepicker( "setDate" , addDays(parseDate($("#datepicker1").val()),'-'+$('#select_nf').val()));
						$("#datepicker1").datepicker( "setDate" , addDays(date_start,'-'+$('#select_nf').val()));
						$('#date_start').text($("#datepicker1").val());
						$('#date_df').val($("#datepicker1").val());
					}
				}	
	});
	$("#datepicker2").datepicker( "setDate" , '+7');
	$.datepicker.regional['ru'];
	
   /* $(".hasDatepicker")//.css({opacity:0.5})
    .mouseenter(function(){$(this).stop().animate({opacity:1}, 100)})
    .mouseleave(function(){$(this).stop().animate({opacity:0.5}, 300)});*/
	$(".hasDatepicker").css({opacity:1});
	$('#datepicker1').mouseenter(function(){
		$(this).stop().animate({opacity:1}, 100);
		$('#datepicker2').stop().animate({opacity:0.5}, 300);
	}).mouseleave(function(){
		$('#datepicker2').stop().animate({opacity:1}, 100);
	});
	
	$('#datepicker2').mouseenter(function(){
		$(this).stop().animate({opacity:1}, 100);
		$('#datepicker1').stop().animate({opacity:0.5}, 300);
	}).mouseleave(function(){
		$('#datepicker1').stop().animate({opacity:1}, 100);
	});

	
	$('#datepicker1').tooltip({track: true, delay:0});
	$('#datepicker2').tooltip({track: true, delay:0});
	
	$("#form_search_l select").selectbox();
	
	
	//-------------
	$('#date_start').text($("#datepicker1").val());
	$('#date_df').val($("#datepicker1").val());
	if(!$('#date_expl').text()) {
						$('#date_expl').text('по');
					}
	$('#date_end').text($("#datepicker2").val());
	$('#date_dt').val($("#datepicker2").val());
	//-------------
	//-----------------------------------------------
	$('.button_disabled').css({opacity:0.7});
	$('.button_disabled').live("click", function(e) {
		//"x=" + e.pageX + "; y= " + e.pageY
		e.stopImmediatePropagation();
		
		//alert("x=" + e.pageX + "; y= " + e.pageY);
		$('#form_login_reg').show().css({top:e.pageY, left:e.pageX});
		
		//------------
		// назначаем переменную передающую нахождение мыши вне или за пределами обьекта 
        var form_login_reg = 0;
        $('#form_login_reg').hover(function(){
            form_login_reg = 1;
        }, function(){
            form_login_reg = 0;
        });
        // Ловим событие клика мыши во всем теле страницы
        $(document).click(function(){
            if(form_login_reg == 0){
				$('#form_login_reg').hide();
            }
        });
		//------------
		return false;
	});
	//-----------------------------------------------
	//---------------------
			
	$(".add_to_cart").live("click", function(e) {
		var tour_id = $(this).attr('href');
		var tour_inf = $(this).parent().parent().parent().html();
		
		//----------------
		tour_inf = '<td class="td_name">'+$('#select_ct option:selected').text()+' - '+$('#select_co option:selected').text()+' число дней: '+$('#select_nf').val()+'</td>'+tour_inf;
		//----------------
				  
		var ajax_url = '/wp-admin/admin-ajax.php';
		var data = {
			action: 'of_ajax_cart_action',
			tour_id: tour_id,
			tour_inf : tour_inf
		};
		var obj = $(this);
		$.post(ajax_url, data, function(response, textStatus, XMLHttpRequest){
			//alert(textStatus);
			if(textStatus=='success') {
				obj.parent().html('Тур добавлен. <br><a href="/cabinet/">Перейти в корзину</a>');
			}
		});

		return false;
	});
	//------------------------
	
	$('.comment_buts .com_add').click(function(){
		$(this).parent().parent().children('#comments').children('.respond_form').show();
		return false;
	});
	
	//------------------------------------
	$("#but_add_request").live("click", function(e) {
		$('#form_add_request').show();
		//------------
		// назначаем переменную передающую нахождение мыши вне или за пределами обьекта 
        var form_request_open = 0;
        $('#form_add_request').hover(function(){
            form_request_open = 1;
        }, function(){
            form_request_open = 0;
        });
        // Ловим событие клика мыши во всем теле страницы
        $(document).click(function(){
            if(form_request_open == 0){
				$('#form_add_request').hide();
            }
        });
		//------------
		return false;
	});
	
	function clear_form(form) {
		form.find('input[type="text"]').val('');
		form.find('textarea').val('');
	}
	
	$("#form_add_request .submit").live("click", function(e){
	  var form=$(this).parent();
	  var title = form.find('[name="request_title"]').val();
	  var text = form.find('[name="request_text"]').val();
	  
	  if(!title.length || !text.length){
		$("#form_add_request .alert").text("Пожалуйста, заполните все поля формы");
		return false;
	  }
	  
	  var ajax_url = '/wp-admin/admin-ajax.php';
	  var data = {
		action: 'of_ajax_addmyrequest_action',
		request_title: title,
		request_text: text
	  };
	  $.post(ajax_url, data, function(response){
		//$("#form_add_request .alert").text("Ваш запрос отправлен");
		$(".req_text").text("запрос успешно отправлен");
		clear_form(form);
		$('#form_add_request').hide();	
	  });

	  return false;
	});
	
	$("#form_add_request .form_close").click(function(){
		$('#form_add_request').hide();
	});
	
	//-----------------------------
	$(".g_send_request").live("click", function(e) {
		var form = $(this).parent();
		
		var deal = form.find('.send_request_v').val();
		  
		var ajax_url = '/wp-admin/admin-ajax.php';
		var data = {
			action: 'of_ajax_sendrequest_action',
			deal: deal
		};
		//var obj = $(this);
		$.post(ajax_url, data, function(response, textStatus, XMLHttpRequest){
			//alert(textStatus);
			//if(textStatus=='success') {
			if(response=='10') {
				form.find('.alert').text('Заявка отправлена');
			} else if(response=='20') {
				form.find('.alert').text('Вы уже оставляли заявку на это предложение');
			}
		});

		return false;
	});
	
	$('#but_foto_upload').live("click", function(e) {
		//$('#foto_upload_form').show();
		$('#pfs-post-box-shadow').show();
		$('#pfs-post-box-foto').show();
		//------------
		// назначаем переменную передающую нахождение мыши вне или за пределами обьекта 
        var form_fotoform_open = 0;
        $('#pfs-post-box-foto').hover(function(){
            form_fotoform_open = 1;
        }, function(){
            form_fotoform_open = 0;
        });
        // Ловим событие клика мыши во всем теле страницы
        $(document).click(function(){
            if(form_fotoform_open == 0){
				$('#pfs-post-box-foto').hide();
				$('#pfs-post-box-shadow').hide();
            }
        });
		//------------
		return false;
	});
	
	$("#but_foto_upl").live("click", function(e) {
		var form = $(this).parent();
		
		//var deal = form.find('.send_request_v').val();
		var foto = $('#foto_upl').val();
		  
		var ajax_url = '/wp-admin/admin-ajax.php';
		var data = {
			action: 'of_ajax_picture_action',
			foto: foto
		};
		//var obj = $(this);
		$.post(ajax_url, data, function(response, textStatus, XMLHttpRequest){
			alert(textStatus);
			//if(textStatus=='success') {
			/*if(response=='10') {
				form.find('.alert').text('Заявка отправлена');
			} else if(response=='20') {
				form.find('.alert').text('Вы уже оставляли заявку на это предложение');
			}*/
		});

		return false;
	});
	
	//=====================================
	 $('.foto_line').mousedown(function (event) {
            $(this)
                .data('down', true)
                .data('x', event.clientX)
                .data('scrollLeft', this.scrollLeft);
                
            return false;
        }).mouseup(function (event) {
            $(this).data('down', false);
        }).mousemove(function (event) {
            if ($(this).data('down') == true) {
                this.scrollLeft = $(this).data('scrollLeft') + $(this).data('x') - event.clientX;
            }
        }).mousewheel(function (event, delta) {
            this.scrollLeft -= (delta * 30);
			return false;
        }).css({
            'overflow' : 'hidden',
            'cursor' : '-moz-grab'
        });
		
		 $(window).mouseout(function (event) {
        if ($('.foto_line').data('down')) {
            try {
                if (event.originalTarget.nodeName == 'BODY' || event.originalTarget.nodeName == 'HTML') {
                    $('.foto_line').data('down', false);
                }                
            } catch (e) {}
        }
    });
	//=====================================
	


})