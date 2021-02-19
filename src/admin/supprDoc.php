<?php
require('top.php');
if(!isset($_GET["idSpec"]))
	echo '<div class="alert alert-danger"><strong>Erreur de récupération du moyen</strong></div>';
else
{
	$idSpec=$_GET["idSpec"];
	require('../conf/connexion_param.php');
	require('../conf/enregistrerDoc_param.php');
	$str="select nomFichier from spec_client where idSpec=$idSpec";
	$req=mysqli_query($bdd, $str);
	$nomFic=mysqli_fetch_object($req)->nomFichier;
	$str="delete from spec_client where idSpec=$idSpec";
	$req=mysqli_query($bdd, $str);
	if(!$req)//si échoué
		echo '<center><div class="alert alert-warning"><strong>Impossible de supprimer ce document, il est probablement utilisé par une ou plusieurs demande(s)</strong></div></center>';
	else //sinon on efface le fichier puis redirection vers la page docClient
	{	
		unlink(give_me_link_DocClient().$nomFic);
		echo "<script>document.location.href='docClient.php';</script>";
	}

}
require('bottom.php');
?>