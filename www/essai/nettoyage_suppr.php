<?php
require('../conf/connexion_param.php'); 
//Traitement des essais reçu en POST
$output=array();
if (isset ($_POST['essais'])){
	
	$success = false;
	if (isset ($_POST['essaisSuppr'])){
		
		$liste_essais_suppr = explode (" ", $_POST['essaisSuppr']);
		for ($i=0; $i<count($liste_essais_suppr); $i++){
			
			$idEssai=$liste_essais_suppr[$i];
			$str="delete from essaisupp where idessaiSupp=$idEssai;";
			$req=@mysqli_query($bdd, $str);
			if(!$req)
				$success = false;
			else{
				$success = true;
			}
			
		}
		
	}
	
	//Split de la chaîne de caractère de type "123456 789123 456789 etc."
	$liste_essais = explode (" ", $_POST['essais']);
	
	//Pour chaque essai
	for ($i=0; $i<count($liste_essais); $i++){
		
		//Recupération de l'id de l'essais
		$idEssai=$liste_essais[$i];
		//Attribution de la raison
		$raison="Nettoyage";
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
			$success = false;
		else
		{
			$str="delete from essai where idessai=$idEssai;";
			$req=@mysqli_query($bdd, $str);
			if(!$req)
				$output[]=$idEssai;

		}
	}
	
	echo json_encode(array('data'=>$output));
}
?>