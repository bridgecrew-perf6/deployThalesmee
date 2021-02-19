<?php

header("Cache-Control: no-cache"); //fixe un bug sous ie, ie garde le premier resultat de la page en cache, et le reutilise pour les appel en ajx...
require('../conf/connexion_param.php'); //connexion a la bdd
require('../fonction.php'); //connexion a la bdd
//recup des parametres
$labo=$_GET["labo"];
if(isset($_GET["ladate"]))
{
	$res = array();
	$l = date('Y-m-d',strtotime($_GET["ladate"]));//Jour "actif"
	$lundi= date('Y-m-d H:i',strtotime($l." 08:00")); //Ajour actif avec heures
	//Date du jour
	$today= date('Y-m-d');
	if ($_GET["affichage"] == "semaine") $date_act = date('Y-m-d H:i',strtotime($l." 08:00 +6 days"));
	else $date_act = date('Y-m-d H:i',strtotime($l." 08:00 +30 days"));

	while (strtotime($lundi) <= strtotime($date_act))
	{
		$planifie = 0;
		$actuelle = 0;
		$lim_inf = date('Y-m-d H:i',strtotime($l." 00:00 -8 days")); //Calcul sur une semaine avant la jour "actif"
		$lim_sup = date('Y-m-d H:i',strtotime($l." 23:59 -1 days"));
		$str = "SELECT date_debut, date_fin, date_debut_prevu, date_fin_prevu, duree_planifie, duree_actuelle FROM essai e WHERE date_fin >= '$lim_inf' and date_fin <= '$lim_sup' and e.idService_service=$labo and EXISTS (SELECT idEtat_etat from etatessai et where (idEtat_etat=24) and idEssai_essai=e.idessai)";
		$req = mysqli_query($bdd, $str);

		if (mysqli_num_rows($req) > 0)
		{
			while ($lg = mysqli_fetch_object($req))
			{
				$duree_planifie = $lg->duree_planifie;
				$duree_actuelle = $lg->duree_actuelle;
				if ($duree_planifie == 0) $duree_planifie = dureePrimavera($lg->date_debut_prevu, $lg->date_fin_prevu);
				if ($duree_actuelle == 0) $duree_actuelle = dureePrimavera($lg->date_debut, $lg->date_fin);
				$planifie += $duree_planifie;
				$actuelle += $duree_actuelle;
			}

		}

		if ($actuelle != 0){
			$diff = $planifie / $actuelle * 100;
			array_push($res, round($diff, 1));
		} else array_push($res, "");
		
		

		$l = date('Y-m-d',strtotime($l." +1 days"));
		$lundi= date('Y-m-d H:i',strtotime($l." 08:00")); //Changement du jour "actif"
	}


	echo json_encode($res);
	
	
}
?>