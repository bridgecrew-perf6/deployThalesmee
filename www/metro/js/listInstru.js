var instruments_excel = [];

function takeFilter ()
{	
	document.location.href = "exportExcel_liste_instruments.php?filter="+instruments_excel;
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


function check(id){
	if($("#"+id).prop("checked") == true){
		instruments_excel.push(id);
	}else{
		instruments_excel = instruments_excel.filter(function(item){
			return item != id
		})
	}
}


/*Lors de la charge complète du document
*/
$(document).ready(function(){
	//Affichage du tableau
	var filter = getFilter();


	$('#tri').dataTable( {
		"oSearch" : {"sSearch" : filter},
		"bServerSide": true,
		"sAjaxSource": "../server_side/vib/servSide_listInstru_vib.php",
		"fnCreatedRow": function( nRow, aData, iDataIndex ) {
			$(nRow).css('cursor', 'pointer');
			$(nRow).prepend("<td><input class='text-center' id='"+aData[0].replace("/", "")+"' onclick='check(\""+aData[0]+"\")' type='checkbox'/></td>")
			if(instruments_excel.includes(aData[0])) {
				setTimeout(function (){
					$("input[type=checkbox]").each(function(){
						$("#"+aData[0].replace("/", "")).prop("checked", "true");
					})
				}, 100)
			}
			$(nRow).on('dblclick', function () {
				var filter = $("#tri_filter label input").val()
				document.location.href='detailsInstru.php?numInstru='+aData[0]+'&filter='+filter+''
			});
		}
	} ).columnFilter();

	
	$('#tri_filter input').attr("placeholder", "Rechercher");
	$('#tri_filter input').attr("class", "form-control");
	$('#tri_filter input').attr("style", "font-weight:normal;");
	$('#tri_length select').attr("class", "form-control");

	var listCapteur = [];

	$("thead tr").prepend("<th></th>");
	$("tfoot tr").prepend("<th></th>");

	$("#tri_next").mouseup(function (){
		setTimeout(function (){
			$("input[type=checkbox]").each(function(){
				if(instruments_excel.includes(this.id)) $("#"+this.id).prop("checked", "true");
			})
		}, 100)
		
	})

	$("#tri_previous").mouseup(function (){
		setTimeout(function (){
			$("input[type=checkbox]").each(function(){
				if(instruments_excel.includes(this.id)) $("#"+this.id).prop("checked", "true");
			})
		}, 100)
		
	})

});

