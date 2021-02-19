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

$workbook = new PHPExcel();
$sheet = $workbook->getActiveSheet();
$libele_ligne = array();

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

if (isset ($_GET['Total'])){
	
	array_push($libele_ligne, "Total");
}

if (isset ($_GET['HorsDite'])){
	
	array_push($libele_ligne, "HorsDite");
}
		
$dateDeb=$_GET["dateDeb"];
$dateFin=$_GET["dateFin"];
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
if (in_array("Total", $libele_ligne)){
	
	array_push($data, "Total");
}
if (in_array("HorsDite", $libele_ligne)){
	
	array_push($data, "Hors I2PT");
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
	if (in_array("Total", $libele_ligne)){
		
		array_push($tab, $total[$i]);
	}
	if (in_array("HorsDite", $libele_ligne)){
		
		array_push($tab, $horsDite[$i]);
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
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$H$1'.$size, null, 4),
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$I$1'.$size, null, 4),   

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
  new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$H$2:$H$'.$size, null, 4),
  new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$I$2:$I$'.$size, null, 4),   

);
$series = new PHPExcel_Chart_DataSeries(
  PHPExcel_Chart_DataSeries::TYPE_LINECHART,       // plotType
  PHPExcel_Chart_DataSeries::GROUPING_STANDARD,  // plotGrouping
  array(0,1,2,3,4,5,6,7),                             		// plotOrder
  $labels,                                        // plotLabel
  $categories,                                    // plotCategory
  $values                                         // plotValues
);  

$series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);
$plotarea = new PHPExcel_Chart_PlotArea(NULL, array($series));
$title = new PHPExcel_Chart_Title('Écart cumulatif de test en anomalie par secteur d\'origine');  
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
	
?>