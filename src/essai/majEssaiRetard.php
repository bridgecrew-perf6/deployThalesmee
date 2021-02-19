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

if(isset($_GET['retard'])){
	
	if(isset($_GET['id'])){

		$id = $_GET['id'];
		$str = "update essai set retardME=1 where idEssai = $id;";
		$req=mysqli_query($bdd,$str);
		
	}
	
}else{
	
	if(isset($_GET['id'])){

		$id = $_GET['id'];
		$str = "update essai set pastilleOrange=1 where idEssai = $id;";
		$req=mysqli_query($bdd,$str);	

	}

	if(isset($_GET['idRouge'])){

		$id = $_GET['id'];
		$str = "update essai set pastilleRouge=1 where idEssai = $id;";
		$req=mysqli_query($bdd,$str);
		
	}
}

?>
