<?php
/*cette page traite le detail d'un of
si l'of n'est integré que dans un seul essai -> on montre directement le detail de l'essai
si plusieurs essais, on propose la liste de ceux ci
*/
require('top.php');
if(!isset($_GET["noOf"])) //on verifi qu'on a bien recu le numéro d'of
	echo '<div class="alert alert-danger"><strong>Erreur de récupération du numéro de l\'Of</strong></div>';
else
{
	$noOf=$_GET["noOf"];
	require('../conf/connexion_param.php');
	//si non inclus dans une autre page
	$idLabo=$_SESSION['infoUser']['idService'];// service du labo
	//on recupere les of
	$str="SELECT e.idEssai, e.badge, e.affaire, e.equipement, et.dateEtat, m.nomMoyen 
	from ESSAI e, etatEssai et, tester t, moyen m
	where t.noOf_equipement_of='$noOf'
	and et.idEssai_ESSAI=e.idEssai
	and t.idEssai_essai=e.idEssai
	and m.idService_service=$idLabo
	and e.idMoyen_MOYEN=m.idMoyen
	and et.idEtat_ETAT=(select max(idEtat_ETAT) from etatEssai et2 where et2.idEssai_ESSAI=e.idEssai)
	order by et.dateEtat;";
	$req=@mysqli_query($bdd, $str); //le @ est un parametre de gestion d'erreur, il evite un affichage incompréhensible pour un utilisateur (warning / error), à enlever pour tester l'erreur 
	echo mysqli_error($bdd);
	if(!$req) //une erreur dans la requete renvera false
		echo '<div class="alert alert-danger"><strong>Une erreur s\'est produite lors de la récupération des OF</strong></div>';
	else
	{
		//si un seul essai correspondant - redirection vers la page de detail de l'essai
		if(mysqli_num_rows($req)==1)
		{
			$idEssai=mysqli_fetch_object($req)->idEssai;
			echo "<script>document.location.href='detailsEssai.php?idEssai=$idEssai'</script>";	
			
		}
		else //sinon on affiche la liste des essai correspondants
		{
			$nbEssai=mysqli_num_rows($req);
?>
			<div class="container">
				<div class="page-header">
					<h2><?php echo $nbEssai; ?> essais en ce moment sur l'Of <?php echo $noOf; ?></h2>
				</div>
				<div class="container theme-showcase" role="main">
					<div class="jumbotron">
						<div class="table-responsive">
							<table class="table table-striped table-tri" id="tri">
								<thead >
									<tr>
										<th>N° essai</th>
										<th>Affaire</th>
										<th>Moyen</th>
										<th>Équipement</th>
										<th>Liste des OF</th>
										<th>Badge</th>
										<th>Date</th>
									</tr>
								</thead>
								<tbody>
									<?php
										while($lg=mysqli_fetch_object($req)) 
										{
											$idEssai=$lg->idEssai;
											$affaire=$lg->affaire;
											$moyen=$lg->nomMoyen;
											$equipement=$lg->equipement;
											$badge=$lg->badge;
											$dateEtat=date('d/m/Y H:i',strtotime($lg->dateEtat));
											//on recupere les OF de l'essai
											$str="select noOf_EQUIPEMENT_OF
											from tester 
											where idEssai_ESSAI=$idEssai;";
											$reqOF=@mysqli_query($bdd,$str);												
											$listeOF="";
											while($lgOF=mysqli_fetch_object($reqOF))
												$listeOF.=$lgOF->noOf_EQUIPEMENT_OF." ";													
													
											echo "<tr style='cursor:pointer;' onclick='document.location.href=\"detailsEssai.php?idEssai=$idEssai\"'>";
												echo "<td>$idEssai</td>";
												echo "<td>$affaire</td>";
												echo "<td>$moyen</td>";
												echo "<td>$equipement</td>";
												echo "<td>$listeOF</td>";
												echo "<td>$badge</td>";
												echo "<td>$dateEtat</td>";
											echo "</tr>";
										
											
										}
									?>
								</tbody>
								<tfoot >
									<tr>
										<th>N° essai</th>
										<th>Affaire</th>
										<th>Moyen</th>
										<th>Équipement</th>
										<th>Liste des OF</th>
										<th>Badge</th>
										<th>Date</th>
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
}
require('bottom.php');
