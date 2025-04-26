function prepocti(zmena) {
	obnov_tab("zam");
	obnov_tab("zad");
	obnov_tab("ciz");
	obnov_tab("cid");

	if (zmena == null)
		top.zmeneno = true;
	
	function obnov_tab(index) {
		getObj("t_"+index+"_cel").innerText = format(1*getObj("c_"+index+"_pou").value+1*parseFloat(getObj("c_"+index+"_dph").value)+1*getObj("c_"+index+"_rek").value) + " Kè";
	}
}
function nastav_kurzor() {
	getObj("nazev").focus();
	prepocti(false);
}
function zmenNadpis(volba) {
	switch (volba) {
		case "o1" :	getObj("nadpis").innerText = "Seznam rekreaèních objektù"; break;
		case "o2" :	getObj("nadpis").innerText = "Rekreaèní objekt è. "+getObj("objekt").value;
	}
}
function jmenoSloupce(text) {
	return "";
}