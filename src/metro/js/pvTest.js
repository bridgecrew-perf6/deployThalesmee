function ajouterInstruPv(data)
{
	for(var i in data) {
	   if(data[i]===null){data[i]="";}
	}
	
	var newRow = document.getElementById('tab').insertRow(-1);
	newRow.id=data["numInstru"];
	
	var newCell = newRow.insertCell(-1);
	newCell.innerHTML ="<input type='hidden' value='"+data["numInstru"]+"' name='numInstru[]' />"+data["numInstru"];
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML =data["fonction"];

	newCell = newRow.insertCell(-1);
	newCell.innerHTML =data["model"];
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML =data["manu"]; 
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML =data["numSerie"]; 
	
	//conversion de la date format fr
	var dateVal="";
	if(data["cal"]!="")
	{
		date=data["cal"].split("-"); 
		var dateVal=date[2]+"/"+date[1]+"/"+date[0];
	}
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML =dateVal;
	
	//test si date de calib depassé
	var dateAct = new Date();
	var dateInstru=new Date(date[0],date[1],date[2]);
	if(dateInstru-dateAct < 0)
	{
		var child= document.createElement("div");
		child.className="text-center";
		child.innerHTML="<div class='alert alert-warning'><strong>Date de calibration depassée pour l'instument "+data["function"]+" n°"+data["numInstru"]+" </strong><input type='button' class='btn btn-warning' onclick='suppAlerte(this)' value='Ok' /></div>";
		document.body.insertBefore(child, document.body.firstChild);
		window.scrollTo(0,0);
	}
	
	var test="";
	var idTest="";
	if(data["test"]!=undefined)
	{
		test=data["test"];
		idTest=data["idTest"];
	}
	else
	{
		$("#typeT > div > select").each(function(){
			if(this.value!=-1)
			{
				test+= this.options[this.selectedIndex].innerHTML+", ";
				idTest+=this.value+"$$";
			}
		});
		//suppression des deux derniers caracteres
		test=test.substring(0,test.length-2);
		idTest=idTest.substring(0,idTest.length-2);
	}
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML ="<input type='hidden' value='"+idTest+"' name='test[]' />"+test;
	
	//cellule pour supprimer la ligne
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = "<img class='imgSupp'  SRC='../img/supr.png' class='btnSuppLigne'/>";
	//ajout de l'evenement onclick
	$(newCell).children().click(function() {
		suppLigne(data["numInstru"]);
	});
}
function detailsPvInstru(data)
{
	for(var i in data) {
	   if(data[i]===null){data[i]="";}
	}
	
	var newRow = document.getElementById('tab').insertRow(-1);
	newRow.id=data["numInstru"];
	
	var newCell = newRow.insertCell(-1);
	newCell.innerHTML =data["numInstru"];
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML =data["fonction"];

	newCell = newRow.insertCell(-1);
	newCell.innerHTML =data["model"];
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML =data["manu"]; 
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML =data["numSerie"]; 
	
	//conversion de la date format fr
	var dateVal="";
	if(data["cal"]!="")
	{
		date=data["cal"].split("-"); 
		var dateVal=date[2]+"/"+date[1]+"/"+date[0];
	}
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML =dateVal;
	
	var test="";
	var idTest="";
	test=data["test"];
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML =test;
	
}

function confirmSupp()
{
	return confirm("Voulez vous vraiment supprimer ce PV ?");
}

function suppLigne(num)
{
	//if(confirm("Voulez vous vraiment supprimer l'instrument n°"+num+" de cette liste ?"))
	//{
		var element = document.getElementById(num);
		element.parentNode.removeChild(element);
	//}
}

function lancerRecherche(num)
{
	if($("#typeT > div > select:first").val()!=null)
	{
		var url="./ajaxInfoPvTest.php?num="+num;
		$.getJSON( url, function(data) {
				ajouterInstruPv(data);
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
		child.innerHTML="<div class='alert alert-warning'><strong>Veuillez choisir au moins un test </strong><input type='button' class='btn btn-warning' onclick='suppAlerte(this)' value='Ok' /></div>";
		document.body.insertBefore(child, document.body.firstChild);
		window.scrollTo(0,0);
	}
}

//supprimer les messages d'alerte
function suppAlerte(elem)
{
	var element =  elem.parentNode.parentNode;
	element.parentNode.removeChild(element);
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
		
		//construit la boite de dialogue
		$("#dialog_t").dialog({ 
			autoOpen: false,
			width: 400,
			modal: true,
			buttons: {
				//clic sur valider lance la recherche
				"Valider": function() {
					$("#formDialog").submit();
					if($('#nouvNum').val()!="")
					{	
						lancerRecherche($('#nouvNum').val());
						$('#nouvNum').val("");
						$("#dialog_t").dialog( "close" );	
					}
				},
				"Annuler": function() {
					$('#nouvNum').val("");
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
		
		$('#addL').click(function() {
				ajoutNouvelleLigne();
			});

		$('#addT').click(function() {
			$("#typeT > div:last").after($("#typeT > div:first").clone());
		});

		$('#suppT').click(function() {
			if($("#typeT > div > select").length > 1)
				$("#typeT > div:last").remove();
		});
	}
});