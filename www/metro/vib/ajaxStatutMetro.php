<?php
header("Cache-Control: no-cache"); //fixe un bug sous ie, ie garde le premier resultat de la page en cache, et le reutilise pour les appel en ajx...
if(!isset($_GET["numInstru"]))
	echo "Erreur de récéption des parametres";
else
{
	require('../conf/connexion_param.php'); //connexion a la bdd
	$numInstru=$_GET["numInstru"];
	$str="select idStatut_statut from instrument where numInstru='$numInstru'";
	$req=mysqli_query($bdd, $str);
	if(!$req)
		echo "Erreur d'accés au statut actuelle";
	else
	{
		$idStatut=mysqli_fetch_object($req)->idStatut_statut;
		$ajout="";
		if($idStatut==1) //pas en calib -> passage en calib
			$newIdStatut=2;
		else //deja en calib -> passage en normal
		{
			$newIdStatut=1;
			$ajout=",date_derniereInt=CURDATE()";
		}
		$str="update instrument set idStatut_statut='$newIdStatut' $ajout where numInstru='$numInstru';";
		$req=mysqli_query($bdd, $str);
		
		echo "0"; 
	}
}