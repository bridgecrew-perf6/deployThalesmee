<?php
require('top.php');

if(!isset($_POST["numInstru"]))
	echo "<div class='alert alert-danger'><strong>Erreur de récupération des parametres</strong></div>";
else
{
	require('../conf/connexion_param.php');
	$numInstru=$_POST["numInstru"];

	//On supprime l'employe, le On delete cascade s'occupe de supprimer l'utilisateur de l'appareil
	$str="delete from instrument where numInstru = $numInstru;";
	$req=mysqli_query($bdd, $str);
	if(!$req)
		echo "<div class='alert alert-danger'><strong>Erreur de suppression de l'instrument</strong></div>";
	else //tout a fonctionné
	{
		echo '<div class="text-center">';
			echo "<div class='alert alert-success'><strong>Suppression éffectuée</strong></div>";
			echo "<input class='btn btn-lg btn-primary' type='button' value='Retour' onclick='document.location.href=\"index.php\"' />";
		echo '</div>';
	}
}

require('bottom.php');