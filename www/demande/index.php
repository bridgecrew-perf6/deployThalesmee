<?php
require('top.php');
?>

<div class="container index">
	<div class="row">
		<div class="col-md-4">
			<div class="page-header">
				<h2>Demande de procédure</h2>
			</div>
			<p><a href="DProc_1.php" class="btn btn-primary btn-lg" role="button">Nouvelle demande de procédure</a></p>
			<?php if($nbDP !=0) echo '<p><a href="listDP.php?mode=0" class="btn btn-primary btn-lg" role="button">Terminer une demande de procédure</a></p>'; ?>
			<p><a href="listDP.php?mode=1" class="btn btn-primary btn-lg" role="button">Supprimer une demande de procédure</a></p>
			
		</div>
		<div class="col-md-4">
			<div class="page-header">
				<h2>Demande validée</h2>
			</div>
			<p><a href="listDP.php?mode=2" class="btn btn-primary btn-lg" role="button">Modifier une demande validée</a></p>
			<p><a href="evoProc.php" class="btn btn-primary btn-lg" role="button">Évolution d'une procédure</a></p>
		</div>
		<div class="col-md-4">
			<div class="page-header">
				<h2>Suivi et documents</h2>
			</div>
			<p><a href="listDP.php?mode=3" class="btn btn-primary btn-lg" role="button">Suivi avancement rédaction procédure</a></p>
			<p><a href="docClient.php" class="btn btn-primary btn-lg" role="button">Ajouter / Rechercher des documents client</a></p>
			<p><a href="rechProc.php" class="btn btn-primary btn-lg" role="button">Procédure</a></p>
		</div>
	</div>
</div><!-- /.container -->


<?php
require('bottom.php');

?>