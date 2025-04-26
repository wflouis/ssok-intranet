function getObj(id) {
	return document.getElementById(id);
}
function rozbal(id) {
  with (getObj(id)) {
	  if (style.display == 'none' || className.indexOf('skryty')>-1) {
	  	if (style.display != 'none')
		  	className = className.substr(0,className.indexOf('skryty'));
	  	style.display = 'block'; 
	  } else 
	    style.display = 'none'; 
  }
}
function ukaz(co) {
	getObj('oknoSeznam').style.display = 'none';
	getObj('oknoDetail').style.display = 'none';
	getObj('ousko1').className = '';
	getObj('ousko2').className = '';
	getObj(co).style.display = 'block';
	if (co == 'oknoSeznam')
		getObj('ousko1').className = 'aktivni';
	else {
		getObj('ousko2').className = 'aktivni';
		getObj('nazev').focus();
	}
}
$(document).bind("ready",function(){
	$("#formAkce input").keydown(function(event){ 
		if (event.keyCode == 13)
			if (this.name != "akce") 
				event.keyCode = 9;
	});
	$("#formAkce input").keypress(function(event){ 
		if (this.className.indexOf("cislo") >= 0 || this.name.indexOf("c_") >= 0 || this.name.indexOf("osob_") >= 0) {
			var seznam = '0123456789.';
		if (event.keyCode == 44) 
			event.keyCode = 46; 
		if (dalsi != null)
			seznam+=dalsi;
		if (seznam.indexOf(String.fromCharCode(event.keyCode)) == -1 && event.keyCode != 13)
			return false;
		}
	});
});

