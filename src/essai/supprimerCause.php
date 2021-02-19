<?php
require('../conf/connexion_param.php'); //connexion a la bdd

if (isset($_POST['cause'])){
	
	//Sauvegarde de l'ancien tableau de familles
	$str="SELECT nomCause, idCause FROM cause";	
	$req=mysqli_query($bdd, $str);
	$cause = "";
	
	$id = $_POST['cause'];
	$str = "DELETE FROM cause WHERE idCause = $id";
	$req = mysqli_query($bdd, $str);
	
}


?>