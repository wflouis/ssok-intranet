dny = new Array ('neděle','pondělí','úterý','středa','čtvrtek','pátek','sobota');
timeoutID = 0;

$(document).ready(function() {
	$('#hledat').find('button').on('click', function() {
		$('#hledat').submit();
	});
	$('#folders thead tr td').click(function() {
		location = $(this).parent().attr('path')+'&podle='+$(this).attr('sort')+'&najit='+$('#hledat input[name=\'najit\']').val();
	});
	// $('#folders tbody td:not([onclick])').each(function(i, el) {
	// 	let href = el.parentNode.getAttribute('path')

	// 	el.innerHTML = `<a href='${href}' ${el.parentNode.getAttribute('folder') ? '' : 'target="_blank"'}
	// 	style="
	// 	display:flex;width:100%;height:45px;
	// 	align-items:center;
	// 	color:white;text-decoration:none;
	// 	padding:4px 8px;
	// 	box-sizing:border-box;
	// 	">
	// 	${el.innerHTML}</a>`
	// });
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

function loadingScreen(){
	let loadingScreen = document.createElement('div')
	loadingScreen.classList.add('loading-screen')
	let loader = document.createElement('div')
	loader.appendChild(document.createElement('span'))
	loader.classList.add('loader-5')
	loadingScreen.appendChild(loader)

	document.body.appendChild(loadingScreen)

	return loadingScreen
}
function downloadFile(url, path){
	let name = path.split('/').pop()
	
	let isUrl = name.endsWith('.url') || name.endsWith('.URL')
	if (isUrl) {
		let loading = loadingScreen();
		fetch(url +'?path=' + path)
		.then(async r => {
			if(r.status != 200){
				alert('Soubor nenalezen')
				return
			}

			return r.blob().then(async blob => {
				let text = await blob.text()
				let url = text.split("\n")[1].split('=').pop()
				window.open(url)
			});
		})
		.finally(() => {
			loading.remove();
		})
		return
	}

	window.open(url + "?path=" + path, '_blank')
	
	// fetch(url +'?path=' + path)
	// .then(async r => {
	// 	if(r.status != 200){
	// 		alert('Soubor nenalezen')
	// 		return
	// 	}

	// 	return r.blob().then(async blob => {
	// 		let file = window.URL.createObjectURL(blob);

	// 		let a = document.createElement("a")
	// 		a.setAttribute("download", name)
	// 		a.href = file
	// 		a.target = '_blank'
	// 		a.click()
	// 	})
	// 	.finally(() => {
	// 		loading.remove()
	// 	})
	// })
}
