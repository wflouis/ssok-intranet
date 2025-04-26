dny = new Array ('neděle','pondělí','úterý','středa','čtvrtek','pátek','sobota');
timeoutID = 0;

$(document).ready(function() {
	$('#hledat').find('button').on('click', function() {
		$('#hledat').submit();
	});
	$('#folders thead tr td').click(function() {
		location = $(this).parent().attr('path')+'&podle='+$(this).attr('sort')+'&najit='+$('#hledat input[name=\'najit\']').val();
	});
	$('#folders tbody td:not([onclick])').each(function(i, el) {
		let href = el.parentNode.getAttribute('path')

		el.innerHTML = `<a href='${href}' ${el.parentNode.getAttribute('folder') ? '' : 'target="_blank"'}
		style="
		display:flex;width:100%;height:45px;
		align-items:center;
		color:white;text-decoration:none;
		padding:4px 8px;
		box-sizing:border-box;
		">
		${el.innerHTML}</a>`
	});
	DnesJe();
	
	let menuButton = document.getElementById('menu-button')
	let menuDrawer = document.getElementById('menu')
	
	let menuOpen = false
	function toggleMenu(){
		menuOpen = !menuOpen;
		menuButton.classList.toggle('menu-open')
		menuDrawer.classList.toggle('menu-open')
	}
	if(menuButton){
		menuButton.onclick = (e) => {
			e.cancelBubble = true;
			toggleMenu()
		}
		window.onclick = () => {
			if(!menuOpen) return
			toggleMenu()
		}
	}
});

function DnesJe() {
	mojePC = new Date();
	DatumServeru=new Date(mojePC-top.Rozdil);
	$("#AktCas").html(dny[DatumServeru.getDay()]+' '+DatumServeru.toLocaleString());
	setTimeout("DnesJe()",1000);
}