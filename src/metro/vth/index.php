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
			<?php
			$str="select count(*) as nb from ajoutInstru where idLabo_labo=".($categ-1);
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
			<p><a href="listMetroFutCalib.php" class="btn btn-primary btn-lg" role="button">Futures calibrations interne</a></p>
			<p><a href="listMetroCalib.php" class="btn btn-primary btn-lg" role="button">En calibration interne</a></p>
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
				<h2>Divers</h2>
			</div>
			<p><a href="gestionLocal.php" class="btn btn-primary btn-lg" role="button">Gestion des localisations</a></p>
			<p><a id="export" href="exportBase.php" class="btn btn-primary btn-lg" role="button">Exporter la base format excel</a></p>
		</div>
	</div>
	<?php
	//si admin on propose le retour sur la partie administration)
	if($_SESSION["metro"]["categUser"]==1)
	{
		echo '</br>';
		echo '<p><a href="../admin/index.php" class="btn btn-success btn-lg" role="button">Retour administration</a></p>';
	}
	?>
</div><!-- /.container -->
<script type="text/javascript" language="javascript" src="../js/index.js"></script>	
<?php
require("bottom.php");