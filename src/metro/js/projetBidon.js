function init(){
	
	var capteur = "";
	for (i=0;i<($(".val").length);i++){
		
		if ($(".val")[i].id != ""){
			
			capteur += ($(".val")[i].id)+',';
		}
		
		
	}
	var url='saveOrder.php?capteur='+capteur.substring(0, capteur.length-1);
	$.get( url, function(data) {
		console.log("ici");
		console.log(data);
	});
}

function ajouterModifierCapteur(data)
{	
	console.log(data);
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
	
	newRow.id= data["numInstru"].replace("/","");
	newRow.className="val";
	
	$("#"+data["numInstru"].replace("/","")).dblclick(function(){
		
		document.location.href= "modifInstru.php?numInstru="+data["numInstru"];		
	});
	
	var newCell = newRow.insertCell(-1);
	newCell.innerHTML =data["nomDes"];
	if(typeCapt==0) //si capteur simple : name = numInstru
		newCell.innerHTML ="<input type='hidden' value='"+data["numInstru"]+"' name='numInstru[]' />"+data["numInstru"];
	else //sinon, capteur force : name = numInstruForce
		newCell.innerHTML ="<input type='hidden' value='"+data["numInstru"]+"' name='numInstruForce[]' />"+data["numInstru"];
	
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
	newRow.className = "val";
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

function confirmSupp()
{
	return confirm("Voulez vous vraiment supprimer ce pot ?");
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

function lancerRecherche(num)
{
	var url="./ajaxBidon.php?num="+num;
	$.getJSON( url, function(data) 
	{
		//test si le numero entré n'est pas déja dans la liste
		if(document.getElementById(data["numInstru"])==null)
			ajouterModifierCapteur(data);
		else
		{
			var child= document.createElement("div");
			child.className="text-center";
			child.innerHTML="<div class='alert alert-warning'><strong>Instrument n°"+data["numInstru"]+" déja présent dans la liste </strong><input type='button' class='btn btn-warning' onclick='suppAlerte(this)' value='Ok' /></div>";
			$("#dialog_t").children().first().before(child);
			//document.body.insertBefore(child, document.body.firstChild);

		}
	})
	.fail(function() {
		var child= document.createElement("div");
		child.className="text-center";
		child.innerHTML="<div class='alert alert-danger'><strong>Capteur n°"+num+" inconnue </strong><input type='button' class='btn btn-danger' onclick='suppAlerte(this)' value='Ok' /></div>";
		console.log(document.getElementById("dialog_t").firstChild)
		$("#dialog_t").children().first().before(child);
		//document.body.insertBefore(child, document.getElementById("dialog_t").firstChild);

		
	});
}

function ajoutNouvelleLigne()
{
	$('#dialog_t').dialog('open');
}

function excel (){
	
	console.log(capteur);
}
//fonction pour fixer la taille des cellules du tableau		
function fixWidthHelper(e, ui) {
	ui.children().each(function() {
		$(this).width($(this).width());
	});
	return ui;
}

$(document).ready(function() {
	if($("#dialog_t").length) //test si l'element existe, si sur page creerModifierBidon passe, sinon non
	{
		
		var capteur = [];
		for (i=0;i<($(".val").length);i++){
			
			if ($(".val")[i].id != ""){
				
				capteur.push($(".val")[i].id);
			}
			
			
		}
		var url='saveOrder.php?capteur='+capteur;
		$.get( url, function(data) {
			
			console.log(data);

		});
				
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
		
		$('#addL').click(function() {
			ajoutNouvelleLigne();
		});
		
		//desactive la validation du formulaire si l'on presse enter dans un input
		$('#form').on('keyup keypress', function(e) {
		  var keyCode = e.keyCode || e.which;
		  if (keyCode === 13) { 
			e.preventDefault();
			return false;
		  }
		});
		
		//ajout du sortable de jquery-ui
		$('table tbody').sortable({
			tolerance: "pointer",
			axis: "y",
			opacity: 0.5,
			start: function(e, ui){
				
				$(ui.placeholder).hide(300);
			},
			change: function (e,ui){
				$(ui.placeholder).hide().show(300);

				var capteur = [];
				for (i=0;i<($(".val").length);i++){
					
					if ($(".val")[i].id != ""){
						
						capteur.push($(".val")[i].id);
					}
					
					
				}
				var url='saveOrder.php?capteur='+capteur;
				$.get( url, function(data) {
					
					console.log(data);

				});

			},
			helper: fixWidthHelper
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
	}
	
});
