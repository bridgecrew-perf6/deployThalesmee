<?php
session_start();
if(isset($_GET["toggle"]))
{
	$_SESSION['infoUser']['affichage'] = "mois";
}else{
	
	$_SESSION['infoUser']['affichage'] = "semaine";
	
}

?>