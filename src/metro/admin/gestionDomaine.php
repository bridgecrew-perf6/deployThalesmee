<script src="../jquery-ui/js/jquery.js"></script>
<script src="../js/swal.js"></script>
<?php 
require('top.php');
require('../conf/connexion_param.php');
if(isset($_POST["nom"]))
{
	$nom = strtoupper($_POST['nom']);
	$str = "INSERT INTO domaine VALUES (NULL, '$nom')";
	$req = mysqli_query($bdd, $str);
	if (!$req) echo "<div class='alert alert-danger'><strong>Erreur d'ajout du nouveau domaine'</strong></div>";
	else echo '<script src="../js/success.js"></script>';

}
else{
	
?>
	<div class="container">
		<div class="page-header">
			<h2>Ajouter un domaine</h2>
		</div>
		<div class="container theme-showcase" role="main">
			<form method="post" action="gestionDomaine.php" role="form">
				<div class="jumbotron">
					<div class="row">
						<div class="col-md-3">
							<input id="nom" name="nom" title="Nom" type="text" class="form-control" placeholder="Nom du domaine" required autofocus />
						</div>
					</div>
				</div>
				<div class="text-center">
					<input class='btn btn-lg btn-primary' type='submit' value="Créer le domaine" />
					<input class='btn btn-lg btn-primary' type='button' value='Annuler' onclick='document.location.href="index.php"'/>
				</div>
			</form>
		</div>
	</div><!-- /.container -->

<?php
}
require('bottom.php');