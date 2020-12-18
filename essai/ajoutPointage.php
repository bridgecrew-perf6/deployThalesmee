<?php
require('../conf/connexion_param.php'); //connexion a la bdd
if (isset($_GET["idEssai"]) && isset($_GET["idEmp"]) && isset($_GET["heure"])){

	$idEssai = $_GET["idEssai"];
	$idEmp = $_GET["idEmp"];
	$heure = $_GET["heure"];

	$str = "SELECT heure_FAMILLE FROM famille_essai WHERE idEssai_ESSAI = $idEssai";
	$req = mysqli_query($bdd, $str);
	if (mysqli_num_rows($req) == 0)
	{
		echo "Erreur";

	}else {

		$str = "SELECT heure FROM pointage WHERE idEssai_ESSAI = $idEssai and idEmp_EMPLOYE = $idEmp";
		$req = mysqli_query($bdd, $str);

		if (mysqli_num_rows($req) == 0)
		{
			$str = "INSERT INTO pointage VALUES(NULL, $idEssai, $idEmp, '$heure')";
		}else
		{
			$str = "UPDATE pointage SET heure = '$heure' WHERE idEssai_ESSAI = $idEssai and idEmp_EMPLOYE = $idEmp";
		}

		$req = mysqli_query($bdd, $str);
	}
}

?>