<?php
require('top.php');
if(!isset($_GET["idMoyen"]) || !isset($_GET["idService"]))
	echo '<div class="alert alert-danger"><strong>Erreur de récupération du moyen</strong></div>';
else
{
	$idMoyen=$_GET["idMoyen"];
	$idService=$_GET["idService"];
	require('../conf/connexion_param.php');
	$str="delete from moyen where idMoyen=$idMoyen";
	$req=@mysqli_query($bdd, $str); //le @ est un parametre de gestion d'erreur, il evite un affichage incompréhensible pour un utilisateur (warning / error), à enlever pour tester l'erreur 
	if(!$req)//si échoué
		echo '<div class="alert alert-danger"><strong>Erreur de supression du moyen</strong></div>';
	else //sinon on redirige vers la page précédente
		echo "<script>document.location.href='listMoyen.php?idService=$idService'</script>";

}
require('bottom.php');
?>