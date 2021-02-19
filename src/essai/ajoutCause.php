<?php
require('../conf/connexion_param.php'); //connexion a la bdd
if (isset($_POST['cause'])){

	$cause = $_POST['cause'];
	
	$str = "SELECT * FROM cause WHERE nomCause = \"$cause\"";
	$req = mysqli_query($bdd, $str);
	$lg = mysqli_fetch_object($req);
	
	if (mysqli_num_rows ($req) > 0){
		
		echo "false";
		
	}else{
		
		$str = "INSERT INTO cause VALUES (NULL, \"$cause\")";
		$req = mysqli_query($bdd, $str);
	}
}

?>