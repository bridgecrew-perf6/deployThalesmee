<?php 
require('top.php');
//cette page sert de liste pour l'affectation et la reafectation
if($_SESSION['infoUser']['categUser']==3)
{

	require('../conf/connexion_param.php'); 
	require('../fonction.php'); //page contenant verifDPRapide

	// $idServ affecter dans top.php, contient le numéro du service du responsable
	if(isset($_GET["rea"]) && $_GET["rea"]==0)
	{
		$str="select p.idProc, p.idDP_DEMANDE_PROCEDURE, dp.idDP, e.nomEmp, dp.affaire, dp.equipement, dp.delai, eta.nomEtat
		from procedures p, demande_procedure dp, etatproc et, employe e, etat eta
		where p.idDP_demande_procedure= dp.idDP
		and et.idProc_procedures = p.idProc
		and idEtat_etat <17
		and idEtat_etat !=12
		and eta.idEtat=et.idEtat_etat
		and dp.idemp_employe=e.idemp
		and p.idService_service=$idServ
		and et.idEtat_etat = (select max(idEtat_etat)
							from etatProc
							where idProc_PROCEDURES=p.idProc
						  );";
		$lien="reaffecter.php";
		$titre="Réaffecter procédure";
	
	}
	else
	{
		//on regarde si le service du responsable laboratoire a des procedure en attentes d'affectation
		$str="select p.idProc, p.idDP_DEMANDE_PROCEDURE, dp.idDP, e.nomEmp, dp.affaire, dp.equipement, dp.delai
		from procedures p, demande_procedure dp, etatproc et, employe e
		where p.idDP_demande_procedure= dp.idDP
		and et.idProc_procedures = p.idProc
		and idEtat_etat =12
		and dp.idemp_employe=e.idemp
		and p.idService_service=$idServ
		and et.idEtat_etat = (select max(idEtat_etat)
							from etatProc
							where idProc_PROCEDURES=p.idProc
						  );";
		$lien="affectation.php";
		$titre="Affectation procédure";
	}
	$reqProc=mysqli_query($bdd,$str);
	echo mysqli_error($bdd);
	if(!$reqProc)
		echo '<div class="alert alert-danger"><strong>Erreur d\'accés aux données des procédures</strong></div>';
		
	if (!(isset($_GET["rea"])) && mysqli_num_rows($reqProc)==0 ){
		//Erreur pas de demande en attente d affectation
		
		echo "<center>";
		echo '<div class="alert alert-success"><strong>Il n\'y a plus de demande en attente d\'affectation</strong></div>';
		echo '<input class="btn btn-primary btn-lg" type="button" value="Retour" onclick="document.location.href=\'index.php\'" />';
		echo "</center>";
	}
	else //on a des demandes en attente d affectation
	{
	?>
		<div class="container">
			<div class="page-header">
				<h2><?php echo $titre; ?></h2>
			</div>
			<div class="container theme-showcase" role="main">
				<div class="jumbotron">
					<div class="table-responsive">
						<table class="table table-striped table-tri" id="tri">
							<thead>
								<tr >
									<th>Demande n°</th>
									<th>Procédure n°</th>
									<th>Affaire</th>
									<th>Équipement</th>
									<?php
									if(isset($_GET["rea"]) && $_GET["rea"]==0)
										echo "<th>État</th>"
									?>
									<th>Demandeur</th>
									<th>Pour le</th>
									<!-- <th>PDF</th> -->
								</tr>
							</thead>
							<tbody>
							<?php
							
							while($lg=mysqli_fetch_object($reqProc)){
									
								$idProc=$lg->idProc;
								$idDP=$lg->idDP_DEMANDE_PROCEDURE;
							
								$nom=$lg->nomEmp;
								$affaire=$lg->affaire;
								$equipement=$lg->equipement;
								$delais=date('d/m/Y',strtotime($lg->delai));
								$idDP=$lg->idDP;
									
								// affichage info
								echo "<tr style='cursor:pointer' onclick='document.location.href=\"$lien?idProc=$idProc\";'>";
									echo "<td >$idDP</td>";
									echo "<td >$idProc</td>";
									echo "<td >$affaire</td>";
									echo "<td >$equipement</td>";

									if(isset($_GET["rea"]) && $_GET["rea"]==0)
									{
										echo "<td>".$lg->nomEtat."</td>";
									}
									echo "<td >$nom</td>";
									echo "<td >$delais</td>";
									/*
									if(verifDPRapide($idDP,$bdd)) //fonction qui test si la demande est en une étape (true) ou trois (false)
										$recap="../demande/genPDFDemande_rapide.php?idDP=$idDP";
									else
										$recap="../demande/genPDFDemande.php?idDP=$idDP";
									echo "<td><a target='_blank' href='$recap'>Voir le PDF</a></td>";*/
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
									<?php
									if(isset($_GET["rea"]) && $_GET["rea"]==0)
										echo "<th>État</th>"
									?>
									<th>Demandeur</th>
									<th>Pour le</th>
									<!-- <th>PDF</th> -->
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
}
else
	echo '<div class="alert alert-danger"><strong>Accés non autorisé</strong></div>';
require('bottom.php');
?>