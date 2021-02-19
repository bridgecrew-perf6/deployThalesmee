<?php
require('../conf/connexion_param.php'); //connexion a la bdd
if (isset($_POST["idEssai"]) && isset($_POST["cause"])){

	//Récuération des paramètres
	$idEssai = $_POST["idEssai"];
	$cause = $_POST["cause"];
	//Requête de récupération de la cause de l'anomalie
	$str = "SELECT nomCause FROM cause_anomalie WHERE idEssai_ESSAI = $idEssai";
	$req = mysqli_query($bdd, $str);
	//Si la cause n'a pas été donné
	if (mysqli_num_rows($req) == 0)
	{
		//Insertion de la cause
		$str = "INSERT INTO cause_anomalie VALUES ($idEssai, '$cause');";
		$req = mysqli_query($bdd, $str);
		echo "Insert";

	}else //La cause n'a pas été donnée
	{		
		if ($cause == "") //Si la cause est vide
		{
			//Suppression de la cause
			$str = "DELETE FROM cause_anomalie WHERE idEssai_ESSAI = $idEssai";

		}else //Sinon modification la cause
		{
			$str = "UPDATE cause_anomalie SET nomCause = '$cause' WHERE idEssai_ESSAI = $idEssai";
		}
		$req = mysqli_query($bdd, $str);
		echo "Update";
	}
	
}

?>