function getObj(id) {
	return document.getElementById(id); 
}
function ukaz(co) {
	getObj('oknoSeznam').style.display = 'none';
	getObj('oknoDetail').style.display = 'none';
	if (getObj('oknoZaruky')!=undefined) {
		getObj('oknoZaruky').style.display = 'none';
		getObj('ousko3').className = '';
		getObj('oknoChyby').style.display = 'none';
		getObj('ousko4').className = '';
	}
	getObj('ousko1').className = '';
	getObj('ousko2').className = '';
	getObj(co).style.display = 'block';
	switch (co) {
		case 'oknoSeznam': getObj('ousko1').className = 'aktivni'; break;
		case 'oknoDetail': getObj('ousko2').className = 'aktivni'; break;
		case 'oknoZaruky': getObj('ousko3').className = 'aktivni'; break;
		case 'oknoChyby':  getObj('ousko4').className = 'aktivni'; break;
	}
}
function serad(Sloupec) {
	with (getObj('formSeznam')) {
		raditPodle.value = Sloupec;
		akce.value = "Seradit";
		akce.click();
		akce.value = "Najít";
	}
}
function seradZaruky(Sloupec) {
	with (getObj('formZaruky')) {
		raditZaruky.value = Sloupec;
		akce.value = "Seradit";
		submit();
	}
}
function preved() {
	with (document.formDetail) {
		window.opener.document.getElementById("ico").value = ico.value;
		window.opener.document.getElementById("firma").innerText=nazev.value+", "+ulice.value+", "+psc.value+" "+mesto.value;
		window.close();
	}
}
function smazat() {
	if (getObj('id_smlouvy').value>0 && confirm("Opravdu chcete smazat tuto smlouvu?")) {
		with (getObj('formDetail')) {
			akce.value = 'Smazat'; 
			submit();
		}
	}
}
function smazPrilohu(klic) {
	document.getElementById('zrusPrilohu').value=klic;
	document.getElementById('formDetail').submit();
}
function smazPartnera(klic) {
	document.getElementById('zrusPartnera').value=klic;
	document.getElementById('akce').value="";
	document.getElementById('formDetail').submit();
}
function navigace() {
	with (getObj("formSeznam")) {
		with (event.srcElement) {
			switch (tagName) {
				case "A":
					strana.value = innerText; akce.value = "strana"; break;
				case "IMG":
					akce.value = src.substring(src.lastIndexOf("/")+1,src.lastIndexOf("."));
			}
			if (akce.value != "" || strana.value != "") 
				akce.click(); 
		}
	}
}
function kontrola() {
	if (getObj('cisloSmlouvy').value=="" ||  getObj('strediska[]').value==""  ||  getObj('datumUzavreni').value=="") {
		alert("Vyplòte prosím základní údaje!");
		return false;
	} else 
		with (getObj('formDetail')) {
//			akce.value = 'Uložit'; 
//			submit();
		}
}

$(document).bind("ready",function(){
	if (getObj('ousko1') && getObj('ousko1').className == 'aktivni' && getObj('formSeznam').najit.visible)
		getObj('formSeznam').najit.focus();
	$("#seznam tr,#seznamPartneru tr,#seznamHlaseni tr,.zobrazit").click(function(){ 
		radek = this.id.substr(1,10); 
		if (radek.length>0) { 
			if (window.opener) {
				if (this.parentElement.parentElement.id=="seznam") {
					window.opener.document.getElementById("souvisejici").value = radek;
					window.opener.document.getElementById("textVazba").innerHTML = "<table><tr>"+this.innerHTML+"</tr></table>";
				} else {
					window.opener.document.getElementById("ico").value = this.childNodes[0].innerHTML;
					window.opener.document.forms["formDetail"].submit();
				}
				window.close();
			} else {
				with(getObj('formSeznam')) { 
					if (typeof(id_smlouvy)!== "undefined") {
						udaje = radek.split("-");
						id_smlouvy.value=udaje[0];
						if (typeof(udaje[1])!== "undefined") 
							cislo.value=udaje[1];
					} else
						id_smernice.value=radek; 
					if (this.tagName=="A")
						akce.value = "Zobrazit"; 
					else {
						akce.value = "Detail"; 
						okno.value = "2";
					}
					akce.click(); 
					akce.value = "Najít"; 
					window.event.cancelBubble = true;
			    }
			}
		}
	});
	$("#seznam tr,#seznamPartneru tr,#seznamHlaseni tr,.zobrazit,#zaruky tr").hover(function(){ 
		if (this.id != "")
		  this.style.backgroundColor="#2C7387";
		},
		function(){ 
			this.style.backgroundColor="";
	});
	$(".ouska li").click(function(){ 
		if (this.parentElement!=null) {
			radek= this.parentElement.id.substr(1,10);
			if (radek>0) 
				  rozbal('d'+radek);
		}
		switch (this.id) {
		case 'ousko1': ukaz('oknoSeznam',this.id); break;
		case 'ousko2': ukaz('oknoDetail',this.id); break;
		case 'ousko3': ukaz('oknoZaruky',this.id); break;
		case 'ousko4': ukaz('oknoChyby',this.id); break;
		}
	});
	$('body').on('keypress', 'input, select, textarea', function(e) {
    var self = $(this)
      , form = self.parents('form:eq(0)')
      , focusable
      , next
      ;
	    if (e.keyCode == 13 && (this.type=="text" || this.type=="checkbox" || this.nodeName!="INPUT")) {
	        focusable = form.find('input,a,select,textarea').filter(':visible');
	        next = focusable.eq(focusable.index(this)+1);
	        if (next.length) {
	            next.focus();
	        } else {
	            form.submit();
	        }
	        return false;
	    } else 
			if (this.type=="text" && this.className=="cislo") {
				var seznam = '0123456789.: '; 
				if (seznam.indexOf(String.fromCharCode(e.keyCode)) == -1 && (e.keyCode!=8 || e.keyCode!=13)) {
					if (e.which)
						e.preventDefault();
					else
						e.returnValue=false;
				}
			}
	});
/*	$("input").keydown(function(event){ 
		var keyCode = (event.which)?event.which:event.keyCode; 
		if (keyCode == 13 && this.className!="enter") 
			if (this.type == "text" || this.type == "select-one") {
				event.keyCode = 9;
				event.which = 9;
				event.preventDefault();
				return event.which;
			}
	}); */
	$("#zaruky tr").click(function(){ 
		radek = this.id.substr(1,10); 
		if (radek>0) 
			with(getObj('formZaruky')) {
				id_smlouvy.value=radek; 
				okno.value = "2";
				submit(); 
	    }
	});
});

