<?php
require('top.php');

if(!isset($_POST["idPret"]))
	echo "<div class='alert alert-danger'><strong>Erreur de récupération des parametres</strong></div>";
else
{
	require('../conf/connexion_param.php');
	$idPret=$_POST["idPret"];

	//On supprime le pret
	$str="delete from histo_pret where idPret=$idPret;";
	$req=mysqli_query($bdd, $str);
	if(!$req)
		echo "<div class='alert alert-danger'><strong>Erreur de suppression du pret</strong></div>";
	else //tout a fonctionné
	{
		echo '<div class="text-center">';
			echo "<div class='alert alert-success'><strong>Suppression éffectuée</strong></div>";
			echo "<input class='btn btn-lg btn-primary' type='button' value='Retour' onclick='document.location.href=\"index.php\"' />";
		echo '</div>';
	}
}

require('bottom.php');