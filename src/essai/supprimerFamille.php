<?php
require('../conf/connexion_param.php'); //connexion a la bdd

if (isset($_POST['famille'])){
	
	//Sauvegarde de l'ancien tableau de familles
	$str="SELECT nomFamille, idFamille, modeleFamille, heure FROM famille";	
	$req=mysqli_query($bdd, $str);
	$famille = "";
	while ($lg=mysqli_fetch_object ($req)){
		
		
		$famille .= $lg->idFamille.";".$lg->nomFamille.";".$lg->modeleFamille.";".$lg->heure.":";
		
	}
	
	$famille = substr($famille,0,-1);
	
	$str = "INSERT INTO saveFamille VALUES (NULL, '$famille', DEFAULT)";
	$req = mysqli_query($bdd, $str);
	
	$id = $_POST['famille'];
	$str = "DELETE FROM famille WHERE idFamille = $id";
	$req = mysqli_query($bdd, $str);
	
}


?>