<?php
require('top.php');
?>

<div class="container index">
	<div class="row">
		<div class="col-md-4">
			<div class="page-header">
				<h2>Compte utilisateur</h2>
			</div>
			<p ><a href="listUsers.php" class="btn btn-primary btn-lg" role="button">Liste des utilisateurs</a></p>
			<p><a href="creerUser.php" class="btn btn-primary btn-lg" role="button">Créer un compte utilisateur</a></p>
		</div>
		<div class="col-md-4">
			<div class="page-header">
				<h2>Service MEE</h2>
			</div>
			<p><a href="modifRespEMC.php" class="btn btn-primary btn-lg" role="button">Responsable de Laboratoire EMC</a></p>
			<p><a href="modifRespVIB.php" class="btn btn-primary btn-lg" role="button">Responsable de Laboratoire VIB</a></p>
			<p><a href="modifRespVTH.php" class="btn btn-primary btn-lg" role="button">Responsable de Laboratoire VTH</a></p>
		</div>
		<div class="col-md-4">
			<div class="page-header">
				<h2>Primavera</h2>
			</div>
			<p><a href="primavera.php" class="btn btn-primary btn-lg" role="button">Mettre à jour les réservations</a></p>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4">
			<div class="page-header">
				<h2>Gestion des moyens</h2>
			</div>
			<p><a href="gestionMoyen.php" class="btn btn-primary btn-lg" role="button">Gestion des moyens</a></p>
			
		</div>
		<div class="col-md-4">
			<div class="page-header">
				<h2>Gestion des procédures</h2>
			</div>
			<p><a href="rechProc.php" class="btn btn-primary btn-lg" role="button">Procédures</a></p>
			<p><a href="docClient.php" class="btn btn-primary btn-lg" role="button">Ajouter / Supprimer un doc client</a></p>
			<p><a href="listDP.php" class="btn btn-primary btn-lg" role="button">Suvivi avancement rédaction procédure</a></p>
		</div>
		<div class="col-md-4">
			<div class="page-header">
				<h2>Liens</h2>
			</div>
			<p><a href="http://thalesmee/stat" class="btn btn-primary btn-lg" role="button">Statistiques de l'application</a></p>
			<p><a href="../redacteur/index.php" class="btn btn-primary btn-lg" role="button">Rédaction procédure</a></p>
			<p><a href="../essai/index.php" class="btn btn-primary btn-lg" role="button">Planning essai</a></p>
		</div>
	</div>
	
</div><!-- /.container -->


<?php
require('bottom.php');

?>