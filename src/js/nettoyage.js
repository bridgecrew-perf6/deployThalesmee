//Ma&squage des erreurs
$("#erreur").hide();
//Fonction permettant de vérifier qu'une checkbox est bien coché
function verif(){
	
	var check = false
	$("input[type=checkbox]").each(function(){
		
		if ($(this).prop("checked")){
			
			check = true
		}		
	})
	if (check == false){
		
		$("#erreur").show();
	}
	return check;
}

//Fonction permettant d'executer la requete AJAX pour supprimer la liste des essais de la BDD
function suppr(tabEssais, tabEssaisSuppr){

	lancerAnnim();
	if ($("input[name=suppr]").prop("checked")){
		
		$.ajax ({
		
			type :'POST',
			url : 'nettoyage_suppr.php',
			data : 'essais=' + tabEssais+ '&essaisSuppr='+tabEssaisSuppr,
			success : function (data){
				
				swal({
		
					title : "Informations validées",
					text : "Redirection dans quelques instants",
					icon : "success"
					
				});
				setTimeout(redirection, 1000);
			},
			error : function (data){
				alert("Erreur requête AJAX");
				stopAnnim();
			}	
		})
		
	}else {
		
		$.ajax ({
		
			type :'POST',
			url : 'nettoyage_suppr.php',
			data : 'essais=' + tabEssais,
			dataType : 'json',
			success : function (data){
				
				console.log(data.data);
				var error=false;
				var essai = "";
				$.each (data.data, function (value){
					
					error = true;
					
					console.log(data.data[value]);
					essai += data.data[value]+"\n";

					
				});
				if (!error){
					
					swal({
		
						title : "Informations validées",
						text : "Redirection dans quelques instants",
						icon : "success"
					
					});
					setTimeout(redirection, 1000);

					
				}else{
					
					swal({
	
							title : "Erreur de suppression",
							text : "Vérifiez que le ou les essai(s) suivant ne dispose(nt) pas d'anomalie : \n"+ essai,
							icon : "error",
							
					})
					.then((value) => {
						setTimeout(redirection, 100);
					});
					
				}
				
			},
			error : function (data){
				alert("Erreur requête AJAX");
				stopAnnim();
			}	
		})
		
	}
	
}
function redirection(){
	
	document.location.href="index.php";
}

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
	//if(dateDeb
}
//lance une animation le temps du chargement
function lancerAnnim()
{
	$("#se-pre-con-load").show();
	return true;
}
function stopAnnim()
{
	$("#se-pre-con-load").hide();
}

$(".info").hide();

//DataTable
$(document).ready(function() {
	$('#exemple').DataTable( {
		
		"order" : [[1, "desc"]],
		"language": {
			"sProcessing":     "Traitement en cours...",
			"sSearch":         "Rechercher&nbsp;:",
			"sLengthMenu":     "_MENU_ ",
			"sInfo":           "Affichage de l'essai _START_ &agrave; _END_ sur _TOTAL_ essai(s)",
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
	$("#exemple_filter").hide();
});