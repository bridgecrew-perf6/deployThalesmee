<?php
require('../conf/connexion_param.php'); //connexion a la bdd
if (isset($_POST['famille']) && isset($_POST['modele']) && isset ($_POST['heure'])){

	$famille = $_POST['famille'];
	$modele = $_POST['modele'];
	$heure = $_POST['heure'];
	
	$str = "SELECT * FROM famille WHERE nomFamille = '$famille' and modeleFamille = '$modele' and heure = '$heure'";
	$req = mysqli_query($bdd, $str);
	$lg = mysqli_fetch_object($req);
	
	if (mysqli_num_rows ($req) > 0){
		
		echo "false";
		
	}else{
		
		$str = "INSERT INTO famille VALUES (NULL, '$famille', '$modele', '$heure')";
		$req = mysqli_query($bdd, $str);

	}
}
?>