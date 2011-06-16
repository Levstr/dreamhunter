
/*function getE(id){
	return document.getElementById(id);
}
function defined(x){
	return typeof(x) != 'undefined' && x != null;
}*/

var co_def = '12'; 
var select_filter_url = "http://www.webclient.touradmin.ru/b/main/18200/find_tour/get_data/";
function init_form() {
	// страны по городу вылета
	dynamicList({
		'element' : getE('select_co'),
		'param' : getE('select_ct'),
		'url' : select_filter_url+"?country=1&ct=",
		'value' :co_def,
		'defaultName' : ' ',
		'nodef' : true,
		'notify' : function() {
		   //выделяем первый
		    def = co_def;
		    opt = getE('select_co').options;
		    sel = opt[getE('select_co').selectedIndex];
		    key = 0;
		    for (var i = 0; i < opt.length; i++) {
			if(opt[i].value == def){
			    key = i;
			    break;
			}
		    } 
		    opt[key].selected = true;
		 //   window.setTimeout("loadRoomsize()", 100);

		    //reload_filters(0,true);
		    //hidden_div();
		      
		},
		'loadtitle' : '...'
	});
}