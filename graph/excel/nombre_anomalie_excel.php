<?php
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/Paris');

date_default_timezone_set('Europe/Paris');
require('../../conf/connexion_param.php');// connexion a la base
require('../fonction.php');
set_include_path(get_include_path() . PATH_SEPARATOR . '../../Classes/');
/** PHPExcel */
include 'PHPExcel.php';
$libele_ligne = array();
$workbook = new PHPExcel();
$sheet = $workbook->getActiveSheet();


$labo=$_GET["idService"];// service du labo

//Si l'utilisateur veut les essais planifiés
if (isset ($_GET['I2PT'])){
	
	array_push($libele_ligne, "I2PT");
}
//Si l'utilisateur veut les essais reservés
if (isset ($_GET['I2PA'])){
	
	array_push($libele_ligne, "I2PA");
}
//Si l'utilisateur veut les essais en attente
if (isset ($_GET['LPA'])){
	
	array_push($libele_ligne, "LPA");
}

if (isset ($_GET['LPH'])){
	
	array_push($libele_ligne, "LPH");
}

if (isset ($_GET['Autre'])){
	
	array_push($libele_ligne, "Autre");
}

if (isset ($_GET['LPE'])){
	
	array_push($libele_ligne, "LPE");
}
$dateDeb=$_GET["dateDeb"];
$dateFin=$_GET["dateFin"];
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


$data=array('');
if (in_array("I2PT", $libele_ligne)){
			
	array_push($data, "I2PT");
}
if (in_array("LPA", $libele_ligne)){
	
	array_push($data, "LPA");
	
}
if (in_array("LPE", $libele_ligne)){
	
	array_push($data, "LPE");
}
if (in_array("LPH", $libele_ligne)){
	
	array_push($data, "LPH");
}
if (in_array("I2PA", $libele_ligne)){
	
	array_push($data, "I2PA");
}
if (in_array("Autre", $libele_ligne)){
	
	array_push($data, "Autre");
}


$col = count($data)-1;
$res = array($data);


for ($i = 0; $i<count($legend); $i ++){
	
	$tab = array();
	array_push($tab, $legend[$i]);
	if (in_array("I2PT", $libele_ligne)){
			
		array_push($tab, $dite[$i]);
	}
	if (in_array("LPA", $libele_ligne)){
		
		array_push($tab, $lpa[$i]);
		
	}
	if (in_array("LPE", $libele_ligne)){
		
		array_push($tab, $lpe[$i]);
	}
	if (in_array("LPH", $libele_ligne)){
		
		array_push($tab, $lph[$i]);
	}
	if (in_array("I2PA", $libele_ligne)){
		
		array_push($tab, $i2pa[$i]);
	}
	if (in_array("Autre", $libele_ligne)){
		
		array_push($tab, $autre[$i]);
	}

	array_push($res, $tab);
 
}

$size = count($legend) +1;
$sheet->fromArray($res);

$labels = array(
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$B$1', null, 1),
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$C$1', null, 1), 
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$D$1', null, 1),
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$E$1', null, 1),
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$F$1', null, 1), 
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$G$1', null, 1),

);
$categories = array(
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$A$2:$A$'.$size, null, 4),   
);
$values = array(
  new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$B$2:$B$'.$size, null, 4),
  new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$C$2:$C$'.$size, null, 4),  
  new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$D$2:$D$'.$size, null, 4),  
  new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$E$2:$E$'.$size, null, 4), 
  new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$F$2:$F$'.$size, null, 4), 
  new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$G$2:$G$'.$size, null, 4),   

);
$series = new PHPExcel_Chart_DataSeries(
  PHPExcel_Chart_DataSeries::TYPE_BARCHART,       // plotType
  PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,  // plotGrouping
  array(0,1,2,3,4,5),                             // plotOrder
  $labels,                                        // plotLabel
  $categories,                                    // plotCategory
  $values                                         // plotValues
);  

$series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);
$plotarea = new PHPExcel_Chart_PlotArea(NULL, array($series));
$title = new PHPExcel_Chart_Title('Nombre de test en anomalie par secteur d\'origine');  
$legend   = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_BOTTOM, null, false);
$xTitle   = new PHPExcel_Chart_Title('');
$yTitle   = new PHPExcel_Chart_Title('');
$chart    = new PHPExcel_Chart(
  'chart1',                                       // name
  $title,                                         // title
  $legend,                                        // legend 
  $plotarea,                                      // plotArea
  true,                                           // plotVisibleOnly
  0,                                              // displayBlanksAs
  $xTitle,                                        // xAxisLabel
  $yTitle                                         // yAxisLabel
);                      
$chart->setTopLeftPosition('K3');
$chart->setBottomRightPosition('T25');
$sheet->addChart($chart);

for($col = 'A'; $col !== 'K'; $col++) {
    $workbook->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);	
}    

header("Content-Type: application/force-download;charset=UTF-16LE");
header('Content-Transfer-Encoding: binary'); 
header("Content-disposition: attachment; filename=export.xlsx");
header("Pragma: no-cache");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
header("Expires: 0");
$writer = PHPExcel_IOFactory::createWriter($workbook, 'Excel2007');
$writer->setIncludeCharts(TRUE);
$writer->save('php://output');