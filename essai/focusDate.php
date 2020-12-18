<?php
session_start();
if(isset($_GET["jour"]))
{
	if ($_GET["jour"] == 0){
		$_SESSION["infoUser"]["date"]["jour"] -= 1;
	}else{
		$_SESSION["infoUser"]["date"]["jour"] += 1;
	}

}else if (isset($_GET["semaine"])){
	
	if ($_GET["semaine"] == 0){
		$_SESSION["infoUser"]["date"]["semaine"] -= 1;
	}else{
		$_SESSION["infoUser"]["date"]["semaine"] += 1;
	}

}else if (isset($_GET["mois"])){
	
	if ($_GET["mois"] == 0){
		$_SESSION["infoUser"]["date"]["mois"] -= $_GET["decalage"];
	}else{
		$_SESSION["infoUser"]["date"]["mois"] += $_GET["decalage"];
	}

}else if (isset($_GET["init"])){
	
	$_SESSION["infoUser"]["date"]["jour"] = 0;
	$_SESSION["infoUser"]["date"]["mois"] = 0;
	$_SESSION["infoUser"]["date"]["semaine"] = 0;
	
	

}else{
	
	$res = array();
	$jour = $_SESSION["infoUser"]["date"]["jour"];
	$mois = $_SESSION["infoUser"]["date"]["mois"];
	$annee = $_SESSION["infoUser"]["date"]["semaine"];
	$res["jour"]=$jour;
	$res["mois"]=$mois;
	$res["semaine"]=$annee;
	echo json_encode( $res );

}


?>