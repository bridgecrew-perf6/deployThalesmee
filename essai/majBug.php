<?php
require('../conf/connexion_param.php'); 
require('../conf/connexionPDO_param.php');// connexion a la base
require('top.php');



$str = "select distinct(idEssai), et.dateEtat, et.idEtat_etat from essai e, etatessai et, ligneproduit where et.idEssai_essai=e.idEssai and (et.idEtat_etat=22 or idEtat_etat=23) and EXISTS (select idEtat_etat from etatessai et where (idEtat_etat=23) and idEssai_essai=e.idessai) order by e.idessai, et.idEtat_etat;";


$req = mysqli_query($bdd, $str);
$correct = false;
while ($lg = mysqli_fetch_object($req)){
	
	$dateEtat=$lg->dateEtat;
	$idEtat=$lg->idEtat_etat;
	if($idEtat==23 and $correct == true)//dateEtat contient la date de fin d'attente
	{
		$nbj=floor((strtotime($dateEtat) - strtotime($datePrecedente))/86400);
		if ($nbj < 0){
			
			$str = "UPDATE etatessai SET dateEtat = '$dateEtat' WHERE idEssai_ESSAI = $lg->idEssai and idEtat_etat = 22";
			$req2 = mysqli_query($bdd, $str);
			
		}

		$correct = false;
		
	}
	else if ($idEtat==22) {//dateEtat contient la date de debut d'attente
		$correct = true;
		$datePrecedente=$dateEtat;
	}
	
}
?>