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
	//Choix de la famille de l'équipement suivant les cases cochées par l'utilisateur
	if (isset ($_GET['I2PT'])) array_push($libele_ligne, "I2PT");

	if (isset ($_GET['I2PA'])) array_push($libele_ligne, "I2PA");

	if (isset ($_GET['LPA'])) array_push($libele_ligne, "LPA");

	if (isset ($_GET['LPH'])) array_push($libele_ligne, "LPH");

	if (isset ($_GET['Autre'])) array_push($libele_ligne, "Autre");

	if (isset ($_GET['LPE'])) array_push($libele_ligne, "LPE");

	if (isset ($_GET['Total'])) array_push($libele_ligne, "Total");

	if (isset ($_GET['HorsDite'])) array_push($libele_ligne, "HorsDite");

	if (intval($_GET["num"]) == 2){
		
		$dateDeb=$_GET["dateDeb"];
		$dateFin=$_GET["dateFin"];
		//Date du jour (date actuelle)
		$date_act = strftime("%d/%m/%Y", mktime(0,0,0,date('m'), date('d'), date('Y')));

		$legende = array();
		$numMoisPrec = 0;
		$cptMoisDiff=0;
		$essai = false;
		$nomMois = array("", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");
		$date=multiexplode(array("-", " "), $_GET["dateDeb"]);
		$datePrec = multiexplode(array("-", " "), $_GET["dateDeb"]);
		$numMois = $datePrec[1];
		if (intval($numMois[0]) != 0){
				
			$numMois = intval($numMois);

		}else{
			$numMois = intval($numMois[1]);
		} 
		$numAnnee = intval($datePrec[0]);
		$dite=array();
		$lph=array();
		$lpe=array();
		$lpa=array();
		$i2pa = array();
		$autre=array();
		$legend = array();
		$total = array();
		$horsDite = array();
		$prems=true;
		$mois = multiexplode(array("-", " "), $dateDeb);
		$str="Select e.date_debut, nomLigne as autre from ligneproduit, anomalie  a, essai e where autre = idLigne and  e.idService_SERVICE='$labo' and a.quiss_mpti='QUISS' and a.idEssai=e.idEssai and e.date_debut >= '$dateDeb' and e.date_fin <= '$dateFin' order by e.date_debut;";
		$reqEssai=mysqli_query($bdd, $str);
		while($lg=mysqli_fetch_object($reqEssai)){
			
			$mois = multiexplode(array("-", " "), $lg->date_debut);
			$moisCours = $mois[1];
			$anneCours = intval($mois[0]);
			if (intval($moisCours[0]) != 0){
				
				$moisCours = intval($moisCours);

			}else{
				$moisCours = intval($moisCours[1]);
			}
			if ($moisCours != $numMoisPrec){
				if($numMoisPrec != 0){
					
					$cptMoisDiff ++;
				}
				$ecart = ecart_mois($mois, $datePrec);
				$ecart2 = diff_mois($mois, $date);
				if ($ecart2 != 0 && $prems==true) {
					$annee1 = intval($mois[0]);
					$annee2 = intval($date[0]);
					$diff = $annee1 - $annee2;
					if ($diff >= 2){
						
						for ($i=0; $i < $diff; $i++){
						
							$ecart ++;
						}
						
					}else{
						$ecart ++;
					}
				}
				for ($i=0; $i<$ecart;$i++){
					
					//Si ce n'est pas le premier mois
					if ($cptMoisDiff >0 ){
						
						//Somme cumulative :  le mois suivant est la somme des mois précédents
						$dite[$cptMoisDiff]=$dite[$cptMoisDiff-1];
						$lph[$cptMoisDiff]=$lph[$cptMoisDiff-1];
						$lpe[$cptMoisDiff]=$lpe[$cptMoisDiff-1];
						$lpa[$cptMoisDiff]=$lpa[$cptMoisDiff-1];
						$i2pa[$cptMoisDiff]=$i2pa[$cptMoisDiff-1];
						$autre[$cptMoisDiff]=$autre[$cptMoisDiff-1];
						$total[$cptMoisDiff]=$total[$cptMoisDiff-1];
						$horsDite[$cptMoisDiff]=$horsDite[$cptMoisDiff-1];
					//Sinon 0 de base
					}else{
						
						$dite[$cptMoisDiff]=0;
						$lph[$cptMoisDiff]=0;
						$lpe[$cptMoisDiff]=0;
						$lpa[$cptMoisDiff]=0;
						$i2pa[$cptMoisDiff]=0;
						$autre[$cptMoisDiff]=0;
						$total[$cptMoisDiff]=0;
						$horsDite[$cptMoisDiff]=0;
						
					}
					if ($numMois > 12){
						
						$numMois -= 12;
						$numAnnee += 1;
					}
					$legend[$cptMoisDiff] = $nomMois[$numMois]." ".$numAnnee;
					$numMois++;
					$cptMoisDiff ++;

				}
				$numMoisPrec = $moisCours;
				$datePrec = $mois;
				if ($cptMoisDiff >0 ){
						
					$dite[$cptMoisDiff]=$dite[$cptMoisDiff-1];
					$lph[$cptMoisDiff]=$lph[$cptMoisDiff-1];
					$lpe[$cptMoisDiff]=$lpe[$cptMoisDiff-1];
					$lpa[$cptMoisDiff]=$lpa[$cptMoisDiff-1];
					$i2pa[$cptMoisDiff]=$i2pa[$cptMoisDiff-1];
					$autre[$cptMoisDiff]=$autre[$cptMoisDiff-1];
					$total[$cptMoisDiff]=$total[$cptMoisDiff-1];
					$horsDite[$cptMoisDiff]=$horsDite[$cptMoisDiff-1];
				}else{
					
					$dite[$cptMoisDiff]=0;
					$lph[$cptMoisDiff]=0;
					$lpe[$cptMoisDiff]=0;
					$lpa[$cptMoisDiff]=0;
					$i2pa[$cptMoisDiff]=0;
					$autre[$cptMoisDiff]=0;
					$total[$cptMoisDiff]=0;
					$horsDite[$cptMoisDiff]=0;
					
				}
				if ($numMois > 12){
					
					$numMois -= 12;
					$numAnnee += 1;
				}
				$legend[$cptMoisDiff] = $nomMois[$numMois]." ".$numAnnee;
				$numMois++;
			}
			$prems=false;
			if ($lg->autre == "DITE" || $lg->autre == "I2PT"){
					
				$dite[$cptMoisDiff] ++;
				$total[$cptMoisDiff]++;
				$essai=true;
					
			}else if ($lg->autre == "LPH"){
					
				$lph[$cptMoisDiff]++;
				$total[$cptMoisDiff]++;
				$horsDite[$cptMoisDiff]++;
				$essai=true;
					
			}else if ($lg->autre == "LPE"){
					
				$lpe[$cptMoisDiff]++;
				$total[$cptMoisDiff]++;
				$horsDite[$cptMoisDiff]++;
				$essai=true;
					
			}else if ($lg->autre == "LPA"){
					
				$lpa[$cptMoisDiff]++;
				$total[$cptMoisDiff]++;
				$horsDite[$cptMoisDiff]++;
				$essai=true;
			}else if ($lg->autre == "I2PA"){
				
				$i2pa[$cptMoisDiff]++;
				$total[$cptMoisDiff]++;
				$horsDite[$cptMoisDiff]++;
				$essai=true;
			
			}else if ($lg->autre == "Autre"){
				$autre[$cptMoisDiff]++;
				$total[$cptMoisDiff]++;
				$horsDite[$cptMoisDiff]++;
				$essai=true;

			}

		}
		
		if (count($legend) <= 1){
			
			$essai = false;
		}
		
		if ($essai != false){
			$cptMoisDiff ++;
			$mois_fin = multiexplode(array("-", " "), $dateFin);
			$ecart = diff_mois($mois_fin, $mois);
			if ($ecart > 0){
				for ($i=0; $i<$ecart;$i++){

					$dite[$cptMoisDiff]=$dite[$cptMoisDiff-1];
					$lph[$cptMoisDiff]=$lph[$cptMoisDiff-1];
					$lpe[$cptMoisDiff]=$lpe[$cptMoisDiff-1];
					$lpa[$cptMoisDiff]=$lpa[$cptMoisDiff-1];
					$i2pa[$cptMoisDiff]=$i2pa[$cptMoisDiff-1];
					$autre[$cptMoisDiff]=$autre[$cptMoisDiff-1];
					$horsDite[$cptMoisDiff]=$horsDite[$cptMoisDiff-1];
					$total[$cptMoisDiff]=$total[$cptMoisDiff-1];
					if ($numMois > 12){
						
						$numMois -= 12;
						$numAnnee += 1;
					}
					$legend[$cptMoisDiff] = $nomMois[$numMois]." ".$numAnnee;
					$numMois++;
					$cptMoisDiff ++;

				}
			}
		//Si le graphe est vide : obligation de mettre deux point pour tracer la courbe sinon -> Erreur JpGraph
		}else{
			
			$mois_fin = multiexplode(array("-", " "), $dateFin);
			$dite[$cptMoisDiff]=0;
			$lph[$cptMoisDiff]=0;
			$lpe[$cptMoisDiff]=0;
			$lpa[$cptMoisDiff]=0;
			$i2pa[$cptMoisDiff]=0;
			$autre[$cptMoisDiff]=0;
			$horsDite[$cptMoisDiff]=0;
			$total[$cptMoisDiff]=0;
			if ($numMois > 12){
				
				$numMois -= 12;
				$numAnnee += 1;
			}
			$legend[$cptMoisDiff] = "";
			$numMois++;
			$cptMoisDiff ++;
			$mois_fin = multiexplode(array("-", " "), $dateFin);
			$dite[$cptMoisDiff]=0;
			$lph[$cptMoisDiff]=0;
			$lpe[$cptMoisDiff]=0;
			$lpa[$cptMoisDiff]=0;
			$i2pa[$cptMoisDiff]=0;
			$autre[$cptMoisDiff]=0;
			$horsDite[$cptMoisDiff]=0;
			$total[$cptMoisDiff]=0;
			if ($numMois > 12){
				
				$numMois -= 12;
				$numAnnee += 1;
			}
			$legend[$cptMoisDiff] = "";
			$numMois++;
			
		}
		
		$graph = new Graph(1000,800,'auto');
		$graph->SetScale("textint");
		
		$data = array();
		//creation du tableau
		if (count($legend) > 1){
			
			if (in_array("I2PT", $libele_ligne)){
				
				$data1y=$dite;
				$b1plot = new LinePlot($data1y);
				$graph->Add($b1plot);
				$b1plot->SetLegend('I2PT');
				$b1plot->SetWeight(5);
				$b1plot->SetColor("#0B6138");
				$b1plot->SetStyle('dotted');
				$b1plot->value->SetFormat('%d');
			}
			if (in_array("LPA", $libele_ligne)){
				
				$data2y=$lpa;
				$b2plot = new LinePlot($data2y);
				$graph->Add($b2plot);
				$b2plot->SetLegend('LPA');
				$b2plot->SetWeight(2);
				$b2plot->SetColor("#86B404");
				$b2plot->value->SetFormat('%d');
				
			}
			if (in_array("LPE", $libele_ligne)){
				
				$data3y=$lpe;
				$b3plot = new LinePlot($data3y);
				$graph->Add($b3plot);
				$b3plot->SetLegend('LPE');
				$b3plot->SetWeight(2);
				$b3plot->SetColor("#8258FA");
				$b3plot->value->SetFormat('%d');
			}
			if (in_array("LPH", $libele_ligne)){
				
				$data4y=$lph;
				$b4plot = new LinePlot($data4y);
				$graph->Add($b4plot);
				$b4plot->SetLegend('LPH');
				$b4plot->SetWeight(2);
				$b4plot->SetColor("#DF3A01");
				$b4plot->value->SetFormat('%d');
			}
			if (in_array("I2PA", $libele_ligne)){
				
				$data8y=$i2pa;
				$b8plot = new LinePlot($data8y);
				$graph->Add($b8plot);
				$b8plot->SetLegend('I2PA');
				$b8plot->SetWeight(5);
				$b8plot->SetColor("#A4A4A4");
				$b8plot->value->SetFormat('%d');
			}
			if (in_array("Autre", $libele_ligne)){
				
				$data5y=$autre;
				$b5plot = new LinePlot($data5y);
				$graph->Add($b5plot);
				$b5plot->SetLegend('Autre');
				$b5plot->SetWeight(2);
				$b5plot->SetColor("#FF8000");
				$b5plot->value->SetFormat('%d');
			}
			if (in_array("Total", $libele_ligne)){
				
				$data6y=$total;
				$b6plot = new LinePlot($data6y);
				$graph->Add($b6plot);
				$b6plot->SetLegend('Total');
				$b6plot->SetWeight(5);
				$b6plot->SetColor("#FF0000");
				$b6plot->SetStyle('dotted');
				$b6plot->value->SetFormat('%d');
			}
			if (in_array("HorsDite", $libele_ligne)){
				
				$data7y=$horsDite;
				$b7plot = new LinePlot($data7y);
				$graph->Add($b7plot);
				$b7plot->SetLegend('Hors Dite');
				$b7plot->SetWeight(5);
				$b7plot->SetColor("#0080FF");
				$b7plot->SetStyle('dotted');
				$b7plot->value->SetFormat('%d');
			}
		}

		
		$theme_class=new UniversalTheme;
		$graph->SetTheme($theme_class);
		$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,9);
		$graph->yaxis->title->SetFont(FF_ARIAL,FS_BOLD,12);
		$graph->title->SetFont(FF_ARIAL,FS_BOLD, 16);
		$graph->title->Set("ECARTS CUMULATIF\n\nÉdité le ".$date_act);
		if ($essai == false){
			
			$graph->title->Set("AUCUNE DONNEE\n\nÉdité le ".$date_act);
		}
		
		$graph->img->SetAntiAliasing(false);
		
		$graph->SetBox(false);

		$graph->ygrid->SetFill(false);
		$graph->yscale->ticks->Set(1,0.5);
		$graph->xaxis->SetTickLabels($legend);
		$graph->xaxis->SetLabelAngle(90);

		$graph->yaxis->HideLine(false);
		$graph->yaxis->HideTicks(false,false);


		$graph->legend->SetFrameWeight(1);
		$graph->legend->Pos(0,0,'left','top');


		// Display the graph
		$graph->Stroke();
		
		
	}
}
?>