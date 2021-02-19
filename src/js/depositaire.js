$(".alert").hide();

$(".input").change(function (){
	var id =$(this).parent().parent().attr('id');
	var nom = $(this).parent().parent().children().first().children().val();
	var prenom = $(this).parent().parent().children().first().next().children().val();
	var tel = $(this).parent().parent().children().first().next().next().children().val();
	var portable = $(this).parent().parent().children().first().next().next().next().children().val();
	modifDepositaire(id, nom, prenom, tel, portable);
})

function confirmation (id){
	
	if (confirm ("Voulez-vous vraiment supprimer ce dépositaire ?"))
	{
		supprimerDepositaire(id);
	}
}


function ajoutDepositaire (){
	
	var nom = $("#nomDep").val();
	var prenom = $("#prenomDep").val();
	var tel = $("#telDep").val();
	var portable = $("#portableDep").val();

	var cpt = ($('tr').length) -1;
	var err = false;
	
	if (nom == ""){
		
		$("#nomDep").css ('border', '2px solid red');
		err = true;
	}
	if (prenom == ""){
		
		$("#prenomDep").css ('border', '2px solid red');
		err = true;
		
	}
	
	if (err) $("#erreurInfo").show();
	else {
		$.ajax ({
		
			type :'POST',
			url : 'ajoutDepositaire.php',
			data : 'nomDep=' + nom + "&prenomDep=" +prenom+ "&telDep=" +tel+"&portableDep=" + portable,
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
	
	document.location.href="depositaire.php";
}

function supprimerDepositaire (id){
	
	$.ajax ({
		
		type :'POST',
		url : 'supprimerDepositaire.php',
		data : 'idDep=' + id,
		success : function (data){

			$("#erreurSuppr").hide();
			if (data == "false")
			{
				swal({
	
					title : "Impossible de supprimer le dépositaire",
					text : "Le dépositaire est associé à un essai",
					icon : "error"
					
				});
			}else
			{
				swal({
	
					title : "Informations validées",
					text : "Le dépositaire a été supprimé avec succés",
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

function modifDepositaire (id, nom, prenom, tel, portable)
{
	$.ajax ({
		
		type :'POST',
		url : 'modifDepositaire.php',
		data : 'idDep=' + id + '&nomDep=' + nom + '&prenomDep=' + prenom + '&telDep=' + tel + '&portableDep=' + portable,
		success : function (data){
			
			$("#erreurModif").hide();
			console.log(data);			
		},
		error : function (data){
			
			$("#erreurModif").show();
			console.log(data);

		}	
	})	
}


$(document).ready(function() {

	$.fn.dataTableExt.ofnSearch["htmliinput"] = function(value){
		return $(value).val();
	}
	$('#exemple').DataTable( {
		
		"order" : [[0, "desc"]],
		"columnDefs" : [{
			"type": "html-input", "targets" : [0,1,2,3]
		}],
		"language": {
			"sProcessing":     "Traitement en cours...",
			"sSearch":         "Rechercher&nbsp;:",
			"sLengthMenu":     "_MENU_ ",
			"sInfo":           "Affichage du dépositaire _START_ &agrave; _END_ sur _TOTAL_ dépositaires",
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