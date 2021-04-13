<?php
require('../conf/connexion_param.php'); //connexion a la bdd
if (isset($_POST['idDep']) && isset($_POST['nomDep']) && isset($_POST['prenomDep']) && isset ($_POST['telDep']) && isset ($_POST['portableDep'])){

	$id = $_POST["idDep"];
	$nom = $_POST['nomDep'];
	$prenom = $_POST['prenomDep'];
	$tel = $_POST['telDep'];
	$portable = $_POST['portableDep'];
	
	$str = "UPDATE depositaire SET nomDep = '$nom', prenomDep = '$prenom', telDep = '$tel', portableDep = '$portable' WHERE idDep = $id";
	$req = mysqli_query($bdd, $str);
}

?>