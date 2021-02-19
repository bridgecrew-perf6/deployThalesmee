<?php
require('top.php');

if(!isset($_POST["idEssai"]))
		echo "<div class='alert alert-danger'><strong>Erreur de récupération du numéro de l'essai</strong></div>";
else{
	
	
	
	require('../conf/connexion_param.php'); //connexion a la bdd
	$idEssai=$_POST["idEssai"];

	$raison=$_POST["raison"];
	
	//copie de l'essai dans essaiSup
	$str="INSERT INTO essaiSupp SELECT null, `badge`, `affaire`, `equipement`, `os`, `commentaire`, 
		`idService_SERVICE`, `idMoyen_MOYEN`, `fifo`, `idDep_depositaire`, '$raison', date_debut, date_fin FROM essai
		where idessai=$idEssai;";
	$req=@mysqli_query($bdd, $str);
	$idEssaiSupp=mysqli_insert_id($bdd);
	//copie du lien avec les OF (tester) dans testerSupp
	$str="INSERT INTO testerSupp SELECT `noOF_EQUIPEMENT_OF`, '$idEssaiSupp' FROM tester
	where idessai_essai=$idEssai;";
	$reqOf=@mysqli_query($bdd, $str);
	if(!$req || !$reqOf)
		echo "<div class='alert alert-danger'><strong>Erreur de copie dans l'historique, suppression annulée</strong></div>";
	else
	{
		$str="delete from essai where idessai=$idEssai;";
		$req=@mysqli_query($bdd, $str);
		if(!$req)
			echo "<div class='alert alert-danger'><strong>Erreur suppression de l'essai</strong></div>";
		else{
			echo '<center>';
					echo "<div class='alert alert-success'><strong>L'essai n°$idEssai a bien été supprimé </strong></div>";
					echo "<input class='btn btn-lg btn-primary' type='button' value='Retour' onclick='document.location.href=\"index.php\"' />";	
			echo '</center>';
		}
	
	}
}
require('bottom.php');
?>