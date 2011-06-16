DynamicList = new Object();
DynamicList.cache = new Object();



/**
 * options:
 * - element элемент, к которому всё цепляется
 * - url это ссылка из которой будут браться данные
 * - emptyName = "Пусто" то, что будет показываться для пустого списка
 * - emptyValue = 0
 * - defaultName = "Выберите" то, что будет показываться первым пунктом, если нет - не показывается
 * - defaultValue = 0]
 * - intParam = true - считать ли параметр целым числом
 * - intValue = true - --//--
 * - param то, что используется в качестве параметра при получении списка, 
 * 			может быть: функция, возвращающая текущее значение параметра, объект имеющий .value
 *			или просто значение (но это как-то тупо)
 * - value значение по-умолчанию, очень удобно использовать для задания предвыбранного значения
 * - callback(data) = [заполнение списка от] функция, используемая для получения данных
 * - notify() функция, вызываемая после заполнения всего
 * - noHandler = false не устанавливать слушателя (по-умолчанию ставится)
 **/
function dynamicList(options){
	if(!defined(options.element) || typeof options.element != 'object'){
		alert('Не задан элемент');
		return false;
	}	
	
	var element = options.element;
	var dop_data = new Array(); //дополнительные данные, передаются в name2
							//в данном случае передаю количество детей
	
	if(!defined(options.url)){
		alert('Не задан URL списка');
		return false;
	}
	
	if(!defined(options.emptyName))	options.emptyName = 'Пусто';
	if(!defined(options.emptyValue)) options.emptyValue = 0;
	
	if(!defined(options.defaultName)){
//		if(element.options[0])
//			options.defaultName = element.options[0].innerHTML;
//		else
			options.defaultName = 'Выберите';
	}
	
	if(!defined(options.defaultValue)){
//		if(element.options[0])
//			options.defaultValue = element.options[0].value;
//		else
			options.defaultValue = 0;
	}


	if(!defined(options.checkbox)){
		options.checkbox = false;
	}
	if(!defined(options.format)){
		options.format = false;
	}
	if(!defined(options.div_value)){
		options.div_value = new Array();
	}
	if(!defined(options.nodef)){
		options.nodef = false;
	}
	if(!defined(options.loadtitle)){
		options.loadtitle = 'Загрузка данных...';
	}
	
	element.get_def_value = function(){
		if(defined(options.value))
			return options.value;
		else 
			return false;
	}

	element.get_data = function(){
		return fdata;
	}

	element.get_dop_data = function(){
		return dop_data;
	}


	if(!defined(options.intParam)) options.intParam = true;
	if(!defined(options.intValue)) options.intValue = true;
	
	element.updateList = function(param){
	    
		var value = this.value?this.value:0;
		var select = this;
		var div = this;
		
		var paramlistsetvalues = false;

		if(!defined(options.paramlist)){
			options.paramlist = new Array();
			paramlistvalues = new Array();
		}
		
		if(options.intValue && !parseInt(value) 
			|| !options.intValue && value.toString().length == 0)
		{
			value = options.value;
		}
		
		if(options.intValue)
			value = parseInt(value);
		
		
	
		if(!defined(param)){
			if(defined(options.param)){
				if(typeof options.param == 'function')
					param = options.param();
				else if(typeof options.param == 'object')
					param = options.param.value;
				else
					param = options.param;
			}
			else{
				if(defined(options.paramlist)) {
					paramlistvalues = new Array();
					for(var i=0;i<options.paramlist.length;i++){
						paramlistvalues[paramlistvalues.length] = options.paramlist[i];
						paramlistvalues[paramlistvalues.length] = getE(options.paramlist[i]).value;
						if (parseInt(getE(options.paramlist[i]).value)>0) {
							paramlistsetvalues = true;
						}
						
					}
				} else {
					alert("Nor param nor options.param is given");
					return false;
				}
			}
		}		
		
		if(options.intParam)
			param = parseInt(param);
		
		if(!options.checkbox) {
			
			if(!param && !paramlistsetvalues){
				SelectTools.clear(this);
				SelectTools.addOption(this, options.defaultValue, options.defaultName);
				if(typeof this.dynamicListNotifier == 'function')
					this.dynamicListNotifier();
			}
			else{
				//options.defaultName = '--';
				if (param) {
					var url = options.url + param;
				} else if (paramlistsetvalues) {
				
					var url = options.url;
					
					qs = '';
					for(var i=0;i<options.paramlist.length;i++) {
						qs = qs+options.paramlist[i]+'='+getE(options.paramlist[i]).value+'&';
					}
						
					url=url+qs;					
				} else {
					var url = options.url;
				}

				var callback = (typeof(options.callback) == 'function')?options.callback : 
					function(data){
						SelectTools.clear(select);
						if(!options.nodef)
							SelectTools.addOption(select, options.defaultValue, options.defaultName);

						fdata = SelectTools.makeData(data, 'id', 'name');

						SelectTools.fill(select, fdata, value);
						
						if(defined(options.dop_data)) {
							dop_data = SelectTools.makedopData(data, 'id', 'name2');
						}

						if(typeof(options.notify) == 'function')
							options.notify();
							
						if(typeof(select.dynamicListNotifier) == 'function')
								select.dynamicListNotifier();
					};
					
				SelectTools.clear(select);

				SelectTools.addOption(select, 0, options.loadtitle);
				
				if(options.noCache || typeof(DynamicList.cache[url]) == 'undefined'){
					Json.call(url, function(data){ 
							DynamicList.cache[url] = data; 
							callback(data); 
						});
				}
				else{
					callback(DynamicList.cache[url]);
				}

			}
		} else {
			if (param) {
				var url = options.url + param;
			} else if (paramlistsetvalues) {
				var url = options.url;
				qs = '';
				for(var i=0;i<options.paramlist.length;i++) {
					qs = qs+options.paramlist[i]+'='+getE(options.paramlist[i]).value+'&';
				}
				url=url+qs;					
			} else {
				var url = options.url;
			}
			
			var callback = (typeof(options.callback) == 'function')?options.callback : 
					function(data){
						SelectTools.clear_div(options.name);

						fdata = SelectTools.makeData(data, 'id', 'name');
						
						SelectTools.fill_div(options.name, fdata, options.format, options.div_value);
						
						if(typeof(options.notify) == 'function')
							options.notify();
					};

			SelectTools.clear_div(options.name);
			Json.call(url, function(data){ 
				DynamicList.cache[url] = data; 
				callback(data); 
			});
		}



	}
	
	

if(!options.noHandler){
		if (typeof options.param == 'object'){
			var paramObj = options.param;
			if(!paramObj.dynamicListTargets) {
				paramObj.dynamicListTargets = new Array();
			}
			paramObj.dynamicListTargets[paramObj.dynamicListTargets.length] = element;
			
			if(!paramObj.dynamicListListener){
				paramObj.dynamicListListener = true;
				paramObj.dynamicListNotifier = function(){
					for(var i=0;i<paramObj.dynamicListTargets.length;i++){
						paramObj.dynamicListTargets[i].updateList();
					}
				}

  				addListener(paramObj, 'change', paramObj.dynamicListNotifier);
			}
		} else if (options.paramlist.length) {
			
			for(var j=0;j<options.paramlist.length;j++){
				var paramObj = getE(options.paramlist[j]);
				if(!paramObj.dynamicListTargets) {
					paramObj.dynamicListTargets = new Array();
				}

				paramObj.dynamicListTargets[paramObj.dynamicListTargets.length] = element;
				
                /*     
				if(!paramObj.dynamicListListener){
					paramObj.dynamicListListener = true;
					
					paramObj.dynamicListNotifier = function(){
						if(defined(paramObj.dynamicListTargets[0])) paramObj.dynamicListTargets[0].updateList();
						if(defined(paramObj.dynamicListTargets[1])) paramObj.dynamicListTargets[1].updateList();
						if(defined(paramObj.dynamicListTargets[2])) paramObj.dynamicListTargets[2].updateList();
					}
					 addListener(paramObj, 'change', paramObj.dynamicListNotifier);
				}
				*/
			}
		}
	}	

	
}



function prepareValue_cb(Values) {
	var rez = new Array();

	arr = Values.split('_');
	if (arr.length>1)
	{
		for(var i=1;i<=arr.length;i++) {
			rez[arr[i]] = 'checked';
		}
	} else {
		rez[arr] = 'checked';
	}
	
	return rez;
}


/*

function dynamicList(options){
	if(!defined(options.element) || typeof options.element != 'object'){
		alert('Не задан элемент');
		return false;
	}	
	
	var element = options.element;

	if(!defined(options.url)){
		alert('Не задан URL списка');
		return false;
	}
	
	

	if(!defined(options.emptyName))	options.emptyName = 'Пусто';
	if(!defined(options.emptyValue)) options.emptyValue = 0;
	
	if(!defined(options.defaultName)){
		options.defaultName = 'Выберите';
	}
	
	if(!defined(options.defaultValue)){
		options.defaultValue = 0;
	}

	if(!defined(options.intParam)) options.intParam = true;
	if(!defined(options.intValue)) options.intValue = true;

	element.getURL = function(){
		alert(options.url);
		return true;
	}
	
	element.updateList = function(param){
		var value = this.value?this.value:0;
		var select = this;
		var paramlistsetvalues = false;

		if(!defined(options.paramlist)){
			options.paramlist = new Array();
			paramlistvalues = new Array();
		}

		if(options.intValue && !parseInt(value) 
			|| !options.intValue && value.toString().length == 0)
		{
			value = options.value;
		}
		
		if(options.intValue)
			value = parseInt(value);


		if(!defined(param)){
			if(defined(options.param)){
				if(typeof options.param == 'function')
					param = options.param();
				else if(typeof options.param == 'object')
					param = options.param.value;
				else
					param = options.param;
			}
			else{
				if(defined(options.paramlist)) {
					paramlistvalues = new Array();
					for(i=0;i<options.paramlist.length;i++){
						paramlistvalues[paramlistvalues.length] = options.paramlist[i];
						paramlistvalues[paramlistvalues.length] = getE(options.paramlist[i]).value;
						if (parseInt(getE(options.paramlist[i]).value)>0) {
							paramlistsetvalues = true;
						}
					}
				} else {
					alert("Nor param nor options.param is given");
					return false;
				}
			}
		}
		
		
		if(options.intParam)
			param = parseInt(param);
		
		if(!param && !paramlistsetvalues){
			//options.defaultName = options.defaultName;
			SelectTools.clear(this);
			SelectTools.addOption(this, options.defaultValue, options.defaultName);
			if(typeof this.dynamicListNotifier == 'function')
				this.dynamicListNotifier();
		}
		else{
			//options.defaultName = '--';
			if (param) {
				var url = options.url + param;
			} else if (paramlistsetvalues) {
				var url = get_qs(options.url,paramlistvalues);
			} else {
				var url = options.url;
			}
			
			var callback = (typeof(options.callback) == 'function')?options.callback : 
				function(data){
					SelectTools.clear(select);
					SelectTools.addOption(select, options.defaultValue, options.defaultName);
					SelectTools.fill(select, SelectTools.makeData(data, 'id', 'name'), value);
					
					if(typeof(options.notify) == 'function')
						options.notify();
						
					if(typeof(select.dynamicListNotifier) == 'function')
						select.dynamicListNotifier();
				};
				
			SelectTools.clear(select);
			//SelectTools.addOption(select, 0, 'Data load...');
			SelectTools.addOption(select, 0, 'Загрузка данных...');
			
			if(options.noCache || typeof(DynamicList.cache[url]) == 'undefined'){
				Json.call(url, function(data){ 
						DynamicList.cache[url] = data; 
						callback(data); 
					});
			}
			else{
				callback(DynamicList.cache[url]);
			}
		}
	}
	

	if(!options.noHandler){
		if (typeof options.param == 'object'){
			var paramObj = options.param;
			if(!paramObj.dynamicListTargets) {
				paramObj.dynamicListTargets = new Array();
			}
			paramObj.dynamicListTargets[paramObj.dynamicListTargets.length] = element;
			
			if(!paramObj.dynamicListListener){
				paramObj.dynamicListListener = true;
				paramObj.dynamicListNotifier = function(){
					for(i=0;i<paramObj.dynamicListTargets.length;i++){
						paramObj.dynamicListTargets[i].updateList();
					}
				}

  				addListener(paramObj, 'change', paramObj.dynamicListNotifier);
			}
		} else if (options.paramlist.length) {
			
			for(j=0;j<options.paramlist.length;j++){
				var paramObj = getE(options.paramlist[j]);
				if(!paramObj.dynamicListTargets) {
					paramObj.dynamicListTargets = new Array();
				}

				paramObj.dynamicListTargets[paramObj.dynamicListTargets.length] = element;
				
                     
				if(!paramObj.dynamicListListener){
					paramObj.dynamicListListener = true;
					
					paramObj.dynamicListNotifier = function(){
						if(defined(paramObj.dynamicListTargets[0])) paramObj.dynamicListTargets[0].updateList();
						if(defined(paramObj.dynamicListTargets[1])) paramObj.dynamicListTargets[1].updateList();
						if(defined(paramObj.dynamicListTargets[2])) paramObj.dynamicListTargets[2].updateList();
					}
					 addListener(paramObj, 'change', paramObj.dynamicListNotifier);
				}
				
			}
		}
	}	
}
*/