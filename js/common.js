dny = new Array ('neděle','pondělí','úterý','středa','čtvrtek','pátek','sobota');
timeoutID = 0;

$(document).ready(function() {
	$('#hledat').find('button').on('click', function() {
		$('#hledat').submit();
	});
	$('#folders thead tr td').click(function() {
		location = $(this).parent().attr('path')+'&podle='+$(this).attr('sort')+'&najit='+$('#hledat input[name=\'najit\']').val();
	});
	$('#folders tbody tr').click(function() {
		location = $(this).attr('path');
	});
	DnesJe();
});
function DnesJe() {
	mojePC = new Date();
	DatumServeru=new Date(mojePC-top.Rozdil);
	$("#AktCas").html(dny[DatumServeru.getDay()]+' '+DatumServeru.toLocaleString());
	setTimeout("DnesJe()",1000);
}

