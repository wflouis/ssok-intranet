function prepocti(zmena) {
	var t_pou_cel = 0, t_dph_cel = 0, t_rek_cel = 0, t_cel_cel = 0;
	pocNoci = noci();

	obnov_tab("zam");
	obnov_tab("zad");
	obnov_tab("ciz");
	obnov_tab("cid");
	
	getObj("t_pou_cel").innerText = format(t_pou_cel) + " Kè";
	getObj("t_dph_cel").innerText = format(t_dph_cel) + " Kè";	    	    
	getObj("t_rek_cel").innerText = format(t_rek_cel) + " Kè";   
	getObj("t_cel_cel").innerText = format(t_cel_cel) + " Kè"; 
	getObj("kuhrade").innerText = format(t_cel_cel); 

	if (zmena == null)
		top.zmeneno = true;
	
	function obnov_tab(index) {
		getObj("t_noci_"+index).innerText = pocNoci;
		getObj("t_osob_"+index).innerText = getObj("osob_"+index).value;
		getObj("t_pou_"+index).innerText = format(pocNoci*getObj("osob_"+index).value*sazby["c_"+index+"_pou"]) + " Kè";
		getObj("t_dph_"+index).innerText = format(Math.round(pocNoci*getObj("osob_"+index).value*sazby["c_"+index+"_dph"])) + " Kè";
		getObj("t_rek_"+index).innerText = format(pocNoci*(getObj("osob_"+index).value-getObj("inv_"+index).value)*sazby["c_"+index+"_rek"]) + " Kè";
		getObj("t_cel_"+index).innerText = format(Math.round(pocNoci*(getObj("osob_"+index).value*(1*sazby["c_"+index+"_pou"]+1*sazby["c_"+index+"_dph"])+(getObj("osob_"+index).value-getObj("inv_"+index).value)*sazby["c_"+index+"_rek"]))) + " Kè";
		t_pou_cel += pocNoci*getObj("osob_"+index).value*sazby["c_"+index+"_pou"];
		t_dph_cel += Math.round(pocNoci*getObj("osob_"+index).value*sazby["c_"+index+"_dph"]);
		t_rek_cel += pocNoci*(getObj("osob_"+index).value-getObj("inv_"+index).value)*sazby["c_"+index+"_rek"];
		t_cel_cel += Math.round(pocNoci*getObj("osob_"+index).value*(1*sazby["c_"+index+"_pou"]+1*sazby["c_"+index+"_dph"]))+pocNoci*((getObj("osob_"+index).value-getObj("inv_"+index).value)*sazby["c_"+index+"_rek"]);
		if (getObj("osob_"+index).value > 0) { 
			getObj(index+"_c").style.display = "table-row";
			getObj(index+"_j").style.display = "table-row";
		} else {
			getObj(index+"_c").style.display = "none";
			getObj(index+"_j").style.display = "none";
		}
	}
}
function noci() {
	return Math.round((textNaDatum(getObj("konec").value)-textNaDatum(getObj("nastup").value))/86400000);

  	function textNaDatum(text) {
		den = text.substr(0,text.indexOf("."));
		mesic = text.substr(text.indexOf(".")+1,text.lastIndexOf(".")-text.indexOf(".")-1);
		rok = text.substr(text.lastIndexOf(".")+1,4);
		return new Date(mesic+'/'+den+'/'+rok);
	}
}
function nastav_kurzor() {
	getObj("n_objektu").value = getObj("objekt").value;
	getObj("nazev_zam").focus();
	prepocti(false);
}
function zmenNadpis(volba) {
	switch (volba) {
		case "o1" :	getObj("nadpisS").style.display = "block"; 
					getObj("nadpisP").style.display = "none"; break;
		case "o2" :	getObj("nadpisP").style.display = "block"; 
					getObj("nadpisS").style.display = "none"; break;
	}
}
function jmenoSloupce(text) {
	var sloupec = "";
	switch (text) {
		case "Poukaz ": sloupec = "rok DESC,poukaz"; break;
		case "jméno " : sloupec = "prijmeni"; break;
		case "od "	: sloupec = "nastup"; break;
		case "do "	: sloupec = "konec"; break;
		default: sloupec = "";
	}
	return sloupec;
}
function navigace() {
	with (getObj("vyber")) {
		with (event.srcElement) {
			switch (tagName) {
				case "A":
					strana.value = innerText; break;
				case "IMG":
					akce.value = src.substring(src.lastIndexOf("/")+1,src.lastIndexOf("."));
			}
			if (akce.value != "" || strana.value != "") 
				submit(); 
		}
	}
}
