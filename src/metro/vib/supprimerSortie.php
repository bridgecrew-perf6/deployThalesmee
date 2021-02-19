<?php
require('../fonction.php');
require('../conf/connexion_param.php');
if (isset($_GET["idEssai"]))
{
	//update des info de l'instrument
	$num = $_GET["idEssai"];
	$str="update instrument set idStatut_statut='1', idLocal_localisation='1' where numInstru='$num';";
	$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
	$req=mysqli_query($bdd,$str);
}

?>