
function getE(id){
	return document.getElementById(id);
}


function room_children(ch1,ch2,room) {														
	var ch1 = document.getElementById(ch1);
	var ch2 = document.getElementById(ch2);
	var room = document.getElementById(room);
	
	switch (rs[room.options[room.selectedIndex].value]) {
		case '2': ch1.disabled=false; ch2.disabled=false; break;
		case '1': ch1.disabled=false; ch2.disabled=true; ch2.value=""; break;    
		default: ch1.disabled=true; ch2.disabled=true; ch1.value=ch2.value=""; break;        
	}
}


function freset() {
	var ch1 = document.getElementById('ch1');
	var ch2 = document.getElementById('ch2');
	ch1.disabled=true; 
	ch2.disabled=true; 
	ch1.value=ch2.value="";
}


function change_value(elem,oper) {
	var element = document.getElementById(elem);
	if (oper == 'inc') {
		element.value++;			
	} 
	else  {
		if (element.value > 0) element.value--;
		if (element.value == 0) element.value="";			
	}
}












function defined(x){
	return typeof(x) != 'undefined' && x != null;
}

function addListener(element, event, func){
	if(element.attachEvent)
		element.attachEvent('on' + event, function(){ func(window.event) });
	else if(element.addEventListener)
		element.addEventListener(event, func, false);
}

function ShowWin(url,x,y,name,isscrollbars) {
	cx=screen.width / 2 - (x / 2);
	cy=(screen.height/2-(y/2));
    
    isscrollbars=(isscrollbars=="no")?"no":"yes";
	window.open(url,name,"toolbar=no,status=no,directories=no,menubar=no,resizable=yes,width="+x+",height="+y+",scrollbars="+isscrollbars+",top="+cy+",left="+cx);
}



function MOver(MySrc,MyColor) { MySrc.style.cursor="auto"; MySrc.bgColor=MyColor; }
function MOut (MySrc,MyColor) { MySrc.style.cursor="auto"; MySrc.bgColor=MyColor; }



function Selecter(Form, EName, S) {
    var f=Form;
    for ( i=0; i<f.length; i++ ) {
        if (f.elements[i].name==EName) {
            f.elements[i].checked=(S==1)?true:false;
        }
   }        
}


 

function ShowDivWindow(Name, Text, Action) {
    leerId=Name;
    LoadingHTML=Text;

	cx=(document.body.clientWidth/2)-(200/2);
	cy=(document.body.clientHeight/2)-(100/2);


/*    
	cx=screen.width/2;
	cy=screen.height/2;
*/
    
    if ((navigator.userAgent.indexOf("MSIE 5.5")==-1) && (navigator.userAgent.indexOf("MSIE 6")==-1)) {
//      cx += 90;
      cy-= 117;
    }

   
    if (document.all) {
        var leerElem = document.all[leerId];
        leerElem.innerHTML = LoadingHTML;
        leerElem.style.left = cx;
        leerElem.style.top = cy;
        leerElem.style.visibility = ((Action=="show")?"visible":"hidden");
    }
    else if (document.getElementById) {
        var leerElem = document.getElementById(leerId);
        leerElem.innerHTML = LoadingHTML;
        leerElem.style.left = cx;
        leerElem.style.top = cy;
        leerElem.style.visibility = ((Action=="show")?"visible":"hidden");
    }
    else if (document.layers) {
        document.layers[leerId].left = cx;
        document.layers[leerId].top = cy;
        document.layers[leerId].document.open();
        document.layers[leerId].document.write(LoadingHTML);
        document.layers[leerId].document.close();
        document.layers[leerId].visibility = ((Action=="show")?"show":"hide");
    }
}


/*
** Параметры:
** str - путь
** addarray - переменные, которые надо добавить в путь (array('name1','value1','name2','value2',...))
** removearray - перменные, которые необходимо убрать из пути (array('name1','name2',...))
*/
function my_get_qs(str,addarray,removearray) {
	farr = str.split('?');

	var vars = new Array();
	var varval = new Array();

	if(defined(farr[1])) {
		arr = farr[1].split('&');

		for(i=0;i<arr.length;i++) {
			variable = arr[i].split('=');
			vars[i] = variable[0];
			varval[i] = variable[1];
		}
		if(defined(removearray)) {
			if(removearray.length>0) {
				for(i=0;i<vars.length;i++) {
					for(j=0;j<removearray.length;j++) {
						if(vars[i]==removearray[j]) {
							vars[i] = false;
							varval[i] = false;
						}
					}
				}
			}
		}
	}

	if(defined(addarray)) {
		if(addarray.length>0) {
			for(j=0;j<addarray.length;j++) {
				find = false;
				for(i=0;i<vars.length;i++) {
					if(vars[i]==addarray[j]) {
						varval[i] = addarray[j+1];
						find = true;
					}
				}
				if(!find) {
					vars[vars.length] = addarray[j];
					varval[varval.length] = addarray[j+1];
				}
				j++;
			}
		}
	}

	qs = '';
	for(i=0;i<vars.length;i++) {
		if(vars[i]!=false)
			qs = qs+vars[i]+'='+varval[i]+'&';
	}
	
	return farr[0]+'?'+qs;
}



function getAbsolutePos(el) { 
	var SL = 0, ST = 0; 
	var is_div = /^div$/i.test(el.tagName); 
	if (is_div && el.scrollLeft) SL = el.scrollLeft; 
	if (is_div && el.scrollTop) ST = el.scrollTop; 
	var r = { x: el.offsetLeft - SL, y: el.offsetTop - ST }; 
	if (el.offsetParent) { 
		var tmp = this.getAbsolutePos(el.offsetParent); 
		r.x += tmp.x; 
		r.y += tmp.y; 
	} 
	return r; 
}

function showDivByEl(div,el,w,h) {
	pos = getAbsolutePos(getE(el));
	getE(div).style.top = parseInt(pos.y+h);
	getE(div).style.left = parseInt(pos.x+w);
	getE(div).style.display='block';
	return false;
}

function cb2str(name) {
	var inputs = document.body.getElementsByTagName('input');
	var values = '';
	
	var data = new Array();
	var l=0;
	if (inputs != null){
		if (inputs.length != null) {
			for (i = 0; i<inputs.length; i++){
				if (inputs[i].name==name+'[]') {
					data[l] = inputs[i];
					l++;
				}
			}
		}
	}
	
	if (data != null){
		if (data.length != null) {
			for (i = 0; i<data.length; i++){	
				if (data[i].checked) {
					values += data[i].value+'_';
				}								
			}		
			values = values.substr(0,values.length-1);						
		} else {
			if (data.checked) {							
				values = data.value;
			}	
		}
	}
	return values;
}

function rawurlencode(str) {
	var url = new String(str);
	url = url.replace(/([^a-z0-9_\-\.])/gi, function (str, p1, offset, s) {	var hex = new Array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F"); val = p1.charCodeAt(0); s = ""; while (val>=16) { s += hex[val%16]; val = Math.floor(val/16); } s += hex[val]; N = s.length; for (i=0,t="";i<N;i++) t += s.substring(N-i-1,N-i); return "%"+t; });
	return url;
}

function rawurldecode(str) {
	var url = new String(str);
	url = url.replace(/%([0-9ABCDEF]{2}|[0-9ABCDEF]{4})/g, function (str, p1, offset, s) { return String.fromCharCode(parseInt("0x"+p1,16)); });
	return url;
}

// загрузка ночей
function loadNights() {	
   if(getE('co').options.length>0 && getE('ct').options.length>0 && getE('rs').options.length>0 && getE('rs').options[getE('rs').selectedIndex].value>0){
        getE('nf').updateList();getE('nt').updateList();
   }
   else {
        window.setTimeout("loadNights()",500);
   }
}