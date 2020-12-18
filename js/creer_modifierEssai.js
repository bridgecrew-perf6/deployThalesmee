//Ajouter un of obligatoire
function ajout_of(){
	var id="";
	if(document.getElementById('tabOf').rows.length==1) //si premiere ligne on ajoute l'id n_of1 (utile pour la douchette)
		id="n_of1";
		
	var newRow = document.getElementById('tabOf').insertRow(-1); 
	var newCell = newRow.insertCell(-1);
	newCell.innerHTML = '<input class="form-control" id="'+id+'" placeholder="N°OF"  type="text" name="n_of[]" required/>';
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = '<select class="form-control" name="modele[]" required><option value="" selected disabled>Type Modèle</option><option value="5" >EQM</option><option value="3" >PFM</option><option value="4" >FM</option>	<option value="1" >EM</option><option value="2" >QM</option><option value="6" >EBT</option><option value="7" >EXT</option></select>';
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = '<input class="form-control" placeholder="Article"  type="text" name="article[]"/>';
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = "<img class='imgSupp'  SRC='../img/supr.png' class='btnSuppLigne'/>";
	$(newCell).children().click(function() {
		suppLigne(this);
	});
}

function etapePrev(idEssai){

	document.location.href='etapePrecedente.php?idEssai='+idEssai;
}

function ajout_of_param(nOF){
	var id="";
	if(document.getElementById('tabOf').rows.length==1) //si premiere ligne on ajoute l'id n_of1 (utile pour la douchette)
		id="n_of1";
		
	var newRow = document.getElementById('tabOf').insertRow(-1); 

	var newCell = newRow.insertCell(-1);
	newCell.innerHTML = '<input class="form-control" id="'+id+'" placeholder="N°OF" value="'+nOF+'"  type="text" name="n_of[]" required/>';
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = '<select class="form-control" name="modele[]" required><option value="" selected disabled>Type Modèle</option><option value="5" >EQM</option><option value="3" >PFM</option><option value="4" >FM</option>	<option value="1" >EM</option><option value="2" >QM</option><option value="6" >EBT</option><option value="7" >EXT</option></select>';
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = '<input class="form-control" placeholder="Article" value=""  type="text" name="article[]" required/>';
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = "<img class='imgSupp'  SRC='../img/supr.png' class='btnSuppLigne'/>";
	$(newCell).children().click(function() {
		suppLigne(this);
	});
}

function suppLigne(elem)
{
	$(elem).parent().parent().remove();
}

//Supprimer le dernier of ajoutes	
function suppr_of(){	
}

function ajout_doc_utile()
{
	var newRow = document.getElementById('doc_utile').insertRow(-1); 
	var newCell = newRow.insertCell(-1);
	newCell.innerHTML = 'ref';
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = 'iss';
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = 'rev';
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = 'type';
	newCell = newRow.insertCell(-1);
	newCell.innerHTML = 'lien';
}


function infoOF(OF)
{
	jQuery.ajax({
		url:"../alice/AjaxInfoOF.php",
		type:"POST",
		data: 'OF='+OF,
		
		success: function(data){
			if(/^1/.test(data)) //si erreur (le retour commence par un 1)
				alert(data.substr(1));
			else
			{
				if(data!="")
				{
					data=data.substr(1);
					
					var res=data.substr(1).split('/');
					jQuery("#nom_aff").val(res[0]);
					jQuery("#nom_eq").val(res[1]);
					jQuery("#n_os").val(res[2].substr(0,res[2].length-2));
				}					
			}
		},
		error: function(){
			alert("Erreur ajax");
		}
	});
}

jQuery(document).ready(function() {
	var OF="";
	var cpt=0;
	
	jQuery(document).keypress(function(touche){ // ecoute l'evenement keyPress
		var tag = touche.target.tagName.toLowerCase(); //on recupere le focus actuel
		if (tag != 'input' && tag != 'textarea')  //on test que ce n'est pas un input ou un textarea
		{
			var codeTouche = touche.which || touche.keyCode; // le code est compatible tous navigateurs grâce à ces deux propriétés
			if(codeTouche==59 && cpt==6)
			{
				if(jQuery('#n_of1').val()!="") ajout_of_param(OF);
				else
				{
					jQuery('#n_of1').val(OF);
					//infoOF("PD0102");
					infoOF(OF);
				}
			}
			else
			{
				var lettre=String.fromCharCode(codeTouche);
				OF+=lettre;
				
			}
			if(cpt==0) // inutile de lancer la reinitialisation a chaque touche préssé, la premiere suffit
			{
				//reinitialise le compteur et la saisi
				setTimeout(function(){
					cpt=0;	
					OF="";
				;}, 500);
			}
			cpt++;	
		}
	});
	
	$(".js-example-basic-single").select2();
	$(".js-example-basic-single").change(function(){
		jQuery.ajax({
			url:"../autoComp/AjaxListDep.php",
			type:"GET",
			data: 'depTel='+$(this).val(),
			success: function(data){
				console.log(data);
				jQuery("#tel").val(data);
			},
			error: function(){
				alert("Erreur ajax");
			}
		});
	})
});

var ecart;
//Retourne la diffférence entre deux dates
function dateDiff(date1, date2){
	var diff = {}                           // Initialisation du retour
	var tmp = date2 - date1;
	
	tmp = Math.floor(tmp/1000);             // Nombre de secondes entre les 2 dates
	diff.sec = tmp % 60;                    // Extraction du nombre de secondes
	
	tmp = Math.floor((tmp-diff.sec)/60);    // Nombre de minutes (partie entière)
	diff.min = tmp % 60;                    // Extraction du nombre de minutes
 
	tmp = Math.floor((tmp-diff.min)/60);    // Nombre d'heures (entières)
	diff.hour = tmp % 24;                   // Extraction du nombre d'heures
	 
	tmp = Math.floor((tmp-diff.hour)/24);   // Nombre de jours restants
	diff.day = tmp;
	return diff;
}

function change_heure (){

	var famille = $("#famille").val();
	var fam = famille.split("-");
	famille = fam[1];
	var id="famille";
	var modele = fam[0];

	$.ajax ({
		type :'GET',
		url : 'ajaxFamille.php?famille=' + famille +'&modele=' +modele,
		success : function (data){
			heure = JSON.parse(data);
			$("#"+id).parent().next().children().val(heure[0]);
		},
		error : function (data){
			alert("Erreur requête AJAX");
		}	
	})	
}

$("#anomalie").change(function(){
	if($(this).is(':checked')) $("#anom").show();			
	else $("#anom").hide();	
});

function initDate (obj)
{
	var _date = $(obj).val().replace(/\s/g,"");
	var date = _date.split("/");
	var date1 = new Date (date[2]+"-"+date[1]+"-"+date[0]+" "+$("#hDebut").val()+":00");
	var date = $("#dateFin").val().split("/");
	var date2 = new Date (date[2]+"-"+date[1]+"-"+date[0]+" "+$("#hFin").val()+":00");
	ecart = dateDiff (date1, date2);
}

function majDateFin (obj)
{
	var _date = $(obj).val().replace(/\s/g,"");
	var date = _date.split("/");
	var date1 = new Date (date[2]+"-"+date[1]+"-"+date[0]+" "+$("#hDebut").val()+":00");
	date1.setDate(date1.getDate()+ecart.day);
	date1.setHours(date1.getHours()+ecart.hour);
	date1.setMinutes(date1.getMinutes()+ecart.min);

	var res ="";
	if (parseInt(date1.getDate()) < 10) res += "0"+date1.getDate()+"/";	
	else res += date1.getDate()+"/";

	var mois = parseInt(date1.getMonth())+1;

	if (mois > 11) res += "01/";	
	else if (mois < 10) res += "0"+mois+"/";	
	else  res += mois+"/";

	$("#dateFin").val(res+date1.getFullYear());
}

$("#dateDebut").change(function (){
	majDateFin($("#dateDebut"));		
});

$("#hDebut").change(function (){
	
	var _date = $("#dateDebut").val().replace(/\s/g,"");
	var date = _date.split("/");
	var date1 = new Date (date[2]+"-"+date[1]+"-"+date[0]+" "+$(this).val()+":00");
	date1.setDate(date1.getDate()+ecart.day);
	date1.setHours(date1.getHours()+ecart.hour);
	date1.setMinutes(date1.getMinutes()+ecart.min);

	var res_heure = "";
	var heure = date1.getHours();
	if (heure < 10) res_heure += "0"+heure+":";	
	else res_heure += heure+":";
	
	var minute = date1.getMinutes();
	if (minute < 10) res_heure += "0"+minute;	
	else res_heure += minute;
	
	$("#hFin").val(res_heure);
});

$("#dateDebut").click(function (){
	initDate($("#dateDebut"));	
});

$("#hDebut").click(function (){
	var _date = $("#dateDebut").val().replace(/\s/g,"");
	var date = _date.split("/");
	var date1 = new Date (date[2]+"-"+date[1]+"-"+date[0]+" "+$(this).val()+":00");
	var date = $("#dateFin").val().split("/");
	var date2 = new Date (date[2]+"-"+date[1]+"-"+date[0]+" "+$("#hFin").val()+":00");
	ecart = dateDiff (date1, date2);
});