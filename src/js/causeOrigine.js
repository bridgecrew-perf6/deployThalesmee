$(".alert").hide();

$(".input").change(function (){
	var id =$(this).parent().parent().attr('id');
	var nom = $(this).parent().parent().children().first().children().val();
	modifCause(id, nom);
})

function modifCause (id, nom)
{
	$.ajax ({
		
		type :'POST',
		url : 'modifCause.php',
		data : 'idCause=' + id + '&nomCause=' + nom,
		success : function (data){
		
		},
		error : function (data){

		}	
	})	
}

function confirmation (id){
	
	if (confirm ("Voulez-vous vraiment supprimer cette cause ?"))
	{
		supprimerFamille(id);
	}
}

function ajoutCause (){
	
	var cause = $("#ajoutCause").val();
	var cpt = ($('tr').length) -1;
	var err = false;
	
	if (cause == ""){
		
		$("#ajoutCause").css ('border', '2px solid red');
		err = true;
	}
	
	if (err) $("#erreurInfo").show();
	else {
		$.ajax ({
		
			type :'POST',
			url : 'ajoutCause.php',
			data : 'cause=' + cause,
			success : function (data){

				if (data == "false"){
					
					$("#doublon").show();
					
				}else{
					
					$("#doublon").hide();
					$("#erreurSuppr").hide();
					$("#erreurInfo").hide();

					swal({
	
						title : "Informations validées",
						text : "Le dépositaire a été ajouté avec succés",
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

function redirection(){
	
	document.location.href="causeOrigine.php";
}

function supprimerFamille (id){
	
	$.ajax ({
		
		type :'POST',
		url : 'supprimerCause.php',
		data : ' cause=' + id,
		success : function (data){
			
			$("#erreurSuppr").hide();
			console.log(data);
			swal({
	
				title : "Informations validées",
				text : "La cause a été supprimée avec succés",
				icon : "success"
				
			});
			setTimeout(redirection, 1000);
			
		},
		error : function (data){
			
			$("#erreurSuppr").show();

		}	
	})	
	
}
$(document).ready(function() {

	$.fn.dataTableExt.ofnSearch["html-input"] = function(value){
		return $(value).val();
	}
	$('#exemple').DataTable( {
		
		"order" : [[0, "desc"]],
		"columnDefs" : [{
			"type": "html-input", "targets" : [0]
		}],
		"language": {
			"sProcessing":     "Traitement en cours...",
			"sSearch":         "Rechercher&nbsp;:",
			"sLengthMenu":     "_MENU_ ",
			"sInfo":           "Affichage de la cause _START_ &agrave; _END_ sur _TOTAL_ causes",
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