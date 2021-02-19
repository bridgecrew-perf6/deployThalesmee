<?php
require('../conf/connexion_param.php'); //connexion a la bdd
if (isset($_GET["idEssai"]) && isset($_GET["reste"])){

	$idEssai = $_GET["idEssai"];
	$reste = $_GET["reste"];

	$str = "UPDATE famille_essai SET resteHeure = '$reste' WHERE idEssai_ESSAI = $idEssai";
	$req = mysqli_query($bdd, $str);
}

?>