<?php
/*Fichier comportant tous les graphiques de la section indicateurs du menu retrouvable dans top.php
La démarche est identique pour les 4 graphiques suivants. Pour la démarche globale se réferer aux commentaires du graphique1
Chaque graphique dispose de commentaires expliquant le code. Si le code n'est pas commenté voir dans les graphiques précédents.
*/
require('../conf/connexion_param.php');// connexion a la base
require ('jpgraph/jpgraph.php');
require ('jpgraph/jpgraph_bar.php');
require ('jpgraph/jpgraph_line.php');
require('fonction.php');

/*
* Fonction permettant de générer des LinePlot sur le graphe
* @param
* graph : le graph concerné
* data : la donnée à affichée
* legend : la légende associée
* color : la couleur de la barre
* dotted : Booleén permettant de savoir si la ligne est en pointillée ou non
* weight : l'épaisseur de la barre
* @return 
* graph : le nouveau graphe
*/
function creerBarre ($graph, $data, $legend, $color, $dotted, $weight){

	$data1y=$data;
	$b1plot = new LinePlot($data1y);
	$graph->Add($b1plot);
	$b1plot->SetLegend($legend);
	$b1plot->SetWeight($weight);
	$b1plot->SetColor($color);
	if ($dotted) $b1plot->SetStyle('dotted');
	$b1plot->value->SetFormat('%d');
	return $graph;
}

/*
* Fonction permettant de générer une requête dans la base de données
* bdd : la base de données
* ligne : la ligne de produit
* labo : la leboratoire
* dateDeb : la date de début
* dateFin : la date de fin
* @return
* $lg : le résultat de la requête
*/
function selectTotalEssai ($bdd, $ligne, $labo, $dateDeb, $dateFin)
{
	$str="Select count(*) as tot from ligneproduit, anomalie  a, essai e where e.idService_SERVICE='$labo' and a.quiss_mpti='QUISS' and nomLIgne='$ligne' and idLigne = autre and a.idEssai=e.idEssai and e.date_debut >= '$dateDeb' and e.date_debut <= '$dateFin' order by e.date_debut;";
	$req=mysqli_query($bdd, $str);
	return mysqli_fetch_object($req);
}

//Si la date de début, la date de fin et la uméro de service est passé en paramètre
if(isset($_GET["dateDeb"]) && isset($_GET["dateFin"]) && isset($_GET["idService"]))
{	
	$labo=$_GET["idService"];// service du labo

	// Initialisation du tableau qui contiendra les ligne de produit passés en paramètre
	$libele_ligne = array(); 
	if (isset ($_GET['I2PT'])) array_push($libele_ligne, "I2PT");
	if (isset ($_GET['I2PA'])) array_push($libele_ligne, "I2PA");
	if (isset ($_GET['LPA'])) array_push($libele_ligne, "LPA");
	if (isset ($_GET['LPH'])) array_push($libele_ligne, "LPH");
	if (isset ($_GET['Autre'])) array_push($libele_ligne, "Autre");
	if (isset ($_GET['LPE'])) array_push($libele_ligne, "LPE");
	if (isset ($_GET['Total'])) array_push($libele_ligne, "Total");
	if (isset ($_GET['HorsDite'])) array_push($libele_ligne, "HorsDite");

	$dateDeb=$_GET["dateDeb"]; //Date de début
	$dateFin=$_GET["dateFin"]; //Date de fin

	//Date du jour (date actuelle)
	$date_act = strftime("%d/%m/%Y", mktime(0,0,0,date('m'), date('d'), date('Y')));

	$legend = array(); //Tableau pour la légende
	//Tableau permettant d'avoir le nom d'un mois en fonction de son numéro
	$nomMois = array("", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");

	//Initialisation des différents tableau
	$dite=array();
	$lph=array();
	$lpe=array();
	$lpa=array();
	$i2pa=array();
	$autre=array();
	$total = array();
	$horsDite = array();

	//Booléen pour connaître le premier tour de boucle
	$prems=true;
	//Booléen qui permet de savoir si des essais son présent (utlise pour le titre -> si pas d'essai pas le même titre)
	$essai=false;

	/*
	* Récupération des nombres d'essais pour chaque ligne + le total + total hors dite
	*/
	//DITE
	//Récuperation du total d'essai DITE entre les dates fournies, fera office de 100%
	$str="Select count(*) as tot from ligneproduit, anomalie  a, essai e where e.idService_SERVICE='$labo' and a.quiss_mpti='QUISS' and (nomLigne='DITE' or nomLigne = 'I2PT') and idLigne = autre and a.idEssai=e.idEssai and e.date_debut >= '$dateDeb' and e.date_debut <= '$dateFin' order by e.date_debut;";
	$req=mysqli_query($bdd, $str);
	$lg = mysqli_fetch_object($req);
	$totDite = intval($lg->tot);

	//LPE
	//Récuperation du total d'essai LPE entre les dates fournies, fera office de 100%
	$totLpe = intval(selectTotalEssai($bdd, 'LPE', $labo, $dateDeb, $dateFin)->tot);

	//LPA
	//Récuperation du total d'essai LPA entre les dates fournies, fera office de 100%
	$totLpa = intval(selectTotalEssai($bdd, 'LPA', $labo, $dateDeb, $dateFin)->tot);

	//LPH
	//Récuperation du total d'essai LPH entre les dates fournies, fera office de 100%
	$totLph = intval(selectTotalEssai($bdd, 'LPH', $labo, $dateDeb, $dateFin)->tot);

	//I2PA
	//Récuperation du total d'essai I2PA entre les dates fournies, fera office de 100%
	$totI2pa = intval(selectTotalEssai($bdd, 'I2PA', $labo, $dateDeb, $dateFin)->tot);

	//Autre
	//Récuperation du total d'essai Autre entre les dates fournies, fera office de 100%
	$totAutre = intval(selectTotalEssai($bdd, 'Autre', $labo, $dateDeb, $dateFin)->tot);

	//Total
	//Récuperation du total d'essai entre les dates fournies, fera office de 100%
	$str="Select count(*) as tot from anomalie  a, essai e where e.idService_SERVICE='$labo' and a.quiss_mpti='QUISS' and a.idEssai=e.idEssai and e.date_debut >= '$dateDeb' and e.date_debut <= '$dateFin' order by e.date_debut;";
	$req=mysqli_query($bdd, $str);
	$lg = mysqli_fetch_object($req);
	$totTotal = intval($lg->tot);

	//HorsDITE
	//Récuperation du total d'essai Hors DITE entre les dates fournies, fera office de 100%
	$str="Select count(*) as tot from ligneproduit, anomalie  a, essai e where e.idService_SERVICE='$labo' and a.quiss_mpti='QUISS' and nomLigne!= 'DITE' and nomLigne != 'I2PT' and autre = idLigne and a.idEssai=e.idEssai and e.date_debut >= '$dateDeb' and e.date_debut <= '$dateFin' order by e.date_debut;";
	$req=mysqli_query($bdd, $str);
	$lg = mysqli_fetch_object($req);
	$totHorsDite = intval($lg->tot);
	
	//Récupération des essais en anomalie
	$str="Select e.idEssai, e.date_debut, nomLigne as autre from ligneproduit, anomalie  a, essai e where autre = idLigne and e.idService_SERVICE='$labo' and a.quiss_mpti='QUISS' and a.idEssai=e.idEssai and e.date_debut >= '$dateDeb' and e.date_debut <= '$dateFin' order by e.date_debut;";
	$reqEssai=mysqli_query($bdd, $str);

	/*
	* Les anomalies n'étant pas présentent chaques mois, les mois vide doivent être afficher avec une 
	* valeur nulle. Pour cela il faut connaître le mois courant et tester si le mois courant est bien à 1
	* du mois précédent
	*/

	$numMoisPrec = 0; //Initialisation du mois précédent
	$cptMoisDiff = 0; //Compteur
	$date = multiexplode(array("-", " "), $dateDeb); //Initialisation de la date 
	$datePrec = multiexplode(array("-", " "), $dateDeb); //Initialisation de la date précédente
	$numMois = $datePrec[1]; //Initialisation du numéro du mois
	//Conversion du mois en entier
	if (intval($numMois[0]) != 0) $numMois = intval($numMois); 
	else $numMois = intval($numMois[1]);
	$numAnnee = intval($datePrec[0]); //Récupération de l'année
	//POur chaques essais en anomalie
	while($lg=mysqli_fetch_object($reqEssai)){
		//Récupération du mois de l'essai
		$mois = multiexplode(array("-", " "), $lg->date_debut);
		$moisCours = $mois[1];
		$anneCours = intval($mois[0]); //Récupération de l'année
		//Conversion en entier
		if (intval($moisCours[0]) != 0) $moisCours = intval($moisCours);
		else $moisCours = intval($moisCours[1]);
		//Si le numéro de l'essai est différent du numéro du mois courant
		if ($moisCours != $numMoisPrec){
			
			if($numMoisPrec != 0) $cptMoisDiff ++;
			//Ecart strict en les deux mois
			$ecart = ecart_mois($mois, $datePrec);
			//Diférence entre les deux mois
			$ecart2 = diff_mois($mois, $date);
			//Si la différence est différente de 0 et que c'est le premier
			if ($ecart2 != 0 && $prems==true) {
				//année en cours
				$annee1 = intval($mois[0]);
				//année de l'essai
				$annee2 = intval($date[0]);
				$diff = $annee1 - $annee2;
				//Si la différence est supérieure à deux 
				if ($diff >= 2){
					//Correction de l'écart
					for ($i=0; $i < $diff; $i++) $ecart ++;

				}else $ecart ++;				
			}
			//Pour chaque mois sautés
			for ($i=0; $i<=$ecart;$i++){
				//Remplissage de 0
				if ($cptMoisDiff >0 )
				{					
					$dite[$cptMoisDiff]=$dite[$cptMoisDiff-1];
					$lph[$cptMoisDiff]=$lph[$cptMoisDiff-1];
					$lpe[$cptMoisDiff]=$lpe[$cptMoisDiff-1];
					$lpa[$cptMoisDiff]=$lpa[$cptMoisDiff-1];
					$i2pa[$cptMoisDiff]=$i2pa[$cptMoisDiff-1];
					$autre[$cptMoisDiff]=$autre[$cptMoisDiff-1];
					$total[$cptMoisDiff]=$total[$cptMoisDiff-1];
					$horsDite[$cptMoisDiff]=$horsDite[$cptMoisDiff-1];
				}else
				{					
					$dite[$cptMoisDiff]=0;
					$lph[$cptMoisDiff]=0;
					$lpe[$cptMoisDiff]=0;
					$lpa[$cptMoisDiff]=0;
					$i2pa[$cptMoisDiff]=0;
					$autre[$cptMoisDiff]=0;
					$total[$cptMoisDiff]=0;
					$horsDite[$cptMoisDiff]=0;
					
				}
				//Changement d'année
				if ($numMois > 12){
					
					$numMois -= 12;
					$numAnnee += 1;
				}
				//Mise à jour de la légende
				$legend[$cptMoisDiff] = $nomMois[$numMois]." ".$numAnnee;
				//Incrémentation
				$numMois++;
				$cptMoisDiff ++;

			}
			$cptMoisDiff --;
			//Modification du mois précédent
			$numMoisPrec = $moisCours;
			//Modification de la date précédente
			$datePrec = $mois;
			//Correction pour l emois en cours
			
		}
		//Changement du booléen
		$prems=false;
		//Remplissage des tableaux en fonction de la ligne de produit
		if ($lg->autre == "DITE" || $lg->autre == 'I2PT'){
				
			//Ajout de 1 divisé par le nombre total
			$dite[$cptMoisDiff]+= (1/$totDite)*100; //Ajout du ratio
			$total[$cptMoisDiff]+= (1/$totTotal)*100;
			$essai=true;
				
		}else if ($lg->autre == "LPH"){
			
			//Ajout de 1 divisé par le nombre total
			$lph[$cptMoisDiff]+= (1/$totLph)*100; //Ajout du ratio
			$total[$cptMoisDiff]+= (1/$totTotal)*100;
			$horsDite[$cptMoisDiff]+= (1/$totHorsDite)*100;
			$essai=true;
				
		}else if ($lg->autre == "LPE"){

			//Ajout de 1 divisé par le nombre total
			$lpe[$cptMoisDiff]+= (1/$totLpe)*100; //Ajout du ratio
			$total[$cptMoisDiff]+= (1/$totTotal)*100;
			$horsDite[$cptMoisDiff]+= (1/$totHorsDite)*100;
			$essai=true;
					
		}else if ($lg->autre == "LPA"){

			//Ajout de 1 divisé par le nombre total
			$lpa[$cptMoisDiff]+= (1/$totLpa)*100; //Ajout du ratio
			$total[$cptMoisDiff]+= (1/$totTotal)*100;
			$horsDite[$cptMoisDiff]+= (1/$totHorsDite)*100;
			$essai=true;
			
		}else if ($lg->autre == "I2PA"){

			//Ajout de 1 divisé par le nombre total
			$i2pa[$cptMoisDiff]+= (1/$totI2pa)*100;	//Ajout du ratio
			$total[$cptMoisDiff]+= (1/$totTotal)*100;
			$horsDite[$cptMoisDiff]+= (1/$totHorsDite)*100;
			$essai=true;
			
		}else if ($lg->autre == "Autre"){

			//Ajout de 1 divisé par le nombre total
			$autre[$cptMoisDiff]+= (1/$totAutre)*100; //Ajout du ratio
			$total[$cptMoisDiff]+= (1/$totTotal)*100;
			$horsDite[$cptMoisDiff]+= (1/$totHorsDite)*100;
			$essai=true;

		}
	}
	
	//Si le nombre d'élements dans le légende est inférieur à 1 -> pas d'essais
	if (count($legend) <= 1) $essai = false;
	//S'il y a des essais
	if ($essai != false){
		//On compléte jusqu'à la date demandée
		$cptMoisDiff ++;
		$mois_fin = multiexplode(array("-", " "), $dateFin); //Récupération du mois de fin
		$ecart = diff_mois($mois_fin, $mois); //Différence entre le mois de fin et le mois en cours
		if ($ecart > 0){
			
			for ($i=0; $i<$ecart;$i++){ //POur chaque écart
		
				$dite[$cptMoisDiff]=$dite[$cptMoisDiff-1]; //Ajout de même nombre
				$lph[$cptMoisDiff]=$lph[$cptMoisDiff-1];
				$lpe[$cptMoisDiff]=$lpe[$cptMoisDiff-1];
				$lpa[$cptMoisDiff]=$lpa[$cptMoisDiff-1];
				$i2pa[$cptMoisDiff]=$i2pa[$cptMoisDiff-1];
				$autre[$cptMoisDiff]=$autre[$cptMoisDiff-1];
				$horsDite[$cptMoisDiff]=$horsDite[$cptMoisDiff-1];
				$total[$cptMoisDiff]=$total[$cptMoisDiff-1];
				if ($numMois > 12){ //Changement d'année
					
					$numMois -= 12;
					$numAnnee += 1;
				}
				$legend[$cptMoisDiff] = $nomMois[$numMois]." ".$numAnnee; //CHangement de la légende
				$numMois++; //Incrémentation
				$cptMoisDiff ++;

			}
		}
		
	}else{

		//Ajout de la valeur 0 pour pouvoir tracer un trait en deux points
		$dite[$cptMoisDiff]=0;
		$lph[$cptMoisDiff]=0;
		$lpe[$cptMoisDiff]=0;
		$lpa[$cptMoisDiff]=0;
		$i2pa[$cptMoisDiff]=0;
		$autre[$cptMoisDiff]=0;
		$horsDite[$cptMoisDiff]=0;
		$total[$cptMoisDiff]=0;

		$legend[$cptMoisDiff] = "";
		$cptMoisDiff ++;

		$dite[$cptMoisDiff]=0;
		$lph[$cptMoisDiff]=0;
		$lpe[$cptMoisDiff]=0;
		$lpa[$cptMoisDiff]=0;
		$i2pa[$cptMoisDiff]=0;
		$autre[$cptMoisDiff]=0;
		$horsDite[$cptMoisDiff]=0;
		$total[$cptMoisDiff]=0;

		$legend[$cptMoisDiff] = "";
	}

	//Création du graphe
	$graph = new Graph(1000,800,'auto');
	$graph->SetScale("textlin",0,100);
	//creation du tableau
	$data = array();
	/*
	* Ajout de toutes les barres nécessaire au traçage des courbes
	*/
	if (count($legend) > 1){
		
		if (in_array("I2PT", $libele_ligne)) $graph = creerBarre ($graph, $dite, 'I2PT', "#0B6138", true, 5);

		if (in_array("LPA", $libele_ligne)) $graph = creerBarre ($graph, $lpa, 'LPA', "#86B404", false, 2);

		if (in_array("LPE", $libele_ligne)) $graph = creerBarre ($graph, $lpe, 'LPE', "#8258FA", false, 2);

		if (in_array("LPH", $libele_ligne)) $graph = creerBarre ($graph, $lph, 'LPH', "#DF3A01", false, 2);
	
		if (in_array("I2PA", $libele_ligne)) $graph = creerBarre ($graph, $i2pa, 'I2PA', "#A4A4A4", false, 2);
		
		if (in_array("Autre", $libele_ligne)) $graph = creerBarre ($graph, $autre, 'Autre', "#FF8000", false, 2);

		if (in_array("Total", $libele_ligne)) $graph = creerBarre ($graph, $total, 'Total', "#FF0000", true, 5);

		if (in_array("HorsDite", $libele_ligne)) $graph = creerBarre ($graph, $horsDite, 'Hors Dite ', "#0080FF", true, 5);
	}

	//Ajout du titre
	$titre = "Pourcentage cumulatif de test en anomalie";
	if ($essai == false) $titre = "AUCUNE DONNEE";
	$titre .= "\n\nÉdité le ".$date_act;
	afficheGraphe($graph, $legend, $titre)->Stroke();

}

?>