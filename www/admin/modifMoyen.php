<?php 
require('top.php');
if(!isset($_GET["MoyenAnc"]) || !isset($_GET["Moyen"]) || !isset($_GET["idService"]))
	echo '<div class="alert alert-danger"><strong>Erreur de récupération des parametres des moyens</strong></div>';
else{
	require('../conf/connexion_param.php');
	$ancMoyen=$_GET["MoyenAnc"];
	$moyen=$_GET["Moyen"];
	$idService=$_GET["idService"];
	$str="update moyen set nomMoyen='$moyen' where nomMoyen='$ancMoyen'";
	$req=@mysqli_query($bdd, $str);
	if(!$req)
		echo '<div class="alert alert-danger"><strong>Erreur de modification du moyen</strong></div>';
	else
		echo "<script>document.location.href='listMoyen.php?idService=$idService'</script>";

}
require('bottom.php');

?>