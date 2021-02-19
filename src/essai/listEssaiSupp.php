<?php 
require('top.php');

require('../conf/connexion_param.php');


$idServ_Labo=$_SESSION['infoUser']['idService'];
	
$str="select e.idEssaiSupp, e.badge, e.affaire, e.equipement, m.nomMoyen, e.fifo, raison from essaiSupp e
left join moyen m on idMoyen = e.idMoyen_MOYEN
where e.idService_SERVICE=$idServ_Labo;";
$req=mysqli_query($bdd, $str);
echo mysqli_error($bdd);
if(!$req) //une erreur dans la requete renvera false
	echo '<div class="alert alert-danger"><strong>Une erreur s\'est produite lors de la récupération des essais</strong></div>';
else
{
?>
		<style>
			.form-control{
				width : calc(100% - 10px);
			}
		</style>
		<div class="container theme-showcase" role="main">
		<h3>Essais supprimés</h3>
			<div class="jumbotron">
				<div class="table-responsive">
					<table class="table table-striped table-tri" id="tri">
						<thead >
							<tr>
								<th>N°</th>
								<th>Affaire</th>
								<th>Moyen</th>
								<th>Équipement</th>
								<th>Liste des OF</th>
								<th>Badge</th>
								<th>Raison</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$stop=false;
								while(!$stop && $lg=mysqli_fetch_object($req)) //si une erreur dans la recup des of evite l'affichage de l'erreur plusieurs fois
								{
									$idEssaiSupp=$lg->idEssaiSupp;
									$affaire=$lg->affaire;
									$equipement=$lg->equipement;
									$badge=$lg->badge;
									$raison=$lg->raison;
									//formatage de la date
								
									if(!isset($moyen))
										$moyen=$lg->nomMoyen;
									
									
									//on recupere les OF de l'essai
									$str2="select noOf_EQUIPEMENT_OF as nof
									from testerSupp
									where idEssai_ESSAISupp=$idEssaiSupp;";
									$req2=@mysqli_query($bdd,$str2);
									if(!$req2){
										echo '<div class="alert alert-danger"><strong>Une erreur s\'est produite lors de la récupération des OF</strong></div>';
										$stop=true;
									}
									else{
										$listeOF="";
										while($lg2=mysqli_fetch_object($req2)){
											$nof=$lg2->nof;
											$listeOF.="$nof ";
										}
										
										echo "<tr style='cursor:pointer;' onclick='document.location.href=\"detailsEssaiSupp.php?idEssai=$idEssaiSupp\"'>";
											echo "<td>$idEssaiSupp</td>";
											echo "<td>$affaire</td>";
											echo "<td>$moyen</td>";
											echo "<td>$equipement</td>";
											echo "<td>$listeOF</td>";
											echo "<td>$badge</td>";
											echo "<td>$raison</td>";
										echo "</tr>";
									}
								}
							?>
						</tbody>
						<tfoot >
							<tr>
								<th>N°</th>
								<th>Affaire</th>
								<th>Moyen</th>
								<th>Équipement</th>
								<th>Liste des OF</th>
								<th>Badge</th>
								<th>Raison</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>	
		<script type="text/javascript" language="javascript" src="../DataTables/jquery.dataTables.js"></script>
		<script type="text/javascript" language="javascript" src="../DataTables/columnFilter.js"></script>	
		<script type="text/javascript" charset="utf-8">
		$(document).ready(function() {
			$('#tri').dataTable().columnFilter({
				sPlaceHolder : "head:after"
			});
			$('#tri_filter input').attr("placeholder", "Rechercher");
			$('#tri_filter input').attr("class", "form-control");
			$('#tri_filter input').attr("style", "font-weight:normal;");
			$('#tri_length select').attr("class", "form-control");
			
		})
	</script>

<?php
}
require('bottom.php');