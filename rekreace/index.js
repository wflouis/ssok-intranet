kontrolaUlozeniPred = "tisk,novy,nacti";
zmeneno = false;
timeoutID = 0;

function getObj(id) {
	return document.getElementById(id);
}
function enter() {
	if (event.keyCode == 13)
		if (event.srcElement.type == "text" || event.srcElement.type == "select-one") 
			event.keyCode = 9;
}
function cisla(dalsi) {
	if (event.srcElement.className.indexOf("cislo") >= 0 || event.srcElement.name.indexOf("c_") >= 0 || event.srcElement.name.indexOf("osob_") >= 0) {
		var seznam = '0123456789';
		if (event.keyCode == 44) 
			event.keyCode = 46; 
		if (dalsi != null)
			seznam+=dalsi;
		if (seznam.indexOf(String.fromCharCode(event.keyCode)) == -1 && event.keyCode != 13)
			return false;
	}
}
function format(Cislo) {
	Cislo += 0.0001;
	Radek = Cislo.toString(); 
	Cela = Radek.substring(0,Radek.indexOf('.'));
	Desetiny = Radek.substr(Radek.indexOf('.')+1,2);
	Tisice = "";
	for(i=Cela.length-1;i>=0;i--) {
		Tisice = Cela.charAt(i) + Tisice;
		if ((Cela.length-i)%3==0)
			Tisice = " " + Tisice;
	}
	return (Tisice+','+Desetiny);
}
function rozsvit() {
	with (event.srcElement.parentElement) 
		if (className.indexOf("lichy") >= 0 || className.indexOf("sudy") >= 0)
			style.backgroundColor="#E0FFFF";
}
function zhasni() {
	with (event.srcElement.parentElement) 
		if (className.indexOf("lichy") >= 0 || className.indexOf("sudy") >= 0)
			style.backgroundColor="";
}
function nacti(co) {
	with (event.srcElement.parentElement) {
		if (className == "zahlavi" && ulozeno("")) {
			sloupec = jmenoSloupce(event.srcElement.innerText);
			if (sloupec == "")
				return;
			else {
				getObj("sloupec").value = sloupec;
				getObj("vyber").akce.value = "serad";
				getObj("vyber").submit();
			}
		} else
			if (tagName == "TR" && ulozeno("")) {
				getObj(co).value = id;
				getObj("polozka").akce.value = "nacti";
				getObj("polozka").submit();
			}
	}
}
function prepni(volba) {
	if (volba != "") {
		for(i=1;i<=2;i++)
			getObj("o"+i).className = "pasOusko";
		switch (volba) {
			case "o1":
					getObj("posun").style.display = "block";
					getObj("vyber").style.display = "block";
					getObj("polozka").style.display = "none";
					break; 
			case "o2":
					getObj("posun").style.display = "none";
					getObj("vyber").style.display = "none";
					getObj("polozka").style.display = "block";
					nastav_kurzor();
		}
		getObj(volba).className = "aktOusko";
		zmenNadpis(volba);
	}
}
function schovejPopup() {
	getObj("sestavy").style.display = "none";
}
function rozsvitMenu() {
	clearTimeout(timeoutID);
	if (event.srcElement.tagName == "TD") 
		event.srcElement.style.backgroundImage = "url(obrazky/vyb_tlac_poz.gif)";
	if (event.srcElement.innerText == "Sestavy")
		getObj("sestavy").style.display = "block";
	else
		if (event.srcElement.parentElement.className == "menu")
			schovejPopup();
}
function zhasniMenu() {
	event.srcElement.style.backgroundImage = "";
	timeoutID = setTimeout("schovejPopup()",1000);
}
function vyberMenu(volba) {
	if (!ulozeno("")) {
		return;
	}
	switch (volba) {
		case "Objekty": top.telo.window.location = "objekty.php"; break;
		case "Poukazy": top.telo.window.location = "poukazy.php"; break;
		case "1": top.telo.window.location = "prehled.php";
	} 
	if (event.srcElement.parentElement.parentElement.parentElement.className == "popup")
		getObj("sestavy").style.display = "none";
}
function proved(formular) {
	if (event.srcElement.tagName == "BUTTON" && ulozeno(event.srcElement.name)) { 
		if (event.srcElement.name == "tisk") {
			switch (event.srcElement.parentElement.id) {
			case "t_objekt":
				window.open("tisk_o.php","_blank","scrollbars=yes,width=760,height=600,top=50,left=50"); break;
			case "t_poukaz":
				window.open("tisk.php","_blank","scrollbars=yes,width=760,height=600,top=50,left=50"); 
			}
		} else {
			getObj(formular).akce.value = event.srcElement.name;
			top.zmeneno = false;
			getObj(formular).submit(); 
		}
	}
}
function ulozeno(akce) {
	if (top.zmeneno == true && top.kontrolaUlozeniPred.indexOf(akce) >= 0) {
		top.zmeneno = false;
		if (confirm("Provedené zmìny nebyly uloženy! Chcete je uložit?")) {
			top.telo.getObj("polozka").akce.value = "ulozit";
			top.telo.getObj("polozka").submit(); 
			return false;
		}
	}
	return true;
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
