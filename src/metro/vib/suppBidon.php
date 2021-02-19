<?php
require('top.php');

if(!isset($_POST["idProjet"]))
	echo "<div class='alert alert-danger'><strong>Erreur de récupération des parametres</strong></div>";
else
{
	require('../conf/connexion_param.php');
	$idProjet=$_POST["idProjet"];
	
	//on remet les instruments en stock
	$str="update instrument set idLocal_localisation='1'
	where numInstru in
		(select numInstru_instrument from instrument_vib_capteur iv, concerneBidon c 
			where idProjet_projetBidon='$idProjet'
			and iv.idInstruCapt=c.idInstruCapt_instrument_vib_capteur);";
	$req=mysqli_query($bdd, $str);
	
	//On supprime le projet
	$str="delete from projetBidon where idProjet = '$idProjet';";
	$req=mysqli_query($bdd, $str);
	if(!$req)
		echo "<div class='alert alert-danger'><strong>Erreur de suppression du projet</strong></div>";
	else //tout a fonctionné
	{
		echo '<div class="text-center">';
			echo "<div class='alert alert-success'><strong>Suppression éffectuée</strong></div>";
			echo "<input class='btn btn-lg btn-primary' type='button' value='Retour' onclick='document.location.href=\"gestionBidon.php\"' />";
		echo '</div>';
	}
}
require('bottom.php');