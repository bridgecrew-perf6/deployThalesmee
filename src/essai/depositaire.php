
<script src="../js/swal.js"></script>

<?php
require('top.php');
require('../conf/connexion_param.php'); //connexion a la bdd
if (isset($_POST['nom'])){
	
	echo '<script src="../js/success.js"></script>';
}
else{

	?>
	<link href="../calendrier/calendrier.css" rel="stylesheet" />	
	<link href="../css/addons/datatables.min.css" rel="stylesheet">
	<link href="../css/starter-template.css" rel="stylesheet">
	<script type="text/javascript" src="../js/addons/datatables.min.js"></script>
	<script type="text/javascript" src="../js/table.js"></script>
	<div class="container-fluid">
		
		<div class="page-header">
			<h2>Liste des dépositaires</h2>
		</div>
		<div id="erreurSuppr" class="alert alert-danger" role="alert">Erreur de suppression du dépositaire</div>
		<div id="erreurAjout" class="alert alert-danger" role="alert">Erreur d'ajout du dépositaire</div>
	</div>
	<div class="container-fluid">
		<div class="jumbotron">
			<div class="page-header">
				<h4>Ajouter un dépositaire</h4>
			</div>
			<div id="erreurInfo" class="alert alert-danger" role="alert">Veuillez renseigner toutes les informations demandées</div>
			<div id="doublon" class="alert alert-danger" role="alert">Erreur d'ajout du dépositaire : le dépositaire existe déjà</div>
			<div class="form-row">
				
				<div class="form-goup col-md-2">
					<input type="text" class="form-control" id="nomDep" placeholder = "Nom">
				</div>
				<div class="form-goup col-md-2">
					<input type="text" class="form-control" id="prenomDep" placeholder = "Prenom">
				</div>
				<div class="form-goup col-md-2">
					<input type="text" class="form-control" id="telDep" placeholder = "Téléphone fixe">
				</div>
				<div class="form-goup col-md-2">
					<input type="text" class="form-control" id="portableDep" placeholder = "Téléphone portable">
				</div>
				<div class="form-goup col-md-4">
					<input type="button" class="btn btn-block btn-primary" onclick="ajoutDepositaire()" value="Ajouter">
				</div>
			</div>	
		</div>
	</div>
	<div class="container-fluid">
	<div class="jumbotron">
		
		<form method="post" action="depositaire.php">
		<table id="exemple" class=" table table-striped table-bordered"><thead><tr><th class="th-sm" scope="col">Nom</th><th class="th-sm" scope=
		"col">Prénom</th><th class="th-sm" scope="col">Téléphone fixe</th><th class="th-sm" scope="col">Téléphone portable</th><th class="th-sm"  scope="col"
		></th></thead><tbody>
	<?php

	
	$str="SELECT idDep, nomDep, prenomDep, telDep, portableDep FROM depositaire WHERE actif = 1";	
	$req=mysqli_query($bdd, $str);


	$cpt = 0;
	while ($lg=mysqli_fetch_object ($req)){
		
		echo '<tr class="form-group" scope="row" id="'.$lg->idDep.'"><td scope="row"><input name =nom['.$cpt.'] class="form-control input" type=text value="'.$lg->nomDep.'"><td scope="row"><input  class="form-control input" type="text" value="'.$lg->prenomDep.'"></td><td><input class="form-control input"  type="text" value='.$lg->telDep.'></td><td><input class="form-control input"  type="text" value='.$lg->portableDep.'></td><td scope="row"><input type="button" onclick="confirmation(\''.$lg->idDep.'\')" class="btn btn-block btn-danger" value="Supprimer"></td></tr>';
		$cpt += 1;

	}
	?>

						</tbody>
					</table>
					<div class="text-center">
						<center>
							<input type="submit" class="btn btn-lg btn-success" value="Valider" />
							<input type="button" class="btn btn-lg btn-primary" onclick="document.location.href='./index.php'"value="Retour" />
						</center>
					</div>
				</div>
			</form>
		</div>
	</div>
<?php
}
?>
<script src="../js/depositaire.js"></script>
<?php

require('bottom.php');
