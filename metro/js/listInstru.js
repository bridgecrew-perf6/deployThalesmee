function takeFilter ()
{	
	$('#dialog_t').dialog('open');
}

/*Supprime les messages d'alerts
* @param
* elem : l'élement à supprimer
*/
function suppAlerte(elem)
{
	var element =  elem.parentNode.parentNode;
	element.parentNode.removeChild(element);
}

/*Affiche l'erreur
* @param
* dejaPresent : booléen qui indique si le capteur est déjà présent ou non
* num : numéro du capteur
*/
function afficheErreur (dejaPresent, num)
{
	//Si l'élément est déjà présent
	if (dejaPresent)
	{
		var child= document.createElement("div");
		child.className="text-center";
		child.innerHTML="<div class='alert alert-warning'><strong>Instrument n°"+num+" déja présent dans la liste </strong><input type='button' class='btn btn-warning' onclick='suppAlerte(this)' value='Ok' /></div>";
		$("#dialog_t").children().first().before(child);
	//Sinon l'élement n'existe pas
	}else
	{
		var child= document.createElement("div");
		child.className="text-center";
		child.innerHTML="<div class='alert alert-danger'><strong>Capteur n°"+num+" inconnue </strong><input type='button' class='btn btn-danger' onclick='suppAlerte(this)' value='Ok' /></div>";
		$("#dialog_t").children().first().before(child);
	}
}

/*Lors de la charge complète du document
*/
$(document).ready(function(){
	//Affichage du tableau
	$('#tri').dataTable( {
		"bServerSide": true,
		"sAjaxSource": "../server_side/vib/servSide_listInstru_vib.php",
		"fnCreatedRow": function( nRow, aData, iDataIndex ) {
			$(nRow).css('cursor', 'pointer');
			
			$(nRow).on('click', function () {
			document.location.href='detailsInstru.php?numInstru='+aData[0]+'';
			
		});
		}
	} ).columnFilter();
	$('#tri_filter input').attr("placeholder", "Rechercher");
	$('#tri_filter input').attr("class", "form-control");
	$('#tri_filter input').attr("style", "font-weight:normal;");
	$('#tri_length select').attr("class", "form-control");

	var listCapteur = [];

	//Construction la boite de dialogue
	$("#dialog_t").dialog({ 
		autoOpen: false,
		width: 400,
		modal: true,
		buttons: {
			//Clic sur valider lance la recherche
			"Ajouter": function() {
				$("#formDialog").submit();
				if($('#nouvNumCapt').val()!="")
				{	
					num = $('#nouvNumCapt').val();
					var url="./ajaxBidon.php?num="+num;
					$.getJSON( url, function(data) 
					{
						//Si le numero entré n'est pas déja dans la liste
						if(listCapteur.indexOf(num)==-1){
							//Ajout dans la liste
							listCapteur.push(num);
							//Affiche d'une alert du succès
							var child= document.createElement("div");
							child.className="text-center";
							child.innerHTML="<div class='alert alert-success'><strong>Instrument n°"+num+" ajouté à la liste </strong><input type='button' class='btn btn-success' onclick='suppAlerte(this)' value='Ok' /></div>";
							$("#dialog_t").children().first().before(child);
						}
						//Sinon affichage de l'erreur 
						else afficheErreur(true, num);
					})
					//Erreur l'élement n'existe pas
					.fail(function() {
						
						afficheErreur(false, num);
					});
					//Remise à zéro du champ
					$('#nouvNumCapt').val("");
				}
			},
			"Annuler": function() {
				//Réinitilisation de la liste
				listCapteur = [];
				//Fermeture de la pop-up
				$( this ).dialog( "close" );
			},
			"Envoyer" : function (){
				//Envoie de la liste pour l'export Excel
				document.location.href = "exportExcel_liste_instruments.php?filter="+listCapteur;
			}
		},
		open: function() {
			//Appuyer sur entrée simule un clic sur le bouton valider --> meme actions
			$(this).keypress(function(e) {
			  if (e.keyCode == $.ui.keyCode.ENTER) {
				$("#dialog_t").parent().find("button:eq(1)").trigger("click");
			  }
			});
		  }
	});
});

//Cache la pop-up pour entrer les numéros de capteurs
$('#dialog_design').hide();
