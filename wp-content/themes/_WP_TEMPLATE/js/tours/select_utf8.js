var SelectTools = new Object();

SelectTools.clear = function(select){
	select = this._find(select);
	while(select.options.length)
		select.remove(0);
}

SelectTools.clear_div = function(div){
	div = this._find('div_'+div);
	div.innerHTML = '';
}


SelectTools._find = function(select){
	if(typeof(select) == 'string')
		return document.getElementById(select);
	else
		return select;
}

SelectTools.addOption = function(selectElement, optionValue, optionText, selected)
{
	var oOption = document.createElement("OPTION");

	oOption.text = optionText;
	oOption.value = optionValue;
	selectElement.options.add(oOption);

	oOption.selected = selected;

	return oOption;
}

SelectTools.clearAdd = function(select, value, text){
	select = this._find(select);
	
	this.clear(select);
	this.addOption(select, value, text, true);
}

SelectTools.fill = function(select, elements, selectedElement){
	select = this._find(select);
	for(i=0;i<elements.length;i++){
		SelectTools.addOption(select, elements[i][0], elements[i][1], selectedElement == elements[i][0]);
	}
}


SelectTools.fill_div = function(div, elements, format, values, cols){
	name = div;
	colsm = parseInt(cols-1);

	div = this._find('div_'+div);
	html = '<table border="0" cellpadding="0" cellspacing="3" width="100%" class="filter"> ';
	if(format) {
		for(i=0;i<elements.length;i++){
			ost = parseInt(i%cols);
			if (ost==0) {
				html = html + '<tr> ';
				td = '';
			}
			value = '';
			if (defined(values[elements[i][0]]))
				value = values[elements[i][0]];
			td = td + '<td width="1%"><input type="checkbox" name="'+name+'_[]" id="'+name+'_'+elements[i][0]+'" '+value+' value="'+elements[i][0]+'"></td>';
			td = td + '<td nowrap><label for="'+name+'_'+elements[i][0]+'">'+elements[i][1]+'</label></td>';

			if (ost==colsm || i==parseInt(elements.length-1)) {
				if (i==parseInt(elements.length-1) && ost<colsm) {
					for (j=ost;j<colsm;j++) {
						td = td + '<td>&nbsp;</td><td>&nbsp;</td>';
					}
				}
				html = html + td + '</tr>';
			}
		}
	} else {
		tr = '';
		for(i=0;i<elements.length;i++){
			value = '';
			if (defined(values[elements[i][0]]))
				value = values[elements[i][0]];

			tr = tr + '<tr><td width="1%"><input type="checkbox" name="'+name+'_[]" id="'+name+'_'+elements[i][0]+'" '+value+' value="'+elements[i][0]+'"></td>';
			tr = tr + '<td><label for="'+name+'_'+elements[i][0]+'">'+elements[i][1]+'</label></td>';
			tr = tr + '</tr>';
		}
		html = html + tr;
	}
	html = html + '</table>';

	div.innerHTML = html;
}


SelectTools.makeData = function(data, value, text){
	var out = new Array();
	if(typeof(value) == 'function' && typeof(text) == 'function'){
		for(i=0;i<data.length;i++){
			out[i] = [value(data[i]), text(data[i])];
		}
	}
	else if(typeof(value) == 'function'){
		for(i=0;i<data.length;i++){
			out[i] = [value(data[i]), data[i][text]];
		}
	}
	else if(typeof(text) == 'function'){
		for(i=0;i<data.length;i++){
			out[i] = [data[i][value], text(data[i])];
		}
	}
	else{
		for(i=0;i<data.length;i++){
			out[i] = [data[i][value], data[i][text]];
		}
	}
	
	return out;
}


SelectTools.makedopData = function(data, value, text){
	var out = new Array();
	for(i=0;i<data.length;i++){
		out[data[i][value]] = data[i][text];
	}
	return out;
}