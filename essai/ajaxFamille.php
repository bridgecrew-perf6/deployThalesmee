<?php
/** Description générale
* Ce fichier est utilisé pour récupérer les données dans la base de données. Il renvoie le nombre d'heures pour une famille d'équipement. Ce nombre correspond au temps par défault que le technicien doit passer sur l'équipement
*/

/**
* Explication de la démarche
* - Vérification des paramètres
* - Execution de la requête SQL
* - Envoie du résultat
*/
require('../conf/connexion_param.php'); //connexion a la bdd
if (isset ($_GET['famille'])){
	
	$famille = $_GET['famille'];
	$modele = $_GET['modele'];
	$res = array();
	
	$str = "SELECT heure FROM famille WHERE nomFamille = '$famille' and modeleFamille = '$modele'";
	//$str = "SELECT heure FROM famille WHERE nomFamille = '$famille'";
	$req=mysqli_query($bdd,$str);

	if (mysqli_num_rows($req) != 0)
	{
		$lg = mysqli_fetch_object($req);
		$res[] = $lg->heure;

	}else 
	{
		$res[] ="0";
	}

	echo json_encode($res);
}

?>