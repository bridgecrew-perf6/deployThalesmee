<?php
header("Cache-Control: no-cache"); //fixe un bug sous ie, ie garde le premier resultat de la page en cache, et le reutilise pour les appel en ajx...

/** Description générale
* Ce fichier est utilisé pour mettre à jour les informations d'un essai lors de la modification des date de débuts et de fin via redimenssionnement de la barre ou glissé déposé
*/

/**
* Explication de la démarche
* - Vérification des paramètres
* - Calcul de la différence entre la date de début et de fin
* - 
*/
require('../conf/connexion_param.php'); //connexion a la bdd
require('../fonction.php');


$idEssai=$_GET["idEssai"];
if(isset($_GET["dateDebut"]))
{
	$timestamp_unix=$_GET["dateDebut"]/1000;
	$nvDateDebut= date('Y-m-d H:i',$timestamp_unix);
	
	$str="SELECT date_debut ,date_fin from essai where idEssai=$idEssai;";
	$req=mysqli_query($bdd, $str);

	$lg=mysqli_fetch_object($req);
	$date_debut=$lg->date_debut;
	$date_fin=$lg->date_fin;
	
	$diff = duree(date('d/m/Y H:i',strtotime($date_debut)), date('d/m/Y H:i',strtotime($date_fin)));

	$nbHeure = intval($diff["heure"]);
	$date_fin = $nvDateDebut;
	
	while ($nbHeure != 0){
		
		$heure= explode (" ", $date_fin);
		$date_fin = explode("-", $heure[0]);
		$heure_fin = explode (":", $heure[1])[0];
		
		if (intval($heure_fin) >= 8 && intval($heure_fin) < 17){
				
			$date_fin = date("Y-m-d H:i", strtotime($date_fin[0]."-".$date_fin[1]."-".$date_fin[2]." ".$heure[1]." +1 hours"));
			$nbHeure -= 1;
			
		}else {
			
			$date_fin = date("Y-m-d H:i", strtotime($date_fin[0]."-".$date_fin[1]."-".$date_fin[2]." ".$heure[1]." +1 hours"));
		}
	}
	
	$nvDateFin = $date_fin;
	/*$diff=strtotime ($date_fin)-strtotime($date_debut);		
	
	$nvDateFin=date('Y-m-d H:i',$timestamp_unix+$diff);
	
	$new = multiexplode (array(":","-"," "), $nvDateFin);
	if ($new [3] == "00"){
		
		$nvDateFin=date('Y-m-d H:i', strtotime($new[0]."-".$new[1]."-".$new[2]." 17:00:00 -1 day"));
	}else if ($new[3][1] != "0" && $new[3] > "17"){
		
		$nvDateFin=date('Y-m-d H:i', strtotime($new[0]."-".$new[1]."-".$new[2]." 17:00:00"));
	}*/

	$duree = dureePrimavera($nvDateDebut, date('Y-m-d H:i',strtotime($nvDateFin)));
	
	//Requete permettant de modifier la date de début et de fin de l'essai
	$str="UPDATE essai SET date_debut ='$nvDateDebut', date_fin='$nvDateFin', duree_actuelle = $duree WHERE idEssai =$idEssai;";
	$req=mysqli_query($bdd, $str);
	
	//Requete permettant de récupérer l'état maximum de l'essai
	$str="SELECT max(`idEtat_ETAT`)as etat FROM `etatessai` WHERE `idEssai_ESSAI`=$idEssai;";
	$req=mysqli_query($bdd, $str);
	$lg=mysqli_fetch_object($req);
	$etat=$lg->etat;
	
	if ($etat != 22){
		//Requete pour la modification des états des essais 
		//Mise à jour de la date de passage dans  l'état
		$str="UPDATE etatessai SET dateEtat ='$nvDateDebut' WHERE idEssai_ESSAI =$idEssai and idEtat_ETAT = $etat;";
		$req=mysqli_query($bdd, $str);
	}
	
	if ($etat == 23){
		//Requete pour la modification des états des essais 
		//Mise à jour de la date de passage dans  l'état
		$str="SELECT dateEtat FROM etatessai WHERE idEssai_ESSAI =$idEssai and idEtat_ETAT = 22;";
		$req=mysqli_query($bdd, $str);
		$lg = mysqli_fetch_object($req);
		$date = $lg->dateEtat;

		$heure= explode (" ", $nvDateDebut);
		$date_fin = explode("-", $heure[0]);
		$heure_fin = explode (":", $heure[1])[0];
		$diff = strtotime($date_fin[0]."-".$date_fin[1]."-".$date_fin[2]." ".$heure[1]) - strtotime($date);

		if ($diff < 0){
			
			$str="UPDATE etatessai SET dateEtat ='$nvDateDebut' WHERE idEssai_ESSAI =$idEssai and idEtat_ETAT = 22;";
			$req=mysqli_query($bdd, $str);
		}
	}
	
	if ($etat <= 22){

		$str="UPDATE essai SET date_debut_prevu ='$nvDateDebut', date_fin_prevu='$nvDateFin',duree_planifie = $duree WHERE idEssai =$idEssai;";
		$req=mysqli_query($bdd, $str);
	}
	
}
elseif(isset($_GET["dateFin"]))
{
	$str="SELECT date_debut ,date_fin from essai where idEssai=$idEssai;";
	$req=mysqli_query($bdd, $str);

	$lg=mysqli_fetch_object($req);
	$date_debut=$lg->date_debut;
	$date_fin=$lg->date_fin;	

	$timestamp_unix=$_GET["dateFin"]/1000;
	$nvDateFin= date('Y-m-d H:i',$timestamp_unix);

	$duree = dureePrimavera(date('Y-m-d H:i',strtotime($date_debut)), $nvDateFin);
	
	$str="UPDATE essai SET date_fin='$nvDateFin', duree_actuelle=$duree WHERE idEssai =$idEssai;";
	$req=mysqli_query($bdd, $str);
	
	$str="SELECT max(`idEtat_ETAT`)as etat FROM `etatessai` WHERE `idEssai_ESSAI`=$idEssai;";
	$req=mysqli_query($bdd, $str);
	$etat=mysqli_fetch_object($req)->etat;
	if ($etat <=22){
		
		$str="UPDATE essai SET date_fin_prevu='$nvDateFin', duree_planifie = $duree WHERE idEssai =$idEssai;";
		$req=mysqli_query($bdd, $str);
	}
}