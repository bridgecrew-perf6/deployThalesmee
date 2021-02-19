<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/Paris');
date_default_timezone_set('Europe/Paris');
require('../../conf/connexion_param.php');// connexion a la base
set_include_path(get_include_path() . PATH_SEPARATOR . '../../Classes/');

include 'PHPExcel.php';

//Initialisation du classeur Excel
$workbook = new PHPExcel();
$sheet = $workbook->getActiveSheet();

//Récupération des lignes de produit passés en paramètre
$ligne = "";
//Permet de gérer les lignes de produit passée(s) en paramètre
if (isset ($_GET['I2PT'])) $ligne .= "'I2PT',";

if (isset ($_GET['I2PA'])) $ligne .= "'I2PA',";

if (isset ($_GET['LPA'])) $ligne .= "'LPA',";

if (isset ($_GET['LPH'])) $ligne .= "'LPH',";
	
if (isset ($_GET['Autre'])) $ligne .= "'Autre',";

if (isset ($_GET['LPE'])) $ligne .= "'LPE',";

//On enlève le dernier caractère qui est une virgule
$ligne = substr($ligne,0,-1);

$condDate="";
$idService=$_GET["idService"];

if($idService==1) $labo="EMC";
elseif($idService==2) $labo="VIB";
else $labo="VTH";

$mois_lettre = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");
$annee_en_cours = explode("-", $_GET["dateDeb"])[0];
$mois = 0;
$annee_actuel = date("Y");
if ($annee_en_cours < $annee_actuel){
	
	$nb_col = 12;
}else {
	
	$nb_col = date("n") ;
}

$res = array();
array_push($res, array('Période', 'Ratio'));
$tab_annee_prec = array();
$tab_annee_prec_vu = array();
$tab_annee_en_cours = array();
$tab_annee_en_cours_vu = array();

$annee_prec = $annee_en_cours - 1;

$str = "SELECT * FROM plannification WHERE annee = $annee_prec";
$req = mysqli_query($bdd, $str);
$legende =  $annee_prec;
if (mysqli_num_rows($req) == 0){
	
	array_push($res, array($legende,''));
}else {
	
	$str_vu = "SELECT * FROM plannification_vu WHERE annee = $annee_prec";
	$req_vu = mysqli_query($bdd, $str_vu);
	
	if (mysqli_num_rows($req_vu) == 0){
	
		array_push($res, array($legende,''));
		
	}else {
		
		$lg = mysqli_fetch_object ($req);
		array_push ($tab_annee_prec, $lg->janvier);
		array_push ($tab_annee_prec, $lg->fevrier);
		array_push ($tab_annee_prec, $lg->mars);
		array_push ($tab_annee_prec, $lg->avril);
		array_push ($tab_annee_prec, $lg->mai);
		array_push ($tab_annee_prec, $lg->juin);
		array_push ($tab_annee_prec, $lg->juillet);
		array_push ($tab_annee_prec, $lg->aout);
		array_push ($tab_annee_prec, $lg->septembre);
		array_push ($tab_annee_prec, $lg->octobre);
		array_push ($tab_annee_prec, $lg->novembre);
		array_push ($tab_annee_prec, $lg->decembre);
		
		$lg_vu = mysqli_fetch_object ($req_vu);
		array_push ($tab_annee_prec_vu, $lg_vu->janvier);
		array_push ($tab_annee_prec_vu, $lg_vu->fevrier);
		array_push ($tab_annee_prec_vu, $lg_vu->mars);
		array_push ($tab_annee_prec_vu, $lg_vu->avril);
		array_push ($tab_annee_prec_vu, $lg_vu->mai);
		array_push ($tab_annee_prec_vu, $lg_vu->juin);
		array_push ($tab_annee_prec_vu, $lg_vu->juillet);
		array_push ($tab_annee_prec_vu, $lg_vu->aout);
		array_push ($tab_annee_prec_vu, $lg_vu->septembre);
		array_push ($tab_annee_prec_vu, $lg_vu->octobre);
		array_push ($tab_annee_prec_vu, $lg_vu->novembre);
		array_push ($tab_annee_prec_vu, $lg_vu->decembre);

		array_push($res, array($legende,(array_sum($tab_annee_prec_vu)/array_sum($tab_annee_prec))*100));
	
	}
	
}


$str = "SELECT * FROM plannification WHERE annee = $annee_en_cours";
$req = mysqli_query($bdd, $str);
$legende =  $annee_en_cours;
if (mysqli_num_rows($req) == 0){
	
	array_push($res, array($legende,''));
}else {
	
	$str_vu = "SELECT * FROM plannification_vu WHERE annee = $annee_en_cours";
	$req_vu = mysqli_query($bdd, $str_vu);
	
	if (mysqli_num_rows($req_vu) == 0){
	
		array_push($res, array($legende,''));
		
	}else {
		
		$lg = mysqli_fetch_object ($req);
		array_push ($tab_annee_en_cours, $lg->janvier);
		array_push ($tab_annee_en_cours, $lg->fevrier);
		array_push ($tab_annee_en_cours, $lg->mars);
		array_push ($tab_annee_en_cours, $lg->avril);
		array_push ($tab_annee_en_cours, $lg->mai);
		array_push ($tab_annee_en_cours, $lg->juin);
		array_push ($tab_annee_en_cours, $lg->juillet);
		array_push ($tab_annee_en_cours, $lg->aout);
		array_push ($tab_annee_en_cours, $lg->septembre);
		array_push ($tab_annee_en_cours, $lg->octobre);
		array_push ($tab_annee_en_cours, $lg->novembre);
		array_push ($tab_annee_en_cours, $lg->decembre);
		
		$lg_vu = mysqli_fetch_object ($req_vu);
		array_push ($tab_annee_en_cours_vu, $lg_vu->janvier);
		array_push ($tab_annee_en_cours_vu, $lg_vu->fevrier);
		array_push ($tab_annee_en_cours_vu, $lg_vu->mars);
		array_push ($tab_annee_en_cours_vu, $lg_vu->avril);
		array_push ($tab_annee_en_cours_vu, $lg_vu->mai);
		array_push ($tab_annee_en_cours_vu, $lg_vu->juin);
		array_push ($tab_annee_en_cours_vu, $lg_vu->juillet);
		array_push ($tab_annee_en_cours_vu, $lg_vu->aout);
		array_push ($tab_annee_en_cours_vu, $lg_vu->septembre);
		array_push ($tab_annee_en_cours_vu, $lg_vu->octobre);
		array_push ($tab_annee_en_cours_vu, $lg_vu->novembre);
		array_push ($tab_annee_en_cours_vu, $lg_vu->decembre);
		
		array_push($res, array($legende,(array_sum($tab_annee_en_cours_vu)/array_sum($tab_annee_en_cours))*100));
	
	}
	
}

for ($i=0; $i<$nb_col; $i++){
	
	$mois += 1;
	$legende =  $mois_lettre[$i];
	if (isset($tab_annee_en_cours[$i]) && isset ($tab_annee_en_cours_vu[$i])){
		
		array_push($res, array($legende,($tab_annee_en_cours_vu[$i]/$tab_annee_en_cours[$i])*100));

	}else {
		
		array_push($res, array($legende,''));
		
	}	
	
}

while ($nb_col < 12 ){
	
	$legende =  $mois_lettre[$nb_col];
	array_push($res, array($legende,''));
	$nb_col += 1;
	
}		

$nb_col += 3;

$sheet->fromArray($res);

$categories = array(
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$A$2:$A$'.$nb_col, null, 4),   
);

$labels = array(
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$B$1', null, 1),
);

$values = array(
  new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$B$2:$B$'.$nb_col, null, 4),

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
$title = new PHPExcel_Chart_Title('Fiabilité prévisions LdP (%)');  
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
$chart->setTopLeftPosition('G10');
$chart->setBottomRightPosition('Q25');
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