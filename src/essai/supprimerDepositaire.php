<?php
require('../conf/connexion_param.php'); //connexion a la bdd

if (isset($_POST['idDep'])){
	
	$id = $_POST['idDep'];
	$str = "UPDATE depositaire SET actif = 0 WHERE idDep = $id";
	$req = mysqli_query($bdd, $str);
	if (!$req)
		echo "false";
	
}


?>