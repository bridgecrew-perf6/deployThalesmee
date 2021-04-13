<?php 
require('top.php');
require('../conf/connexion_param.php');
//on se sert de cette page pour tous les besoin de liste des demandes de procédure, cela évite de multiplier le nombre de page pour un code presque recopier
//pour cela on se sert d'un parametre mode que l'on envoi en GET


	
$str="select a.idDP, a.affaire, a.equipement , a.os
from DEMANDE_PROCEDURE a
where a.validiteDP=4 group by a.idDP;";
$title="Suivi de l'avancement de la rédaction des procédures";
$lien="suiviDP.php";	

$req=mysqli_query($bdd, $str);
if(!$req) //une erreur dans la requete renvera false
	echo '<div class="alert alert-danger"><strong>Une erreur s\'est produite lors de la récupération des données des demandes</strong></div>';
else
{
?>
	<div class="container">
		<div class="page-header">
			<h2><?php echo $title; ?></h2>
		</div>
		<div class="container theme-showcase" role="main">
			<div class="jumbotron">
				<div class="table-responsive">
					<table class="table table-striped table-tri" id="tri">
						<thead>
							<tr >
								<th>Numéro</th>
								<th>Affaire</th>
								<th>Équipement</th>
								<th>N°OS</th>
							</tr>
						</thead>
						<tbody>
							<?php
								while($lg=mysqli_fetch_object($req))
								{
									$idDP=$lg->idDP;
									$affaire=$lg->affaire;
									$equipement=$lg->equipement;
									$os=$lg->os;
								
									
									echo "<tr style='cursor:pointer;' onclick='document.location.href=\"$lien?idDP=$idDP\"'>";
										echo "<td>$idDP</td>";
										echo "<td>$affaire</td>";
										echo "<td>$equipement</td>";
										echo "<td>$os</td>";
									echo "</tr>";
								}
							?>
						</tbody>
						<tfoot>
							<tr >
								<th>Numéro</th>
								<th>Affaire</th>
								<th>Équipement</th>
								<th>N°OS</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript" language="javascript" src="../DataTables/jquery.dataTables.js"></script>
	<script type="text/javascript" language="javascript" src="../DataTables/columnFilter.js"></script>
	<script type="text/javascript" charset="utf-8">
		$(document).ready(function() {
			$('#tri').dataTable().columnFilter();
			$('#tri_filter input').attr("placeholder", "Rechercher");
			$('#tri_filter input').attr("class", "form-control");
			$('#tri_filter input').attr("style", "font-weight:normal;");
			$('#tri_length select').attr("class", "form-control");
		} );
	</script>

<?php

}
require('bottom.php');

?>