<?php
require('top.php');

if(!isset($_POST["idPV"]))
	echo "<div class='alert alert-danger'><strong>Erreur de récupération des parametres</strong></div>";
else
{
	require('../conf/connexion_param.php');
	$idPV=$_POST["idPV"];

	//On supprime le pret
	$str="delete from PV where idPV=$idPV;";
	$req=mysqli_query($bdd, $str);
	if(!$req)
		echo "<div class='alert alert-danger'><strong>Erreur de suppression du PV</strong></div>";
	else //tout a fonctionné
	{
		echo '<div class="text-center">';
			echo "<div class='alert alert-success'><strong>Suppression éffectuée</strong></div>";
			echo "<input class='btn btn-lg btn-primary' type='button' value='Retour' onclick='document.location.href=\"index.php\"' />";
		echo '</div>';
	}
}
require('bottom.php');