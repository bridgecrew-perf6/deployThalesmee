function ajouterInstru(data)
{
	var datePret,dateRetour;
	//remplace les null par une chaine vide
	for(var i in data) {
	   if(data[i]===null){data[i]="";}
	}

	//format des dates
	if(data["datePret"]==null)
		datePret=document.getElementById('pretGen').value;
	else
	{
		datePret=data["datePret"].split("-"); 
		datePret=datePret[2]+"/"+datePret[1]+"/"+datePret[0];
	}
	if(data["dateRetour"]==null)
		dateRetour=document.getElementById('retourGen').value;
	else
	{
		dateRetour=data["dateRetour"].split("-"); 
		dateRetour=dateRetour[2]+"/"+dateRetour[1]+"/"+dateRetour[0];
	}
	
	var newRow = document.getElementById('tabBody').insertRow(-1); 
	newRow.id=data["numInstru"];
	var newCell = newRow.insertCell(-1);
	newCell.innerHTML ="<input type='hidden' value='"+data["numInstru"]+"' name='numInstru[]' />"+data["numInstru"];
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML =data["nomDes"];
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML =data["marque"];
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML =data["modele"];
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML =data["numSerie"];
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML =data["nextEtal"];
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML ="<input placeholder='Date de sortie' type='text' value='"+datePret+"' name='dateP[]' class='calendrier form-control' size='8' required/>";
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML ="<input placeholder='Date de retour' type='text' value='"+dateRetour+"' name='dateR[]' class='calendrier form-control' size='8' required/>";
	
	//cellule pour supprimer la ligne
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = "<img class='imgSupp'  SRC='../img/supr.png' class='btnSuppLigne'/>";
	//ajout de l'evenement onclick
	$(newCell).children().click(function() {
		suppLigne(data["numInstru"]);
	});
}

function ajouterInstruEmc(data)
{
	var datePret,dateRetour;
	//remplace les null par une chaine vide
	for(var i in data) {
	   if(data[i]===null){data[i]="";}
	}

	//format des dates
	if(data["datePret"]==null)
		datePret=document.getElementById('pretGen').value;
	else
	{
		datePret=data["datePret"].split("-"); 
		datePret=datePret[2]+"/"+datePret[1]+"/"+datePret[0];
	}
	if(data["dateRetour"]==null)
		dateRetour=document.getElementById('retourGen').value;
	else
	{
		dateRetour=data["dateRetour"].split("-"); 
		dateRetour=dateRetour[2]+"/"+dateRetour[1]+"/"+dateRetour[0];
	}
	
	var newRow = document.getElementById('tabBody').insertRow(-1); 
	newRow.id=data["numInstru"];
	var newCell = newRow.insertCell(-1);
	newCell.innerHTML ="<input type='hidden' value='"+data["numInstru"]+"' name='numInstru[]' />"+data["numInstru"];
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML =data["fonction"];
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML =data["modele"];
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML =data["marque"];
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML ="<input placeholder='Date de sortie' type='text' value='"+datePret+"' name='dateP[]' class='calendrier form-control' size='8' required/>";
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML ="<input placeholder='Date de retour' type='text' value='"+dateRetour+"' name='dateR[]' class='calendrier form-control' size='8' required/>";
	
	//cellule pour supprimer la ligne
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = "<img class='imgSupp'  SRC='../img/supr.png' class='btnSuppLigne'/>";
	//ajout de l'evenement onclick
	$(newCell).children().click(function() {
		suppLigne(data["numInstru"]);
	});
}

function ajoutNouvelleLigne()
{
	$('#dialog_t').dialog('open');
}
//supprimer les messages d'alerte
function suppAlerte(elem)
{
	var element =  elem.parentNode.parentNode;
	element.parentNode.removeChild(element);
}

function suppLigne(num)
{
	//if(confirm("Voulez vous vraiment supprimer l'instrument n°"+num+" de cette liste ?"))
	//{
		var element = document.getElementById(num);
		console.log(element.id);
		var elt = element.id;
		element.parentNode.removeChild(element);
		
		$.get("./supprimerSortie.php?idEssai="+elt, function(data){
		})
	//}
}
function lancerRecherche(num)
{
	var url="./ajaxInfoPret.php?num="+num;
	jQuery.getJSON( url, function(data) {
		//test si le numero entré n'est pas déja dans la liste
		if(document.getElementById(data["numInstru"])==null)
		{
			if(data["fonction"])
				ajouterInstruEmc(data);
			else
				ajouterInstru(data);
		}
		else
		{
			var child= document.createElement("div");
			child.className="text-center";
			child.innerHTML="<div class='alert alert-warning'><strong>Instrument n°"+data["numInstru"]+" déja présent dans la liste </strong><input type='button' class='btn btn-warning' onclick='suppAlerte(this)' value='Ok' /></div>";
			document.body.insertBefore(child, document.body.firstChild);
			window.scrollTo(0,0);	
		}
	})
	.fail(function() {
		var child= document.createElement("div");
		child.className="text-center";
		child.innerHTML="<div class='alert alert-danger'><strong>Instrument n°"+num+" inconnue </strong><input type='button' class='btn btn-danger' onclick='suppAlerte(this)' value='Ok' /></div>";
		document.body.insertBefore(child, document.body.firstChild);
		window.scrollTo(0,0);
	});
}
$(document).ready(function() {
	$('#dialog_t').hide(); //on cache la boite de dialog
	
	if($("#typeES").val()==0)//si calib
	{
		$("#loca").hide();//on cache la localisation -> par defaut calibration
	}
	else//sinon
	{
		$("#loca").show(); //on demande la loc
		$("#loca").attr('required', '');
	}
	
	var num="";
	jQuery(document).keypress(function(touche){ // ecoute l'evenement keyPress
		var tag = touche.target.tagName.toLowerCase(); //on recupere le focus actuel
		if (tag != 'input' && tag != 'textarea')  //on test que ce n'est pas un input ou un textarea
		{
			var codeTouche = touche.which || touche.keyCode; // le code est compatible tous navigateurs grâce à ces deux propriétés	
			if (codeTouche!=116 && codeTouche!=27) //f5 && echap
			{
				if(codeTouche==13 && num!="") //enter 
				{
					lancerRecherche(num);
					num="";	
				}
				else
				{
					var lettre=String.fromCharCode(codeTouche);
					num+=lettre;
				}
			}
		}
	});
	
	$("#typeES").on('change',function() {
		if($(this).val()==0)
		{
			$("#loca").hide();
			$("#loca").removeAttr('required');
		}
		else
		{
			$("#loca").show();
			$("#loca").attr('required', '');
		}
	});
	
	//desactive la validation du formulaire si l'on presse enter dans un input
	$('#form').on('keyup keypress', function(e) {
	  var keyCode = e.keyCode || e.which;
	  if (keyCode === 13) { 
		e.preventDefault();
		return false;
	  }
	});
	
	jQuery('#addL').click(function() {
		ajoutNouvelleLigne();
	});
	
	//construit la boite de dialogue
	$("#dialog_t").dialog({ 
		autoOpen: false,
		width: 400,
		modal: true,
		buttons: {
			//clic sur valider lance la recherche
			"Valider": function() {
				$("#formDialog").submit();
				if($('#nouvNumCapt').val()!="")
				{	
					lancerRecherche($('#nouvNumCapt').val());
					$('#nouvNumCapt').val("");	
				}
			},
			"Annuler": function() {
				$('#nouvNumCapt').val("");
				$( this ).dialog( "close" );
			}
		},
		open: function() {
			//appuyer sur entrée simule un clic sur le bouton valider --> meme actions
			$(this).keypress(function(e) {
			  if (e.keyCode == $.ui.keyCode.ENTER) {
				$("#dialog_t").parent().find("button:eq(1)").trigger("click");
			  }
			});
		  }
	});
});