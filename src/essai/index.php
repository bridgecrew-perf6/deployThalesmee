<?php

$affichage =  "";
$labo= "";
require('top.php');
require('../conf/connexion_param.php');


$nbJours = 31;
$str="select idMoyen, nomMoyen from moyen where idService_service='".$labo."';";
$reqMoyen=mysqli_query($bdd, $str);
$str="select idEtat, nomEtat from etat where idEtat>=20 and idEtat<=27;";
$reqEtat=mysqli_query($bdd, $str);

?>
<link rel="stylesheet" href="../jquery-ui/css/redmond/jquery-ui.min.css">
<div id="se-pre-con-load" ></div>
<center>
	<div class="flex-container" id="agenda-header">

		<div class="flex-button">
		<?php
		
			if ($affichage == "semaine"){
				
				echo '<button type="button" class="btn btn-default btn-arrow-left" id="sem_prec" >Semaine précédente</button>
				<button type="button" class="btn btn-default btn-arrow-left" id="jour_prec" >Jour précédent</button>
				<button type="button" class="btn btn-link" id="infos_date">Link</button>
				<button type="button" class="btn btn-default btn-arrow-right" id="jour_suiv">Jour suivant</button>
				<button type="button" class="btn btn-default btn-arrow-right" id="sem_suiv">Semaine suivante</button>';
				
			}else {
				
				echo '<button type="button" class="btn btn-default btn-arrow-left" id="mois_prec" >Mois précédent</button>
				<button type="button" class="btn btn-default btn-arrow-left" id="sem_prec" >Semaine précédente</button>
				<button type="button" class="btn btn-link" id="infos_date">Link</button>
				<button type="button" class="btn btn-default btn-arrow-right" id="sem_suiv">Semaine suivante</button>
				<button type="button" class="btn btn-default btn-arrow-right" id="mois_suiv">Mois suivant</button>';
				
			}
		?>
			<div class="width" style="background-color:#F2F2F2; border-radius:10px;">
				<table class="first" style="border-spacing:2px 2px; text-align:center;">
					<tr>
						<td class="first"><div class="width vertical-align" style="padding:0" id="ici"></div></td>
						<td class="first"><strong style="opacity:0">---------</strong><span class='carre bar-plannifie'></span><strong>---------</strong><p class="retourLigne font">Essai planifié</p></td>

						<td class="first"><strong>---------></strong><span class='carre bar-reserve'></span><strong>---------</strong><p class="retourLigne font">Essai reservé</p></td>

						<td class="first"><strong>--------></strong><span class='carre bar-attente'></span><strong>---------</strong><p class="retourLigne font">Essai en attente</p></td>

						<td class="first"><strong>--------></strong><span class='carre bar-cours'></span><strong>---------</strong><p class="retourLigne font">Essai en cours</p></td>

						<td class="first"><strong>--------></strong><span class='carre bar-fin'></span><strong>---------</strong><p class="retourLigne font">Essai terminé</p></td>

						<td class="first"><strong>--------></strong><span class='carre bar-retour'></span><strong>---------</strong><p class="retourLigne font">Retour équipement </p></td>

						<td class="first"><strong>----------></strong><span class='carre bar-validation'></span><strong style="opacity:0">----------></strong><p class="retourLigne font">Validation PV</p></td>

					</tr>
				</table>
			</div>
		</div>
		<div class="flex-legend">
			<table style="border-spacing:2px 2px; text-align:center;">
				<tr>
					<th rowspan="2" id="la">

						<select id="chkveg" multiple="multiple" style="position:relative; height:100%;">
							<?php
							while($lg=mysqli_fetch_object($reqEtat))
							{
								$selected="selected";
								if(isset($_SESSION['essaiFiltre']) && !in_array($lg->idEtat, $_SESSION['essaiFiltre']) || !isset($_SESSION['essaiFiltre']) && $lg->idEtat
==20)
									$selected="";

								echo '<option value="'.$lg->idEtat.'" '.$selected.' >'.$lg->nomEtat.'</option>';
							}
							?>
						</select>
					</th>

					<td class="second"><strong style="opacity:0">-----------</strong><span class='carre bar-plannifie'></span><strong>-----------</strong><p class="retourLigne font">Essai planifié</p></td>
					
					<td class="second"><strong>----------></strong><span class='carre bar-reserve'></span><strong>-----------</strong><p class="retourLigne font">Essai reservé</p></td>

					<td class="second"><strong>----------></strong><span class='carre bar-attente'></span><strong>------------</strong><p class="retourLigne font">Essai en attente</p></td>

					<td class="second"><strong>----------></strong><span class='carre bar-cours'></span><strong>-----------</strong><p class="retourLigne font">Essai en cours</p></td>

					<td class="second"><strong>----------></strong><span class='carre bar-fin'></span><strong>-----------</strong><p class="retourLigne font">Essai terminé</p></td>

					<td class="second"><strong>----------></strong><span class='carre bar-retour'></span><strong>-----------</strong><p class="retourLigne font">Retour équipement</p></td>

					<td class="second"><strong>----------></strong><span class='carre bar-validation'></span><strong style="opacity:0">----------></strong><p  class="retourLigne font">Validation PV</p></td>


				</tr>
				<tr>
					<th class="first">			

						<div class="col-md-2">
							<div class="width"><span class='rondOrange'></span><p class="font">Retard livraison</p></div> 
						</div>
						<div class="col-md-2">
							<div class="width"><span class='rondRouge'></span><p class="font">Non planifié</p></div>
						</div>
						<div class="col-md-2">
							<div class="width"><span>&#x26A0;</span><p class="font">Retard ME</p></div>
						</div>
						<div class="col-md-3">
							<div class="width"><span class='carre carreRouge'></span><p class="font ">Anomalie</p></div>
						</div>
						<div class="col-md-2" style="padding-left:0px;">
							<div class="width"><span class='carre bar-maintenance'></span><p class="font">Maintenance</p></div>
						</div>

					</th>
					<td class="second"><strong style="opacity:0">-----------</strong><span class='rondOrange'></span><strong style="opacity:0">-----------</strong><p  
style="padding-left:15px;" class="retourLigne font">Retard Livraison</p></td>
					<td class="second"><strong style="opacity:0">----------></strong><span class='rondRouge'></span><strong style="opacity:0">-----------</strong><p 
class="retourLigne font">Non planifié</p></td>
					<td class="second"><strong style="opacity:0">----------></strong><span>&#x26A0;</span><strong style="opacity:0">-----------</strong><p class=
"retourLigne font">Retard ME</p></td>
					<td class="second"><strong style="opacity:0">----------></strong><span class='carre carreRouge'></span><strong style="opacity:0">-----------
</strong><p style="padding-left:5px" class="retourLigne font">Anomalie</p></td>
					<td class="second"><strong style="opacity:0">----------></strong><span class='carre bar-maintenance'></span><strong style="opacity:0">-----------
</strong><p style="padding-left:5px" class="retourLigne font">Maintenance</p></td>
					<td></td>
					

				</tr>

			</table>
			
		</div>
	</div>
</center>

<div class="div-agenda" id="div-agenda">
	<table class="table-agenda" id='agend'>
		<thead>
			<tr>
			<th <?php if ($affichage == 'mois') echo ' colspan=3 '; ?>>
					<select id="moyenCheck" multiple="multiple">
						<?php
						while($lg=mysqli_fetch_object($reqMoyen))
						{
							$selected="selected";
							if(isset($_SESSION['moyenFiltre']) && !in_array($lg->idMoyen, $_SESSION['moyenFiltre']))
								$selected="";
							echo '<option value="'.$lg->idMoyen.'" '.$selected.' >'.$lg->nomMoyen.'</option>';
						}
						?>
					</select>
				</th>
				<?php
				
				if ($affichage == "mois"){
					
					for ($i=0; $i<$nbJours; $i++){
						
						echo '<th rowspan="2"></th>';
						
					}
				}else{
					
					echo '<th rowspan="2" colSpan="2"></th>
				<th rowspan="2" colSpan="2"></th>
				<th rowspan="2" colSpan="2"></th>
				<th rowspan="2" colSpan="2"></th>
				<th rowspan="2" colSpan="2"></th>
				<th rowspan="2" colSpan="2"></th>
				<th rowspan="2" colSpan="2"></th>';
					
				}
				?>
				
			</tr>
			<tr>
				<th <?php if ($affichage == 'mois') echo ' colspan=3 '; ?>>
				<center><input id="toggle" data-on="Mois" <?php if ($affichage == "mois") echo " checked " ?> data-width="75" data-size="mini" data-off="Semaine" type="checkbox" data-toggle="toggle" /><center>
				</th>
			</tr>
		</thead>
		<tbody id="tb_agend">
		</tbody>
	</table>
	<div class="row" id="tab_att">
		<div class="col-md-5">
			<h4>Essais non planifiés en attente</h4>
			<div class="table-responsive">
				<table class="jumbotron table table-striped" id='att'>
					<thead>
					<tr>
						<th>Badge</th>
						<th>Affaire</th>
						<th>Équipement</th>
					</tr>
					</thead>
					<tbody id="tb_att">
					</tbody>
				</table>
			</div>
		</div>
	</div>			
</div>
<div id="dialog" title="Information">
	<p id="dialText"></p>
</div>
<script src="../jquery-ui/js/jquery-ui.min.js"></script>
<script src="../bootstrap/js/bootstrap-multiselect.js"></script>
<script src="../js/loading.js"></script>
<?php

if ($affichage == "mois"){
	
	echo '<script>var affichage = "mois";</script>';
	
}else{
	
	echo '<script>var affichage = "semaine";</script>';
}
?>
<script src="../js/essaiindex.js"></script>
<script>
var labo = <?php echo $labo; ?>;
var mtre;
function montre(event, id){

	var x_parent = $("#"+id).position().right;
	mtre = setTimeout(function (){ 

		var x = event.clientX - x_parent;
		var y = event.clientY;
		$("#infobulle"+id).css ("left", x);
		
		$("#infobulle"+id).show(); 

	}, 650);
}
function cache(id){

	$("#infobulle"+id).hide();
	clearTimeout(mtre);
}
if (document.body.clientWidth > 1894){
	
	$(".first").hide();
	$(".second").show();
	
}else{
	$(".first").show();
	$(".second").hide();
	var copie = $('#chkveg').clone();
	$('#chkveg').remove();
	copie.appendTo("#ici");
}
$(window).resize(function(e){
	
	if (e.target == this){
		window.location.href =  window.location.href;
	}

});	

$(function(){
	$("#toggle").change (function () {
		
		if ($(this).prop('checked')){
			
			jQuery.ajax({
				url:"setDisplay.php",
				type:"GET",
				data: 'toggle=false',
				
				success: function(data){
					document.location.href="index.php";
				},

			});
		}else{
			
			jQuery.ajax({
				url:"setDisplay.php",
				
				success: function(data){
					document.location.href="index.php";
				},

			});
			
		}
	});
})

</script>

<?php
require('bottom.php');
