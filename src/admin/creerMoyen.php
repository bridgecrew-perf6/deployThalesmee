<?php 
require('top.php');
if(!isset($_GET["nouvMoyen"]) || !isset($_GET["idService"]))
	echo '<div class="alert alert-danger"><strong>Erreur de récupération du nom du nouveau moyen</strong></div>';
else
{
	$nouvMoyen=$_GET["nouvMoyen"];
	$idService=$_GET["idService"];
	require('../conf/connexion_param.php');
	$str="insert into moyen values(NULL,'$nouvMoyen',$idService)";
	$req=@mysqli_query($bdd, $str); //le @ est un parametre de gestion d'erreur, il evite un affichage incompréhensible pour un utilisateur (warning / error), à enlever pour tester l'erreur 
	if(!$req)//si échoué
		echo '<div class="alert alert-danger"><strong>Erreur d\'ajout du moyen</strong></div>';
	else //sinon on redirige vers la page précédente
		echo "<script>document.location.href='listMoyen.php?idService=$idService'</script>";
}
require('bottom.php');
?>