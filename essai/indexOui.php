<?php
require('top.php');
require('../conf/connexion_param.php');
$str="select idMoyen, nomMoyen from moyen where idService_service='".$_SESSION['infoUser']['idService']."';";
$reqMoyen=mysqli_query($bdd, $str);
$str="select idEtat, nomEtat from etat where idEtat>=20 and idEtat<=27;";
$reqEtat=mysqli_query($bdd, $str);

$labo=$_SESSION["infoUser"]["idService"];
?>


<div class="agenda-header">
	<span class="agenda-btn">
		<button type="button" class="btn btn-default btn-arrow-left" id="mois_prec" >Mois précédent</button>
		<button type="button" class="btn btn-default btn-arrow-left" id="sem_prec" >Semaine précédente</button>
		<button type="button" class="btn btn-default btn-arrow-left" id="jour_prec" >Jour précédent</button>
		<button type="button"  class="btn btn-link" id="infos_date">Link</button>
		<button type="button" class="btn btn-default btn-arrow-right" id="jour_suiv">Jour suivant</button>
		<button type="button" class="btn btn-default btn-arrow-right" id="sem_suiv">Semaine suivante</button>
		<button type="button" class="btn btn-default btn-arrow-right" id="mois_suiv">Mois suivant</button>
	</span>
</div>
<div class="div-agenda">
	<table class="table table-agenda" id='agend'>
		<thead>
			<tr>
				<th>
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
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
			</tr>
		</thead>
		<tbody id="tb_agend">
		</tbody>
	</table>
	<div class="legende" id="leg">
		<select id="chkveg" multiple="multiple" class="form-control">
			<?php
			while($lg=mysqli_fetch_object($reqEtat))
			{
				$selected="selected";
				if(isset($_SESSION['essaiFiltre']) && !in_array($lg->idEtat, $_SESSION['essaiFiltre']) || !isset($_SESSION['essaiFiltre']) && $lg->idEtat==20)
					$selected="";

				echo '<option value="'.$lg->idEtat.'" '.$selected.' >'.$lg->nomEtat.'</option>';
			}
			?>
		</select>
		<ul>
			<li><span class='carre bar-planifie'></span> Essai plannifié</li>
			<li><span class='carre bar-reserve'></span> Essai reservé</li>
			<li><span class='carre bar-attente'></span> Essai en attente</li>
			<li><span class='carre bar-cours'></span> Essai en cours</li>
			<li><span class='carre bar-fin'></span> Essai terminé</li>
			<li><span class='carre bar-retour'></span> Essai terminé et retour équipement</li>
			<li><span class='carre bar-maintenance'></span> Maintenance</li>
		</ul>
		
	</div>
	<div class="row" id="tab_att">
		<div class="col-md-5">
			<h4>Essais non planifiés</h4>
			<div class="table-responsive">
				<table class="table table-striped" id='att'>
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
<script src="../jquery-ui/js/jquery-ui.min.js"></script>
<script src="../js/essaiindex_sauv.js"></script>
<script src="../bootstrap/js/bootstrap-multiselect.js"></script>
<script>
var labo = <?php echo $labo; ?>;
</script>
<?php
mysqli_close($bdd);
require('bottom.php');
