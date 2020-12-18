$(document).ready(function() {
	$('#dialog').hide();
		
	$("#dialog").dialog({ 
		autoOpen: false,
		height: 420,
		width: 450,
		modal: true,
		buttons: {
			"Valider": function() {
				envoyerDoc();
				this.addClass('btn btn-lg btn-primary');
			},
			"Annuler": function() {
				$( this ).dialog( "close" );
			}
		}
	});
	
	$(".ui-dialog button").addClass('ui-button ui-corner-all ui-widget');
	$("#tri").dataTable();
	$('#tri_filter input').attr("placeholder", "Rechercher");
	$('#tri_filter input').attr("class", "form-control");
	$('#tri_filter input').attr("style", "font-weight:normal;");
	$('#tri_length select').attr("class", "form-control");
	$("#tri").dataTable().fnSort( [ [0,'desc'] ] ); //tri sur la premiere colonne
	
	$("#enrDoc").click(function(){ 
		$('#dialog').dialog('open');					
	});
		
	function envoyerDoc()
	{
		//var formData = new FormData();
		var form = $('#formDoc');
		var formdata = false;
		if (window.FormData){
			formdata = new FormData(form[0]);
		}
		else
			alert("Ce navigateur ne supporte pas la technologie employé pour l'envoi de fichier");
		
		//envoie des parametres a la page
		jQuery.ajax({
			type:"POST",
			data: formdata ? formdata : form.serialize(),
			url:"../demande/enregistrerDoc.php",
			contentType : false,
			processData : false,
			success: function(data){
				data=data.trim(); //on supprime des espaces qui peuvent se généré sur la page appelé
				if(/^1/.test(data)) //si erreur (le retour commence par un 1)
					alert(data.substr(1));
				else //sinon le retour commence par un 0 -> pas d'erreur
				{
					//on ajoute la ligne a la liste des docs
					var tab=$('#formDoc').serializeArray(); //forme un tableau a partir des arguments du formulaire
					var nomDoc;
					if(tab[0].value==20)
						nomDoc="Spécifications Electrique et Environnement";
					else if(tab[0].value==21)
						nomDoc="Spécifications Electrique";
					else if(tab[0].value==22)
						nomDoc="Spécifications Environnement";
					else if(tab[0].value==23)
						nomDoc="RFD Electrique";
					else if(tab[0].value==24)
						nomDoc="RFD Mécanique";
					else if(tab[0].value==25)
						nomDoc="RFD Thermique";
					else if(tab[0].value==26)
						nomDoc="Autre";
					var nomFic=$("#file").val();//recupere le nom du fichier (sauf sous ie, donne le chemin entier...faille de sécurité)	
					
					if(admin) //si page aadmin on ajoute l'image de suppression
					{
						$("#tri").dataTable().fnAddData( [
							nomDoc,
							tab[1].value,
							tab[2].value,
							tab[3].value,
							tab[4].value,
							"<a style='color:blue' href='download.php?link="+data.substr(1)+nomFic+"&nomOr="+nomFic+"'>"+nomFic+"</a>",
							"<IMG style='cursor:pointer;float:right;max-height:20px' SRC='../img/supr.png' onclick='confirmSuppr(\""+data.substr(1)+"\");' />"
						] );					
					}
					
					else
					{
						 $("#tri").dataTable().fnAddData( [
							nomDoc,
							tab[1].value,
							tab[2].value,
							tab[3].value,
							tab[4].value,
							"<a style='color:blue' href='download.php?link="+data.substr(1)+nomFic+"&nomOr="+nomFic+"'>"+nomFic+"</a>"
							
						] );
					}
					$("#tri").dataTable().fnSort( [ [0,'desc'] ] );
					
					alert("Le document a bien été enregister");
					$('#dialog').dialog( "close" );
					
				}	
			},
			error: function(){
				alert("Erreur d'enregistrement du document");
			}
		});
	}
	
	function validDoc(ref,issue,rev,idDoc,verif)
	{
		var retour=jQuery.ajax({
			type:"POST",
			data: "ref="+ref+"&issue="+issue+"&rev="+rev+"&idDoc="+idDoc,
			url:"verifDoc.php",
			async: false, //passe la fonction en synchrone, on veut attendre la fin de toute les requete pour passer a la suite
			error: function(){
				alert("Erreur de verification du document");
				return false;
			}
		}).responseText;
		
		retour=retour.trim(); //on supprime des espaces qui peuvent se généré sur la page appelé
		if (retour.match(/^E/)){ //si le retour commencer par 'E' (message d'erreur), on affiche dans un alert
			alert(retour);
			return false;
		}
		else //sinon 
		{
			if(retour!="") //si renvois quelque chose on affiche ce message dans des div car erreur venant de l'utilisateur
			{
				var child= document.createElement("center");
				child.innerHTML='<div class="alert alert-warning"><strong>Le document '+retour+' n\'est pas présent dans la base de données</strong></div>';
				document.body.insertBefore(child, document.body.firstChild);
				return false;
			}
			//dans le cas ou tout c'est bien passer on ne change rien
		}
		return verif; //si on arrive jusqu'ici, pas d'erreur, on renvoi la valeur que contient deja verif (true a la base, false si il y a deja eu une erreur)
	}
	
	$('#form').on('submit',function(){
	
		return validProc_3();
	
	});
});
