dny = new Array ('nedìle','pondìlí','úterý','støeda','ètvrtek','pátek','sobota');
timeoutID = 0;

function getObj(id) {
	return document.getElementById(id);
}
function DnesJe() {
	mojePC = new Date();
	DatumServeru=new Date(mojePC-top.Rozdil);
//	getObj("AktCas").innerHTML = 'Dnes je '+dny[DatumServeru.getDay()]+'<br>' +DatumServeru.toLocaleString();
	getObj("AktCas").innerHTML = 'Dnes je '+'<br>' +DatumServeru.toLocaleString();
	setTimeout("DnesJe()",1000);
}
function rozsvit(popup) {
	if (popup==1)
		clearTimeout(timeoutID);
	else
		if (event.srcElement.parentElement.id != "")
			schovejPopup();
	with (event.srcElement.parentElement) {
		if (className != "vybrany")
			className = "zvyrazni";
	}
}
function zhasni(popup) {
	if (popup==1)
		zhasniPopup();
	with (event.srcElement.parentElement) {
		if (className != "vybrany")
			className = "";
	}
}
function schovejPopup() {
//	getObj("popup").style.display = "none";
}
function zhasniPopup() {
	timeoutID = setTimeout("schovejPopup()",1000);
}
function aktivni() {
	with (event.srcElement.parentElement) {
		if (className != "HlTab")
			if (className == "suda")
				className = "suda odkaz";
			else
				className = "odkaz";
	}
}
function pasivni() {
	with (event.srcElement.parentElement) {
		if (className != "HlTab")
			if (className == "odkaz")
				className = "";
			else
				className = "suda";
	}
}
function ukaz_old(adresar) {
	with (document.all(event.srcElement.parentElement.sourceIndex+2)) {
		if (tagName == "IMG" && src.substr(src.length-7,7) == "dir.gif") {
			text = document.all(event.srcElement.parentElement.sourceIndex+1).innerText;
			text = text.substr(1,text.length);
			if (text == "..") 
				with (document.razeni.Adresar) {
					value = value.substr(0,value.lastIndexOf("/"));
				}
			else
				document.razeni.Adresar.value +=  '/'+text;
			document.razeni.Sloupec.value = "";
			document.razeni.submit();
			return;
		}
	}
	with (document.all(event.srcElement.parentElement.sourceIndex+1)) {
		if (tagName == "TD" && parentElement.className != "HlTab")
			if (adresar == "")
				window.open("soubor.php?adresar="+encodeURI(document.razeni.Adresar.value)+"&soubor="+encodeURI(innerText),"");
			else
				window.open("soubor.php?adresar="+encodeURI(adresar)+"&soubor="+encodeURI(innerText),"");
	}
}
function serad(Sloupec) {
 	getObj("Sloupec").value = Sloupec;
	getObj("akce").value = "Seradit";
	getObj("razeni").submit();
}
function vyber() {
	if (event.srcElement.parentElement.tagName == "TR") {
		nabidka = document.all.tags("tr");
		for (i=0; i<nabidka.length; i++) {
			nabidka[i].className="";
		}
		event.srcElement.parentElement.className = "vybrany";
		schovejPopup();
		with (getObj("telo")) {
			switch (event.srcElement.parentElement.id) {
			 case '1': src='uvod.php'; break;
			 case '2': src='predpisy.php'; break;
			 case '3': src='registr.php'; break;
			 case '4': src='zakony.php'; break;
			 case '5': src='rekreace.php'; break;
			 case '6': src='seznam.php'; break;
			 case '7': src='zpravy.php'; break;
			 case '8': src='procesy.php'; break;
			 case '9': src='vyberovaRizeni.php'; break;
			 case '10': src='zaruky.php'; break;
			 case '11': src='partneri.php'; break;
			 case '12': src='smlouvy2.php?new=true'; break;
			 case '13': src='smerniceNew.php'; break;
			 case '14': src='majetek.php'; break;
			 case '15': src='bezpecnost.php'; break;
			 case '16': src='portalpo.php'; break;
			 case '17': src='gdpr.php'; break;
			 case '20': src='ZrizovaciListina.php'; break;
			 case '21': src='rejstrik.php'; break;
			 case '22': src='rejstrik.php'; break;
			 case '23': src='rejstrik.php'; break;
			 case '24': src='struktura.php'; break;
			 case '25': src='rejstrik.php'; break;
//			 case '15': src='KolektivniSmlouva.php'; break;
			}
		}
	}
}
function zmenaVelikosti() {
    getObj("telo").style.height = document.body.offsetHeight-95;
    getObj("telo").style.width = document.body.offsetWidth-220;
}
$(document).bind("ready",function(){
	$("#akce").click(function(){ 
		if (window.confirm("Opravdu chcete vybraný záznam smazat?")) {
			getObj("akce").value = "Smazat";
			this.parentElement.parentElement.submit();
		}
	});
	$(".suda,.licha").click(function(){ 
		if (getObj("id_vr")) {
			getObj("id_vr").value = Math.floor(this.id/1000); 
			getObj("id_verze").value = this.id%1000; 
			getObj("razeni").submit();
		} 
		if (getObj("nacist")) {
			getObj("nacist").value = 1; 
			getObj("id_jmeno").value = this.id; 
			getObj("razeni").submit();
		}
	});
	$("#seznam tr").hover(function(){ 
			if (this.className != "HlTab")
				this.style.backgroundColor="#2C7387";
		},
		function(){ 
			if (this.className != "HlTab")
				this.style.backgroundColor="";
	});
	$("#seznam tr").click(function(){ 
		if (this.className != "HlTab") {
			with (this.cells[0]) {
				text = childNodes[1].nodeValue;
				text = text.substr(1,text.length);
				if (childNodes[0].tagName == "IMG") {
					if (childNodes[0].src.substr(childNodes[0].src.length-7,7) == "dir.gif") {
						if (text == "..") 
							with (document.razeni.Adresar) {
								value = value.substr(0,value.lastIndexOf("/"));
							}
						else
							document.razeni.Adresar.value +=  '/'+text;
						document.razeni.Sloupec.value = "";
						document.razeni.submit();
						return;
					} else
						if (name!="")
							window.open("soubor.php?adresar="+encodeURI(name)+"&soubor="+encodeURI(text),"");
						else
							window.open("soubor.php?adresar="+encodeURI(document.razeni.Adresar.value)+"&soubor="+encodeURI(text),"");
				}
			}
		}
	});
	$("input .cislo").keypress(function(event){ 
		keyCode = (event.which)?event.which:event.keyCode; 
		var seznam = '0123456789.: '; 
		if (seznam.indexOf(String.fromCharCode(keyCode)) == -1 && (keyCode!=8 || keyCode!=13)) {
			if (event.which)
				event.preventDefault();
			else
				event.returnValue=false;
		} 
	});
});


