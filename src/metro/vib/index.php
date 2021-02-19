<?php 
require("top.php");
require('../conf/connexion_param.php');
?>
<div class="container index">
	<div class="row">
		<div class="col-md-4">
			<div class="page-header">
				<h2>Instruments</h2>
			</div>
			<p><a href="listInstru.php" class="btn btn-primary btn-lg" role="button">Liste des instuments</a></p>
			<p><a href="creerInstru.php" class="btn btn-primary btn-lg" role="button">Ajouter un instrument</a></p>
			<p><a href="associerDocument.php" class="btn btn-primary btn-lg" role="button">Associer un document</a></p>
			<?php
			$str="select count(*) as nb from ajoutInstru where idLabo_labo='2'";
			$req=mysqli_query($bdd, $str);
			$nb=mysqli_fetch_object($req)->nb;
			if($nb>0)
				echo "<p><a href='listAjoutViaTresc.php' class='btn btn-danger btn-lg' role='button'>$nb Instruments ajoutés</a></p>";
			?>
		</div>
		<div class="col-md-4">
			<div class="page-header">
				<h2>Calibration</h2>
			</div>
			<p><a href="infoFutCalib.php" class="btn btn-primary btn-lg" role="button">Infos futures calibrations</a></p>
		</div>
		<div class="col-md-4">
			<div class="page-header">
				<h2>Rébut - Réforme</h2>
			</div>
			<p><a href="listInstruNonDispo.php" class="btn btn-primary btn-lg" role="button">Listes des intruments</a></p>	
		</div>
	</div>
	<div class="row">
		<div class="col-md-4">
			<div class="page-header">
				<h2>Capteurs</h2>
			</div>
			<p><a href="listCapteur.php" class="btn btn-primary btn-lg" role="button">Liste des capteurs</a></p>	
			<p><a href="gestionBidon.php" class="btn btn-primary btn-lg" role="button">Gestion projet bidon</a></p>	
			
		</div>
		<div class="col-md-4">
			<div class="page-header">
				<h2>Entrées-sorties</h2>
			</div>
			<p><a href="listPret.php" class="btn btn-primary btn-lg" role="button">Liste des entrées-sorties en cours</a></p>	
			<p><a href="listHistoPret.php" class="btn btn-primary btn-lg" role="button">Historique des entrées-sorties cloturées</a></p>	
			<p><a href="creerModifierPret.php" class="btn btn-primary btn-lg" role="button">Ajouter une entrée-sortie</a></p>
		</div>
	
		<div class="col-md-4">
			<div class="page-header">
				<h2>Divers</h2>
			</div>
			<p><a href="gestionLocal.php" class="btn btn-primary btn-lg" role="button">Gestion des localisations</a></p>
			<p><a href="gestionUnite.php" class="btn btn-primary btn-lg" role="button">Gestion des unités</a></p>
			<p><a id="export" href="exportBase.php" class="btn btn-primary btn-lg" role="button">Exporter la base format excel</a></p>
		</div>
		<div class="col-md-4">
			<div class="page-header">
				<h2>Historique suppression</h2>
			</div>
			<p><a href="instru_suppr.php" class="btn btn-primary btn-lg" role="button">Instruments supprimés</a></p>
		</div>
		<div class="col-md-4">
			<div class="page-header">
				<h2>Tableaux récapitulatifs</h2>
			</div>
			<p><a role="button"  class="btn btn-primary btn-lg" href='tableau_recap.php' />Tableaux récapitulatifs</a></p>
			<p><a role="button"  class="btn btn-primary btn-lg" href='historique.php' />Fichier historique</a></p>
		</div>
	</div>
</div><!-- /.container -->
<script type="text/javascript" language="javascript" src="../js/index.js"></script>	
<?php
require("bottom.php");