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
$labo=$_GET["idService"];// service du labo
$libele_ligne = array();

if(isset($_GET["dateDeb"]) && isset($_GET["dateFin"]) && isset($_GET["num"]))
{
	/**
	* Partie permettant de récuperer les choix de l'utilisateur dans les lignes de produit
	*/
	if (isset ($_GET['I2PT'])) array_push($libele_ligne, "I2PT");
	
	if (isset ($_GET['I2PA'])) array_push($libele_ligne, "I2PA");

	if (isset ($_GET['LPA'])) array_push($libele_ligne, "LPA");
	
	if (isset ($_GET['LPH'])) array_push($libele_ligne, "LPH");
	
	if (isset ($_GET['Autre'])) array_push($libele_ligne, "Autre");
	
	if (isset ($_GET['LPE'])) array_push($libele_ligne, "LPE");
	
	$dateDeb=$_GET["dateDeb"];
	$dateFin=$_GET["dateFin"];

	//Date du jour (date actuelle)
	$date_act = strftime("%d/%m/%Y", mktime(0,0,0,date('m'), date('d'), date('Y')));

	$legende = array();
	//Numero du mois précedent
	$numMoisPrec = 0;
	//Compteur pour le nombre de mois
	$cptMoisDiff=0;
	//Tableau avec le nom des mois
	$nomMois = array("","Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");
	//Utilisé pour le calcul de la date du premier essai du graphe
	$date=multiexplode(array("-", " "), $_GET["dateDeb"]);
	//Utilisé pour le calcul du nombre de mois vide
	$datePrec = multiexplode(array("-", " "), $_GET["dateDeb"]);
	//Numero du mois en entier pour la légende
	$numMois = $datePrec[1];
	if (intval($numMois[0]) != 0){
			
		$numMois = intval($numMois);

	}else{
		$numMois = intval($numMois[1]);
	}
	//Numero de l'année en entier pour la légende
	$numAnnee = intval($datePrec[0]);
	//Déclarartion des tableaux de résultat
	$dite=array();
	$lph=array();
	$lpe=array();
	$lpa=array();
	$i2pa = array();
	$autre=array();
	//Tableau de la légende
	$legend = array();
	//Booleen permettant de savoir si le graphe commence par un essai ou une case vide
	$essai = false;
	//Booleen pour le premier tour
	$prems=true;
	//Mois courrant mis a jour à chaque nouvelle date
	$mois = multiexplode(array("-", " "), $dateDeb);
	$str="Select e.date_debut, nomLigne as autre from anomalie  a, ligneproduit, essai e where autre = idLigne and e.idService_SERVICE='$labo' and a.quiss_mpti='QUISS' and a.idEssai=e.idEssai and e.date_debut >= '$dateDeb' and e.date_fin <= '$dateFin' order by e.date_debut;";
	$reqEssai=mysqli_query($bdd, $str);
	while($lg=mysqli_fetch_object($reqEssai)){
		
		$mois = multiexplode(array("-", " "), $lg->date_debut);
		//Numero du mois en cour
		$moisCours = $mois[1];
		//Numero de l'année en cour
		$anneCours = intval($mois[0]);
		if (intval($moisCours[0]) != 0){
			
			$moisCours = intval($moisCours);

		}else{
			$moisCours = intval($moisCours[1]);
		}
		//Si on change de mois
		if ($moisCours != $numMoisPrec){
			//Calcul de l'écart avec le mois précedent
			$ecart = ecart_mois($mois, $datePrec);
			//Si ce n'est pas le premier tour : pour garder l'indice 0 dans le tableau
			if($numMoisPrec != 0){
				
				$cptMoisDiff ++;

			}
			//Calcul de la différence entre les deux mois
			$ecart2 = diff_mois($mois, $date);
			//Si la différence est différente de 0 et que c'est le premier tour
			if ($ecart2 != 0 && $prems==true) {
				//Recupération de l'année en cour
				$annee1 = intval($mois[0]);
				//Recupération de l'année du début
				$annee2 = intval($date[0]);
				//Calcul de la différence entre les années
				$diff = $annee1 - $annee2;
				//Si la différence est supérieur ou égale à 2
				if ($diff >= 2){
					//Mise à jour de l'écart
					for ($i=0; $i < $diff; $i++){
						$ecart ++;
					}
				//Sinon écart +1
				}else{
					$ecart ++;
				}

			}
			//Remplissage du tableau pour les mois qui ne dispose pas d'essis
			for ($i=0; $i<$ecart;$i++){
				
				//Mise à null par défault
				$dite[$cptMoisDiff]=null;
				$lph[$cptMoisDiff]=null;
				$lpe[$cptMoisDiff]=null;
				$lpa[$cptMoisDiff]=null;
				$autre[$cptMoisDiff]=null;
				$i2pa[$cptMoisDiff]=null;
				//Remise en janvier si le numéro du mois dépasse 12
				if ($numMois > 12){
					
					$numMois -= 12;
					$numAnnee += 1;
				}
				//Ajout de la légende
				$legend[$cptMoisDiff] = $nomMois[$numMois]." ".$numAnnee;
				//Incrémentation du nombre de mois
				$numMois++;
				//Incrémentation de l'indice
				$cptMoisDiff ++;

			}
			
			//Mise à jour du précedent
			$numMoisPrec = $moisCours;
			$datePrec = $mois;
			
			//Remise à zéro pour chaque domaine à chaque nouveau mois
			$dite[$cptMoisDiff]=null;
			$lph[$cptMoisDiff]=null;
			$lpe[$cptMoisDiff]=null;
			$lpa[$cptMoisDiff]=null;
			$autre[$cptMoisDiff]=null;
			$i2pa[$cptMoisDiff]=null;

			if ($numMois > 12){
				
				$numMois -= 12;
				$numAnnee += 1;
			}
			//Ajout de la légende
			$legend[$cptMoisDiff] = $nomMois[$numMois]." ".$numAnnee;
			//Incrémentation du nombre de mois
			$numMois++;
		}
		$prems =false;
		//Ajout des infos dans les teableau correspondant
		if ($lg->autre == "DITE" || $lg->autre == "I2PT"){
				
			$dite[$cptMoisDiff]++;
			$essai = true;
				
		}else if ($lg->autre == "LPH"){
				
			$lph[$cptMoisDiff]++;
			$essai = true;
				
		}else if ($lg->autre == "LPE"){
				
			$lpe[$cptMoisDiff]++;
			$essai = true;
				
		}else if ($lg->autre == "LPA"){
				
			$lpa[$cptMoisDiff]++;
			$essai = true;
		
		}else if ($lg->autre == "I2PA"){
			
			$i2pa[$cptMoisDiff]++;
			$essai = true;

		
		}else if ($lg->autre == "Autre"){
			$autre[$cptMoisDiff]++;
			$essai = true;

		}
	}
	
	//Si l'intervale de date n'est pas vide 
	if ($essai != false){
		
		$cptMoisDiff ++;
		$mois_fin = multiexplode(array("-", " "), $dateFin);
		$ecart = diff_mois($mois_fin, $mois);
		//Calucul de la différence entre le mois du dernier essai et la fin de la date passé en paramètre
		if ($ecart > 0){
			
			//Remplissage des mois
			for ($i=0; $i<$ecart;$i++){
		
				$dite[$cptMoisDiff]=null;
				$lph[$cptMoisDiff]=null;
				$lpe[$cptMoisDiff]=null;
				$lpa[$cptMoisDiff]=null;
				$i2pa[$cptMoisDiff]=null;
				$autre[$cptMoisDiff]=null;
				if ($numMois > 12){
					
					$numMois -= 12;
					$numAnnee += 1;
				}
				$legend[$cptMoisDiff] = $nomMois[$numMois]." ".$numAnnee;
				$numMois++;
				$cptMoisDiff ++;

			}
		}
		
	//Sinon mise à 0 par défault
	}else{
		
		$mois_fin = multiexplode(array("-", " "), $dateFin);
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
	
	$data = array();
	//creation du tableau
	/*
	* Affichage des lignes de produit choisies par l'utilisateur
	*/
	if (in_array("I2PT", $libele_ligne)){
		
		$data1y=$dite;
		$b1plot = new BarPlot($data1y);
		array_push ($data, $b1plot);
		$b1plot->SetLegend('I2PT');
		$b1plot->SetColor("white");
		$b1plot->SetFillColor("#00CC33");
		$b1plot->value->SetFormat('%d');
	}
	if (in_array("LPA", $libele_ligne)){
		
		$data2y=$lpa;
		$b2plot = new BarPlot($data2y);
		array_push ($data, $b2plot);
		$b2plot->SetLegend('LPA');
		$b2plot->SetColor("white");
		$b2plot->SetFillColor("#FFCC66");
		$b2plot->value->SetFormat('%d');
		
	}
	if (in_array("LPE", $libele_ligne)){
		
		$data3y=$lpe;
		$b3plot = new BarPlot($data3y);
		array_push ($data, $b3plot);
		$b3plot->SetLegend('LPE');
		$b3plot->SetColor("white");
		$b3plot->SetFillColor("#DD0000");
		$b3plot->value->SetFormat('%d');
	}
	if (in_array("LPH", $libele_ligne)){
		
		$data4y=$lph;
		$b4plot = new BarPlot($data4y);
		array_push ($data, $b4plot);
		$b4plot->SetLegend('LPH');
		$b4plot->SetColor("white");
		$b4plot->SetFillColor("#6699FF");
		$b4plot->value->SetFormat('%d');
	}
	if (in_array("I2PA", $libele_ligne)){
		
		$data5y=$i2pa;
		$b5plot = new BarPlot($data5y);
		array_push ($data, $b5plot);
		$b5plot->SetLegend('I2PA');
		$b5plot->SetColor("white");
		$b5plot->SetFillColor("#8258FA");
		$b5plot->value->SetFormat('%d');
	}
	if (in_array("Autre", $libele_ligne)){
		
		$data6y=$autre;
		$b6plot = new BarPlot($data6y);
		array_push ($data, $b6plot);
		$b6plot->SetLegend('Autre');
		$b6plot->SetColor("white");
		$b6plot->SetFillColor("#A4A4A4");
		$b6plot->value->SetFormat('%d');
	}

	$graph = new Graph(1000,800,'auto');
	$graph->SetScale("textint");

	$theme_class=new UniversalTheme;
	$graph->SetTheme($theme_class);
	$graph->xaxis->SetFont(FF_ARIAL,FS_BOLD,8);
	$graph->yaxis->title->SetFont(FF_ARIAL,FS_BOLD,12);
	$graph->title->SetFont(FF_ARIAL,FS_BOLD, 16);

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
	
	//ajout de la legende		
	$graph->legend->Pos(0,0,'left','top');

	$graph->legend->SetFrameWeight(1);
	//Titre
	$titre="QUISS PAR SECTEUR D'ORIGINE";
	$graph->title->Set($titre."\n\nÉdité le ".$date_act);
	//Si vide
	if ($essai == false){
		
		$graph->title->Set("AUCUNE DONNEE\n\nÉdité le ".$date_act);
	}

	// Display the graph
	$graph->Stroke();
	
}
?>