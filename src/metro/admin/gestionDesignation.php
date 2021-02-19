<script src="../jquery-ui/js/jquery.js"></script>
<script src="../js/swal.js"></script>
<?php 
require('top.php');
require('../conf/connexion_param.php');
if(isset($_POST["nom"]))
{
	$nom = strtoupper($_POST['nom']);
	$dom = $_POST["dom"];

	$str = "INSERT INTO designation VALUES (NULL, '$nom', $dom)";
	$req = mysqli_query($bdd, $str);
	if (!$req) echo "<div class='alert alert-danger'><strong>Erreur d'ajout de la nouvelle désignation'</strong></div>";
	else echo '<script src="../js/success.js"></script>';

}
else{

	$str = "SELECT idDom, nomDom FROM domaine";
	$req = mysqli_query($bdd, $str);	
?>
	<div class="container">
		<div class="page-header">
			<h2>Ajouter une localisation</h2>
		</div>
		<div class="container theme-showcase" role="main">
			<form method="post" action="gestionDesignation.php" role="form">
				<div class="jumbotron">
					<div class="row">
						<div class="col-md-6">
							<input id="nom" name="nom" title="Nom" type="text" class="form-control" placeholder="Nom de la désignation" required autofocus />
						</div>
						<div class="col-md-6">
							<select id="dom" name="dom" title="Domaine" class="form-control" required>
							<option value="" disabled selected>Domaine associé</option>
							<?php
								while($lg=mysqli_fetch_object($req))
									echo '<option value="'.$lg->idDom.'">'.$lg->nomDom.'</option>';
							?>
							</select>
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