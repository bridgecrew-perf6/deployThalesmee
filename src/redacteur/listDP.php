<?php 
require('top.php');
require('../conf/connexion_param.php');
require('../fonction.php');
//page de suivi, permet d'acceder aux details d'une demande

$str="select a.idDP, a.affaire, a.equipement , a.os
from DEMANDE_PROCEDURE a
where a.validiteDP=4 group by a.idDP;";
$req=mysqli_query($bdd, $str);
if(!$req) //une erreur dans la requete renvera false
	echo '<div class="alert alert-danger"><strong>Une erreur s\'est produite lors de la récupération des données des demandes</strong></div>';
else
{
?>
	<div class="container">
		<div class="page-header">
			<h2>Liste des demandes de procédure</h2>
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
								<th>PDF</th>
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
								
									
									echo "<tr>";
										echo "<td>$idDP</td>";
										echo "<td>$affaire</td>";
										echo "<td>$equipement</td>";
										echo "<td>$os</td>";
										if(verifDPRapide($idDP,$bdd)) //fonction qui test si la demande est en une étape (true) ou trois (false)
											$recap="../demande/genPDFDemande_rapide.php?idDP=$idDP";
										else
											$recap="../demande/genPDFDemande.php?idDP=$idDP";
										echo "<td><a target='_blank' href='$recap'>Voir le PDF</a></td>";
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
								<th>PDF</th>
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