<?php
//lancée tout les jours par
require('../conf/connexion_param.php'); //connexion a la bdd
/*
$str="update essai e left join etatessai et on et.idEssai_ESSAI=e.idEssai
set e.date_Fin=now(), e.retard_interne=1
where e.date_Fin < CURDATE() 
and et.idetat_etat=(select max(idetat_etat) from etatessai where idEssai_ESSAI=e.idEssai) 
and et.idetat_etat=23";
$req=mysqli_query($bdd,$str);
echo mysqli_error($bdd);*/
if(isset($_GET['annee']) && (isset($_GET['mois'])) && (isset($_GET['jour']))){

	$annee = $_GET['annee'];
	$mois = $_GET['mois'];
	$jour = $_GET['jour'];
	$time = mktime(0,0,0,$mois, $jour, $annee);
	echo json_encode(intval(strval(date('W', $time))));
	

}


?>