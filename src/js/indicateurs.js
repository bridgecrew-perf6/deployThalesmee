/**
*Ce fichier est utilisé dans www/essai/indicateurs.php
*/

//On cache le bouton pour l'export PDF au chargement de la page
$("#export").hide();
$("#ligneProd").hide();
$("#moyen").hide();
$(".limite").hide();
$(".fpy").hide();
$(".cause").hide();
$(".date_annuelle").hide();
$(".date").show();
$(".retard").hide();
$("input[type=checkbox]").each(function(){
		
	$(this).prop("checked", true);			
})

/* Permet de gérer le décochage de la case TOUS si une case n'est pas coché et 
inversement si toutes les cases sont cochées la case TOUS sera coché pour garder la cohérence
*/
$(".filtre").click(function(){

	var filtre = true;

	$(".filtre").each(function()
	{
		if ($(this).is(':visible'))
		{
			if (!$(this).prop("checked")){
		
				filtre = false;
				return true;
			}
		}
	})

	if (filtre)
	{
		$("#tous").prop("checked", true);

	}else
	{
		$("#tous").prop("checked", false);
	}
})

/* Permet de gérer le décochage de la case TOUS si une case n'est pas coché et 
inversement si toutes les cases sont cochées la case TOUS sera coché pour garder la cohérence
*/
$(".filtremoyen").click(function(){

	var filtre = true;

	$(".filtremoyen").each(function()
	{
		if ($(this).is(':visible'))
		{
			if (!$(this).prop("checked")){
		
				filtre = false;
				return true;
			}
		}
		
	})

	if (filtre)
	{
		$("#tousmoyen").prop("checked", true);

	}else
	{
		$("#tousmoyen").prop("checked", false);
	}
})

/*Récupère les informations pour afficher le tableau du graphique des FPY
* rex_test_url : url permettant de récupérer les données via une requête AJAX
*/
function afficheTableau(rex_test_url){
	$("#tab").html("");
	$.get(rex_test_url+"&tableau=1", function(data) 
	{
		$("#tab").innerHTML="";
		data = JSON.parse(data);

		var table = "<div class='jumbotron'><table class='table table-striped text-center'><tr><th>Mois</th>";
		for (elt in data["legende"])
		{
			table += "<th>"+data["legende"][elt]+"</th>";
		}
		table += "</tr><tr><th>Nombre de tests</th>";
		table = remplirTab (data["tests"], table);
		table += "</tr><tr><th>Nombre d'anomalie total</th>";
		table = remplirTab (data["anomalie"], table);
		table += "</tr><tr><th>Nombre d'anomalie I2PT</th>";
		table = remplirTab (data["i2pt"], table);
		table += "</tr></table></div>";
		$("#tab").append(table);
	})
}

/*Permet de remplir le tebleau
* @param
* ligne : une ligne du tableau
* table : chaîne de caracyère à remplir
* @return
* la table modifié
*/
function remplirTab (ligne, table)
{
	for (elt in ligne)
	{
		table += "<td>"+ligne[elt]+"</td>";
	}
	return remplirCaseVide (ligne,table);
}

/*Permet de remplir le tebleau avec des cases vide (jusqu'à décembre)
* ligne : une ligne du tableau
* table : chaîne de caracyère à remplir
* @return
* table : la table modifié
*/
function remplirCaseVide (ligne, table)
{
	var cpt = ligne.length;
	while (cpt < 14)
	{
		table += "<td></td>";
		cpt += 1;
	}
	return table;
}

/*Permet de gérer la case Tous cpour cocher ou décocher tous les boutons
* @param
* num : type de checkbox
*/
function check_all (num)
{
	if (num == 2)
	{		
		if ($("#tous").prop("checked"))
		{
			$("input[name=ligneproduit]").each(function()
			{
				$(this).prop("checked", true);
			})
		}else 
		{
			$("input[name=ligneproduit]").each(function()
			{
				$(this).prop("checked", false);
			})
		}

	}else if (num == 1)
	{		
		if ($("#tousmoyen").prop("checked"))
		{
			$("input[name=moyen]").each(function()
			{
				$(this).prop("checked", true);
			})

		}else
		{
			$("input[name=moyen]").each(function()
			{
				$(this).prop("checked", false);
			})
		}
	}else {

		if ($("#tousretard").prop("checked"))
		{
			$("input[name=retard]").each(function()
			{
				$(this).prop("checked", true);
			})

		}else
		{
			$("input[name=retard]").each(function()
			{
				$(this).prop("checked", false);
			})
		}

	}
}

/*Nombre de jour dans un mois
* @param
* date : date a calculer
* @return
* nombre de jours
*/
function get_nbJour (date)
{	
	return new Date(date.getFullYear(), date.getMonth()+1, -1).getDate()+1;
}

/*Modifie la valeur des dates au mois précedent
*/
function mois_prec (){
	
	var res = splitDate();
	var jour = res[0];
	var mois = res[1];
	var annee = res[2];
	var fin = new Date (parseInt(annee),parseInt(mois),0);
	
	var deb = new Date (parseInt(annee),parseInt(mois),1);
	deb = new Date (deb.setMonth(deb.getMonth()-2));
	
	$("#dateDeb").val("01/"+deb.getMonth()+"/"+deb.getFullYear());
	
	if (deb.getMonth() == "00")
	{		
		deb = new Date (deb.setFullYear(deb.getFullYear()-1));
		$("#dateDeb").val("0"+deb.getDate()+"/12/"+deb.getFullYear());	
	}

	else if (deb.getMonth() < 10) $("#dateDeb").val("0"+deb.getDate()+"/0"+deb.getMonth()+"/"+deb.getFullYear());
	else $("#dateDeb").val("0"+deb.getDate()+"/"+deb.getMonth()+"/"+deb.getFullYear());
	
	var fin = new Date (deb.getFullYear(), deb.getMonth(), 0);
	
	if (deb.getMonth() == "00") $("#dateFin").val(fin.getDate()+"/12/"+deb.getFullYear());	
	else if (deb.getMonth() < 10) $("#dateFin").val(fin.getDate()+"/0"+deb.getMonth()+"/"+deb.getFullYear());
	else $("#dateFin").val(fin.getDate()+"/"+deb.getMonth()+"/"+fin.getFullYear());
	
	changeDate()
}

/*Modifie la valeur des dates au mois en cours
*/
function mois_cours ()
{	
	var now = new Date();
	var first = new Date (now.setDate(1));
	var month_first = first.getMonth() + 1;
	if (month_first < 10) $("#dateDeb").val("0"+first.getDate()+"/0"+month_first+"/"+first.getFullYear());
	else $("#dateDeb").val("0"+first.getDate()+"/"+month_first+"/"+first.getFullYear());

	var last = new Date (now.setDate(get_nbJour(now)));
	var month_last = last.getMonth() + 1;
	if (month_last < 10) $("#dateFin").val(get_nbJour(now)+"/0"+month_last+"/"+last.getFullYear());
	else $("#dateFin").val(get_nbJour(now)+"/"+month_last+"/"+last.getFullYear());
	changeDate()
}

/*Modifie la valeur des dates au mois suivant 
*/
function mois_suiv()
{	
	var res = splitDate();
	var jour = res[0];
	var mois = res[1];
	var annee = res[2];
	$("#dateDeb").val("01/"+mois+"/"+annee);
	var deb = new Date (parseInt(annee),parseInt(mois),1);
	majDateMoisSuiv (deb);
	changeDate()
}

/*Permet de mettre à jour le mois suivant lors de la modification du mois de début
* @param
* deb : date de début
*/
function majDateMoisSuiv (deb)
{	
	var fin = new Date (deb.getFullYear(), deb.getMonth(), 0);
	if (deb.getMonth() == "00") $("#dateFin").val(fin.getDate()+"/12/"+fin.getFullYear());	
	else if (deb.getMonth() < 10) $("#dateFin").val(fin.getDate()+"/0"+deb.getMonth()+"/"+deb.getFullYear());
	else $("#dateFin").val(fin.getDate()+"/"+deb.getMonth()+"/"+fin.getFullYear());
}
/*Modifie la valeur des dates à l'année précédente
*/
function annee_prec()
{	
	var res = splitDate();
	var annee = res[2];
	annee -= 1;
	$("#dateFin").val("31/12/"+annee);
	$("#dateDeb").val("01/01/"+annee);
	changeDate();
}

/*Modifie la valeur des dates à l'année en cours
*/
function annee_cours ()
{	
	setAnnee();
	changeDate()
}

/*Modifie la valeur des dates à l'année suivante
*/
function annee_suiv()
{	
	var res = splitDate();
	var annee = res[2];
	annee ++;
	$("#dateFin").val("31/12/"+annee);
	$("#dateDeb").val("01/01/"+annee);
	changeDate();
}

/*Permet de découper une date
* @return
* array : [jour, mois, année]
*/
function splitDate ()
{	
	var deb = $("#dateDeb").val();
	var date = deb.split("/");
	var jour = date[0];
	var mois = date[1];
	var annee = date[2];
	//Si le mois est entre 01 et 08
	if (mois[0] == "0" && mois[1] != "9")
	{		
		var new_mois = parseInt(mois[1]) +1;
		mois = "0" + new_mois;
	//Si le mois est 09	
	}else if (mois[0] == "0" && mois[1] == "9") mois = "10";
	//Si le mois est 10 ou 11
	else if (mois != "12")
	{		
		var new_mois = parseInt(mois)+1;
		mois = new_mois;
	//Si le mois est 12	
	}else
	{		
		mois = "01";
		var new_annee = parseInt(annee)+1;
		annee = new_annee;
	}
	res = [jour, mois, annee];
	return res;
}

/*Modifie l'année à l'année en cours
*/
function setAnnee ()
{
	var now = new Date();
	var year = now.getFullYear();
	$("#dateDeb").val("01/01/"+year);	
	$("#dateFin").val("31/12/"+year);
}

/*Vérifie la sélection des checkbox lors de la validation
* @return
* ligne : Booleén désignant si une ligne de produit a été saisie
* moy_med : Booleén désignant si un moyen a été saisi
* fpy : Booleén désignant si des fpy global ou i2pt ont été saisies
* moyen : Booleén désignant si un moyen à été saisi
*/
function verificationSelection ()
{
	//Vérification de la saisie d'au moins une ligne de produit
	var ligneSaisie = false;
	$("input[name=ligneproduit]").each(function()
	{		
		if ($(this).prop("checked")) ligneSaisie = true;
	});
	//Vérification de la saisie de la moyenne ou de la médiane (pour les graphes de temps d'attente annuel)
	var moy_medSaisie = false
	$(".limite input[type=checkbox]").each(function()
	{		
		if ($(this).prop("checked")) moy_medSaisie = true;
	});
	//Vérification de la saisie des fpy global ou i2pt (pour le graphe sur les FPY)
	var fpySaisie = false
	$(".fpy input[type=checkbox]").each(function()
	{		
		if ($(this).prop("checked")) fpySaisie = true;
	});
	//Vérification de la sélection d'un moyen (pour le graphe Occupation annuel)
	var moyenSaisie = false
	$(".moyen .listemoyen input[type=checkbox]").each(function()
	{		
		if ($(this).prop("checked")) moyenSaisie = true;
	});
	//Vérification de la saisie de la moyenne ou de la médiane (pour les graphes de temps de retard en fin de test)/
	var moy_medRetardSaisie = false
	$(".moyen .limiteretard input[type=checkbox]").each(function()
	{		
		if ($(this).prop("checked")) moy_medRetardSaisie = true;
	});
	return {
		ligne : ligneSaisie,
		moy_med : moy_medSaisie,
		fpy : fpySaisie,
		moyen : moyenSaisie,
		retard_moy_med : moy_medRetardSaisie
	};
}

/*Configure la date au bon format
* @param
* dateDeb : date de début
* dateFin : date de fin
* @return
* dateDeb : date de fin au bon format
* dateFin : date de fin au bon format
*/
function configDate(dateDeb, dateFin)
{
	//Split de la date pour la transformer en format SQL
	dateDeb=dateDeb.split("/");
	dateFin=dateFin.split("/");
	//Composition du format SQL
	date1 = dateDeb[2]+"-"+dateDeb[1]+"-"+dateDeb[0];
	date2 = dateFin[2]+"-"+dateFin[1]+"-"+dateFin[0];
	//Ajout des heures pour le chargement de graphique
	dateDeb=date1+" 00:00:00";
	dateFin=date2+" 23:59:00";
	return {
		dateDeb : dateDeb,
		dateFin : dateFin,
	}
}

/*Ajoute les paramètres des checkbox cochés
* @param
* rex_test_url : url de l'image
* pdf_url : url de l'export pdf
* excel_url : url de l'export excel
* target : booleén désignant si il faut ajouter la valeur de la target ou nom
* @return
* rex_test_url : url de l'image modifié
*/
function ajoutParam (rex_test_url, pdf_url, excel_url, target)
{
	//Ajout de la valeur de chaque input
	$("input[type=checkbox]").each(function()
	{			
		if ($(this).prop("checked"))
		{					
			rex_test_url += "&"+$(this).val().replace(" ", "-")+"=1";
			pdf_url += "&"+$(this).val().replace(" ", "-")+"=1";
			excel_url += "&"+$(this).val()+"=1";
		}		
	})

	if (target) rex_test_url = getTarget(rex_test_url, pdf_url, excel_url)
	else changeLien (rex_test_url, pdf_url, excel_url)
	return rex_test_url
}

/*Change le lien de l'image, de l'export pdf, de l'export excel
* @param
* rex_test_url : url de l'image
* pdf_url : url de l'export pdf
* excel_url : url de l'export excel
*/
function changeLien (rex_test_url, pdf_url, excel_url)
{
	//Ajout des liens
	$("#rex_test").attr("src", rex_test_url);
	$("#pdf").attr("href", pdf_url);
	$("#excel").attr ("href", excel_url);
	$("#export").show();
}

/*Mise à jour de la valeur de la target dans la base de données et ajoute la target à l'url
* @param
* rex_test_url : url de l'image
* pdf_url : url de l'export pdf
* excel_url : url de l'export excel
* @return
* rex_test_url : url de l'image modifié
*/
function getTarget(rex_test_url, pdf_url, excel_url)
{
	var target = 0;
	if ($("#targetfpy").is(":visible"))
	{			
		target = document.getElementById("targetfpy").value;
		var url='target.php?idTarget=2&target='+target;
		$.get( url);
		
	}else if ($("#target").is(":visible"))
	{
		target = document.getElementById("target").value;
		var url='target.php?idTarget=1&target='+target;
		$.get( url);
		
	}else if ($("#targetmoyen").is(":visible"))
	{
		target = document.getElementById("targetmoyen").value;
		var url='target.php?idTarget=3&target='+target;
		$.get( url);

	}else if ($("#targetcause").is(":visible"))
	{
		target = document.getElementById("targetcause").value;
		var url='target.php?idTarget=4&target='+target;
		$.get( url);

	}else if ($("#targetretardtest").is(":visible"))
	{
		target = document.getElementById("targetretardtest").value;
		var url='target.php?idTarget=5&target='+target;
		$.get( url);
	}

	//Ajout de la target
	rex_test_url += "&target="+target;
	pdf_url += "&target="+target;
	excel_url += "&target="+target;
	changeLien (rex_test_url, pdf_url, excel_url)
	return rex_test_url;
}

/*Affiche les messages d'erreurs si aucune checkbox n'est cochées
*/
function afficheErreur (select)
{
	var child= document.createElement("center");
	if (!select.ligne) child.innerHTML='<div class="alert alert-warning"><strong>Veuillez saisir une ligne de produit</strong></div>';
	else if (!select.moy_med ||!select.retard_moy_med) child.innerHTML='<div class="alert alert-warning"><strong>Veuillez choisir le type de valeur (Moyenne ou médiane)</strong></div>';
	else if (!select.moyen || !select.retard_moyen) child.innerHTML='<div class="alert alert-warning"><strong>Veuillez choisir un moyen</strong></div>';
	else child.innerHTML='<div class="alert alert-warning"><strong>Veuillez choisir le type de valeur (Global ou I2PT)</strong></div>';
	document.body.insertBefore(child, document.body.firstChild);
	window.scrollTo(0,0);
}

/*Permet de mettre à jour le graphe avec les nouveaux paramètres donnés par l'utilisateur
*/
function changeDate()
{	
	var select = verificationSelection();
	$("#tab").children().remove();
	//Si au mois une case est saisie
	if (select.ligne && select.moy_med && select.fpy && select.moyen && select.retard_moy_med)
	{		
		//On cache tous les alerts
		$(".alert").remove();
		//Récupération des dates saisies pas l'utilisateur
		var dateDeb=document.getElementById("dateDeb").value;
		var dateFin=document.getElementById("dateFin").value;
		//Vérification des dates
		if(isGoodDate(dateDeb) && isGoodDate(dateFin) && verifDate(dateDeb,dateFin))
		{
			var elt = $(".select");
			var date = configDate(dateDeb, dateFin);
			dateDeb = date.dateDeb;
			dateFin = date.dateFin;
			//Récupération des urls
			var url = $("#rex_test").attr("src");
			var url2 = $("#pdf").attr("href");
			var url3 = $("#excel").attr("href");
			//Modification de l'url de l'image et de l'export PDF		
			var rex_test_url = url.split("dateDeb")[0]+"dateDeb="+dateDeb+"&dateFin="+dateFin;
			var pdf_url = url2.split("dateDeb")[0]+"dateDeb="+date1+"&dateFin="+date2;
			var excel_url = url3.split("dateDeb")[0]+"dateDeb="+date1+"&dateFin="+date2;		
			//Changement des urls avec les nouvelles valeurs
			rex_test_url = ajoutParam (rex_test_url, pdf_url, excel_url, 1);
			//Affichage du tableau si le graphe est celui dess FPY
			if (elt !== undefined && elt.attr("id") == "18") afficheTableau(rex_test_url);
		}
	}else afficheErreur(select);
}

/*Initialisation de la page pour l'affichage du graphe (valeur par défault)
* @param
* elt : type de graphe cliqué
*/
function initialisation (elt)
{
	//Suppression du fond gris pour le type de graphique en cours
	$(".list-group-item").removeClass("select");
	//Application d'un fond gris sur l'élement cliqué
	elt.classList.add("select");
	//On cache tous les alerts
	$(".alert").remove();
	//Suppression du disabled des input
	$(".date").prop("disabled", false);
	//Suppression du disabled du bouton "valider"
	$("#valider").removeAttr("disabled");
	//Affichage des lignes de produits
	$("#ligneProd").show();
}

/*Affiche la zone de sélection de la date et les boutons de navigation si le graphe seléctionné le permet
* @param
* elt : type de graphe cliqué
*/
function afficheDate (elt)
{
	//Si le graphique n'est pas dynamique (changer la date n'a aucun impact sur le graphique)
	if ($("#"+elt.id).is(".nodate"))
	{	//les input sont disabled
		$(".date").prop("disabled", true);
		//le bouton est disabled
		$("#valider").attr("disabled", true);
	}
	//Bouton de navigation (Ex : Année suivant, Année en cours, etc.)
	if ($("#"+elt.id).is(".date_annu"))
	{			
		setAnnee();
		$(".date").hide();
		$(".date_annuelle").show();
		
	}else
	{			
		$(".date").show();
		$(".date_annuelle").show();
		$(".btn_annuelle").hide();
	}
}

/*Affichage de la target si le type de graphe le permet
* @param
* elt : type de graphe cliqué
* @return
* target : valeur de la target actuelle
*/
function affichageTarget (elt)
{
	if ($("#"+elt.id).is(".target"))
	{
		$(".limite").show();
		$("#valider").attr("disabled", false);

	}else $(".limite").hide();

	if (elt.id == "18")
	{
		$(".fpy").show();
		$("#valider").attr("disabled", false);
		
	}else $(".fpy").hide();

	if (elt.id == "21")
	{
		$("#targetmoyen").show();
		
	}else $("#targetmoyen").hide();

	if (elt.id == "23")
	{
		$(".moyen").show();
		$(".limiteretard").show();
		
	}else {

		$(".limiteretard").hide();
	}

	if (elt.id == "22")
	{
		$(".cause").show();
		$("#valider").attr("disabled", false);
		
	}else $(".cause").hide();

	if (elt.id == "24")
	{
		$(".retard").show();
		$("#valider").attr("disabled", false);
		
	}else $(".retard").hide();

	var target = 0;
	if (elt.id == "16" || elt.id == "17") target = document.getElementById("target").value;
	else if (elt.id == "18") target = document.getElementById("targetfpy").value;
	else if (elt.id == "21") target = document.getElementById("targetmoyen").value;
	else if (elt.id == "22") target = document.getElementById("targetcause").value;
	else if (elt.id == "23") target = document.getElementById("targetretard").value;
	else if (elt.id == "24") target = document.getElementById("targetretardtest").value;
	
	return target;
}

/*Affiche les checkbox qui correspondent au type de graphe
* @param
* elt : type de graphe cliqué
*/
function affichageCheckbox (elt)
{
	if ($("#"+elt.id).is(".noligneprod")) $("#ligneProd").hide();
	
	if ($("#"+elt.id).is(".moyen")) $("#moyen").show();
	else $("#moyen").hide();
	
	if (elt.id != "13" && elt.id != "15") $(".spe").hide();
	else $(".spe").show();

	
}

/*Regroupe l'ensemble des appels de fonctions pour l'affichage des différents widgets pour les utilisateurs
* @param
* elt : type de graphe cliqué
* @return
* target : valeur de la target
*/
function affichageParGraphique (elt)
{
	initialisation(elt);
	afficheDate (elt)
	affichageCheckbox (elt);
	return affichageTarget (elt);
}

/*Permet d'afficher le nouveau graphe sélectionné par l'utilisateur
* @param
* elt : type de graphe cliqué
* url : url de récupération du ficher de génération de l'image graphe
* pdf : url de récupération du ficher de génération de l'export pdf 
* excel : url de récupération du ficher de génération de l'export excel 
*/
function changeUrl (elt, url, pdf, excel)
{	
	//Vérification de la selection des paramètres
	var select = verificationSelection();
	//Si au mois une case est saisie
	if (select.ligne && select.moy_med && select.fpy && select.moyen && select.retard_moy_med)
	{	

		//Affichage des en-têtes pour chaque type de graphique
		var target = affichageParGraphique(elt)
		//Récupération des valeurs saisie pas l'utilisateur (dates)
		var dateDeb=document.getElementById("dateDeb").value;
		var dateFin=document.getElementById("dateFin").value;
		//Si les dates sont valides
		if(isGoodDate(dateDeb) && isGoodDate(dateFin) && verifDate(dateDeb,dateFin))
		{
			var date = configDate(dateDeb, dateFin);
			dateDeb = date.dateDeb;
			dateFin = date.dateFin;
			//Modification de l'url de l'image et de l'export PDF
			var rex_test_url = "../graph/"+url+"&idService="+labo+"&dateDeb="+dateDeb+"&dateFin="+dateFin+"&target="+target;
			var pdf_url = pdf+"&idGraphe="+elt.id+"&idService="+labo+"&dateDeb="+date1+"&dateFin="+date2+"&target="+target;
			var excel_url = "../graph/excel/"+excel+"idGraphe="+elt.id+"&idService="+labo+"&dateDeb="+date1+"&dateFin="+date2+"&target="+target;
			//Changement des urls avec les nouvelles valeurs
			rex_test_url = ajoutParam (rex_test_url, pdf_url, excel_url, 0);
			//Affichage du tableau si le graphe est celui dess FPY
			if (elt.id == "18") afficheTableau(rex_test_url);
			else $("#tab").children().remove();
		}
	}else afficheErreur(select);
}

/*Fonction permettant de vérifier si la date est correctement renseignée
* @param
* mydate : la date à verifié
* @return
* Booléen
*/
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
			
	return true;
}

/*Vérifie si les deux dates sont cohérentes. /Cette fonction n'est executé que si les deux dates sont valides (isGoodDate avant dans le if), on peut donc assumer que l'on travaille avec des dates valides
* @param
* dateDeb : date de début
* dateFin : date de fin
* @return
* Booléen
*/
function verifDate(dateDeb,dateFin)
{
	dateDeb=dateDeb.split('/');
	dateFin=dateFin.split('/');
	dateDeb=new Date(dateDeb[2], dateDeb[1]-1,dateDeb[0]);
	dateFin=new Date(dateFin[2], dateFin[1]-1,dateFin[0]);
	var dateAct= new Date();
	if(dateAct<dateDeb)
	{
		var child= document.createElement("center");
		child.innerHTML='<div class="alert alert-warning"><strong>La date de début ne peut pas être supérieur à la date du jour </strong></div>';
		document.body.insertBefore(child, document.body.firstChild);
		window.scrollTo(0,0);
		return false;
	}
	
	if(dateDeb>dateFin)
	{
		var child= document.createElement("center");
		child.innerHTML='<div class="alert alert-warning"><strong>La date de début ne peut pas être supérieur à la date de fin</strong></div>';
		document.body.insertBefore(child, document.body.firstChild);
		window.scrollTo(0,0);
		return false;
	}
	return true;
}