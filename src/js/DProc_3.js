$(document).ready(function() {

	$('#dialog').hide();
	$('#dialog_liste').hide();
	
	$("#dialog").dialog({ 
		autoOpen: false,
		height: 390,
		width: 400,
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
	$("#dialog_liste").dialog({ 
		autoOpen: false,
		height:650 ,
		width: 900,
		modal: true,
		buttons: {
			"Valider": function() {
				selectList();
				this.addClass('btn btn-lg btn-primary');
			},
			"Annuler": function() {
				$( this ).dialog( "close" );
			}
		}
	});
	$(".ui-dialog button").addClass('ui-button ui-corner-all ui-widget');
	$("#tri").dataTable().columnFilter();
	$('#tri_filter input').attr("placeholder", "Rechercher");
	$('#tri_filter input').attr("class", "form-control");
	$('#tri_filter input').attr("style", "font-weight:normal;");
	$('#tri_length select').attr("class", "form-control");
	$("#tri").dataTable().fnSort( [ [1,'desc'] ] ); //tri sur la deuxieme colonne
	
	$("#enrDoc").click(function(){ 
		$('#dialog').dialog('open');					
	});
	
	$("#listDoc").click(function(){ 
		$('#dialog_liste').dialog('open');					
	});
	
	//autocompletion voir jquery-ui widget autocompletion
	jQuery('#ref').autocomplete({
		source : "../autoComp/AjaxListRef.php", //lit le fichier et recupere un JSon
		 select: function( event, ui ) { //quand on selection un dep on doit recuperer son telephone
			jQuery.ajax({
				url:"../autoComp/AjaxListRef.php",
				type:"GET",
				data: 'ref='+ui.item.label,
				success: function(data){
					var d=data.split("/");
					jQuery("#issue").val(d[0]);
					jQuery("#rev").val(d[1]);
					
					
				},
				error: function(){
					alert("Erreur ajax");
				}
			});
		}
	});
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
		url:"enregistrerDoc.php",
		contentType : false,
		processData : false,
		success: function(data){
			console.log(data)
			data=data.trim(); //on supprime des espaces qui peuvent se généré sur la page appelé
			if(/^1/.test(data)) //si erreur
				alert(data.substr(1));
			else
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
					
				 $("#tri").dataTable().fnAddData( [
					"<input type='checkbox' class='checkDoc' />",
					"<input type='hidden' value='"+tab[0].value+"' />"+nomDoc,
					tab[1].value,
					tab[2].value,
					tab[3].value,
					tab[4].value,
					"<a style='color:blue' href='download.php?link="+data.substr(1)+nomFic+"&nomOr="+nomFic+"'>"+nomFic+"</a>"
				] );
				$("#tri").dataTable().fnSort( [ [1,'desc'] ] );
				
				alert("Le document a bien été enregister");
				$('#dialog').dialog( "close" );
				
			}	
		},
		error: function(data){
			console.log(data)
			alert("Erreur d'enregistrement du document");
		}
	});
}


function selectList()
{
	//on recupere le numero des lignes cochés
	$('#tri .checkDoc:checked').each(function(){
		var idTypeDoc=$(this).parent().next().children().val();
		var nomDoc=$(this).parent().next().text();
		var ref=$(this).parent().next().next().text();
		var iss=$(this).parent().next().next().next().text();
		var rev=$(this).parent().next().next().next().next().text();
		ajoutDoc_Client_avecParam(ref,iss,rev,idTypeDoc,nomDoc,"",0);
		$('#dialog_liste').dialog( "close" );
	});



}


function validProc_3()
{
	var ref,issue,rev;
	var verif=true;
	//supprime les anciens alert
	$('.alert').each(function(){
		$(this).parent().remove();
	});
	
	//doc obligatoire
	//on verifie d'abord que le champs spec elec et env est rempli OU le champs elec et le champ spec
	if($('#ref_elec_env').val()!="" && $('#issue_elec_env').val()!="" 
	|| ($('#ref_elec').value!="" && $('#issue_elec').val()!="" 
	&& $('#ref_env').val()!="" && $('#issue_env').val()!=""))
	{
		
		//verification de l'existance des doc obligatoires
		if($('#ref_elec_env').val()!="" && $('#issue_elec_env').val()!="")//si doc env_elec renseigné
			verif=validDoc($('#ref_elec_env').val(),$('#issue_elec_env').val(),$('#rev_elec_env').val(),20,verif);
		if(($('#ref_elec').value!="" && $('#issue_elec').val()!="" && $('#ref_env').val()!="" && $('#issue_env').val()!=""))
		{
			if($('#ref_elec').length)
				verif=validDoc($('#ref_elec').val(),$('#issue_elec').val(),$('#rev_elec').val(),21,verif);
			verif=validDoc($('#ref_env').val(),$('#issue_env').val(),$('#rev_env').val(),22,verif);
		}
				
		
		//doc non obligatoire
		$('[name="ref_doc[]"]').each(function(){
			
			ref=$(this).val();
			issue=$(this).parent().next().children().val();
			rev=$(this).parent().next().next().children().val();
			idDoc=$(this).parent().prev().children().val();
			if(ref!="" && issue!="" || ref!="" && idDoc==26)
			{
				verif=validDoc(ref,issue,rev,idDoc,verif);
			}
			else
			{	
				var child= document.createElement("center");
				child.innerHTML='<div class="alert alert-warning"><strong>Veuillez remplir les champs optionnels</strong></div>';
				document.body.insertBefore(child, document.body.firstChild);
				verif= false;
			}
		});
			
	}
	else
	{
		var child= document.createElement("center");
		child.innerHTML='<div class="alert alert-warning"><strong>Veuillez remplir les champs obligatoires</strong></div>';
		document.body.insertBefore(child, document.body.firstChild);
		verif= false;
	}
	
	//on arrive ici seulement si l'on n'a pas traversé un seul des message d'erreur
	return verif;
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


function ajoutDoc_Client_avecParam(ref,iss,rev,idTypeDoc,nomDoc,com,mode)
{
	//mode pour évité le message a chaque retour sur la page, ne se produit que si l'on selectionne deux doc de meme type dans la liste
	var ajoutDoc=false;
	if(idTypeDoc==21 && document.getElementById('ref_elec').value!="" && document.getElementById('issue_elec').value!=""){
		if (mode==1 || confirm ('Un document de specification electrique est deja présent, voulez vous vraiment ajouter celui-ci ?')) 
			ajoutDoc=true;
	}
	else if(idTypeDoc==21 && document.getElementById('ref_elec').value=="" && document.getElementById('issue_elec').value==""){
		document.getElementById('ref_elec').value=ref;
		document.getElementById('issue_elec').value=iss;
		document.getElementById('rev_elec').value=rev;
		document.getElementById('tcom_elec').value=com;
		
	}
	else if(idTypeDoc==22 && document.getElementById('ref_env').value!="" && document.getElementById('issue_env').value!=""){ 
		if (mode==1 || confirm ('Un document de specification d\'environnement est deja présent, voulez vous vraiment ajouter celui ci ?')) 
			ajoutDoc=true;
	}
	else if(idTypeDoc==22 && document.getElementById('ref_env').value=="" && document.getElementById('issue_env').value==""){
		document.getElementById('ref_env').value=ref;
		document.getElementById('issue_env').value=iss;
		document.getElementById('rev_env').value=rev;
		document.getElementById('tcom_env').value=com;
	}
	else if(idTypeDoc==20 ){
		document.getElementById('ref_elec_env').value=ref;
		document.getElementById('issue_elec_env').value=iss;
		document.getElementById('rev_elec_env').value=rev;
		document.getElementById('tcom_elec_env').value=com;
	}
	else
		ajoutDoc=true;
	
	if(ajoutDoc)
	{
		var newRow = document.getElementById('tableRefSpec').insertRow(-1);
		var newCell = newRow.insertCell(-1);
		newCell.className = 'td-label';
		//on ajoute un champs hidden pour le type du doc
		newCell.innerHTML = '<input type="hidden" name="idType[]" value="'+idTypeDoc+'"/>'+nomDoc; 
		
		newCell = newRow.insertCell(-1);
		newCell.innerHTML = '<input class="form-control" placeholder="Référence"  name="ref_doc[]" value="'+ref+'" />';
		
		newCell = newRow.insertCell(-1);
		newCell.innerHTML = '<input class="form-control" placeholder="Issue"  name="issue_doc[]" value="'+iss+'" />';
		
		newCell = newRow.insertCell(-1);
		newCell.innerHTML = '<input class="form-control" placeholder="Révision"  name="rev_doc[]" value="'+rev+'" />';
		
		newCell = newRow.insertCell(-1);
		newCell.innerHTML = '<input id="tcom" placeholder="Com / Type Article" class="form-control" name="com_doc[]" value="'+com+'" />';
	}
}

function supprDOC_Client(){
	
	var nbLigne=document.getElementById('tableRefSpec').rows.length -5;
	if (confirm ('Voulez vous supprimer ces documents ?')){
		for (var i=0;i<nbLigne;i++){
				document.getElementById('tableRefSpec').deleteRow(-1);		
		}

			
			var nbCol=document.getElementById('tableRefSpec').rows[0].cells.length-1;
			var lesInp=document.getElementById('tableRefSpec').getElementsByTagName('input');
			
			for(var j=0;j<nbCol*3;j++)
				lesInp[j].value="";
			

	}
}



