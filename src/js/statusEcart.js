/* Fonction qui change le format de la date et redirige lors du clique sur le bouton valider
*/
function changeDate(){
	
	var dateDeb=document.getElementById("dateDeb").value;
	var dateFin=document.getElementById("dateFin").value;
	if(isGoodDate(dateDeb) && isGoodDate(dateFin) && verifDate(dateDeb,dateFin))
	{
		dateDeb=dateDeb.split("/");
		dateDeb=dateDeb[2]+"-"+dateDeb[1]+"-"+dateDeb[0];
	
		dateFin=dateFin.split("/");
		dateFin=dateFin[2]+"-"+dateFin[1]+"-"+dateFin[0];
		
		document.location.href="statusEcart.php?date_deb="+dateDeb+"&date_fin="+dateFin;
	}
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
	
	document.location.href="statusEcart.php";
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

/* Fonction qui est éxecuté lorsque le document est chargé
*/
$(document).ready(function() {

	$('#exemple').DataTable( {
		
		"order" : [[0, "desc"]],
		"language": {
			"sProcessing":     "Traitement en cours...",
			"sSearch":         "Rechercher&nbsp;:",
			"sLengthMenu":     "_MENU_ ",
			"sInfo":           "Affichage de l'anomalie _START_ &agrave; _END_ sur _TOTAL_ anomalie",
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

	//Fonction executé lors du changement de l'input de la cause
	$(".cause").change(function (){
		var cause = $(this).val(); //Valeur de l'input
		var id = $(this).parent().parent().attr('id'); //Valeur de l'identifiant de l'essai (id de la ligne)
		$.ajax ({ //Requête AJAX
			type :'POST',
			url : 'ajaxCause.php',
			data : 'cause=' + cause +"&idEssai=" +id, //Envoie des paramètres
			success : function (data){
			},
			error : function (data){
			}	
		})
	})
});