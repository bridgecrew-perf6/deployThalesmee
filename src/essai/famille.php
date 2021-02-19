<script src="../jquery-ui/js/jquery.js"></script>
<script src="../js/swal.js"></script>
<script src="../calendrier/calendrier.js"></script>
<script src="../js/rex_fifo.js"></script>
<?php
require('top.php');
$labo=$_SESSION["infoUser"]["idService"];
require('../conf/connexion_param.php'); //connexion a la bdd
if (isset($_POST['famille'])){
	
	/*//Sauvegarde de l'ancien tableau de familles
	$str="SELECT nomFamille, idFamille, modeleFamille, heure FROM famille";	
	$req=mysqli_query($bdd, $str);
	$famille = "";
	while ($lg=mysqli_fetch_object ($req)){
		
		
		$famille .= $lg->idFamille.";".$lg->nomFamille.";".$lg->modeleFamille.";".$lg->heure.":";
		
	}
	
	$famille = substr($famille,0,-1);
	
	$str = "INSERT INTO saveFamille VALUES (NULL, '$famille', DEFAULT)";
	$req = mysqli_query($bdd, $str);
		
	$num=$_POST['numero'];
	$famille = $_POST['famille'];
	$modele = $_POST['modele'];
	$heure = $_POST['heure'];
	$err = false;
	$str = "DELETE FROM famille";
	$req = mysqli_query($bdd,$str);
	for ($cpt=0; $cpt < count($num) ; $cpt++){
		
		if (isset ($num[$cpt]) and isset ($heure[$cpt])){
			
			$fam = htmlspecialchars($famille[$cpt]);
			$mod = htmlspecialchars(mysqli_real_escape_string($bdd,$modele[$cpt]));
			$str = "INSERT INTO famille VALUES ( $num[$cpt], '$fam', '$mod', $heure[$cpt]);";
			$req=mysqli_query($bdd,$str);
			if(!$req){
				echo "<div class='alert alert-danger'><strong>Erreur de modification des familles d'equipements </strong></div>";
				$err = true;
				break;
			}
		}
			
	}*/

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
			<h2>Famille équipements</h2>
		</div>
		<div id="erreurSuppr" class="alert alert-danger" role="alert">Erreur de suppression de la famille d'équipement</div>
		<div id="erreurAjout" class="alert alert-danger" role="alert">Erreur d'ajout de la famille d'équipement</div>
		<div id="erreurSave" class="alert alert-danger" role="alert">Erreur de récuperation de la sauvegarde</div>
	</div>
	<div class="container-fluid">
		<div class="jumbotron">
			<div class="page-header">
				<h4>Ajouter une famille</h4>
			</div>
			<div id="erreurInfo" class="alert alert-danger" role="alert">Veuillez renseigner toutes les informations demandées</div>
			<div id="doublon" class="alert alert-danger" role="alert">Erreur d'ajout de la famille : la famille existe déjà</div>
			<div class="form-row">
				
				<div class="form-goup col-md-3">
					<input type="text" class="form-control" id="ajoutFamille" placeholder = "Famille">
				</div>
				<div class="form-goup col-md-3">
					<select id="ajoutModele" class="form-control">
					<option value="" selected>Modèle</option>
					<?php
						while ($lg = mysqli_fetch_object ($req_modele)){
							
							echo '<option value="'.$lg->nomModele.'">'.$lg->nomModele.'</option>';
						}
					?>
					</select>

				</div>
				<div class="form-goup col-md-3">
					<input type="text" class="form-control" id="ajoutHeure" placeholder = "Heure">
				</div>
				<div class="form-goup col-md-3">
					<input type="btton" class="btn btn-block btn-primary" onclick="ajoutFamille()" value="Ajouter">
				</div>
			</div>	
		</div>
	</div>
	<div class="container-fluid">
	<div class="jumbotron">
		
		<form method="post" action="famille.php">
		<table id="exemple" class=" table table-striped table-bordered"><thead><tr><th class="th-sm" scope="col">Famille</th><th class="th-sm" scope="col">Modele</th><th class="th-sm" scope="col">Heure</th><th class="th-sm"  scope="col"
		></th></thead><tbody>
	<?php
	/*if (isset($_GET['num'])){
		
		$id = $_GET['num'];
		if ($id > 1){
			
			$prec = $id - 1;
		}else{
			
			$prec = "";
		}
		echo "ici";
		$str = "SELECT famille FROM saveFamille WHERE idSaveFamille = $id";
		$req = mysqli_query($bdd, $str);
		while ($lg = mysqli_fetch_object ($req)){
			
			$tab = explode (":",$lg->famille);
	
			for ($i=0 ; $i< count($tab); $i++){
								
				$fam = explode (";",$tab[$i]);
				
				$str = "DELETE FROM famille";
				$req = mysqli_query($bdd, $str);
				
				$str = "INSERT INTO famille VALUES ($fam[0], '$fam[1]', '$fam[2]', $fam[3]);";
				$req = mysqli_query($bdd, $str);

				
			}
		}
		
		
	}
		
	$str="SELECT max(`idSaveFamille`) as maxFamille FROM `savefamille` ;";	
	$req=mysqli_query($bdd, $str);
	$lg = mysqli_fetch_object ($req);
	$prec = $lg->maxFamille;*/
	
	$str="SELECT nomFamille, idFamille, modeleFamille, heure FROM famille";	
	$req=mysqli_query($bdd, $str);


	$cpt = 0;
	
	while ($lg=mysqli_fetch_object ($req)){
		
		echo '<tr class="form-group" scope="row" id="'.$lg->idFamille.'">
				<td scope="row">
					<input class="form-control input" name = famille['.$cpt.'] type=text value="'.$lg->nomFamille.'">
				</td>
				<td scope="row">
					<select class="form-control input">
					<option value="" selected>Modèle</option>';
						$req_modele = mysqli_query($bdd, $str_modele);
						while ($lg_req = mysqli_fetch_object ($req_modele)){
							
							if ($lg->modeleFamille == $lg_req->nomModele)
								echo '<option selected value="'.$lg_req->nomModele.'">'.$lg_req->nomModele.'</option>';
							else
								echo '<option value="'.$lg_req->nomModele.'">'.$lg_req->nomModele.'</option>';
						}

					echo '</select>
				</td>
				<td scope="row">
					<input class="form-control input"  type="text" value='.$lg->heure.'>
				</td>
				<td scope="row"><input type="button" onclick="confirmation(\''.$lg->idFamille.'\')" class="btn btn-block btn-danger" value="Supprimer">
				</td>
			</tr>';
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
<script src="../js/famille.js"></script>
<?php

require('bottom.php');
