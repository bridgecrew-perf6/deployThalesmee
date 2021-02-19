<script src="../jquery-ui/js/jquery.js"></script>
<script src="../js/swal.js"></script>
<script src="../calendrier/calendrier.js"></script>
<script src="../js/rex_fifo.js"></script>
<?php
require('top.php');
require('../conf/connexion_param.php'); //connexion a la bdd
if (isset($_POST['cause'])){
	
	/*$err = false;
	$num=$_POST['numero'];
	$cause = $_POST['cause'];
	
	$str = "DELETE FROM cause";
	$req = mysqli_query($bdd,$str);
	for ($cpt=0; $cpt < count($num) ; $cpt++){
		
		if (isset ($num[$cpt])) {
			
			$cau = htmlspecialchars($cause[$cpt]);
			$str = "INSERT INTO cause VALUES ( $num[$cpt], '$cau');";
			$req=mysqli_query($bdd,$str);
			if(!$req){
				echo "<div class='alert alert-danger'><strong>Erreur de modification des familles d'equipements </strong></div>";
				$err = true;
				break;
			}
		}
			
	}
	*/

	echo '<script src="../js/success.js"></script>';

}
else{
		
	$str_modele = "SELECT nomModele FROM type_modele";
	$req_modele = mysqli_query($bdd, $str_modele);

	?>
	<link href="../calendrier/calendrier.css" rel="stylesheet" />	
	<link href="../css/addons/datatables.min.css" rel="stylesheet">
	<link href="../css/starter-template.css" rel="stylesheet">
	<script type="text/javascript" src="../js/addons/datatables.min.js"></script>
	<script type="text/javascript" src="../js/table.js"></script>
	<div class="container-fluid">
		
		<div class="page-header">
			<h2>Cause origine</h2>
		</div>
		<div id="erreurSuppr" class="alert alert-danger" role="alert">Erreur de suppression de la cause</div>
		<div id="erreurAjout" class="alert alert-danger" role="alert">Erreur d'ajout de la cause</div>
		<div id="erreurSave" class="alert alert-danger" role="alert">Erreur de récuperation de la sauvegarde</div>
	</div>
	<div class="container-fluid">
		<div class="jumbotron">
			<div class="page-header">
				<h4>Ajouter une cause</h4>
			</div>
			<div id="erreurInfo" class="alert alert-danger" role="alert">Veuillez renseigner toutes les informations demandées</div>
			<div id="doublon" class="alert alert-danger" role="alert">Erreur d'ajout de la cause : la cause existe déjà</div>
			<div class="form-row">
				
				<div class="form-goup col-md-3">
					<input type="text" class="form-control" id="ajoutCause" placeholder = "Cause">
				</div>
				<div class="form-goup col-md-3">
					<input type="btton" class="btn btn-block btn-primary" onclick="ajoutCause()" value="Ajouter">
				</div>
			</div>	
		</div>
	</div>
	<div class="container-fluid">
	<div class="jumbotron">
		
		<form method="post" action="causeOrigine.php">
		<table id="exemple" class=" table table-striped table-bordered"><thead><tr><th class="th-sm" scope=
		"col">Cause</th><th class="th-sm"  scope="col"
		></th></thead><tbody>
	<?php

	
	$str="SELECT nomCause, idCause FROM cause";	
	$req=mysqli_query($bdd, $str);


	$cpt = 0;
	while ($lg=mysqli_fetch_object ($req)){
		
		echo '<tr class="form-group" scope="row" id="'.$lg->idCause.'"><td scope="row"><input name =cause['.$cpt.'] class="form-control input" type=text value="'.$lg->nomCause.'"></td><td scope="row"><input type="button" onclick="confirmation(\''.$lg->idCause.'\')" class="btn btn-block btn-danger" value="Supprimer"></td></tr>';
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
<script src="../js/causeOrigine.js"></script>
<?php

require('bottom.php');
