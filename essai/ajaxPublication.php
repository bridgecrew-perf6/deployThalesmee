<?php

	require('../conf/connexion_param.php');
	session_start();
	$labo=$_SESSION['infoUser']['idService'];// service du labo
	//On souhaite les remarques pour la semaine et l'année
	if (isset($_GET["semaine"]) && isset($_GET["annee"]))
	{
		//Récupération des paramètres
		$semaine = $_GET["semaine"];
		$annee = $_GET["annee"];
		$res = array();
		//Requete SQL
		$str = "SELECT analyse, mesure, fait, deviation FROM publication WHERE numSemaine = $semaine AND annee = $annee AND idService_SERVICE = $labo";
		$req = mysqli_query($bdd, $str);
		//Si l'object existe
		if(mysqli_num_rows($req) != 0)
		{
			//Remplissage du tableau avec les valeurs de la base de données
			$lg = mysqli_fetch_object($req);
			echo json_encode($res = array (
				"analyse" => $lg->analyse,
				"mesure" => $lg->mesure,
				"fait" => $lg->fait,
				"deviation" => $lg->deviation
			));
		//Sinon remplissage avce des valeurs pas défault
		}else
		{
			echo json_encode($res = array (
				"analyse" => "",
				"mesure" => "",
				"fait" => "",
				"deviation" => "null"
			));
		}
	}
?>