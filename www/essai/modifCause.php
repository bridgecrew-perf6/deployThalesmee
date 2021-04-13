<?php
require('../conf/connexion_param.php'); //connexion a la bdd
if (isset($_POST['idCause']) && isset($_POST['nomCause'])){

	$id = $_POST["idCause"];
	$nom = $_POST['nomCause'];	
		
	$str = "UPDATE cause SET nomCause = \"$nom\" WHERE idCause = $id";
	$req = mysqli_query($bdd, $str);

}

?>