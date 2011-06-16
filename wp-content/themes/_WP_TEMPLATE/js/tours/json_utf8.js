var Json = new Object();
Json.callbacks = new Array();
Json.trace = false;
Json._debug = false;
Json.cache = false;

Json.call = function(url, callback){
	var i = this.callbacks.length;
	this.callbacks[i] = callback;
	
	if(url.indexOf('?') != -1)
		url += '&';
	else
		url += '?';
		
	url += '__json_call='+i;

	if(this.trace)
		prompt('', url);
	var script = document.createElement('SCRIPT');
	script.src = url;
	if (IE='\v'=='v') {
		document.getElementsByTagName('head')[0].appendChild(script);
	} else {
		document.getElementsByTagName('body')[0].appendChild(script);
	}
}

Json.done = function(num, result){
	if(this._debug)
		alert(this.dump(result, ''));
		
	this.callbacks[num](result);
}

Json.dump = function(v, t){
	if(typeof(t) == 'undefined') 
		t = '';
	if(typeof(v) == 'object'){
		var out = t + '[\n';
		
		for(var i in v){
			out += t + '  ' + i + ': ' + this.dump(v[i], t + '  ') + '\n';
		}
		
		out += t + ']';
		
		return out;
	}
	else if(typeof(v) == 'array'){
		var out = t + '[';
		
		for(var i in v){
			out += t + '  ' + i + ': ' + this.dump(v[i], t + '  ');
		}
		
		out += t + ']';
		
		return out;
	}
	else{
		return v;
	}
}

Json.debug = function(debug){
	this._debug = (typeof(debug) != 'undefined')?debug:true;
}