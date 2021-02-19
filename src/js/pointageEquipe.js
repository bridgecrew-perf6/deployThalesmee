/* Fonction qui change le format de la date et redirige lors du clique sur le bouton valider
*/
function changeDate(){
	
	var dateDeb=document.getElementById("dateDeb").value; //Date de début
	var dateFin=document.getElementById("dateFin").value; //Date de fin
	if(isGoodDate(dateDeb) && isGoodDate(dateFin) && verifDate(dateDeb,dateFin))
	{
		//Formatage
		dateDeb=dateDeb.split("/");
		dateDeb=dateDeb[2]+"-"+dateDeb[1]+"-"+dateDeb[0];
		dateFin=dateFin.split("/");
		dateFin=dateFin[2]+"-"+dateFin[1]+"-"+dateFin[0];

		url = "pointageEquipe.php?date_deb="+dateDeb+"&date_fin="+dateFin; //URL

		if ($("#status").prop('checked')) url += "&status=1"; //Si checkbox cochée
		if ($("#reste").prop('checked')) url += "&reste=1"; //Si checkbox cochée

		document.location.href=url; //Redirection
	}
}

/* Fonction qui vérifie la bon format de la date
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

/* Fonction qui vérifie la validaité des dates envoyées
*/
function verifDate(dateDeb,dateFin) //Cette fonction n'est executé que si les deux dates sont valides (isGoodDate avant dans le if), on peut donc assumer que l'on travaille avec des dates valides
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

/* Fonction qui ajoute les heures poibtées dez techniciens et qui soustrait ce résulat au nombre d'heure
* @param
* id : L'identifiant de l'essai
* @return
* res : la différence entre le nombre d'heure et les heures pointées
*/
function calculReste (id)
{
	res = 0; //Initialisation
	$("#"+id+" .emp").each(function (){ //Pour chaque techniciens

		val = $(this).val().replace(",","."); //Si l'utilisateur utilise une virgule pour séparer les décimales
		if (isNaN(val)) res += 0; //Si la veleur n'est pas un nombre
		else res += parseFloat(val); //Sinon convertion en float
	})

	if ($("#"+id+" .heure").val() == "") return 0 - res; //Si le nombre d'heure est vide
	else return parseFloat($("#"+id+" .heure").val().replace(",",".")) - res; //Sinon return la soustraction
}

/* Fonction qui modifie le reste (est appelé à chaque changement sur les input)
* @param
* id : Identifiant de l'esai
*/
function modifReste (id)
{
	var reste = calculReste(id); //Calcul du reste
	$.ajax ({ //Requête AJAX
		type :'GET',
		url : 'modifReste.php?idEssai='+id+"&reste="+reste,
		success : function (data){
			//Modification de la valeur de l'input
			$("#"+id).children().first().next().next().next().next().next().children().first().val(reste);
		},
		error : function (data){
		}	
	})
}

/* Fonction qui affiche un message à l'utilisateur indiquant la validation des informations
*/
function submit()
{
	swal({
	
		title : "Informations validées",
		text : "Les informations ont été enregistrées",
		icon : "success"
		
	});
	setTimeout(redirection, 1000); //Redirection
}

/* Redirection après la validation
*/
function redirection(){
	
	document.location.href="pointageEquipe.php";
}

/* Fonction qui est éxecuté lorsque le document est chargé
*/
$(document).ready(function() {

	/* Fonction qui est exécuté lorsque la veleur d'un input est modifié (heures pointées)
	*/
	$(".emp").change(function (){

		var input = this; //Stockage de l'input pour la portée
		var idEssai = $(this).parent().parent().attr("id"); //Récupération de l'identifiant de l'essai qui est l'id de la ligne
		var idEmp = $(this).attr('id'); //Récupération de l'identifiant de l'employé qui est l'id de l'input
		var heure = $(this).val(); //Récupération de la valeur

		$.ajax ({ //Requête AJAX
			type :'GET',
			url : 'ajoutPointage.php?idEssai='+idEssai+"&idEmp="+idEmp+"&heure="+heure,
			success : function (data){
				if (data == "Erreur") //Si erreur affichage d'un message (La famille doit êter associée)
				{
					swal({

						title : "Une erreur est survenue",
						text : "Veuillez associer une famille à l'essai",
						icon : "error"
						
					});
					$(input).val("0"); //Remise à zéro de l'input
				}else
				{
					modifReste (idEssai); //Sinon modification du reste
				}
				
			},
			error : function (data){
			}	
		})
	})

	/* Fonction éxecutée lorsque le status change
	*/
	$(".status").change(function ()
	{
		var idEssai = $(this).parent().parent().attr("id"); //Récupération de l'id de l'essai (id de la ligne)
		var status = $(this).val(); //Récupération de la valeur du status
		$.ajax ({ //Requête AJAX
			type :'GET',
			url : 'modifStatus.php?idEssai='+idEssai+"&status="+status,
			success : function (data){
				if (data == "Erreur") //Si erreur
				{
					swal({
						title : "Une erreur est survenue",
						text : "Veuillez associer une famille à l'essai",
						icon : "error"
					});
				}
			},
			error : function (data){
			}	
		})
	})
	
	var size = $(".jumbotron").width() / 14; //Calcul de la taille pour ensuite calculer la taille de chaque colonne
	$.fn.dataTableExt.ofnSearch["html-input"] = function(value){ //Tri sur les input
		return $(value).val();
	}
	$('#exemple').DataTable({ //Création de la table
		"columnDefs" : [{
			"targets": "_all", //Toutes les colonnes
			"width" : size+"px", //Taille
			"targets" : [5], //Pour la colonne 5
			"type": "html-input" //Ajout de la possibilité de trier
		}],
		"order" : [[0, "desc"]],
		"language": {
			"sProcessing":     "Traitement en cours...",
			"sSearch":         "Rechercher&nbsp;:",
			"sLengthMenu":     "_MENU_ ",
			"sInfo":           "Affichage de l'essai _START_ &agrave; _END_ sur _TOTAL_ essais",
			"sInfoEmpty":      "Affichage de l'&eacute;l&eacute;ment 0 &agrave; 0 sur 0 &eacute;l&eacute;ment",
			"sInfoFiltered":   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
			"sInfoPostFix":    "",
			"sLoadingRecords": "Chargement en cours...",
			"sZeroRecords":    "Aucun &eacute;l&eacute;ment &agrave; afficher",
			"sEmptyTable":     "Aucune donn&eacute;e disponible dans le tableau",
			"oPaginate": {
				"sFirst":      "Premier",
				"sPrevious":   "Pr&eacute;c&eacute;dent",
				"sNext":       "Suivant",
				"sLast":       "Dernier"
			},
			"oAria": {
				"sSortAscending":  ": activer pour trier la colonne par ordre croissant",
				"sSortDescending": ": activer pour trier la colonne par ordre d&eacute;croissant"
			},
			"select": {
					"rows": {
						_: "%d lignes séléctionnées",
						0: "Aucune ligne séléctionnée",
						1: "1 ligne séléctionnée"
					} 
			}
		}

	});

});