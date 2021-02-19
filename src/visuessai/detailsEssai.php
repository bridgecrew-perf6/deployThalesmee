<?php
require('top.php');
require('../conf/connexion_param.php'); //connexion a la bdd
if (!isset($_GET['idEssai']))
	echo "<div class='alert alert-danger'><strong>Erreur de récupération du numéro de l'essai</strong></div>";
else{
	$idEssai=$_GET['idEssai'];
	
	//on recupere les infos des of qui n'ont pas encore eu de retour équipement
	$str="SELECT e.idEssai, e.badge, e.affaire, e.equipement, e.depositaire, e.telDep, e.os, e.commentaire, et.idEtat_ETAT
	FROM essai e, etatEssai et, tester t
	where e.idEssai =$idEssai
	and t.idEssai_ESSAI=e.idEssai
	and et.dateEtat=(select max(dateEtat) from etatEssai where idEssai_ESSAI=e.idEssai)
	and et.idEssai_ESSAI=e.idEssai;";

	$req=mysqli_query($bdd,$str);
	if(!$req)
		echo "<div class='alert alert-danger'><strong>Erreur de récupération des infos de l'essai</strong></div>";
	else{
		$lg=mysqli_fetch_object($req);
		$badge=$lg->badge;
		$affaire=$lg->affaire;
		$equipement=$lg->equipement;
		$depositaire=$lg->depositaire;
		$telDep=$lg->telDep;
		$os=$lg->os;
		$remarque=html_entity_decode(htmlspecialchars_decode($lg->commentaire));
		$idEtat=$lg->idEtat_ETAT;
		
		if($idEtat==22)
		{
			$idServ_Labo=$_SESSION['infoUser']['idService'];
			$etat="essai en attente";
			$etatSuivant="Lancer l'essai";
			$moyen="Indéterminé";
			// on recupere les moyens du labo pour la box passage a l'etat suivant
			$str="SELECT idMoyen, nomMoyen from moyen where idService_SERVICE=$idServ_Labo;";
			$reqMoyen=mysqli_query($bdd, $str);
		}
		else{
			//on recupere les infos des of qui n'ont pas encore eu de retour équipement
			$str="SELECT  m.nomMoyen FROM tester t, moyen m
			where t.idMoyen_MOYEN=m.idMoyen
			and t.idEssai_ESSAI=$idEssai";
			$req=mysqli_query($bdd,$str);
			$moyen=mysqli_fetch_object($req)->nomMoyen;
			if($idEtat==23){
				$etat="essai en cours";
				$etatSuivant="Terminer l'essai";
			}
			elseif($idEtat==24)
			{
				$etat="essai terminé";
				$etatSuivant="Retour équipement";
			}		
		}
		//on recupere les of concernés
		$str="SELECT e.noOF, m.nomModele FROM equipement_of e, type_modele m
		where noOF in (select noOF_equipement_of from tester where idEssai_Essai=$idEssai)
		and e.idModele_TYPE_MODELE=m.idModele;";
		$req=@mysqli_query($bdd,$str);
		if(!$req)
			echo "<div class='alert alert-danger'><strong>Erreur de récupération des infos de des of</strong></div>";
		else{
		

?>

			<script src="../js/detailsEssai.js"></script>

			<div class="container">
				<div class="page-header">
					<h2>Essai n°<?php echo $idEssai; ?>: <?php echo $etat; ?></h2>
				</div>
				
				<div class="container theme-showcase" role="main">
					<h4>Informations générales</h4>
					<div class="jumbotron">
						<div class="row">
							<div class="col-md-4">
								<div class="info"><label>Badge:&nbsp </label><?php echo $badge; ?></div>
								<div class="info"><label>Nom de l'affaire:&nbsp </label><?php echo $affaire; ?></div>
								<div ><label>Moyen:&nbsp </label><?php echo $moyen; ?></div>
									
							</div>
							<div class="col-md-4">
								<div class="info"><label>Nom de l'équipement:&nbsp </label><?php echo $equipement; ?></div>
								<div><label>Dépositaire:&nbsp </label><?php echo $depositaire; ?></div>						
							</div>
							<div class="col-md-4">
								<div class="info"><label>Téléphone:&nbsp </label><?php echo $telDep; ?></div>
								<div><label>N° d'OS:&nbsp </label><?php echo $os; ?></div>
							</div>
						</div>
					</div>
				</div>
				<div class="container theme-showcase" role="main">
					<h4 class="sub-header">N°OF concernés</h4>
					<div class="jumbotron" >
						<div class="row">
							<div class="col-md-6" id="col0" >
							</div>						
							<div class="col-md-6" id="col1">
							</div>			
							<?php 
								$nb=0; //permet au .js de correctement répartir les infos dans les colonnes (0 dans la colonne 0, 1 dans la 1, 2 dans la 0, 3 dans la 1, ect)
								while($lg=mysqli_fetch_object($req)){
									$noOf=$lg->noOF;
									$nomModele=$lg->nomModele;
									echo "<script>ecrireLigneOF('$noOf','$nomModele','$nb')</script>";
									$nb++;
								}
							?>
						</div>
					</div>
				</div>
				<div class="container theme-showcase" role="main">
					<h4 class="sub-header">Remarques</h4>
					<div class="jumbotron">
						<div><?php echo $remarque; ?></div>	
					</div>
				</div>
				<center>
					<input type="button" class="btn btn-lg btn-primary" value="Retour" onclick="document.location.href='visuLabo.php?idLabo=<?php echo $idService; ?>'"/>
				</center>
			</div><!-- /.container -->
<?php
		}
	}
}
require('bottom.php');
?>