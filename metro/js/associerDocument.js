$(document).ready(function(){

	cpt = 0;

	tableau = $('#res').DataTable( {
		"order" : [[0, "desc"]],
		"columnDefs" : [{
			"type": "html-input", "targets" : [0]
		}],
		"language": {
			"sProcessing":     "Traitement en cours...",
			"sSearch":         "Rechercher&nbsp;:",
			"sLengthMenu":     "_MENU_ ",
			"sInfo":           "Affichage de l'instrument _START_ &agrave; _END_ sur _TOTAL_ instruments",
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

	$("#res tbody").on("click", 'div', function(){
		tableau.row($(this).parent().parent()).remove().draw();
	})

	//Affichage du tableau
	$('#tri').dataTable( {
		"bServerSide": true,
		"sAjaxSource": "../server_side/vib/servSide_listInstru_vib.php",
		"language": {
			"sProcessing":     "Traitement en cours...",
			"sSearch":         "Rechercher&nbsp;:",
			"sLengthMenu":     "_MENU_ ",
			"sInfo":           "Affichage de l'instrument _START_ &agrave; _END_ sur _TOTAL_ instruments",
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
		},
		"fnCreatedRow": function( nRow, aData, iDataIndex ) {

			$(nRow).css('cursor', 'pointer');

			$(nRow).on('click', function () {
				$(nRow).find("td").each(function (){
					$(this).css("background-color", "#dff0d8");
				})
				var num = $(this).children().first().text();

				if ($("#"+num).length == 0)
				{
					var type = $(this).children().first().next().text();
					var des = $(this).children().first().next().next().text();
					var fourn = $(this).children().first().next().next().next().text();
					var model = $(this).children().first().next().next().next().next().text();
					var numSerie = $(this).children().first().next().next().next().next().next().text();
					var dateFI = $(this).children().first().next().next().next().next().next().next().text();
					var local = $(this).children().first().next().next().next().next().next().next().next().text();
					tableau.row.add(['<input class="form-control" name="numInstru[]" type="hidden" value="'+num+'">'+num, type, des, fourn, model, numSerie, dateFI, local, "<div class='btn btn-danger btn-block'>Supprimer</div>"]).node().id = num;
					tableau.draw();

					cpt ++;
				}else{

					swal({

						title : "Impossible d'ajouter l'instrument",
						text : "Instrument déjà présent",
						icon : "error"
						
					});
				}
				
				console.log("<input type='button' class='btn btn-danger />");

			});
		}
	} ).columnFilter();
	$('#tri_filter input').attr("placeholder", "Rechercher");
	$('#tri_filter input').attr("class", "form-control");
	$('#tri_filter input').attr("style", "font-weight:normal;");
	$('#tri_length select').attr("class", "form-control");


})
