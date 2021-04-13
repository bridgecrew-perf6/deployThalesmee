<?php
require('../conf/connexion_param.php');// connexion a la base
require ('jpgraph/jpgraph.php');
require ('jpgraph/jpgraph_bar.php');
require ('jpgraph/jpgraph_line.php');
require('fonction.php');
/*
* Fonction permettant d'ajouter les essais duanr le mois dans un tableau
* @param
* bdd : la base de données
* anneeCours : l'année en cours dans le programme
* mois : le mois dont on souhaite récupérer les essais
* dateFin : la limite données par l'utilisateur
* cptMoisDiff : l'indice du tableau où ajouter les essais
* tab : le tableau
* @return
* tab : le tableau rempli
*/
function ajoutTableau ($bdd, $anneCours, $mois, $dateFin, $cptMoisDiff, $tab)
{
	//Récuperation de la date de début du mois concerné
	$debut = $anneCours."-".$mois[1]."-01 00:00:00";
	$moisSuiv = moisSuivant(intval($mois[1]));
	if ($moisSuiv == '01') $an = $anneCours+1;
	else $an = $anneCours;
	
	//Verification date de mois suivant n'est pas superieur à la limite donnée
	if (intval($moisSuiv[0]) != 0) $moisf = intval($moisSuiv);
	else $moisf = intval($moisSuiv[1]);
	
	//Récuperation de la limite donnée
	$mois_F = multiexplode(array("-", " "), $dateFin);
	$moisF =  $mois_F[1];
	$anneeF = $mois_F[0];
	
	if (intval($moisF[0]) != 0) $moisF = intval($moisF);
	else $moisF = intval($moisF[1]);
	
	//Si superieur utilisation de la valeur de fin donnée
	if ($anneeF == $anneCours && $moisf > $moisF) $fin = $an."-".$moisF."-".$mois_F[2]." 23:59:00";
	//Récuperation de la date de début du mois suivant
	else $fin = $an."-".$moisSuiv."-01 00:00:00";

	//Selection du nombre d'essais DITE durant le mois
	$str="Select count(*) as tot from essai  e where ligneProd = 1  and e.date_debut >= '$debut' and e.date_debut < '$fin' order by e.date_debut;";
	$req=mysqli_query($bdd, $str);
	$lg = mysqli_fetch_object($req);
	//Si différent de 0 on en ajoute un
	if ($lg->tot != 0) $tab[$cptMoisDiff]+= (1/(intval($lg->tot)))*100;
	//Si 0
	else $tab[$cptMoisDiff]+= 100;
	
	//Si supérieur à 100, remise à 100
	//Peut arriver si les informations sont mal renseignées
	if ($tab[$cptMoisDiff] >100) $tab[$cptMoisDiff] = 100;

	return $tab;
}

/*
* Fonction permettant de modfifier les valeurs des barPlot
* @param
* data : le tableau regroupant tous les barPlot
* bplot : le barplot
* legend : la légende à lier à la barre
* color : la couleur de la barre
* @return 
* data : le tableau avec le barPlot ajouté
*/
function setBarPLot($data, $bplot, $legend, $color)
{
	$bplot->SetLegend($legend);
	$bplot->SetColor("white");
	$bplot->SetFillColor($color);
	$bplot->value->SetFormat('%d');
	array_push ($data, $bplot);
	return $data;
}

/*
* Fonction qui converti un mois entier (gestion du zéro)
* @param
* numMois : mois en chaîne de caractère
* @return
* numMois : le mois en Int
*/
function convertMoisEnEntier ($numMois)
{
	if (intval($numMois[0]) != 0) $numMois = intval($numMois);
	else $numMois = intval($numMois[1]);
	return $numMois;
}

if(isset($_GET["dateDeb"]) && isset($_GET["dateFin"]) && isset($_GET["num"]))
{
	$labo=$_GET["idService"]; // service du labo
	$libele_ligne = array(); //Tableau contenant les lignes demandées

	//Ajout des contraintes de ligne de produit
	if (isset ($_GET['I2PT'])) array_push($libele_ligne, "I2PT");
	if (isset ($_GET['I2PA'])) array_push($libele_ligne, "I2PA");
	if (isset ($_GET['LPA'])) array_push($libele_ligne, "LPA");
	if (isset ($_GET['LPH'])) array_push($libele_ligne, "LPH");
	if (isset ($_GET['Autre'])) array_push($libele_ligne, "Autre");
	if (isset ($_GET['LPE'])) array_push($libele_ligne, "LPE");

	//Date du jour (date actuelle)
	$date_act = strftime("%d/%m/%Y", mktime(0,0,0,date('m'), date('d'), date('Y')));
	//Si le numéro du graphe est le bon
	if (intval($_GET["num"]) == 3)
	{		
		$dateDeb = $_GET["dateDeb"]; //Date de début demandé
		$dateFin = $_GET["dateFin"]; //Date de fin demandé
		$date = multiexplode(array("-", " "), $_GET["dateDeb"]);
		$datePrec = multiexplode(array("-", " "), $_GET["dateDeb"]);

		//Initialisation des variables
		$legende = array();
		$numMoisPrec = 0;
		$cptMoisDiff = 0;
		$tabMois = array();
		$essai = false;
		//Tableau pour trouver le mois en fonction d'un indice
		$nomMois = array("", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");
		//Numéro du mois
		$numMois = convertMoisEnEntier ($date[1]);
		//Numéro de l'année
		$numAnnee = intval($date[0]);
		//Initialisation des tableaux
		$dite=array();
		$lph=array();
		$lpe=array();
		$lpa=array();
		$i2pa=array();
		$autre=array();
		//Initilisation des booléens
		$essai=false;
		$prems=true;
		//Récupération des anomalies entre les dates demandées
		$str="Select e.idEssai, e.date_debut, nomLigne as autre from ligneproduit, anomalie  a, essai e where autre = idLigne and e.idService_SERVICE='$labo' and a.quiss_mpti='QUISS' and a.idEssai=e.idEssai and e.date_debut >= '$dateDeb' and e.date_debut <= '$dateFin' order by e.date_debut;";
		$reqEssai=mysqli_query($bdd, $str);
		while($lg=mysqli_fetch_object($reqEssai))
		{		
			//Mois de l'anomalie	
			$mois = multiexplode(array("-", " "), $lg->date_debut);
			$moisCours = convertMoisEnEntier ($mois[1])
			//Année de l'anomalie
			$anneCours = intval($mois[0]);
			//Si le mois de l'anomalie est différent du mois de la précédente anomalie
			if ($moisCours != $numMoisPrec)
			{	
				//Si le mois dela précédente anomalie est différent de zéro => on passe au mois suivant dans le tableau (Les indices du tableau représentent les mois)	
				if($numMoisPrec != 0) $cptMoisDiff ++;
				$ecart = ecart_mois($mois, $datePrec); //Calcul de l'écart stict entre les deux mois (doc : fonction.php)
				$ecart2 = diff_mois($mois, $date); //Calcul de la différence entre les deux mois (doc : fonction.php)
				//Si la différence entre les deux mois est supérieur à zéro et que c'est le permier tour
				if ($ecart2 != 0 && $prems==true) 
				{		
					$annee2 = intval($date[0]); //Calcul de l'année de la date de début demandée
					$diff = $anneCours - $annee2; //Calcul de la différence
					if ($diff >= 2) //Si la différence est supérieur à deux
					{						
						for ($i=0; $i < $diff; $i++) //Incrémentation de l'écart du montant de la différence
						{						
							$ecart ++; //Pour chaque nouvelle année faire +1 
						}
						
					}else $ecart ++; //Incrémentation de un  sinon		
				}
				for ($i=0; $i<$ecart;$i++) //Pour le temps d'écart entre les deux dates
				{
					//Null par défault
					$dite[$cptMoisDiff]=null; 
					$lph[$cptMoisDiff]=null;
					$lpe[$cptMoisDiff]=null;
					$lpa[$cptMoisDiff]=null;
					$i2pa[$cptMoisDiff]=null;
					$autre[$cptMoisDiff]=null;
					//Changement d'année si besoin
					if ($numMois > 12)
					{						
						$numMois -= 12;
						$numAnnee += 1;
					}
					$legend[$cptMoisDiff] = $nomMois[$numMois]." ".$numAnnee; //Ajout de la légende
					//Incrémentation des variables
					$numMois++;
					$cptMoisDiff ++;
				}
				
				$numMoisPrec = $moisCours; //Changement du moi précédent
				$datePrec = $mois; //Changement de la date précédente

				//Dernier tour
				$dite[$cptMoisDiff]=null;
				$lph[$cptMoisDiff]=null;
				$lpe[$cptMoisDiff]=null;
				$lpa[$cptMoisDiff]=null;
				$i2pa[$cptMoisDiff]=null;
				$autre[$cptMoisDiff]=null;
				if ($numMois > 12)
				{					
					$numMois -= 12;
					$numAnnee += 1;
				}
				$legend[$cptMoisDiff] = $nomMois[$numMois]." ".$numAnnee;
				$numMois++;
			}

			$prems=false; //Ce n'est plus le premier tour

			//Ajout des valeur dans chaque tableaux
			if ($lg->autre == "DITE" || $lg->autre == "I2PT")
			{					
				$dite = ajoutTableau ($bdd, $anneCours, $mois, $dateFin, $cptMoisDiff, $dite);
				$essai=true;
					
			}else if ($lg->autre == "LPH")
			{					
				$lph = ajoutTableau ($bdd, $anneCours, $mois, $dateFin, $cptMoisDiff, $lph);
				$essai=true;
					
			}else if ($lg->autre == "LPE")
			{				
				$lpe = ajoutTableau ($bdd, $anneCours, $mois, $dateFin, $cptMoisDiff, $lpe);
				$essai=true;
	
			}else if ($lg->autre == "LPA")
			{
				$lpa= ajoutTableau ($bdd, $anneCours, $mois, $dateFin, $cptMoisDiff, $lpa);
				$essai=true;
					
			}else if ($lg->autre == "I2PA")
			{				
				$i2pa = ajoutTableau ($bdd, $anneCours, $mois, $dateFin, $cptMoisDiff, $i2pa);
				$essai=true;	
				
			}else if ($lg->autre == "Autre")
			{				
				$autre = ajoutTableau ($bdd, $anneCours, $mois, $dateFin, $cptMoisDiff, $autre);
				$essai=true;
			}			
		}
		
		//S'il n'y a pas d'essais
		if ($essai != false)
		{			
			$cptMoisDiff ++;
			$mois_fin = multiexplode(array("-", " "), $dateFin);
			$ecart = diff_mois($mois_fin, $mois); //Calcul de l'écart entre le mois demandés et le mois de fin
			if ($ecart > 0) //S'il y a une écart
			{				
				for ($i=0; $i<$ecart;$i++) //Mise à null pour tout l'interval
				{
					$dite[$cptMoisDiff]=null;
					$lph[$cptMoisDiff]=null;
					$lpe[$cptMoisDiff]=null;
					$lpa[$cptMoisDiff]=null;
					$i2pa[$cptMoisDiff]=null;
					$autre[$cptMoisDiff]=null;
					if ($numMois > 12)
					{						
						$numMois -= 12;
						$numAnnee += 1;
					}
					$legend[$cptMoisDiff] = $nomMois[$numMois]." ".$numAnnee;
					$numMois++;
					$cptMoisDiff ++;
				}
			}
		}else //S'il y a des essais
		{			
			//Ajout d'un colonne de zéro
			$dite[$cptMoisDiff]=0;
			$lph[$cptMoisDiff]=0;
			$lpe[$cptMoisDiff]=0;
			$lpa[$cptMoisDiff]=0;
			$i2pa[$cptMoisDiff]=0;
			$autre[$cptMoisDiff]=0;
			if ($numMois > 12){
				
				$numMois -= 12;
				$numAnnee += 1;
			}
			$legend[$cptMoisDiff] = $nomMois[$numMois]." ".$numAnnee;
			$numMois++;
		}

		//creation du tableau du résultat
		$data = array();
		//Création des barPlots
		if (in_array("I2PT", $libele_ligne))
		{			
			$b1plot = new BarPlot($dite);
			$data = setBarPLot($data, $b1plot, 'I2PT', "#00CC33");
			
		}
		if (in_array("LPA", $libele_ligne))
		{			
			$b2plot = new BarPlot($lpa);
			$data = setBarPLot($data, $b1plot, 'LPA', "#FFCC66");
			
		}
		if (in_array("LPE", $libele_ligne))
		{			
			$b3plot = new BarPlot($lpe);
			$data = setBarPLot($data, $b1plot, 'LPE', "#DD0000");
		}
		if (in_array("LPH", $libele_ligne))
		{			
			$b4plot = new BarPlot($lph);
			$data = setBarPLot($data, $b1plot, 'LPH', "#6699FF");
		}
		if (in_array("I2PA", $libele_ligne))
		{			
			$b5plot = new BarPlot($i2pa);
			$data = setBarPLot($data, $b1plot, 'I2PA', "#8258FA");
		}
		if (in_array("Autre", $libele_ligne))
		{			
			$b6plot = new BarPlot($autre);
			$data = setBarPLot($data, $b1plot, 'Autre', "#A4A4A4");
		}

		$graph = new Graph(1000,800,'auto');
		$graph->SetScale("textint",0,100);

		$theme_class=new UniversalTheme;
		$graph->SetTheme($theme_class);
		$graph->xaxis->SetFont(FF_ARIAL,FS_BOLD,8);
		$graph->yaxis->title->SetFont(FF_ARIAL,FS_BOLD,12);
		$graph->title->SetFont(FF_ARIAL,FS_BOLD, 16);
		$titre = "Pourcentage de test en anomalie";
		$graph->title->Set($titre."\n\nÉdité le ".$date_act);
		if ($essai == false) $graph->title->Set("AUCUNE DONNEE"); //Si pas de données
		
		$graph->SetBox(false);
		$graph->ygrid->SetFill(false);
		$graph->xaxis->SetTickLabels($legend);
		$graph->xaxis->SetLabelAngle(90);
		$graph->yaxis->HideLine(false);
		$graph->yaxis->HideTicks(false,false);

		//Creation du groupe de barPlot
		$gbplot = new GroupBarPlot($data);
		//ajout au graph
		$graph->Add($gbplot);
		$graph->legend->SetFrameWeight(1);
		$graph->legend->Pos(0,0,'left','top');

		// Display the graph
		$graph->Stroke();
	}
}