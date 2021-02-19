<?php
require('../conf/connexion_param.php'); //connexion a la bdd
if (isset($_GET["idEssai"]) && isset($_GET["status"])){

	$idEssai = $_GET["idEssai"];
	$status = $_GET["status"];

	$str = "SELECT heure_FAMILLE FROM famille_essai WHERE idEssai_ESSAI = $idEssai";
	$req = mysqli_query($bdd, $str);
	if (mysqli_num_rows($req) == 0)
	{
		echo "Erreur";

	}else{
		
		$str = "UPDATE famille_essai SET status = $status WHERE idEssai_ESSAI = $idEssai";
		$req = mysqli_query($bdd, $str);
	}
	
}

?>