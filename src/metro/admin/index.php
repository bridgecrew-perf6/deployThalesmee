<?php 
require("top.php");
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
				<h2>Instruments</h2>
			</div>
			<p><a href="listInstru.php" class="btn btn-primary btn-lg" role="button">Liste des instruments</a></p>	
			<p><a href="creerInstru.php" class="btn btn-primary btn-lg" role="button">Ajouter un instrument</a></p>	
		</div>
		<div class="col-md-4">
			<div class="page-header">
				<h2>Divers</h2>
			</div>
			<p><a href="gestionLocal.php" class="btn btn-primary btn-lg" role="button">Gestion des localisations</a></p>	
			<p><a href="gestionType.php" class="btn btn-primary btn-lg" role="button">Gestion des types</a></p>
			<p><a href="gestionDomaine.php" class="btn btn-primary btn-lg" role="button">Gestion des domaines</a></p>
			<p><a href="gestionDesignation.php" class="btn btn-primary btn-lg" role="button">Gestion des désignations</a></p>
			
		</div>
	</div>	
</div><!-- /.container -->
<?php
require("bottom.php");