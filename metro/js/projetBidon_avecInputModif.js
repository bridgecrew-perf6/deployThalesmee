function ajouterModifierCapteur(ligneTab,data)
{
	//remplace les null par une chaine vide
	for(var i in data) {
	   if(data[i]===null){data[i]="";}
	}
	var typeCapt; //0-->normal, 1 --> force
	var newRow;
	
	//ajout de la ligne au tableau desiré
	//ici on check le type de capteur avec le nom, ce n'est pas optimal, mais c'est la seule façon perenne de le faire, avec les contraintes de TAS
	// --> en cas de modification de base de données, la ligne dans la bdd n'aurait plus le meme id
	if(data["nomDes"] === "CAPTEUR DE FORCE")
	{
		typeCapt=1;
		//si modification d'une ligne existante
		if(ligneTab!=null)
		{
			//on verifie que l'ancienne ligne était deja dans le bon tableau
			if(ligneTab.parentNode==document.getElementById('tabCaptForce'))
			{
				//si oui on modifie la ligne actuel
				newRow=ligneTab;
				newRow.innerHTML="";
			}
			else
			{
				//sinon suppression de ligne est ajout de la nouvelle ligne dans le bon tableau
				ligneTab.parentNode.removeChild(ligneTab);
				newRow = document.getElementById('tabCaptForce').insertRow(-1);
			}
		}
		else //sinon ajout d'une nouvelle ligne
			newRow = document.getElementById('tabCaptForce').insertRow(-1);	
	}
	else
	{
		typeCapt=0;
		//si modification d'une ligne existante
		if(ligneTab!=null)
		{
			//on verifie que l'ancienne ligne était deja dans le bon tableau
			if(ligneTab.parentNode==document.getElementById('tabCapt'))
			{
				//si oui on modifie la ligne actuel
				newRow=ligneTab;
				newRow.innerHTML="";
			}
			else
			{
				//sinon suppression de ligne est ajout de la nouvelle ligne dans le bon tableau
				ligneTab.parentNode.removeChild(ligneTab);
				newRow = document.getElementById('tabCapt').insertRow(-1);
			}
		}
		else //sinon ajout d'une nouvelle ligne
			newRow = document.getElementById('tabCapt').insertRow(-1);
	}

	
	newRow.id=data["numInstru"];
	var newCell = newRow.insertCell(-1);
	newCell.innerHTML =data["nomDes"];
	if(typeCapt==0) //si capteur simple : name = numInstru
		newCell.innerHTML ="<input class='form-control' type='text' placeholder='N°' value='"+data["numInstru"]+"' name='numInstru[]' />";
	else //sinon, capteur force : name = numInstruForce
		newCell.innerHTML ="<input class='form-control' type='text' placeholder='N°' value='"+data["numInstru"]+"' name='numInstruForce[]' />";
	ajoutEventModifNumCpat(newCell);
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML =data["nomDes"];

	newCell = newRow.insertCell(-1);
	newCell.innerHTML =data["nomStatut"];
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML =data["marque"];
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML =data["modele"];
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML =data["numSerie"];
	
	//conversion de la date format fr
	var date=data["date_futureInt"].split("-"); 
	var dateVal=date[2]+"/"+date[1]+"/"+date[0];
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = dateVal;
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = data["nomLocal"];
	
	//si c'est un capteur de force, on ajoute une liste pour selectionné le pied
	if(typeCapt==1)
	{
		newCell = newRow.insertCell(-1);

		//Create and append select list
		var selectList = document.createElement("select");
		selectList.name="sPied[]";
		newCell.appendChild(selectList);

		//Create and append the options
		for (var i = 1; i < 21; i++) {
			var option = document.createElement("option");
			option.value = i;
			option.text = i;
			if(data["pied"]!="" && data["pied"]==i)
				option.selected='selected';
			selectList.appendChild(option);
		}
	}
	
	//cellule pour supprimer la ligne
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = "<img class='imgSupp'  SRC='../img/supr.png' class='btnSuppLigne'/>";
	//ajout de l'evenement onclick
	$(newCell).children().click(function() {
		suppLigne(data["numInstru"]);
	});
	
	//si deja présent dans sur un autre pot on avertie l'utilisateur
	if(data["dejaUtil"])
	{
		var child= document.createElement("div");
		child.className="text-center";
		child.innerHTML="<div class='alert alert-warning'><strong>Capteur n°"+data["numInstru"]+" déja utilisé par un autre pot, le valider ici le supprimera de l'autre pot </strong><input type='button' class='btn btn-warning' onclick='suppAlerte(this)' value='Ok' /></div>";
		document.body.insertBefore(child, document.body.firstChild);
		window.scrollTo(0,0);
	}
	
	
}

function detailCapteur(data) //fonction pour détailsCapteur, remplie le tableau en positionnant les capteurs en fonction de leurs type
{
	//remplace les null par une chaine vide
	for(var i in data) {
	   if(data[i]===null){data[i]="";}
	}
	var typeCapt; //0-->normal, 1 --> force
	var newRow;
	
	//ajout de la ligne au tableau desiré
	//ici on check le type de capteur avec le nom, ce n'est pas optimal, mais c'est la seule façon perenne de le faire, avec les contraintes de TAS
	// --> en cas de modification de base de données, la ligne dans la bdd n'aurait plus le meme id
	if(data["nomDes"] === "CAPTEUR DE FORCE")
	{
		typeCapt=1;
		newRow = document.getElementById('tabCaptForce').insertRow(-1);	
	}
	else
	{
		typeCapt=0;
		newRow = document.getElementById('tabCapt').insertRow(-1);
	}

	
	newRow.id=data["numInstru"];
	var newCell = newRow.insertCell(-1);
	newCell.innerHTML =data["numInstru"];
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML =data["nomDes"];

	newCell = newRow.insertCell(-1);
	newCell.innerHTML =data["nomStatut"];
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML =data["marque"];
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML =data["modele"];
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML =data["numSerie"];
	
	//conversion de la date format fr
	var date=data["date_futureInt"].split("-"); 
	var dateVal=date[2]+"/"+date[1]+"/"+date[0];
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = dateVal;
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = data["nomLocal"];
	if(typeCapt==1) //si c'est un capteur de force, on affiche le pied
	{
		newCell = newRow.insertCell(-1);
		newCell.innerHTML = data["pied"];
	}
}

//fonction qui sert à ajouter des listener sur les input des n° 'instruments
function ajoutEventModifNumCpat(cell)
{
	//evenement avec touche entrée
	$(cell).children().keypress(function(touche){ // ecoute l'evenement keyPres
		var codeTouche = touche.which || touche.keyCode; // le code est compatible tous navigateurs grâce à ces deux propriétés	
		if(codeTouche==13 && $(this).val()!="") //enter 
		{
			if((cell.parentNode.id != $(this).val())) //verification qu'on a pas retaper la meme chose
				lancerRecherche(cell.parentNode,$(this).val());
		}
	});
	//evenement si perte du focus
	$(cell).children().focusout(function() {
		if((cell.parentNode.id != $(this).val())) //verification qu'on a pas retaper la meme chose
			lancerRecherche(cell.parentNode,$(this).val());
	});
}

function suppLigne(num)
{
	//if(confirm("Voulez vous vraiment supprimer l'instrument n°"+num+" de cette liste ?"))
	//{
		var element = document.getElementById(num);
		element.parentNode.removeChild(element);
	//}
}

//supprimer les messages d'alerte
function suppAlerte(elem)
{
	var element =  elem.parentNode.parentNode;
	element.parentNode.removeChild(element);
}

function lancerRecherche(ligneTab,num)
{
	//test si le numero entré n'est pas déja dans la liste
	if(document.getElementById(num)==null)
	{
		var url="./ajaxBidon.php?num="+num;
		$.getJSON( url, function(data) {
				ajouterModifierCapteur(ligneTab,data);
		})
		.fail(function() {
			var child= document.createElement("div");
			child.className="text-center";
			child.innerHTML="<div class='alert alert-danger'><strong>Capteur n°"+num+" inconnue </strong><input type='button' class='btn btn-danger' onclick='suppAlerte(this)' value='Ok' /></div>";
			document.body.insertBefore(child, document.body.firstChild);
			window.scrollTo(0,0);
			
		});
	}
	else
	{
		var child= document.createElement("div");
			child.className="text-center";
			child.innerHTML="<div class='alert alert-warning'><strong>Instrument n°"+num+" déja présent dans la liste </strong><input type='button' class='btn btn-danger' onclick='suppAlerte(this)' value='Ok' /></div>";
			document.body.insertBefore(child, document.body.firstChild);
			window.scrollTo(0,0);	
	}
}

function ajoutNouvelleLigne()
{
	$('#dialog_t').dialog('open');
}


$(document).ready(function() {
	if($("#dialog_t").length) //test si l'element existe, si sur page creerModifierBidon passe, sinon non
	{
		$('#dialog_t').hide(); //on cache la boite de dialog
		var num="";
		var numAlert=0;
		$(document).keypress(function(touche){ // ecoute l'evenement keyPress
			var tag = touche.target.tagName.toLowerCase(); //on recupere le focus actuel
			//on test que ce n'est pas un input ou un textarea et que la fenetre de dialog n'est pas visible
			if (tag != 'input' && tag != 'textarea' && !($("#dialog_t").is(":visible")))   
			{
				var codeTouche = touche.which || touche.keyCode; // le code est compatible tous navigateurs grâce à ces deux propriétés	
				if (codeTouche!=116 && codeTouche!=27) //f5 && echap
				{
					if(codeTouche==13 && num!="") //enter 
					{
						lancerRecherche(null,num);
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
		
		$('#addL').click(function() {
			ajoutNouvelleLigne();
		});
		$('#btnValid').click(function() {
			$("#form").submit();
		});
		
		//desactive la validation du formulaire si l'on presse enter dans un input
		$('#form').on('keyup keypress', function(e) {
		  var keyCode = e.keyCode || e.which;
		  if (keyCode === 13) { 
			e.preventDefault();
			return false;
		  }
		});
		
		//fonction pour fixer la taille des cellules du tableau		
		function fixWidthHelper(e, ui) {
			ui.children().each(function() {
				$(this).width($(this).width());
			});
			return ui;
		}
		
		//ajout du sortable de jquery-ui
		$('table tbody').sortable({
			tolerance: "pointer",
			cancel: ".fixed,input",
			axis: "y",
			opacity: 0.5,
			start: function(e, ui){
				$(ui.placeholder).hide(300);
			},
			change: function (e,ui){
				$(ui.placeholder).hide().show(300);
			},
			helper: fixWidthHelper
		}).disableSelection();
		
		
		
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
						lancerRecherche(null,$('#nouvNumCapt').val());
						$('#nouvNumCapt').val("");
						$("#dialog_t").dialog( "close" );	
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
	}
});
