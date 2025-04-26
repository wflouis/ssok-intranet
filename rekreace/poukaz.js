function prepocti(zmena) {
	var t_pou_cel = 0, t_dph_cel = 0, t_rek_cel = 0, t_cel_cel = 0;
	pocNoci = noci(); 

	if (getObj("typCeny").value==1) {
		if (pocNoci<4) {
			pocetTydnu=1;
			if (getObj("nazev_zam").value!="" && getObj("nazev_zam").value!="ne") {
				pocetOsob = 1*getObj("osob_zam").value+1*getObj("osob_zad").value-1*getObj("inv_zam").value
				obnov_tab_obdobi("zam",1);
			} else {
				pocetOsob = 1*getObj("osob_ciz").value+1*getObj("osob_cid").value-1*getObj("inv_ciz").value
				obnov_tab_obdobi("zam",0);
			}
			if (getObj("nazev_ciz").value!="" && getObj("nazev_ciz").value!="ne")
				obnov_tab_obdobi("zad",1);
			else
				obnov_tab_obdobi("zad",0);
			obnov_tab_obdobi("ciz",0);
			obnov_tab_obdobi("cid",0);
		} else {
			pocetTydnu=Math.round(pocNoci/7);
			obnov_tab_obdobi("zam",0);
			obnov_tab_obdobi("zad",0);
			if (getObj("nazev_zam").value!="" && getObj("nazev_zam").value!="ne") {
				pocetOsob = 1*getObj("osob_zam").value+1*getObj("osob_zad").value-1*getObj("inv_zam").value
				obnov_tab_obdobi("ciz",1);
			} else {
				pocetOsob = 1*getObj("osob_ciz").value+1*getObj("osob_cid").value-1*getObj("inv_ciz").value
				obnov_tab_obdobi("ciz",0);
			}
			if (getObj("nazev_ciz").value!="" && getObj("nazev_ciz").value!="ne")
				obnov_tab_obdobi("cid",1);
			else
				obnov_tab_obdobi("cid",0);
		}
	} else {
		obnov_tab("zam");
		obnov_tab("zad");
		obnov_tab("ciz");
		obnov_tab("cid");
	}
	getObj("t_pou_cel").innerText = format(t_pou_cel) + " Kè";
	getObj("t_dph_cel").innerText = format(t_dph_cel) + " Kè";	    	    
	getObj("t_rek_cel").innerText = format(t_rek_cel) + " Kè";   
	getObj("t_cel_cel").innerText = format(t_cel_cel) + " Kè"; 
	getObj("kuhrade").innerText = format(t_cel_cel); 
	getObj("celkem").value = t_cel_cel;

	if (zmena == null)
		top.zmeneno = true;
	
	function obnov_tab(index) {
		getObj("t_noci_"+index).innerText = pocNoci;
		getObj("t_osob_"+index).innerText = getObj("osob_"+index).value; 
		nasobit = pocNoci;
			getObj("t_pou_"+index).innerText = format(nasobit*getObj("osob_"+index).value*sazby["c_"+index+"_pou"]) + " Kè";
			getObj("t_dph_"+index).innerText = format(nasobit*getObj("osob_"+index).value*sazby["c_"+index+"_dph"]) + " Kè";
			getObj("t_rek_"+index).innerText = format(nasobit*(getObj("osob_"+index).value-getObj("inv_"+index).value)*sazby["c_"+index+"_rek"]) + " Kè";
			getObj("t_cel_"+index).innerText = format(nasobit*(getObj("osob_"+index).value*(1*sazby["c_"+index+"_pou"]+1*sazby["c_"+index+"_dph"])+(getObj("osob_"+index).value-getObj("inv_"+index).value)*sazby["c_"+index+"_rek"])) + " Kè";
			t_pou_cel += nasobit*getObj("osob_"+index).value*sazby["c_"+index+"_pou"];
			t_dph_cel += nasobit*getObj("osob_"+index).value*sazby["c_"+index+"_dph"];
			t_rek_cel += nasobit*(getObj("osob_"+index).value-getObj("inv_"+index).value)*sazby["c_"+index+"_rek"];
			t_cel_cel += nasobit*(getObj("osob_"+index).value*(1*sazby["c_"+index+"_pou"]+1*sazby["c_"+index+"_dph"])+(getObj("osob_"+index).value-getObj("inv_"+index).value)*sazby["c_"+index+"_rek"]);
		if (getObj("osob_"+index).value > 0) { 
			getObj(index+"_c").style.display = "block";
			if (getObj("typCeny").value==0)
				getObj(index+"_j").style.display = "block";
		} else {
			getObj(index+"_c").style.display = "none";
			getObj(index+"_j").style.display = "none";
		}
	}
	function obnov_tab_obdobi(index,zobrazit) {
		if (zobrazit) { 
			getObj("t_noci_"+index).innerText = pocNoci;
			getObj("t_osob_"+index).innerText = pocetTydnu; 
			getObj("t_pou_"+index).innerText = format(pocetTydnu*sazby["c_"+index+"_pou"]) + " Kè";
			getObj("t_dph_"+index).innerText = format(pocetTydnu*sazby["c_"+index+"_dph"]) + " Kè";
			getObj("t_rek_"+index).innerText = format(pocNoci*pocetOsob*sazby["c_"+index+"_rek"]) + " Kè";
			getObj("t_cel_"+index).innerText = format(pocetTydnu*sazby["c_"+index+"_pou"]+pocetTydnu*sazby["c_"+index+"_dph"]+pocetOsob*sazby["c_"+index+"_rek"]*pocNoci) + " Kè";
			t_pou_cel += pocetTydnu*sazby["c_"+index+"_pou"];
			t_dph_cel += pocetTydnu*sazby["c_"+index+"_dph"];
			t_rek_cel += pocNoci*pocetOsob*sazby["c_"+index+"_rek"];
			t_cel_cel += (pocetTydnu*sazby["c_"+index+"_pou"]+pocetTydnu*sazby["c_"+index+"_dph"]+pocetOsob*sazby["c_"+index+"_rek"]*pocNoci);
			getObj(index+"_c").style.display = "block";
			if (getObj("typCeny").value==0)
				getObj(index+"_j").style.display = "block";
		} else {
			getObj(index+"_c").style.display = "none";
			getObj(index+"_j").style.display = "none";
		}
	}
}
function noci() {
//	if (getObj("typCeny").value==0)
		return (textNaDatum(getObj("konec").value)-textNaDatum(getObj("nastup").value))/86400000;
//	else
//		return 1;

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
