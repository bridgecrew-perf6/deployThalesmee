function confirmSupp(numInstru)
{
	var motif = prompt("Veuillez entrer un motif de suppression")
	if(confirm("Voulez vous vraiment supprimer cet instrument ?"))
		document.location.href='./suppInstru.php?numInstru='+numInstru+"&motif="+motif;
	return false;
}

//supprime une ligne selon l'id
function deleteRow(rowid)  
{   
    var row = document.getElementById(rowid);
    row.parentNode.removeChild(row);
}

//n'existe que pour les vib -> suppression d'une ligne dans l'historique de sensibilité
function confirmSupprHisto(idHisto)
{
	if(confirm("Voulez vous vraiment supprimer cette ligne de l'historique ?"))
	{
		$.ajax({
			url:"./ajaxSupprHistoSensi.php",
			type:"GET",
			data: 'idHisto='+idHisto,
			error: function(){
				alert("Erreur ajax");
				ok=false;
			}
		});
		deleteRow(idHisto);
	}
}


$(document).ready(function() {
	//Bouton +/- sur les pages details/modif
	$("#b_det").on('click', function () {
		$('#detailsSup').slideToggle(300);
		if($(this).val()=="+")
		{
			$(this).val("-");
			$("#div_info_aff").css({
				"top": $("#info_aff").top - $("#div_info_aff").height() -40 + "px",
				"left": $("#info_aff").left + "px",
			});
			
		}
		else
		{
			$(this).val("+");
		}
	});
	
	//quand on change le type d'équipement, on propose les fonctions associés
	$('#equip').on('change', function() {
		var url="./ajaxModifInstru.php?equip="+this.value;
		$.getJSON( url, function(data) {
			//supprime les anciens option du select fonction
			$('#fonc option').filter(function() {
				return +this.value > 0;
			}).remove();
			//ajoute les option correspondant a l'equipement choisi
			$.each(data["equip"], function(key, value) {   
				$('#fonc')
				.append($("<option></option>")
				.attr("value",value["idDes"])
				.text(value["fonction"])); 
			});
		})
		.fail(function() {
			alert("Erreur Ajax");
		});
	});
	
	//quand on change le domaine d'un équipement, on propose les désignation associé
	$('#dom').on('change', function() {
		var url="./ajaxCreerInstru.php?dom="+this.value;
		$.getJSON( url, function(data) {
			//supprime les anciens option du select fonction
			$('#des option').filter(function() {
				return +this.value > 0;
			}).remove();
			//ajoute les option correspondant au domaine choisi
			$.each(data["des"], function(key, value) {
				$('#des')
				.append($("<option></option>")
				.attr("value",value["idDes"])
				.text(value["nomDes"])); 
			});
		})
		.fail(function() {
			alert("Erreur Ajax");
		});
	});
	
	//permet de montrer la partie sensibilité lors d'un ajout
	$("#capt").change(function() {
		if($(this).prop('checked')) //si coché
		{
			//on affiche la partie sensi
			$('#sensi').show();
			
			//l'axe X + sensi X au minimum est requis pour valider
			$("#axeX").attr('required', '');
			$("#sensiX").attr('required', '');
			$("#typeC").attr('required', '');
			$("#unite").attr('required', '');
			$("#dSensi").attr('required', '');
		}
			
		else
		{
			//on masque la partie sensi
			$('#sensi').hide();
			//on retire le required de l'axe et sensi X
			$("#axeX").removeAttr('required');
			$("#sensiX").removeAttr('required');
			$("#typeC").removeAttr('required');
			$("#unite").removeAttr('required');
			$("#dSensi").removeAttr('required');
			$("#axeY").removeAttr('required');
			$("#sensiY").removeAttr('required');
			$("#axeZ").removeAttr('required');
			$("#sensiZ").removeAttr('required');
			$("#type").removeAttr('required');
		}
	});

	//par défaut on cache la partie sensibilité
	$('#sensi').hide();
	//on retire le required de l'axe et sensi X
	$("#axeX").removeAttr('required');
	$("#sensiX").removeAttr('required');
	$("#typeC").removeAttr('required');
	$("#unite").removeAttr('required');
	$("#dSensi").removeAttr('required');
	$("#axeY").removeAttr('required');
	$("#sensiY").removeAttr('required');
	$("#axeZ").removeAttr('required');
	$("#sensiZ").removeAttr('required');
	$("#type").removeAttr('required');

});
