<?php
//page de retour a l'etape precedente pour les essais
require('top.php');

if(!isset($_GET["idEssai"]))
		echo "<div class='alert alert-danger'><strong>Erreur de récupération du numéro de l'essai</strong></div>";
else{
	require('../conf/connexion_param.php'); //connexion a la bdd
	$idEssai=$_GET["idEssai"];
	
	$str="SELECT et.idEtat_ETAT	FROM essai e, etatEssai et
	where idEssai =$idEssai
	and et.idEtat_ETAT=(select max(idEtat_ETAT) from etatEssai where idEssai_ESSAI=e.idEssai)
	and et.idEssai_ESSAI=e.idEssai;";
	$req=mysqli_query($bdd, $str);
	if(!$req)
		echo "<div class='alert alert-danger'><strong>Erreur de récupération de l'état</strong></div>";
	else
	{
		$idEtat=(mysqli_fetch_object($req)->idEtat_ETAT);
		if($idEtat==22)
		{
			
			$str= "SELECT date_debut_prevu, date_fin_prevu FROM essai WHERE idEssai='$idEssai'";
			$req = mysqli_query($bdd, $str);
			$lg = mysqli_fetch_object($req);
			
			$date_debut_prevu = $lg->date_debut_prevu;
			$date_fin_prevu = $lg -> date_fin_prevu;
			
			$str = "UPDATE essai SET planifie='1', pastilleOrange=0, date_debut = '$date_debut_prevu', date_fin = '$date_fin_prevu' WHERE idEssai='$idEssai'";
			$req = mysqli_query($bdd, $str);
		}
		elseif($idEtat==23)
		{
			$str="delete from vibtesterpar where idEssai='$idEssai'";
			$req=mysqli_query($bdd, $str);
			//$str="UPDATE essai SET retard_interne='0' WHERE idEssai =$idEssai;";
			//$req=mysqli_query($bdd, $str);
		}
		
		$str="delete from etatEssai where idEssai_ESSAI='$idEssai' and idEtat_ETAT='$idEtat';";
		$req=mysqli_query($bdd, $str);
		if(!$req)
			echo "<div class='alert alert-danger'><strong>Erreur de changement d'état</strong></div>";
		else{
			echo "<script>document.location.href='index.php'</script>";
		}
	}		
}
require('bottom.php');
?>