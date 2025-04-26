zmenaFiltru = false;

function zmena() {
	zmenaFiltru = true;
}	
function vytiskni() {
	window.open('prehled.html','_blank','scrollbars=yes,width=760,height=600,top=50,left=50'); 
}
function kontrola() {
	if (zmenaFiltru == true) {
		getObj("akce").value = "tisk";
		getObj("prehled").submit();
	} else
		vytiskni();
}

