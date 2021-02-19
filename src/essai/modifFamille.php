<?php
require('../conf/connexion_param.php'); //connexion a la bdd
if (isset($_POST['idFamille']) && isset($_POST['nomFamille']) && isset($_POST['modeleFamille']) && isset ($_POST['heureFamille']) ){

	$id = $_POST["idFamille"];
	$nom = $_POST['nomFamille'];
	$modele = $_POST['modeleFamille'];
	$heure= $_POST['heureFamille'];
		
	$str = "UPDATE famille SET nomFamille = '$nom', modeleFamille = '$modele', heure = '$heure'  WHERE idFamille = $id";
	$req = mysqli_query($bdd, $str);

}

?>