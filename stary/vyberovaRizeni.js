var poradi=1;

function doplnUdaje(kod) { 
	if (kod=="")
		return;
	if (window.XMLHttpRequest)
		xmlhttp=new XMLHttpRequest();
	else 
   		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); 
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) { 
		    xmlDoc=xmlhttp.responseXML; 
    		x=xmlDoc.getElementsByTagName("cpv"); 
			if (x[0]==null)
				return;
			document.getElementById("text").innerHTML = x[0].childNodes[0].childNodes[0].nodeValue; 
    	}
 	} 
	xmlhttp.open("GET","xml.php?kod="+kod,true);
	xmlhttp.send(null);
}
function smazPrilohu(klic) {
	document.getElementById('zrusPrilohu').value=klic;
	document.getElementById('razeni').submit();
}
function zobrazRozklad(zobrazit) {
	alert(zobrazit.checked);
}