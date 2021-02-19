//active/desactive les input text des type de modeles
function typemodele(equip,checked)
{
	//si la chekbox est coché checked vaut true -> !checked vaut donc false et disabled = false -> enable (donc quand on coche la checkbox les input passent en enable), l'inverse se produit si on la decoche
	var nb="nb_"+equip;
	var max="max_"+equip;
	var min="min_"+equip;

	document.getElementById(nb).disabled= !checked;
	document.getElementById(max).disabled= !checked;
	document.getElementById(min).disabled= !checked;
	
	if(!checked) //si on decoche
	{
		//on efface les valeurs deja entrées
		document.getElementById(nb).value= "";
		document.getElementById(max).value= "";
		document.getElementById(min).value= "";
	}
	else
	{
		//on ajoute les valeurs pas defauts si besoin
		if(document.getElementById(max).value=="")
			document.getElementById(max).value= "1";
		if(document.getElementById(min).value=="")
			document.getElementById(min).value= "1";
	}
}


function onlyNumber(event)
{
	
	var codeTouche = window.event ? event.keyCode : event.which;
	//si on appuie pas sur backspace && enter && supr && tab && fleches ect...
	if(codeTouche!=8 && codeTouche!=13 && codeTouche!=9 && codeTouche!=0 && codeTouche!=46 && codeTouche!=37 && codeTouche!=38 && codeTouche!=39 && codeTouche!=40)
	{
		var lettre=String.fromCharCode(codeTouche);
		if(lettre.match(/[^0-9]/))
				return false;
	}
}

//Ajouter un article obligatoire
function ajout_article(){
	var newRow = document.getElementById('tabArticle').insertRow(-1); 

	var newCell = newRow.insertCell(-1);
	newCell.innerHTML = '<input pattern=".{3,}" title="3 chiffres minimum" placeholder="N° d\'article" onkeyUp="onlyNumber(this)" class="form-control article" type="text" name="article[]" required />';
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = '<input placeholder="Désignation" class="form-control" type="text" id="type_art" name="type_art[]" />';
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = '<input class="form-control procEMC"  placeholder="À créer" type="text" id="EMC1" name="EM1[]"/>';
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = '<input class="form-control procEMC"  placeholder="À créer" type="text" id="EMC2" name="EM2[]" />';
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML ='<input class="form-control procEMC"  placeholder="À créer" type="text" id="EMC3" name="EM3[]" />';
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = '<input class="form-control procVIB"  placeholder="À créer" type="text" id="VIB1" name="VIB1[]" />';
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = '<input class="form-control procVIB"  placeholder="À créer"  type="text" id="VIB2" name="VIB2[]" />';
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = '<input class="form-control procVIB"  placeholder="À créer" type="text" id="VIB3" name="VIB3[]" />';
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = '<input class="form-control procVTH"  placeholder="À créer" type="text" id="VTH1" name="VTH1[]" />';
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = '<input class="form-control procVTH" placeholder="À créer"  type="text" id="VTH2" name="VTH2[]" />';
	
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = '<input class="form-control procVTH" placeholder="À créer"  type="text" id="VTH3" name="VTH3[]" />';

}



//Supprimer tous les articles ajoutes	
function suppr_art(){
	var nb=document.querySelectorAll('.article').length; //ie8 ne supporte pas getElementsByClassName, on utilise querySelectorAll à la place

	if (confirm ('Voulez vous supprimer ces '+nb+' article(s) ?')){
		for (var i=0;i<nb;i++){
			document.getElementById('tabArticle').deleteRow(-1);
		}
		ajout_article();
	}
}

//Supprimer le dernier articles ajoutes	
function suppr_un_art(){
	document.getElementById('tabArticle').deleteRow(-1);
	if(document.querySelectorAll('.article').length ==0)
		ajout_article();
	
}

//test si le formulaire est valide 
function verifFormProc_1()
{
	//supprime les anciens alert
	var t=document.querySelectorAll('.alert');  //ie8 ne supporte pas getElementsByClassName, on utilise querySelectorAll à la place
	
	for (var i=0;  i< t.length; i++)
		t[i].parentNode.removeChild(t[i]);
	//on test la validité de la date
	if(isGoodDate(document.getElementById('date_besoin').value))
	{		
		//au moins une checkbox de type modele
		if(document.getElementById('EM').checked || document.getElementById('EQM').checked || document.getElementById('PFM').checked || document.getElementById('FM').checked)
		{
			//au moins une checkbox de test
			if(document.getElementById('EMC').checked || document.getElementById('VIB').checked || document.getElementById('VTH').checked)
			{
				//verifie que pour le tableau de modele les valeurs soint >0
				var tab=document.getElementById('typeModele');
				var tab_td = tab.getElementsByTagName('input');
				var ok=true;
				for (var i=4; ok && i< tab_td.length; i++) { //on commence a 4 car les 4 premier sont des checkbox
					//si valeur <0 ou != "" (pour celles desactiver)
					if(tab_td[i].value != "" && tab_td[i].value <= 0)
						ok=false;
				}
				if(ok)
				{
					submit=true;//pour eviter le message avant de quitter;
					return true;
				}
				else
				{
					var child= document.createElement("center");
					child.innerHTML='<div class="alert alert-warning"><strong>Les nombres d\'équipements ne sont pas valides</strong></div>';
					document.body.insertBefore(child, document.body.firstChild);
					window.scrollTo(0,0);
				
				}
			}
			else{
				var child= document.createElement("center");
				child.innerHTML='<div class="alert alert-warning"><strong>Veuillez choisir au moins un essai</strong></div>';
				document.body.insertBefore(child, document.body.firstChild);
				window.scrollTo(0,0);
			}
		}
		else{
			var child= document.createElement("center");
			child.innerHTML='<div class="alert alert-warning"><strong>Veuillez choisir au moins un type de modele</strong></div>';
			document.body.insertBefore(child, document.body.firstChild);
			window.scrollTo(0,0);
		}
	}
	return false;
}

function ajoutProc(obj,serv) //fonction qui ajoute la partie EMC
{
	var type="";
	if(serv==1)
		type=".procEMC";
	else if(serv==2)
		type=".procVIB";
	else
		type=".procVTH";
		
	var t=document.querySelectorAll(type);
	for (var i=0;  i< t.length; i++)
		t[i].disabled=!obj.checked;
}


// teste la validité d'une date au format dd/mm/yyyy 
function isGoodDate(mydate)
{
	var thedate, day, month, year;
	var onedate;
	var onetime;
 
	thedate=mydate.split('/');
	day = parseInt(thedate[0],10);	
	month = parseInt(thedate[1],10);	
	year = parseInt(thedate[2],10);
 
	if ((mydate.length != 10) || (thedate.length != 3) ||
		(isNaN(year)) || (isNaN(month)) || (isNaN(day)) ||
		(thedate[0].length < 2) || (thedate[1].length < 2) || (thedate[2].length < 4))
	{
		var child= document.createElement("center");
		child.innerHTML='<div class="alert alert-warning"><strong>La date doit étre au format dd/mm/yyyy </strong></div>';
		document.body.insertBefore(child, document.body.firstChild);
		window.scrollTo(0,0);
		return false;
	}
			
 
	onedate = new Date(year, month-1, day);
	year = onedate.getFullYear();
 
	if ((onedate.getDate() != day) ||
		(onedate.getMonth() != month-1) ||
		(onedate.getFullYear() != year )) 
	{
		var child= document.createElement("center");
		child.innerHTML='<div class="alert alert-warning"><strong>La date doit étre au format dd/mm/yyyy </strong></div>';
		document.body.insertBefore(child, document.body.firstChild);
		window.scrollTo(0,0);
		return false;
	}
			
	//on verifi les delais
	today=new Date();//date actuelle
	var diffDay=parseInt((onedate-today)/1000/3600/24);//difference en jour
	if (diffDay<0){
		var child= document.createElement("center");
		child.innerHTML='<div class="alert alert-warning"><strong>Date de besoin antérieur à ce jour</strong></div>';
		document.body.insertBefore(child, document.body.firstChild);
		window.scrollTo(0,0);
		return false;
	}
	else if (diffDay<19){
		// le delais est de moins de 3 semaines
		// on avertit le demandeur qui pourra quand meme passer a l etape suivante
		var avertissement="Le délai minimum pour rédiger une procédure est de 3 semaines.\nVotre demande ne respecte pas ce délai.\nLes différents laboratoires ne peuvent garantir la livraison de la procédure dans le délai imparti.\nVeillez à prendre en compte les éventuels congés, ponts et RTT dans le calcul de votre date de besoin.\nMerci.\n\nSouhaitez-vous continuer votre demande?\n\nCliquez sur 'Annuler' pour modifier la date de besoin.";
		if (!confirm(avertissement)){
			return false;
		}
	}
	return true;
}






