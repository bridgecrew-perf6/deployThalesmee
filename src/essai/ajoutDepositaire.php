<?php
require('../conf/connexion_param.php'); //connexion a la bdd
if (isset($_POST['nomDep']) && isset($_POST['prenomDep']) && isset ($_POST['telDep']) && isset ($_POST['portableDep'])){

	$nom = $_POST['nomDep'];
	$prenom = $_POST['prenomDep'];
	$tel = $_POST['telDep'];
	$portable = $_POST['portableDep'];
	
	$str = "SELECT * FROM depositaire WHERE nomDep = '$nom' and prenomDep = '$prenom' and telDep = '$tel' and portableDep = '$portable'";
	$req = mysqli_query($bdd, $str);
	$lg = mysqli_fetch_object($req);
	
	if (mysqli_num_rows ($req) > 0){
		
		echo "false";
		
	}else{
		
		$str = "INSERT INTO depositaire VALUES (NULL, '$nom', '$prenom', '$tel', '$portable', 1)";
		$req = mysqli_query($bdd, $str);

	}


}

?>