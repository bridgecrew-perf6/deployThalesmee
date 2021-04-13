var x = document.getElementsByClassName("of");
var y = $("#tab").offset().top;
console.log(y);

$('input[type="text"]').attr("class", "form-control");

if ($('#save').offset().top > y){
	
	
	$('#save').offset ({top: y});
	$('#save').css ({bottom : 'auto'});
}else{
	$('#save').css ({bottom : '20px'});
	$('#save').css ({top: ""});
}
document.addEventListener ('scroll', function (event){
	
	var y = $("#tab").offset().top;
	console.log(y);
	if ($('#save').offset().top > y){
		
		
		$('#save').offset ({top: y});
		$('#save').css ({bottom : 'auto'});
	}else{
		$('#save').css ({bottom : '20px'});
		$('#save').css ({top: ""});
	}
	
})

$(".plus").change(function(){
	
	id = $(this).val();
	console.log(id);
	newOF(id);

	
});

$(".anomalieX").change(function(){
	
	id = $(this).attr('class');
	console.log("ici");
	if($(this).is(':checked')){
		
		
		$(this).next().show();
	
		
	}else{
		
		$(this).next().hide();
		
	}
	
});
$(".anomalieY").change(function(){
			
	id = $(this).attr('class');
	if($(this).is(':checked')){
		
		$(this).next().show();
		
		
	}else{
		
		$(this).next().hide();
		
	}
	
});
$(".anomalieZ").change(function(){
		
	id = $(this).attr('class');
	if($(this).is(':checked')){
		
		console.log($(this).parent().next());
		$(this).next().show();
		
		
	}else{
		
		$(this).next().hide();
		
	}
	
});

$(".init_freq").blur(function (e){
		
	if (e.target == this){
		console.log("ici");
		console.log($(this).parent().next().next().next().children().val());
		var res = Math.round(Math.abs(($(this).parent().next().children().val() - $(this).val()) / $(this).parent().next().next().children().val())*1000)/10;
		if (!isNaN(res) && res != 'Infinity'){
			
			$(this).parent().next().next().children().val(res + " %");
		}else{
			
			$(this).parent().next().next().children().val ('Erreur');
		}
	}

});
$('.fin_freq').blur(function (e){
	
	if (e.target == this){
		console.log("ici");

		var res = Math.round(Math.abs(($(this).val() - $(this).parent().prev().children().val()) / $(this).val())*1000)/10;
		if (!isNaN(res) && res != 'Infinity'){
			
			$(this).parent().next().children().val(res + " %");
		}else{
			
			$(this).parent().next().children().val ('Erreur');
		}
	}
	
});

$('.init_ampl').blur(function (e){
	
	if (e.target == this){
		console.log("ici");

		var res = Math.round(Math.abs(($(this).parent().next().children().val() - $(this).val()) / $(this).parent().next().next().children().val())*1000)/10;
		if (!isNaN(res) && res != 'Infinity'){
			
			$(this).parent().next().next().children().val(res + " %");
		}else{
			
			$(this).parent().next().next().children().val ('Erreur');
		}
	}

});
$('.fin_ampl').blur(function (e){
	
	if (e.target == this){
		console.log("ici");
		var res = Math.round(Math.abs(($(this).val() - $(this).parent().prev().children().val()) / $(this).val())*1000)/10;
		if (!isNaN(res) && res != 'Infinity'){
			
			$(this).parent().next().children().val(res + " %");
		}else{
			
			$(this).parent().next().children().val ('Erreur');
		}
	}
	
});



function ajout_capt(axe, OF){


	console.log('tabCapt'+axe+OF);
	console.log("init_freq"+axe+OF);
	console.log(OF.replace("/","\\\/"));
	console.log('tabCapt'+axe+OF);
	console.log("init_freq"+axe+OF);
	var newRow = document.getElementById('tabCapt'+axe+OF).insertRow(-1); 
	var newCell = newRow.insertCell(-1);

	var valeur = $(document.getElementById('tabCapt'+axe+OF)).children().last().prev().children().children().attr('class');
	console.log(valeur);
	
	if (valeur == undefined ) {
		
		valeur=0;
		
	}else{
		valeur = parseInt(valeur)+1;
	}
	newCell.innerHTML = '<input type="checkbox" class='+valeur+' id="spec" value='+valeur+' name="spec'+axe+OF+'[]" size=6>';
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = '<input type="text" class="nom form-control" value="" name="nom'+axe+OF+'[]" size=6>';
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = '<input type="text" class="init_freq form-control" value="0" name="init_freq'+axe+OF+'[]" size=6>';
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = '<input type="text" class="fin_freq form-control" value="0" name="fin_freq'+axe+OF+'[]" size=6>';
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = '<input type="text" class="shift_freq form-control" value="0" name="shift_freq'+axe+OF+'[]" size=6>';
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = '<input type="text" class="init_ampl form-control" value="0" name="init_ampl'+axe+OF+'[]" size=6>';
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = '<input type="text" class="fin_ampl form-control" value="0" name="fin_ampl'+axe+OF+'[]" size=6">';
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = '<input type="text" class="shift_ampl form-control" value="0" name="shift_ampl'+axe+OF+'[]" size=6>';
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = "<img class='imgSupp'  SRC='../img/supr.png' class='btnSuppLigne' onclick='suppLigne(this)' />";

	$(".init_freq").blur(function (e){
		
		if (e.target == this){
			console.log("ici");
			console.log($(this).parent().next().next().next().children().val());
			var res = Math.round(Math.abs(($(this).parent().next().children().val() - $(this).val()) / $(this).parent().next().next().children().val())*1000)/10;
			if (!isNaN(res) && res != 'Infinity'){
				
				$(this).parent().next().next().children().val(res + " %");
			}else{
				
				$(this).parent().next().next().children().val ('Erreur');
			}
		}

	});
	$('.fin_freq').blur(function (e){
		
		if (e.target == this){

			var res = Math.round(Math.abs(($(this).val() - $(this).parent().prev().children().val()) / $(this).val())*1000)/10;
			if (!isNaN(res) && res != 'Infinity'){
				
				$(this).parent().next().children().val(res + " %");
			}else
			{
				
				$(this).parent().next().children().val ('Erreur');
			}
		}
		
	});

	$('.init_ampl').blur(function (e){
		
		if (e.target == this){
			console.log("ici");

			var res = Math.round(Math.abs(($(this).parent().next().children().val() - $(this).val()) / $(this).parent().next().next().children().val())*1000)/10;
			if (!isNaN(res) && res != 'Infinity'){
				
				$(this).parent().next().next().children().val(res + " %");
			}else{
				
				$(this).parent().next().next().children().val ('Erreur');
			}
		}

	});
	$('.fin_ampl').blur(function (e){
		
		if (e.target == this){
			console.log("ici");
			var res = Math.round(Math.abs(($(this).val() - $(this).parent().prev().children().val()) / $(this).val())*1000)/10;
			if (!isNaN(res) && res != 'Infinity'){
				
				$(this).parent().next().children().val(res + " %");
			}else{
				
				$(this).parent().next().children().val ('Erreur');
			}
		}
		
	});
	$(newCell).children().click(function() {
		console.log("ici");
		console.log($(this).parent().parent().children().val());
		suppLigne(this);
	});

	
}


function suppLigne(elem)
{
	console.log($(elem).parent().parent());
	console.log($(elem).parent().parent().children().first());
	console.log($(elem).parent().parent().children().first().children().val());
	var value = $(elem).parent().parent().children().first().children().val();
	$(elem).parent().parent().siblings().each (function (num){
		
		if (parseInt($(this).children().first().children().val()) > parseInt(value)){
			
			$(this).children().first().children().val(parseInt($(this).children().first().children().val())-1);
		}
		
	});
	
	$(elem).parent().parent().remove();
}

function newOF (ofs1){
	
	console.log("ofs1");
	console.log(ofs1)
	var tabOF = ofs1.split('-');
	for (var t =0; t< tabOF.length; t++){
		
		var OF = tabOF[t];
		console.log(OF);
		if($("#"+ofs1).is(':checked')){
			console.log("ici");
			$("."+OF).parent().remove();
			var clone = $('#new').clone();
			console.log(clone);
			clone.empty();
			clone.html(" <div><h4 class="+OF+"  >OF concerné : "+OF+" </h4><div class='jumbotron'><div class='row'><div class='col-md-1' ><h4 style='display:inline'>Axe X </h4></div><input type='checkbox' class=anomalieX "+OF+" value='1' name='anomalie' /><div style='display:none' id=anomX"+OF+" class='jumbotron'><table class='table table-striped'><thead><tr><th >Spécifique</th><th>Nom du capteur</th><th>Fréquence initiale</th><th >Fréquence finale</th><th >Décalage sur la fréquence</th><th >Amplitude initiale</th><th >Amplitude finale</th><th >Décalage sur l'amplitude</th><th>Supprimer</th></tr></thead><tbody id=tabCaptX"+OF+"><tr><td><input type='checkbox' class = '0' id='spec' value='0' name=specX"+OF+"[] size=12></td><td><input type='text' class='nom' value='' name=nomY"+OF+"[] size=6></td><td><input type='text' class='init_freq' value='0' name=init_freqX"+OF+"[]' size=12></td><td><input type='text' class='fin_freq' value='0' name=fin_freqX"+OF+"[] size=12></td><td><input type='text' class='shift_freq' value='0' name=shift_freqX"+OF+"[] size=12></td><td><input type='text' class='init_ampl' value='0' name=init_amplX"+OF+"[] size=12></td><td><input type='text' class='fin_ampl' value='0' name=fin_amplX"+OF+"[] size=12'></td><td><input type='text' class='shift_ampl' value='0' name=shift_amplX"+OF+"[] size=12></td><td><img class='imgSupp'  SRC='../img/supr.png' class='btnSuppLigne' onclick='suppLigne(this)' /></td></tr></tbody></table><center><input type='button' class='btn  btn-primary' value='Ajouter un capteur' onclick=ajout_capt('X','"+OF+"') /></center></div></div><div class='row'><div class='col-md-1' ><h4 style='display:inline'>Axe Y </h4></div><input type='checkbox' class=anomalieY "+OF+" value='1' name='anomalie'><div style='display:none;' id=anomY"+OF+" class='jumbotron'><table class='table table-striped'><thead><tr><th>Spécifique</th><th >Nom du capteur</th><th >Fréquence initiale</th><th >Fréquence finale</th><th >Décalage sur la fréquence</th><th >Amplitude initiale</th><th >Amplitude finale</th><th>Décalage sur l'amplitude</th><th>Supprimer</th></tr></head><tbody id=tabCaptY"+OF+"><tr><td><input type='checkbox' class = '0' id='spec' value='0' name=specY"+OF+"[] size=12></td><td><input type='text' class='nom' value='' name=nomY"+OF+"[] size=6></td><td><input type='text' class='init_freq' value='0' name=init_freqY"+OF+"[] size=12></td><td><input type='text' class='fin_freq' value='0' name=fin_freqY"+OF+"[] size=12></td><td><input type='text' class='shift_freq' value='0' name=shift_freqY"+OF+"[] size=12></td><td><input type='text' class='init_ampl' value='0' name=init_amplY"+OF+"[] size=12></td><td><input type='text' class='fin_ampl' value='0' name=fin_amplY"+OF+" size=12'></td><td><input type='text' class='shift_ampl' value='0' name=shift_amplY"+OF+"[] size=12></td><td><img class='imgSupp'  SRC='../img/supr.png' class='btnSuppLigne' onclick='suppLigne(this)' /></td></tr></tbody></table><center><input type='button' class='btn  btn-primary' value='Ajouter un capteur' onclick=ajout_capt('Y','"+OF+"') /></center></div></div><div class='row'><div class='col-md-1' ><h4 style='display:inline'>Axe Z </h4></div><input type='checkbox' class=anomalieZ "+OF+" value='1' name='anomalie'><div  style='display:none' id=anomZ"+OF+" class='jumbotron'><table class='table table-striped'><thead><tr><th >Spécifique</th><th>Nom du capteur</th><th>Fréquence initiale</th><th >Fréquence finale</th><th >Décalage sur la fréquence</th><th >Amplitude initiale</th><th >Amplitude finale</th><th >Décalage sur l'amplitude</th><th>Supprimer</th></tr></thead><tbody id=tabCaptZ"+OF+"><tr><td><input type='checkbox' class = '0' id='spec' value='0' name=specZ"+OF+"  size=12></td><td><input type='text' class='nom' value='' name=nomY"+OF+"[] size=6></td><td><input type='text' class='init_freq' value='0' name=init_freqZ"+OF+" size=12></td><td><input type='text' class='fin_freq' value='0' name=fin_freqZ"+OF+" size=12></td><td><input type='text' class='shift_freq' value='0' name=shift_freqZ"+OF+" size=12></td><td><input type='text' class='init_ampl' value='0' name=init_amplZ"+OF+" size=12></td><td><input type='text' class='fin_ampl' value='0' name=fin_amplZ"+OF+" size=12'></td><td><input type='text' class='shift_ampl' value='0' name=shift_amplZ"+OF+" size=12></td><td><img class='imgSupp'  SRC='../img/supr.png' class='btnSuppLigne' onclick='suppLigne(this)' /></td></tr></tbody></table><center><input type='button' class='btn  btn-primary' value='Ajouter un capteur' onclick=ajout_capt('Z','"+OF+"') /></center></div></div></div>");

			console.log($('#contain').children().last());
			console.log(clone.insertBefore($('#contain').children().last()));
			clone.insertBefore($('#contain').children().first());
			console.log(OF);
			$("."+OF).parent().show();
			var ides = $("#idEssai").val ();
			ides += "-"+$("#"+ofs1).parent().attr('id');
			$("#idEssai").val (ides);
			console.log(ides);
			var ofs = $(".of").attr('id');
			ofs += " "+OF;
			$(".of").attr('id', ofs);
			console.log("critere.php?of="+ofs+"&idEssai="+ides+"");
			document.forms['form'].action = "critere.php?of="+ofs+"&idEssai="+ides+"";
			$(".anomalieX").change(function(){
				
				id = $(this).attr('class');
				console.log("ici");
				if($(this).is(':checked')){
					
					
					$(this).next().show();
				
					
				}else{
					
					$(this).next().hide();
					
				}
				
			});
			$(".anomalieY").change(function(){
						
				id = $(this).attr('class');
				if($(this).is(':checked')){
					
					$(this).next().show();
					
					
				}else{
					
					$(this).next().hide();
					
				}
				
			});
			$(".anomalieZ").change(function(){
					
				id = $(this).attr('class');
				if($(this).is(':checked')){
					
					console.log($(this).parent().next());
					$(this).next().show();
					
					
				}else{
					
					$(this).next().hide();
					
				}
				
			});

			$(".init_freq").blur(function (e){
					
				if (e.target == this){
					console.log("ici");
					console.log($(this).parent().next().next().next().children().val());
					var res = Math.round(Math.abs(($(this).parent().next().children().val() - $(this).val()) / $(this).parent().next().next().children().val())*1000)/10;
					if (!isNaN(res) && res != 'Infinity'){
						
						$(this).parent().next().next().children().val(res + " %");
					}else{
						
						$(this).parent().next().next().children().val ('Erreur');
					}
				}

			});
			$('.fin_freq').blur(function (e){
				
				if (e.target == this){
					console.log("ici");

					var res = Math.round(Math.abs(($(this).val() - $(this).parent().prev().children().val()) / $(this).val())*1000)/10;
					if (!isNaN(res) && res != 'Infinity'){
						
						$(this).parent().next().children().val(res + " %");
					}else{
						
						$(this).parent().next().children().val ('Erreur');
					}
				}
				
			});

			$('.init_ampl').blur(function (e){
				
				if (e.target == this){
					console.log("ici");

					var res = Math.round(Math.abs(($(this).parent().next().children().val() - $(this).val()) / $(this).parent().next().next().children().val())*1000)/10;
					if (!isNaN(res) && res != 'Infinity'){
						
						$(this).parent().next().next().children().val(res + " %");
					}else{
						
						$(this).parent().next().next().children().val ('Erreur');
					}
				}

			});
			$('.fin_ampl').blur(function (e){
				
				if (e.target == this){
					console.log("ici");
					var res = Math.round(Math.abs(($(this).val() - $(this).parent().prev().children().val()) / $(this).val())*1000)/10;
					if (!isNaN(res) && res != 'Infinity'){
						
						$(this).parent().next().children().val(res + " %");
					}else{
						
						$(this).parent().next().children().val ('Erreur');
					}
				}
				
			});
		}else{
			console.log("ici");
			var ides = $("#idEssai").val ();
			console.log(ides);

			ides = ides.split("-");
			ofs1 = $("#"+ofs1).parent().attr('id');
			console.log(ofs1);
			var retire1 = "";
			for (var i =0; i<ides.length;i++){
				
				console.log(ides);
				console.log(ofs1);
				if (ides[i]!=ofs1){
					
					retire1 += ides[i]+"-";
				}
			}
			retire1 = retire1.substring(0, retire1.length-1);
			$("#idEssai").val (retire1);
			console.log("retire");
			console.log(retire1);
			
			var ofs = $(".of").attr('id');
			ofs = ofs.split(" ");
			var retire2 = "";
			for (var i =0; i<ofs.length;i++){
				
				if (ofs[i]!=ofs1){
					
					retire2 += ofs[i]+" ";
				}
			}
			retire2 = retire2.substring(0, retire2.length-1);
			$(".of").attr('id', retire2);
			document.forms['form'].action = "critere.php?of="+retire2+"&idEssai="+retire1+"";
			$("."+OF).parent().hide();
		}
	}
	
}