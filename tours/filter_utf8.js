/*	Работа с фильтрами

	Внимание!
	Должны быть установлены переменные: 
		form_name (имя формы в которой находится фильтр) и 
		iframe_name (имя iframe-а в который подгружаются данные)
		а также массив - filters.
		На страничке необходимо задать какие фильтры будут подгружаться и параметры, следующим образом - 
			filters['filter_name'] = new Array();			
			filters['filter_name']['cols'] = 3;	 - количество столбцов при выводе	
			filters['filter_name']['reloader'] = 1;	- должен ли инициировать перезагрузку
			filters['filter_name']['interdep'] = 1;	- зависят ли от этого фильтра остальные фильтры
			filters['filter_name']['active'] = 1;	- активность
		Т.е. подгружаются те фильтры, имена которых ключи массива filters
		
		Далее необходимо либо вызвать reload_filters(null,1);
		либо задать начальные данные
			filters['filter_name']['items'] = [[id,'name',checked],.....];
		и вызвать fill_divs();
		
	Также скрипт должен сохранять в hidden-ах все переменные из get, которые необходимо "таскать с собой"
		
	Для подгрузки/сброса фильтров вызывается reload_filters()
	При этом выдающий скрипт вызывается с get запросом содержащим: все select-ы из формы,
	все элементы формы с именами filter[]  (filter - ключ массива filters для которого filters[filter]['interdep']=1) 
	и вcе hidden-ы из div c id=div_hidden
	а также масив filters[] - c именами фильтров которые надо подгрузить. 
	
	На выходе из выдающего скрипта должна быть инициализация массива filters:
		window.parent.filters['filter']['items'] = [[id,'name',checked],.....];		
		filter - имя фильтра
		id - id элемента
		name - имя элемента
		checked - либо 1, либо не установлен
	и вызов window.parent.fill_divs();
	
	После отработки скриптов содержимое div-ов с id='div_'+filter подменяется соответствующей таблицей подгруженных элементов.		
*/

//var js_url = '';
var filter_not_reload = '';
var filter_not_reload_html = '';


// reload_filters(filter) - перезагружает фильтры, заполняет filter_not_reload  - фильтр, который не перезагружается

function hidden_div() {
	for (felem in filters) {

		if(typeof(filters[felem]) == "function"){ continue; }
		
	/*       
		по 0028920
		тут и далее добавлены проверки на наличие элемента массива "$family"
		if(felem == "$family") { continue; }
		Этот элемент добавляет MooTools, а в обработке фильтров он вызывает ошибки
	*/
		if(felem == "$family") { continue; }


			var div = document.getElementById('div_'+felem);	
			if (!div) { alert('Ошибка фильтра: Отсутствует div с id = div_'+felem) } 		
			
			div.innerHTML="";
		}
}


function reload_param(param) {
	var param = document.getElementById(param);
	
	if (param.value==0)
	{
		for (felem in filters) {	
			if(typeof(filters[felem]) == "function"){ continue; }
			if(felem == "$family") { continue; }
			var div = document.getElementById('div_'+felem);	
			if (!div) { alert('Ошибка фильтра: Отсутствует div с id = div_'+felem) } 		
			
			div.innerHTML="";
		}
	} else {
		reload_filters();
	}
}


function reload_filters(filter,reset) {
		
	if (filter) {
		filter_not_reload = filter;				
	} else {
		filter_not_reload = '';								
	}				
	
	var qs;
	
	if (reset) {
		qs = get_qs(1);
	} else {		
		qs = get_qs(0);
	}
		

	for (felem in filters) {
		if(typeof(filters[felem]) == "function"){ continue; }	
		if(felem == "$family") { continue; }
		hide_div(felem);					
	}				
	
	var url = js_url+qs;	
	
	var frame = document.getElementById(iframe_name);						
	
	if (!frame) { alert('Ошибка фильтра: Отсутствует frame с name = '+iframe_name) } 	
	
	//prompt(1,url);
		//alert(url);
	frame.src=url; 
}

// set_filter(filter) - устанавливает чекбоксы в фильтре в st (true, false)

function set_filter(filter,st) {
	
	var form = document.forms[form_name]
	if (!form) { alert('Ошибка фильтра: Отсутствует form с name = '+form_name) } 	
	
	// 
	// var inputs = form.item(filter+'_[]');
	var inputs = form[filter+'_[]'];
					
	if (inputs != null){
		if (inputs.length != null) {																		
			for (i = 0; i<inputs.length; i++){	
				inputs[i].checked = st;	
				cc(inputs[i].id);	
			}
		} else {
			inputs.checked = st;		
			cc(inputs.id);
		}		
	} 

}

function set_filter_find(filter,st) {
	
	var check = new Array();
	check = st;

	var form = window.opener.document.getElementById('fform');
	if (!form) { alert('Ошибка фильтра: Отсутствует form с name = fform') } 	

	var inputs = form[filter+'_[]'];
					
	if (inputs != null){
		if (inputs.length != null) {
			for (i = 0; i<inputs.length; i++){
				if (check[inputs[i].id]==1)
				{
					inputs[i].checked = true;
				}
				
				cc_find(inputs[i].id);	
			}
		} else {
			inputs.checked = st;		
			cc_find(inputs.id);
		}		
	} 

}

function cc_find(id) {
	var inp = window.opener.document.getElementById(id);
	var lbl = window.opener.document.getElementById('lb_'+id);		
	
	if (inp.checked) {lbl.style.color=t_chk_color; }
	else {lbl.style.color=""; }
}


// fill_divs() - т.е. устанавливает содержимое div-ов c id ='div_'+fltr где fltr - ключи глобального массива filters	
// если установлен filter_not_reload, то соответствующий не изменяется.
function fill_divs() {
	if (filter_not_reload.length==0) {
		
		for (filter in filters) {
			if(typeof(filters[filter]) == "function"){ continue; }					
			if(filter == "$family") { continue; }
			if (filters[filter]['active']) {
				fill_div(filter);				
			}		
			show_div(filter);	
		}	
	} else {
		
		for (filter in filters) {					
			if(typeof(filters[filter]) == "function"){ continue; }
			if(filter == "$family") { continue; }
			if ((filters[filter]['active'])&&(filter!=filter_not_reload)) {				
				fill_div(filter);	
			} 
			show_div(filter);				
		}
	}
}
	
	
// get_qs(reset) - формирует запрос для скрипта из всех checkbox-ов и hidden-ов формы заданной form_name (если не установлен reset, 
// если установлен, то только  из тех что hidden-ы),
// а также массив filters - из индексов массива filters

function get_qs(reset) {
	var qs = qs_div_filter;
	
	var form = document.forms[form_name];
	//var form = document.getElementsByName(form_name);
	if (!form) { alert('Ошибка фильтра: Отсутствует form с name = '+form_name) } 	
		
		
	if (reset) {		
	
		for (filter in filters) {										
			if(typeof(filters[filter]) == "function"){ continue; }	
			if(filter == "$family") { continue; }
			if (filters[filter]['active']) {												
				//пихаем в qs ключи массива filters
				if (filter!=filter_not_reload) {
					qs += 'filters[]='+filter+'&';						
					// для инициализации фильтров из строки запроса
					qs += filter + '=' + get_qs_value(filter) + '&';
				}
			}
		}
	
	} else {

		
		for (filter in filters) {			
			if(typeof(filters[filter]) == "function"){ continue; }
			if(filter == "$family") { continue; }
			if (filters[filter]['active']) {								
				
				//пихаем в qs ключи массива filters
				if (filter!=filter_not_reload) {
					qs += 'filters[]='+filter+'&';	
				}	
				
				if (filters[filter]['interdep']) {	
					qs += 'interdep[]='+filter+'&';		
				}									
							
				
				//var inputs = form.item(filter+'_[]');
				var inputs = form[filter+'_[]'];
				var values = '';
				
				//пихаем в qs все из filters		
				if (inputs != null){
					if (inputs.length != null) {				
						for (i = 0; i<inputs.length; i++){	
							if (inputs[i].checked) {						
								values += inputs[i].value+'_';
							}								
						}		
						values = values.substr(0,values.length-1);						
					} else {
						if (inputs.checked) {						
							values = inputs.value;
						}	
					}	
					qs += filter+'='+values+'&';				
				} 				
			}
		}
	}
	
	//пихаем в qs все select-ы формы
	var selects = form.getElementsByTagName('select');
	
	if (selects != null) {
		if (selects.length != null) {
			for (i=0; i<selects.length; i++){														
				qs += selects[i].name+'='+selects[i].value+'&';				
			}		
		} else {
			qs += selects.name+'='+selects.value+'&';				
		}
	}	
	
	qs += 'page_id='+page_id+'&';	
	
	
	//пихаем в qs все hidden-ы из div_hidden
	var hdiv = document.getElementById('div_hidden');
	
	if (hdiv) {		
		var hiddens = hdiv.getElementsByTagName('input');
		
		if (hiddens != null) {
			if (hiddens.length != null) {
				for (i = 0; i<hiddens.length; i++){														
					qs += hiddens[i].name+'='+hiddens[i].value+'&';				
				}		
			} else {
				qs += hiddens.name+'='+hiddens.value+'&';				
			}
		}
	}	
				
	
	qs = qs.substr(0,qs.length-1);
	
	//alert(qs);	
	return qs;
}


		
// get_qs_value(name)  возвращает значение параметра, который был передан в командной строке. Ищет первое значение.
// Если запрошенный параметр отсутствует возвращает пустую строку
function get_qs_value(name)
{
    var qs = location.search;
    var pos = qs.indexOf('&' + name + '=');
    if ( pos == -1 )
        pos = qs.indexOf('?' + name + '=');
    if ( pos == -1 )
        return '';
    pos += name.length + 2;
    var pos2 = qs.indexOf('&', pos);
    if ( pos2 == -1 )
        pos2 = qs.length;
    return qs.substr(pos, pos2 - pos);
}
		
	
// format_table(filter,elements,selected,cols) - формирует и возвращает табличку с элементами фильтра из массива elements 
// по шаблону t_o_table, t_o_tr, t_o_td, t_item, t_separator, t_c_td, t_c_tr, t_c_table		
// filter - имя фильтра (просто пихается в шаблон)
// elements - массив вида [[id,'name',checked],....]
// cols - количество столбцов в таблице (4 по умолчанию)
// reloader - будет ли использоваться шаблон t_item или t_item_r
// Если количество элементов массива меньше чем (количество столбцов)*2, то в результате количество столбцов
// может получиться меньше чем требуемое		
				
function format_table(filter,elements,table) {						

	//-----------------
	var need_tr = true;
	if(filter=='ac') {
		need_tr = false;
	}
	//-----------------
	
	var cols = table['cols'];
	var reloader = table['reloader'];
	
	if (!cols) { cols=4; }	
	
	var el_in_col = Math.ceil(elements.length/cols);						
	
	if (elements.length==0)
		return t_not_found;
	
	var HTML = '';						
	HTML += t_o_table;
	HTML += t_o_tr;
				
					
	//#29469
	// при округлении к ближайшему большему ширина последнего столбца всегда меньше, 
	// т.к. в сумме выходит больше 100% Заметно только при большом числе столбцов
	// добавил -0.5, чтобы было математическое округление
					
	var width = Math.ceil(100/Math.ceil(elements.length/el_in_col) - 0.5);				
	
	
	var re = /\[width\]/g;
	var o_td = t_o_td.replace(re,width);
											
	re = /\[filter\]/g;
	
	if (reloader>0) {
		var pr_item	= t_item_r.replace(re,filter);
	} else {	
		var pr_item	= t_item.replace(re,filter);		
	}
							
	
	var col_elem = 0;
	HTML += o_td;				
	
	
	var rech = /\[checked\]/g;
	var recl = /\[color\]/g;
	var renm = /\[name\]/g;		
	var reid = /\[id\]/g;		
			
					
	for (i=0; i<elements.length; i++) {				
	
		if (col_elem==el_in_col) {						
			col_elem = 0;
			
			HTML += t_c_td;		
			//HTML += t_separator;	
			
			if(need_tr) {
				HTML += t_c_tr;
				HTML += t_o_tr;
			}
			HTML += o_td;									
		}
		
		col_elem++;									
		
		var elem = elements[i];			
									
		var r_item = pr_item.replace(reid,elem[0]);			
		r_item = r_item.replace(renm,elem[1]);			

		if (elem[2]) {						
			r_item = r_item.replace(rech,'checked');
			r_item = r_item.replace(recl,t_chk_color);
		} else {				
			r_item = r_item.replace(rech,'');
			r_item = r_item.replace(recl,'');
		}			
														
		HTML += r_item;							
	}
	
	
	HTML += t_c_td;		

	HTML += t_c_tr;
	HTML += t_o_table;
			
	return HTML;			
}
	
	
	
// fill_div(filter) - заменяет содержимое div-a c id='div_'+filter	
// на табличку составленную с помощью format_table с элементами из filters[filter]		

function fill_div(filter) {						
	var HTML;
	
	if (filters[filter]['ft_ex'])
		HTML = format_table_ex(filter,filters[filter]['items'],filters[filter]['table']);
	else {
		if (filters[filter]['ft_star'])
			HTML = format_table_ex(filter,filters[filter]['items'],filters[filter]['table']);
		else 
			HTML = format_table(filter,filters[filter]['items'],filters[filter]['table']);
	}
			
		
	set_div(filter,HTML);		
}
	
	
//cc(id) устанавливает цвет label к сheckbox-у с id в зависомости от того выбран он или нет
// id label-а - 'lb_'+id

function cc(id) {
	//alert(id);
	var inp = document.getElementById(id);
	var lbl = document.getElementById('lb_'+id);		
	
	//alert(inp.checked);
	
	if (inp.checked) {lbl.style.color=t_chk_color; }
	else {lbl.style.color=""; }
	
	//alert(lbl.style.color);
}


// get_div(filter) возвращает содержимое  div-a c id='div_'+filter

function get_div(filter) {
	var div = document.getElementById('div_'+filter);	
	if (!div) { alert('Ошибка фильтра: Отсутствует div с id = div_'+filter) } 
	return div.innerHTML;	
}

//fill_div(filter) - устанавливает содержимое div-a c id='div_'+filter
function set_div(filter,content) {
	var div = document.getElementById('div_'+filter);	
	if (!div) { alert('Ошибка фильтра: Отсутствует div с id = div_'+filter) } 
	div.innerHTML = content;	
}

//hide_div(filter) - скрывает div c id='div_'+filter
function hide_div(filter) {
	var div = document.getElementById('div_'+filter);
	var div_load = document.getElementById('div_load_'+filter);
	if (!div) { alert('Ошибка фильтра: Отсутствует div с id = div_'+filter) }

	div.style.visibility="hidden";

	if (typeof(div_load) != 'undefined' && div_load != null) {
		div_load.style.display="";	
	}

/*	
	var ldiv = document.getElementById('div_load');
	
	if (!ldiv) 
		div.style.visibility="hidden";	
	else { 
		if (filter==filter_not_reload) 
			filter_not_reload_html = div.innerHTML;
					
		div.innerHTML = ldiv.innerHTML;
	}
*/
}

//show_div(filter) - показывает div-a c id='div_'+filter
function show_div(filter) {
	var div = document.getElementById('div_'+filter);
	var div_load = document.getElementById('div_load_'+filter);
	if (!div) { alert('Ошибка фильтра: Отсутствует div с id = div_'+filter) } 

/*	
	var ldiv = document.getElementById('div_load');
	if (ldiv and filter==filter_not_reload)
		div.innerHTML = filter_not_reload_html;
*/
	
	div.style.visibility="visible";
	if (typeof(div_load) != 'undefined' && div_load != null) {
		div_load.style.display="none";	
	}
}


function prepare_submit(not_reset) {
	
	var form = document.forms[form_name]
		
	
	for (filter in filters) {					
		if(typeof(filters[filter]) == "function"){ continue; }	
		if(filter == "$family") { continue; }
		if (filters[filter]['active']) {												
						
			// item не работает в каком-то из браузеров....
			//var ft = form.item(filter);						
			var ft = form[filter];						
			
			if (ft==null) {
				alert('Ошибка фильтра: отсутствует hidden с name = '+filter);
				return false;	
			}													
			
			// item не работает в каком-то из браузеров....
			//var inputs = form.item(filter+'_[]'); 
			var inputs = form[filter+'_[]']; 
			
			var values = '';
				
			if (inputs != null){
				if (inputs.length != null) {				
					for (i = 0; i<inputs.length; i++){	
						if (inputs[i].checked) {
							if (!not_reset) inputs[i].checked = false;					
							values += inputs[i].value+'_';
						}								
					}		
					values = values.substr(0,values.length-1);						
				} else {
					if (inputs.checked) {	
						if (!not_reset) inputs.checked = false;							
						values = inputs.value;
					}	
				}	
				ft.value = values;				
			} 										
		}
	}

	pp_submit();

	return true;	
}


function pp_submit() {
	var form = document.forms[form_name];

	var inputs = form['f_m[]']; 
	var values = '';
	var ft = form['f_m']; 

	if (inputs != null){
		if (inputs.length != null) {				
			for (i = 0; i<inputs.length; i++){	
				if (inputs[i].checked) {
					values += inputs[i].value+'_';
					inputs[i].disabled = true;
				}
			}		
			values = values.substr(0,values.length-1);						
		} else {
			if (inputs.checked) {	
				values = inputs.value;
				inputs.disabled = true;
			}
		}	
	}
	
	if (values != '')
	{
		ft.value = values;
	}

	return true;
}


function sel_submit_str(all) {
	
	var form = document.forms[form_name]
		
		if (filters['exc']['active']) {												
						
			var ft = form['exc'];						
			
			if (ft==null) {
				alert('Ошибка фильтра: отсутствует hidden с name = exc');
				return false;	
			}													
			
			var inputs = form['exc_[]']; 
			
			var values = '';
				
			if (inputs != null){
				if (inputs.length != null) {				
					for (i = 0; i<inputs.length; i++){
						if (!all)
						{
							if (inputs[i].checked) {
								values += inputs[i].value+'_';
							}
						} else {
							values += inputs[i].value+'_';
						}
							
					}		
					values = values.substr(0,values.length-1);						
				} else {
					if (inputs.checked) {	
						values = inputs.value;
					}	
				}	
				//ft.value = values;				
			}
		}
	return "?all_exc="+values;
}
