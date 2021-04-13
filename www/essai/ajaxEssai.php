<?php
/** Description générale
* Ce fichier est utilisé pour récupérer les données dans la base de données. Il renvoie les informations nécessaires à la création du planning des essais. Il est appelé depuis js/essaiindex.js via une requête AJAX car cela permet de ne pas attendre la réponse parfois longue lors de la récupération des données
*/

/**
* Explication de la démarche
* - Vérification des paramètres
* - Récupération des différents moyens
* - Récupération des informations des essais pour le moyen concerné
* - Récupération des OFs associés à chaque essais
* - Récupération des informations nécessaires
* - Remplissage du tableau
*/
header("Cache-Control: no-cache"); //fixe un bug sous ie, ie garde le premier resultat de la page en cache, et le reutilise pour les appel en ajx...
require('../conf/connexion_param.php'); //connexion a la bdd
require('../fonction.php'); //connexion a la bdd
//recup des parametres
$labo=$_GET["labo"];
if(isset($_GET["typeEssai"]))
{
	if(isset($_GET["moyenSelect"]) && $_GET["moyenSelect"]!="")
	{
		$moyenSelect=$_GET["moyenSelect"];
		$typeEssai=$_GET["typeEssai"];
		//date
		$timestamp_unix=$_GET["date"]/1000;
		$dateAct= date('Y-m-d 00:00:00',$timestamp_unix);

		$str="SELECT idMoyen, nomMoyen from moyen 
		where idService_SERVICE='$labo' 
		and idMoyen in ($moyenSelect)
		union
		select null,'Moyen non défini' from dual; ";
		$reqMoyen=mysqli_query($bdd, $str);
		$output = array();
		$array = array(); // on créé le tableau
		$array[0]=array();//contient les infos pour planning
		$array[1]=array();//contient les infos pour liste att
		
		while($lgMoyen=mysqli_fetch_object($reqMoyen))
		{
			$idMoyen=$lgMoyen->idMoyen;
			$nomMoyen=$lgMoyen->nomMoyen;
			
			if($idMoyen=="")
				$t="e.idMoyen_Moyen is null";
			else
				$t="e.idMoyen_Moyen='$idMoyen'";
			
			// on recupere les moyens 
			$str="SELECT e.idEssai, et.idetat_etat, e.date_debut, e.date_fin, e.equipement, e.affaire, e.retard_interne, e.planifie,e.pastilleOrange, e.pastilleRouge, e.retardME, e.commentaire
			from essai e, etatessai et
			where
			 e.idService_SERVICE='$labo'
			and et.idEssai_ESSAI=e.idEssai
			and et.idetat_etat in ($typeEssai)
			and e.date_fin>='$dateAct'
			and e.date_debut < DATE_ADD('$dateAct', INTERVAL 7 DAY)
			and et.idetat_etat=(select max(idetat_etat) from etatessai where idEssai_ESSAI=e.idEssai)
			and (e.planifie=1 or (e.planifie=0 and et.idetat_etat!=22))
			and $t
			order by date_debut;";
			
			
			$req=mysqli_query($bdd, $str);
			if(mysqli_num_rows($req)!=0)
			{
				$output["$nomMoyen"]=array();
				while($lg = mysqli_fetch_array($req))
				{
					//pourrait etre integré dans la requete principal mais double le temps d'execution, plus performant comme cela
					$str="SELECT nomModele, noOF_equipement_of, article from tester, equipement_of, type_modele
					where noOF_equipement_of = noOf and idModele = idModele_TYPE_MODELE and idEssai_ESSAI=".$lg["idEssai"].";";
					
					$anomalie = 0;
					$str3="SELECT idEssai from anomalie where idEssai = ".$lg["idEssai"].";";
					$req3=mysqli_query($bdd,$str3);
					if(mysqli_num_rows($req3)!=0)
					{
						$anomalie=1;
					}else
					{
						$anomalie=0;
					}

					$idEtat_Etat = $lg["idetat_etat"];

					//Correction des erreurs dans la base de données pour les essais de maintenance
					if ($lg["affaire"] != "MAINT")
					{
						$strMaj="DELETE FROM etatEssai WHERE idEssai_ESSAI = ".$lg["idEssai"]." and idEtat_etat='27';";
						$reqMaj=mysqli_query($bdd,$strMaj);
						$strMaj="SELECT max(idetat_etat) as etat from etatessai where idEssai_ESSAI=".$lg["idEssai"]."";
						$reqMaj=mysqli_query($bdd,$strMaj);
						$idEtat_Etat = mysqli_fetch_object($reqMaj)->etat;
						
					}else
					{
						$strMaj="SELECT max(idetat_etat) as etat from etatessai where idEssai_ESSAI=".$lg["idEssai"]."";
						$reqMaj=mysqli_query($bdd,$strMaj);
						$idEtat_Etat = mysqli_fetch_object($reqMaj)->etat;
						if ($idEtat_Etat != 27)
						{
							$strMaj = "INSERT INTO etatEssai VALUES(NOW(), ".$lg["idEssai"].", 27)";
							$reqMaj=mysqli_query($bdd,$strMaj);
							$idEtat_Etat = 27;
						}	
					}
					
					//Récuperation des dates prévues
					$str2="SELECT  date_debut, date_debut_prevu, date_fin_prevu, duree_planifie_primavera, date_debut_primavera, duree_planifie, count(date_debut_prevu) as total FROM essai
					where idEssai=".$lg["idEssai"].";";
					$req2=mysqli_query($bdd,$str2);
					
					$nb=mysqli_fetch_object($req2);
					if ($nb->total > 0)
					{
						$date_prevu = $nb->date_debut_prevu;
						$date_fin_prevu = $nb->date_fin_prevu;
						$date_planifie = strtotime($nb->date_debut_prevu);
						$date_planifie_primavera = strtotime($nb->date_debut_primavera);
						$duree_planifie_primavera = $nb->duree_planifie_primavera;
						$duree_planifie = $nb->duree_planifie;
					}else {
						
						$date_prevu = "null";
						$date_fin_prevu = "null";
						$date_planifie = "null";
						$date_planifie_primavera = "null";
						$duree_planifie_primavera = "null";
						$duree_planifie = "null";
					}

					if ($date_planifie_primavera == NULL) $date_planifie_primavera = $date_planifie;
					if ($duree_planifie == 0) $duree_planifie = dureePrimavera($date_prevu , $date_fin_prevu);
					if ($duree_planifie_primavera == 0) $duree_planifie_primavera = $duree_planifie;

					//Si l'état est à 22
					$str3="SELECT dateEtat ,count(dateEtat)  as total FrOM etatessai where idEssai_ESSAI=".$lg["idEssai"]." and idEtat_Etat = 22;";
					$req3=mysqli_query($bdd,$str3);
					$nb=mysqli_fetch_object($req3);
					if ($nb->total > 0)
					{
						$etat=$nb->dateEtat;
					}else {
						
						$etat=$date_prevu;	
					}
					
					//Si l'état est à 24
					$str4="SELECT dateEtat ,count(dateEtat)  as total FrOM etatessai where idEssai_ESSAI=".$lg["idEssai"]." and idEtat_Etat = 24;";
					$req4=mysqli_query($bdd,$str4);
					$nb=mysqli_fetch_object($req4);
					if ($nb->total > 0)
					{
						$etat_fin=$nb->dateEtat;
					}else 
					{						
						$str5="SELECT dateEtat ,count(dateEtat) as total FrOM etatessai
						where idEssai_ESSAI=".$lg["idEssai"]." and idEtat_Etat = 23;";
						$req5=mysqli_query($bdd,$str5);
						$nb2=mysqli_fetch_object($req5);
						if ($nb2->total > 0)
						{
							$etat_fin=date("Y-m-d H:i:s");
						}else 
							$etat_fin=$date_fin_prevu;
					}
					
					$str5="SELECT descriptif ,count(descriptif) as total from anomalie where idEssai=".$lg["idEssai"].";";
					$req5=mysqli_query($bdd,$str5);
					$nb5=mysqli_fetch_object($req5);
					if ($nb5->total > 0)
					{
						$entete="<br><strong>Anomalie :</strong><br>";
						$descr = htmlspecialchars($nb5->descriptif, ENT_QUOTES);
						$descriptif= $entete.$descr;
					}else 
					{
						$descriptif = "";
					}
					
					if ($lg["commentaire"] != null)
					{
						$entete = "<br><strong>Remarque :</strong><br>";
						$com = htmlspecialchars($lg["commentaire"], ENT_QUOTES);
						$commentaire = $entete.$com;
						
					}else{
						
						$commentaire = "";
					}
					
					$reqOF=mysqli_query($bdd,$str);
					$of="<strong>OF : </strong>";
					while($lgOF=mysqli_fetch_object($reqOF))
					{
						$of.=$lgOF->nomModele." ".$lgOF->noOF_equipement_of;
						if ($lgOF->article != NULL) $of.= " <strong>Art : </strong>".$lgOF->article;
						$of.="</br>";
					}


					/**
					* @Return 
					* Ce fichier renvoie en tableau avec des indices textuels qui sera traité par js/essai/index.js
					* array("idEssai","idetat_etat","date_debut","date_fin","equipement","affaire",
					* "retard_interne","of",planifie","pastilleOrange","pastilleRouge","retardME",
					* "commentaire","descriptif");
					*/
					$row = array(
						"idEssai"=>$lg["idEssai"],
						"idetat_etat"=>$idEtat_Etat,
						"date_debut"=>$lg["date_debut"],
						"date_fin"=>$lg["date_fin"],
						"equipement"=>$lg["equipement"],
						"affaire"=>$lg["affaire"],
						"retard_interne"=>$lg["retard_interne"],
						"of"=>$of,	
						"planifie"=>$lg["planifie"],
						"pastilleOrange"=>$lg["pastilleOrange"],
						"pastilleRouge"=>$lg["pastilleRouge"],
						"retardME"=>$lg["retardME"],
						"commentaire"=>$commentaire,
						"descriptif"=>$descriptif,
						"date_prevu"=>$date_prevu,
						"date_planifie"=>$date_planifie,
						"duree_planifie"=>$duree_planifie,
						"date_planifie_primavera"=>$date_planifie_primavera,
						"duree_planifie_primavera"=>$duree_planifie_primavera,
						"date_fin_prevu"=>$date_fin_prevu,
						"date_livraison"=>$etat,
						"date_termine"=>$etat_fin,
						"anomalie"=>$anomalie
					);

					$output["$nomMoyen"][] = $row;
					
				}
			}
		}
		echo json_encode( $output );
	}
	else
	{
		echo json_encode(array());
	}
}
else
{
	$str="select e.idEssai, e.affaire, e.equipement, e.badge from essai e, service s, etatessai et
	where idService='$labo'
	and s.idservice=e.idService_SERVICE
	and e.planifie=0
	and et.idetat_etat=22
	and et.idEssai_ESSAI=e.idEssai
	and et.idetat_etat=(select max(idetat_etat) from etatessai where idEssai_ESSAI=e.idEssai)
	order by et.dateetat;";
	$req=mysqli_query($bdd,$str);
	$output=array();

	if(mysqli_num_rows($req)!=0)
	{
		while($lg = mysqli_fetch_array($req))
		{
			if ($lg["affaire"] != "MAINT")
			{
				$strMaj="DELETE FROM etatEssai WHERE idEssai_ESSAI = ".$lg["idEssai"]." and idEtat_etat='27';";
				$reqMaj=mysqli_query($bdd,$strMaj);
			}

			$row = array(
				"idEssai"=>$lg["idEssai"],
				"affaire"=>$lg["affaire"],
				"equipement"=>$lg["equipement"],
				"badge"=>$lg["badge"]
			);
			$output[] = $row;
		}
		
	}
	echo json_encode( $output );
}


