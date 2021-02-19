$(".alert").hide(); //Masquage des alert

/* Fonction permettant de faire valider la suppression à l'utilisateur
*/
function confirmation (id){
	
	if (confirm ("Voulez-vous vraiment supprimer cette famille ?"))
	{
		supprimerFamille(id);
	}
}

/* Fnnction éxecutée à chaque changement des inputs
*/
$(".input").change(function (){
	var id =$(this).parent().parent().attr('id'); //Récupération de l'id (id de la ligne)
	var nom = $(this).parent().parent().children().first().children().val(); //Récupréation du nom de la famille
	var modele = $(this).parent().parent().children().first().next().children().val(); //Récupération du modele
	var heure = $(this).parent().parent().children().first().next().next().children().val(); //Récupération du nombred d'heure
	modifFamille(id, nom, modele, heure); //Appel de la fonction modifFamille
})

/* Fonction permettant de modfifier un famille d'équipement
* @param
* id : Identifiant de la famille
* nom: Nom de la famille
* modele : Modele associé
* heure : Le nombre d'heure qu'il faut pointer
*/
function modifFamille (id, nom, modele, heure)
{
	$.ajax ({ //Requête AJAX
		type :'POST',
		url : 'modifFamille.php',
		data : 'idFamille=' + id + '&nomFamille=' + nom + '&modeleFamille=' + modele + '&heureFamille=' + heure,
		success : function (data){
		},
		error : function (data){
		}	
	})	
}

/*function famillePrecedente (){
	
	$.ajax ({
		
		type :'POST',
		url : 'famillePrecedente.php',
		success : function (data){
			
			if (data == "false"){
				
				$("#erreurSave").show();
				
			}else{
				
				$("#erreurSave").hide();
				console.log(data);
				swal({
	
					title : "Sauvegarde récuperées",
					text : "Mise à jour dans quelques instants",
					icon : "success"
					
				});
				setTimeout(redirection, 1000);
				}
		},
		error : function (data){
			
			$("#erreurSuppr").show();
		}	
	})
	
}*/

/* Fonction permettant d'jouter un nouvelle famille
*/
function ajoutFamille (){
	
	var famille = $("#ajoutFamille").val(); //Nom de la famille
	var modele = $("#ajoutModele").val(); //Nom du modele
	var heure = $("#ajoutHeure").val(); //Nombre d'heure
	var err = false;
	
	if (famille == "") //Vérification du champ
	{		
		$("#ajoutFamille").css ('border', '2px solid red');
		err = true;
	}
	if (heure == "") //Verification du champ
	{		
		$("#ajoutHeure").css ('border', '2px solid red');
		err = true;
	}
	
	if (err) $("#erreurInfo").show(); //Si un champ n'est pas rempli
	else {
		$.ajax ({ //Requête AJAX
			type :'POST',
			url : 'ajoutFamille.php',
			data : 'famille=' + famille + "&modele=" +modele+ "&heure=" +heure,
			success : function (data){
				if (data == "false") $("#doublon").show(); //Si la famille existe déjà
				else{
					//Masquage des alert
					$("#doublon").hide();
					$("#erreurSuppr").hide();
					$("#erreurInfo").hide();
					//Affichage de l'alert
					swal({
						title : "Informations validées",
						text : "La famille a été ajoutée avec succés",
						icon : "success"
					});
					setTimeout(redirection, 1000);
				}
			},
			error : function (data){
				$("#erreurSuppr").show();
			}	
		})
	}
}

/* Fonction permettant la redirection après la validation
*/
function redirection(){
	
	document.location.href="famille.php";
}

/* Fonction permettant de supprimer une famille
* @param
* id : Identifiant de la famille
*/
function supprimerFamille (id){
	$.ajax ({ //REquête AJAX
		type :'POST',
		url : 'supprimerFamille.php',
		data : 'famille=' + id,
		success : function (data){
			$("#erreurSuppr").hide(); //Masquage des erreurs
			swal({
				title : "Informations validées",
				text : "La famille a été supprimée avec succés",
				icon : "success"
			});
			setTimeout(redirection, 1000);
		},
		error : function (data){
			$("#erreurSuppr").show(); //Affichage des erreurs
		}	
	})	
}

/* Si le document est chargé
*/
$(document).ready(function() {

	$.fn.dataTableExt.ofnSearch["html-input"] = function(value){ //Activer le tri sur les valeurs des input
		return $(value).val();
	}
	$('#exemple').DataTable( {
		
		"order" : [[0, "desc"]], //Ordonnée les valeurs
		"columnDefs" : [{
			"type": "html-input", "targets" : [0,1,2]
		}],
		"language": {
			"sProcessing":     "Traitement en cours...",
			"sSearch":         "Rechercher&nbsp;:",
			"sLengthMenu":     "_MENU_ ",
			"sInfo":           "Affichage de la famille _START_ &agrave; _END_ sur _TOTAL_ familles",
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