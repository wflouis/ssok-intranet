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
function popisy(typ) { 
	if (typ == 1) {
		zobr1 = "none";
		zobr2 = "block";
	} else	{
		zobr2 = "none";
		zobr1 = "block";
	}
	td = document.getElementsByTagName("TD");
	for (i=0; i<td.length; i++) { 
		if (td(i).className.indexOf("popis1")>=0)
			td(i).style.display = zobr1; 
		if (td(i).className.indexOf("popis2")>=0)
			td(i).style.display = zobr2; 
	}
	td = document.getElementsByTagName("TR");
	for (i=0; i<td.length; i++) { 
		if (td(i).className.indexOf("popis1")>=0)
			td(i).style.display = zobr1; 
		if (td(i).className.indexOf("popis2")>=0)
			td(i).style.display = zobr2; 
	}
}

