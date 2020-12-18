<?php
header("Cache-Control: no-cache"); //fixe un bug sous ie, ie garde le premier resultat de la page en cache, et le reutilise pour les appel en ajx...
if(isset($_GET["idHisto"]))
{
	require('../conf/connexion_param.php'); //connexion a la bdd
	$idHisto=$_GET["idHisto"];
	$str="DELETE FROM `histo_vib_capteur` WHERE idHistoCapt=$idHisto";
	$req=mysqli_query($bdd, $str);
}
