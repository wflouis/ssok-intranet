$(document).ready(function(){ 

	$(".seznamZam tr").mouseover(function(){

		if (this.id>"r1") {

				this.style.color="black";

				this.style.backgroundColor="#888888";

		}

	});

	$(".seznamZam tr").mouseout(function(){

		this.style.color="";

			this.style.backgroundColor="";

	});

	$(".seznamZam tr").click(function(){

	  	if (this.id>"r1") {

			switch (this.id.substr(0,1)) {

			case "r":

				$("#user").val(this.id.substr(1,10)); 

				$("#seznam").submit(); break;

			case "z":

				$("#zaznam").val(this.id.substr(1,10)); 

				$("#zaznamy").submit(); break;

			}

		}

	});

	$(".pichacky td").click(function(){

		window.document.location.href="pichacky.php?preruseni="+this.id+"&poznamka="+window.document.getElementById("poznamka").value; 

	});

	$(".zalozky a").click(function(){
		$(".zalozky a").removeClass('aktivni');
		$(this).addClass('aktivni'); 
		$(".telo").css('display', 'none');
		$("#o"+this.id.substr(this.id.length - 1)).css('display', 'block');
	});

});

function prepni_zalozku(element) { 

		$(".zalozka").css('display', 'none');

		$(".zalozka").css('className', '');

		$(element).css('display', 'block');

		$(element).css('className', 'aktivni');

//		$("#o"+element.id.substr(1,1)).style.display="block";

//		element.className = "aktivni";

}

function novy() {

	with (window.document) {

		if (getElementById("zaznam").value >= 0) {

			getElementById("zaznam").value = 0; 

			getElementById("den").value = ""; 

			getElementById("cas").value = ""; 

			getElementById("idTypuZaznamu").value = ""; 

		} else {

			getElementById("user").value = 0; 

			getElementById("osCislo").value = ""; 

			getElementById("prijmeni").value = ""; 

			getElementById("jmeno").value = ""; 

			getElementById("uvazek").value = ""; 

			getElementById("pracoviste").value = ""; 

			getElementById("heslo").value = ""; 

		}

	}

}