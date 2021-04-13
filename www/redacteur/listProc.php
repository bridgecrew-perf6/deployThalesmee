<?php 
require('top.php');
if(!isset($_GET["idEtat"]))
	echo '<div class="alert alert-danger"><strong>Erreur de recuperation des parametres</strong></div>';
else
{
	require('../conf/connexion_param.php'); //connexion a la bdd
	$idEtat=$_GET["idEtat"];
	$idRedacteur=$_SESSION['infoUser']['idEmp'];
	//on recup les info des procédures dans l'etat idEtat
	// $idServ affecter dans top.php, contient le numéro du service du responsable
	$str="select p.idProc, p.idDP_DEMANDE_PROCEDURE, dp.idDP, e.nomEmp, dp.affaire, dp.equipement, dp.delai
		from procedures p, demande_procedure dp, etatproc et, employe e
		where p.idDP_demande_procedure= dp.idDP
		and et.idProc_procedures = p.idProc
		and p.idEmp_EMPLOYE=$idRedacteur
		and idEtat_etat =$idEtat
		and dp.idemp_employe=e.idemp
		and p.idService_service=$idServ
		and et.idEtat_etat = (select max(idEtat_etat)
							from etatProc
							where idProc_PROCEDURES=p.idProc
	);";
	$req=mysqli_query($bdd,$str);

	if ($idEtat==13){$titre="Nouvelle(s) procédure(s)";}
	else if ($idEtat==14){$titre="Procédure(s) en cours de rédaction";}
	else if ($idEtat==15){$titre="Procédure(s) en cours de relecture";}
	else if ($idEtat==16){$titre="Procédure(s) en cours de signature";}
	
	?>
		
	<div class="container">
		<div class="page-header">
			<h2><?php echo $titre ;?></h2>
		</div>
		<div class="container theme-showcase" role="main">
			<h4>Liste des procédures</h4>
			<div class="jumbotron">
				<table class="table table-striped table-tri" id="tri">
					<thead>
						<tr >
							<th>Demande n°</th>
							<th>Procédure n°</th>
							<th>Affaire</th>
							<th>Équipement</th>
							<th>Demandeur</th>
							<th>Pour le</th>
							<!-- <th>PDF</th> -->
						</tr>
					</thead>
					<tbody>
					<?php
					while($lg=mysqli_fetch_object($req)){
						
						$idProc=$lg->idProc;
						$idDP=$lg->idDP_DEMANDE_PROCEDURE;
					
						$nom=$lg->nomEmp;
						$affaire=$lg->affaire;
						$equipement=$lg->equipement;
						$delais=date('d/m/Y',strtotime($lg->delai));
						$idDP=$lg->idDP;
							
						// affichage info
						echo "<tr style='cursor:pointer' onclick='document.location.href=\"etapeSuivanteProc.php?idProc=$idProc\";'>";
							echo "<td >$idDP</td>";
							echo "<td >$idProc</td>";
							echo "<td >$affaire</td>";
							echo "<td >$equipement</td>";
							echo "<td >$nom</td>";
							echo "<td >$delais</td>";
							
						echo "</tr>";
					}
						?>
					</tbody>
					<tfoot>
						<tr >
							<th>Demande n°</th>
							<th>Procédure n°</th>
							<th>Affaire</th>
							<th>Équipement</th>
							<th>Demandeur</th>
							<th>Pour le</th>
							<!-- <th>PDF</th> -->
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