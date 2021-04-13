function ecrireLigneOF(noOf,nomModele,nb, article){
	//ajoute une ligne et gere le style des div (la derniere ligne de div ne doit pas avoir le style margin-bottom:15px present dans la classe info)
	var col,ancien;
	if(nb%2==0){
		col=document.getElementById('col0');
		if(nb!=0){
			ancien=document.querySelectorAll('.non'); //ie8 ne supporte pas getElementsByClassName, on utilise querySelectorAll à la place
			ancien[1].setAttribute("class","info");
			ancien[0].setAttribute("class","info");
		}
	}
	else
		col=document.getElementById('col1');
	
	col.innerHTML+="<div class='non'><label>N°OF:&nbsp </label>"+noOf+"<label style='margin-left:15px' >Modèle:&nbsp </label>"+nomModele+"<label style='margin-left:15px' >Article:&nbsp </label>"+article+"</div>";

}
function confirmLancerEssaiSansMoyenSansNom(id)
{
	var idMoyen=$('#moy option:selected').val();
	var nom = $('input[name=tech]:checked').val();
	if (!nom){
		
		nom = "Non renseigné";
	}
	document.location.href= 'etapeSuivante.php?idEssai='+id+'&nom='+nom+'&idMoyen='+idMoyen+'';

}

function confirmLancerEssaiSansMoyen(id)
{
	var idMoyen=$('#moy option:selected').val();
	if (!nom){
		
		nom = "Non renseigné";
	}
	document.location.href='etapeSuivante.php?idEssai='+id+'&idMoyen='+idMoyen+'';
}

function confirmLancerEssaiSansNom(id)
{
	var nom = $('input[name=tech]:checked').val();
	if (!nom){
		
		nom = "Non renseigné";
	}
	document.location.href= 'etapeSuivante.php?idEssai='+id+'&nom='+nom+'&idMoyen=0';
}

function valdEtape(idEtat,selectMoyen,idEssai)
{

	if(idEtat==21)
	{
		if (labo == 2 || labo == 3)
			checkListVibVth(idEssai);
		else 
			checkListEmc(idEssai);
			
		
	}
	else if(selectMoyen || idEtat == 22)
		afficheRemarque (idEtat,selectMoyen,idEssai)

	else if(confirm("Valider cette étape ?"))
		document.location.href='etapeSuivante.php?idEssai='+idEssai+'&idMoyen=0';
		
}

function afficheRemarque(idEtat,selectMoyen,idEssai)
{
	if (document.getElementById("remarque")){
		
		
		$("#remarque").dialog({ 
			autoOpen: false,
			height: 350,
			width: 375,
			modal: true,
			buttons: {
				"Ok": function() {
					if(selectMoyen && idEtat == 22)
						choixNomEtMoyen(idEssai);	
					else if (selectMoyen){
						
						lancerEssai(idEssai);
					}
					else if(idEtat == 22)
						choixNom(idEssai);
					
				},
				"Annuler": function() {
					$( this ).dialog( "close" );
				}
			}
		});
		$(".ui-dialog button").addClass('ui-button ui-corner-all ui-widget');
		$(".ui-dialog").css("border", "2px solid red");
		$('#remarque').dialog('open');
		
	}else {
		
		if(selectMoyen && idEtat == 22)
			choixNomEtMoyen(idEssai);	
		else if (selectMoyen){
			
			lancerEssai(idEssai);
		}
		else if(idEtat == 22)
			choixNom(idEssai);
	}
	
}

function checkListVibVth(idEssai){
	$("#dialogCheckList").dialog({ 
		autoOpen: false,
		height: 350,
		width: 375,
		modal: true,
		buttons: {
			"Valider": function() {
				if ($("input[name=fiche]").is(":checked") && $("input[name=ofs]").is(":checked") && $("input[name=savers]").is(":checked")){
					choixFIFO(idEssai);
				}else{
					$("#alert").show();
				}
				
			},
			"Annuler": function() {
				$( this ).dialog( "close" );
			}
		}
	});
	$(".ui-dialog button").addClass('ui-button ui-corner-all ui-widget');
	$('#dialogCheckList').dialog('open');

}

function checkListEmc(idEssai){
	$("#dialogCheckList").dialog({ 
		autoOpen: false,
		height: 350,
		width: 375,
		modal: true,
		buttons: {
			"Valider": function() {
				if ($("input[name=fiche]").is(":checked") && $("input[name=ofs]").is(":checked") ){
					choixFIFO(idEssai);
				}else{
					$("#alert").show();
				}
				
			},
			"Annuler": function() {
				$( this ).dialog( "close" );
			}
		}
	});
	$(".ui-dialog button").addClass('ui-button ui-corner-all ui-widget');
	$('#dialogCheckList').dialog('open');

}

function lancerEssai(id)
{
	$("#dialog4").dialog({ 
		autoOpen: false,
		height: 300,
		width: 350,
		modal: true,
		buttons: {
			"Valider": function() {
				confirmLancerEssaiSansMoyen(id)
				this.addClass('btn btn-lg btn-primary');
			},
			"Annuler": function() {
				$( this ).dialog( "close" );
			}
		}
	});
	$(".ui-dialog button").addClass('ui-button ui-corner-all ui-widget');
	$('#dialog4').dialog('open');
}

function choixFIFO(idEssai)
{
	$("#dialog3").dialog({ 
		autoOpen: false,
		height: 200,
		width: 350,
		modal: true,
		buttons: {
			"Oui": function() {
				document.location.href='etapeSuivante.php?idEssai='+idEssai+'&fifo=1';				
			},
			"Non": function() {
				document.location.href='etapeSuivante.php?idEssai='+idEssai+'&fifo=0';
			},
			"Annuler": function() {
				$( this ).dialog( "close" );
			}
		}
	});
	$(".ui-dialog button").addClass('ui-button ui-corner-all ui-widget');
	$('#dialog3').dialog('open');
}

function choixNomEtMoyen(idEssai)
{	
	var taille = $(window).width() - 100;
	$("#dialog").dialog({ 
		autoOpen: false,
		height: 500 ,
		width: taille,
		modal: true,
		buttons: {
			"Valider": function() {
				confirmLancerEssaiSansMoyenSansNom(idEssai);
				this.addClass('btn btn-lg btn-primary');
							
			},

			"Annuler": function() {
				$( this ).dialog( "close" );
			}
		}
	});
	

	$(".ui-dialog button").addClass('ui-button ui-corner-all ui-widget');
	$('#dialog').dialog('open');
	
}

function choixNom(idEssai)
{	
	var taille = $(window).width() - 100;

	$("#dialog6").dialog({ 
		autoOpen: false,
		height: 500,
		width: taille,
		buttons: {
			"Annuler": function() {
				$( this ).dialog( "close" );
			}
		}
	});
	
	$('input[name=tech]').change(function(){
	
		confirmLancerEssaiSansNom(idEssai);

	});

	$(".ui-dialog button").addClass('ui-button ui-corner-all ui-widget');
	$('#dialog6').dialog('open');
	
}

function confirmSupp()
{
	if(confirm("Voulez vous vraiment supprimer cet essai ?"))
		return true;
	return false;
}

function raisonSuppr()
{
	$("#dialog2").dialog({ 
		autoOpen: false,
		height: 200,
		width: 350,
		modal: true,
		buttons: {
			"Valider": function() {
				if($("#raison").val()!="")
				{
					$("#sendRaison").val($("#raison").val());
					$("#suppr").submit();
				}
				else
					alert("Veuillez indiquer une raison");
				
			},
			"Annuler": function() {
				$( this ).dialog( "close" );
			}
		}
	});
	$(".ui-dialog button").addClass('ui-button ui-corner-all ui-widget');
	$('#dialog2').dialog('open');
}


		


