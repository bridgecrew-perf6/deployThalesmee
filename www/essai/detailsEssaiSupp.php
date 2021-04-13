<?php
require('top.php');
require('../conf/connexion_param.php'); //connexion a la bdd
if (!isset($_GET['idEssai']))
	echo "<div class='alert alert-danger'><strong>Erreur de récupération du numéro de l'essai</strong></div>";
else{
	$idEssaiSupp=$_GET['idEssai'];
	
	//on recupere les infos des of qui n'ont pas encore eu de retour équipement
	$str="SELECT e.idEssaiSupp, e.badge, e.affaire, e.equipement, e.os, e.commentaire, e.fifo, e.raison, e.idDep_depositaire
	FROM essaisupp e
	where e.idessaiSupp =$idEssaiSupp";
	$req=mysqli_query($bdd,$str);
	if(!$req)
		echo "<div class='alert alert-danger'><strong>Erreur de récupération des infos de l'essai</strong></div>";
	else{
		$lg=mysqli_fetch_object($req);
		$badge=$lg->badge;
		$idDep = $lg->idDep_depositaire;
		$affaire=$lg->affaire;
		$equipement=$lg->equipement;
		$os=$lg->os;
		$remarque=$lg->commentaire;
		$raison=$lg->raison;
		
		if($lg->fifo==1)
			$fifo="Oui";
		else
			$fifo="Non";
		
		$idServ_Labo=$_SESSION['infoUser']['idService'];
		
		if ($idDep != NULL)
		{
			//on recupere les informations du dépositaire
			$str="SELECT  nomDep, telDep FROM depositaire
			where idDep = $idDep;";
			echo $str;
			$req=mysqli_query($bdd,$str);
			if(mysqli_num_rows($req)!=0)
			{
				$depositaire=mysqli_fetch_object($req)->nomDep;
				$telDep=mysqli_fetch_object($req)->telDep;
			}
			else
			{
				$depositaire="";
				$telDep="";
			}

		}else
		{
			$depositaire="";
			$telDep="";
		}
		
			
		$selectMoyen=false;
		//on recupere les infos des of qui n'ont pas encore eu de retour équipement
		$str="SELECT  m.nomMoyen FROM essai e, moyen m
		where m.idMoyen = e.idMoyen_MOYEN
		and idEssai=$idEssaiSupp";
		$req=mysqli_query($bdd,$str);
		if(mysqli_num_rows($req)!=0)
			$moyen=mysqli_fetch_object($req)->nomMoyen;
		else
		{
			$moyen="Indeterminé";
			$selectMoyen=true;
		}		
		
		//on recupere les of concernés
		$str="SELECT e.noOF, m.nomModele FROM equipement_of e, type_modele m
		where noOF in (select noOF_equipement_of from testerSupp where idEssai_EssaiSupp=$idEssaiSupp)
		and e.idModele_TYPE_MODELE=m.idModele;";
		$req=@mysqli_query($bdd,$str);
		if(!$req)
			echo "<div class='alert alert-danger'><strong>Erreur de récupération des infos de des of</strong></div>";
		else{
?>
			
			<div class="container">
				<div class="page-header">
					<h2>Essai supprimé n°<?php echo $idEssaiSupp; ?></h2>
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
								<div class="info"><label>Dépositaire:&nbsp </label><?php echo $depositaire; ?></div>
								<div class="info"><label>Nom de l'équipement:&nbsp </label><?php echo $equipement; ?></div>
								<div ><label>Fifo:&nbsp </label><?php echo $fifo; ?></div>								
							</div>
							<div class="col-md-4">
								<div class="info"><label>Téléphone:&nbsp </label><?php echo $telDep; ?></div>
								<div class="info"><label>N° d'OS:&nbsp </label><?php echo $os; ?></div>
								<div><label>Raison:&nbsp </label><?php echo $raison; ?></div>
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
					<input type="button" class="btn btn-lg btn-primary" value="Retour" onclick="document.location.href='listEssaiSupp.php'" />
				</center>
			</div><!-- /.container -->
			
			<link rel="stylesheet" href="../jquery-ui/css/redmond/jquery-ui.min.css">
			<script src="../jquery-ui/js/jquery-ui.min.js"></script>
			<script src="../js/detailsEssai.js"></script>
			<?php 
				$nb=0; //permet au .js de correctement répartir les infos dans les colonnes (0 dans la colonne 0, 1 dans la 1, 2 dans la 0, 3 dans la 1, ect)
				while($lg=mysqli_fetch_object($req)){
					$noOf=$lg->noOF;
					$nomModele=$lg->nomModele;
					echo "<script>ecrireLigneOF('$noOf','$nomModele','$nb')</script>";
					$nb++;
				}
			?>


<?php
		}
	}
}
require('bottom.php');
?>