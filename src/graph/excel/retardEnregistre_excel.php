<?php
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/Paris');

date_default_timezone_set('Europe/Paris');
require('../../conf/connexion_param.php');// connexion a la base
set_include_path(get_include_path() . PATH_SEPARATOR . '../../Classes/');
/** PHPExcel */
include 'PHPExcel.php';

$workbook = new PHPExcel();
$sheet = $workbook->getActiveSheet();

function dateDiff($date1, $date2){
	$diff = array();                           // Initialisation du retour
	$tmp = $date2 - $date1;
	
	// Nombre de secondes entre les 2 dates
	$diff["sec"] = $tmp % 60;                    // Extraction du nombre de secondes
	
	$tmp = round(($tmp-$diff["sec"])/60);    // Nombre de minutes (partie entière)
	$diff["min"] = $tmp % 60;                    // Extraction du nombre de minutes
 
	$tmp = round(($tmp-$diff["min"])/60);    // Nombre d'heures (entières)
	$diff["hour"] = $tmp % 24;                   // Extraction du nombre d'heures
	 
	$tmp = round(($tmp-$diff["hour"])/24);   // Nombre de jours restants
	$diff["day"] = $tmp;
	return $diff;
}

$idService = $_GET['idService'];	
$dateDeb=$_GET["dateDeb"];
$dateFin=$_GET["dateFin"];
	
$ligne = "";
//Si l'utilisateur veut les essais planifiés
if (isset ($_GET['I2PT'])){
	
	$ligne .= "'I2PT',";

}
//Si l'utilisateur veut les essais reservés
if (isset ($_GET['I2PA'])){
	
	$ligne .= "'I2PA',";
}
//Si l'utilisateur veut les essais en attente
if (isset ($_GET['LPA'])){
	
	$ligne .= "'LPA',";
}

if (isset ($_GET['LPH'])){
	
	$ligne .= "'LPH',";
}

if (isset ($_GET['Autre'])){
	
	$ligne .= "'Autre',";
}

if (isset ($_GET['LPE'])){
	
	$ligne .= "'LPE',";
}

//On enlève le dernier caractère qui est une virgule
$ligne = substr($ligne,0,-1);

$str = "Select e.idEssai, e.date_debut_prevu, et.dateEtat, nomLigne from essai e, etatessai et , ligneproduit where idService_SERVICE='$idService'";
if (isset ($_GET['Tous'])){
		
	$str .= " and ((idLigne = ligneProd
	and nomLigne in ($ligne)) or ligneProd is NULL)";
}else{
	
	$str .= " and idLigne = ligneProd
	and nomLigne in ($ligne)";
} 
$str .= " and e.date_debut >'$dateDeb' and date_debut < '$dateFin' and e.idEssai = et.idEssai_ESSAI and idEtat_ETAT >= 22 and e.date_debut_prevu != 'NULL' and e.affaire != 'MAINT' group by e.idEssai ";
$req = mysqli_query($bdd, $str);
$avant = 0;
$Plusde2Heures =0;
$Plusde6h = 0;
$plusde1jours = 0;
$plusde3jours = 0;
$tab = array();
while ($lg=mysqli_fetch_object($req)){

	$dateDebPrevu = $lg->date_debut_prevu;
	$dateLivraison = $lg->dateEtat;
	array_push($tab,$dateDebPrevu);
	$diff = dateDiff (strtotime($dateDebPrevu), strtotime($dateLivraison));
		
	if ($diff["day"] > 3){
			
		$plusde3jours += 1;
	}else if ($diff["day"] >= 1 && $diff["day"] <= 3){
			
		$plusde1jours += 1;
		
	}else if ($diff["day"] == 0 && $diff["hour"] > 6 ){
			
		$Plusde6h += 1;
		
	}else if ($diff["day"] == 0 && $diff["hour"] >= 2 && $diff["min"] > 0 && $diff["hour"] <= 6 ){
			
		$Plusde2Heures += 1;
		
	}else 
		$avant += 1;

}

$data1y=array($avant,$Plusde2Heures,$Plusde6h,$plusde1jours,$plusde3jours);

$sheet->fromArray(  
	array(
		array('','Nombre d\'essais'),
		array('<2 heures',$avant),
		array('2h à 6h',$Plusde2Heures),
		array('6h à 1 jour',$Plusde6h),
		array('1 jour à 3 jours',$plusde1jours),
		array('>3 jours',$plusde3jours),

		)  
);

$labels = array(
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$B$1', null, 1),

);
$categories = array(
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$A$2:$A$6', null, 4),   
);
$values = array(
  new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$B$2:$B$6', null, 4),

);
$series = new PHPExcel_Chart_DataSeries(
  PHPExcel_Chart_DataSeries::TYPE_BARCHART,     // plotType
  PHPExcel_Chart_DataSeries::GROUPING_STANDARD,  // plotGrouping
  array(0),                                     // plotOrder
  $labels,                                        // plotLabel
  $categories,                                    // plotCategory
  $values                                         // plotValues
);  

$series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);
$plotarea = new PHPExcel_Chart_PlotArea(NULL, array($series));
$title = new PHPExcel_Chart_Title('Retard de livraison des équipements avant le test');  
$legend   = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_BOTTOM, null, false);
$xTitle   = new PHPExcel_Chart_Title('');
$yTitle   = new PHPExcel_Chart_Title('Nombre de tests');
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
$chart->setTopLeftPosition('B10');
$chart->setBottomRightPosition('M25');
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