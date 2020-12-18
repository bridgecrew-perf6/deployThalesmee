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

$val_target = $_GET["target"];
$mois_lettre = array("","Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");
$annee_en_cours = explode("-", $_GET["dateDeb"])[0];
$annee_actuel = date("Y");
$resultat = array();
array_push($resultat, array('Période', 'FPY global', 'FPY I2PT', 'target'));

if ($annee_en_cours < $annee_actuel){
	
	$nb_col = 14;
}else {
	
	$nb_col = date("n") + 2;
}
$mois =0;
for ($i=0; $i<$nb_col; $i++){
	
	if ($i == 0){
		
		$annee_prec = $annee_en_cours - 1 ;
		$dateDeb = $annee_prec."-01-01 00:00:00";
		$dateFin = $annee_prec."-12-31 23:59:59";
		$legende =  $annee_prec;
		
	}else if ($i == 1){
		
		$dateDeb = $annee_en_cours."-01-01 00:00:00";
		$dateFin = $annee_en_cours."-12-31 23:59:59";
		$legende =  $annee_en_cours;
		
	}else{
		
		$mois += 1;
		$nb_jour = cal_days_in_month (CAL_GREGORIAN, $mois, $annee_en_cours);
		if ($mois < 10){
			
			$dateDeb = $annee_en_cours."-0".$mois."-01";
			$dateFin = $annee_en_cours."-0".$mois."-".$nb_jour;
			
		}else {
			
			$dateDeb = $annee_en_cours."-".$mois."-01";
			$dateFin = $annee_en_cours."-".$mois."-".$nb_jour;
			
		}
		$legende = $mois_lettre[$mois];
		
	}
	
	$condDate="and et.dateEtat >='$dateDeb' and et.dateEtat <='$dateFin'";
	//on recupere le nombre dtotal d'essai sur la période en question
	$str="select distinct(idEssai) from essai e, etatessai et, ligneproduit
	where et.idEssai_essai=e.idEssai";
	if (isset ($_GET['Tous'])){
		
		$str .= " and ((idLigne = ligneProd
		and nomLigne in ($ligne)) or ligneProd is NULL)";
	}else{
		
		$str .= " and idLigne = ligneProd
		and nomLigne in ($ligne)";
	}
	
	$str .= "and e.idService_service=$idService
	and idEtat_etat = 25 
	and EXISTS 
		(select idEtat_etat from etatessai et
		where (idEtat_etat=25)
		and idEssai_essai=e.idessai
		)
	$condDate
	order by e.idessai, et.idEtat_etat;";
	
	$req = mysqli_query($bdd, $str);
	$total = mysqli_num_rows($req);		
	
	//on recupere le nombre d'anomalie total
	$str="select distinct(a.idEssai) from essai e, etatessai et, ligneproduit, anomalie a
	where et.idEssai_essai=e.idEssai and a.idEssai = e.idEssai";
	if (isset ($_GET['Tous'])){
		
		$str .= " and ((idLigne = ligneProd
		and nomLigne in ($ligne)) or ligneProd is NULL)";
	}else{
		
		$str .= " and idLigne = ligneProd
		and nomLigne in ($ligne)";
	}
	
	$str .= "and e.idService_service=$idService
	and idEtat_etat = 25 
	and EXISTS 
		(select idEtat_etat from etatessai et
		where (idEtat_etat=25)
		and idEssai_essai=e.idessai
		)
	$condDate
	order by e.idessai, et.idEtat_etat;";
	
	$req = mysqli_query($bdd, $str);
	$total_anomalie = mysqli_num_rows($req);
	
	//on recupere le nombre d'anomalie i2pt
	$str="select distinct(a.idEssai) from essai e, etatessai et, ligneproduit, anomalie a
	where et.idEssai_essai=e.idEssai and a.idEssai = e.idEssai and autre=1";
	if (isset ($_GET['Tous'])){
		
		$str .= " and ((idLigne = ligneProd
		and nomLigne in ($ligne)) or ligneProd is NULL)";
	}else{
		
		$str .= " and idLigne = ligneProd
		and nomLigne in ($ligne)";
	}
	
	$str .= "and e.idService_service=$idService
	and idEtat_etat = 25 
	and EXISTS 
		(select idEtat_etat from etatessai et
		where (idEtat_etat=25)
		and idEssai_essai=e.idessai
		)
	$condDate
	order by e.idessai, et.idEtat_etat;";
	
	$req = mysqli_query($bdd, $str);
	$anomalie_i2pt = mysqli_num_rows($req);
	
	if ($total == 0){
		
		$fpy_global = '';
		$fpy_i2pt  = '';
	}else {
		
		$fpy_global = round(($total - $total_anomalie) / $total * 100, 0);
		$fpy_i2pt = round(($total - $anomalie_i2pt) / $total * 100, 0);
	}
	

	
	if (isset($_GET["global"]) && isset($_GET["i2pt"])){
		
		array_push($resultat, array($legende,$fpy_global, $fpy_i2pt,$val_target));
		
	}else if (isset($_GET["global"])){
		
		array_push($resultat, array($legende,$fpy_global,$val_target));
		
	}else if (isset($_GET["i2pt"])){
		
		array_push($resultat, array($legende,$fpy_i2pt,$val_target));
		
	}

}

$nb_col -= 1;
while ($nb_col <= 12 ){
	
	$legende = $mois_lettre[$nb_col];
	if (isset($_GET["global"]) && isset($_GET["i2pt"])){
		
		array_push($resultat, array($legende,'', '',$val_target));
		
	}else{
		
		array_push($resultat, array($legende,'',$val_target));
		
	}
	$nb_col += 1;
	
}

$nb_col += 2;
$sheet->fromArray($resultat);


$categories = array(
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$A$2:$A$'.$nb_col, null, 4),   
);

if (isset($_GET["moyenne"]) && isset($_GET["median"])){
		
	$labels = array(
	  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$B$1', null, 1),
	  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$C$1', null, 1),
	);
	$labels2 = array(
	  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$D$1', null, 1),
	);
	$values = array(
	  new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$B$2:$B$'.$nb_col, null, 4),
	  new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$C$2:$C$'.$nb_col, null, 4),
	);
	$values2 = array(
	  new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$D$2:$D$'.$nb_col, null, 4),
	);
	
	$series = new PHPExcel_Chart_DataSeries(
	  PHPExcel_Chart_DataSeries::TYPE_BARCHART,       // plotType
	  PHPExcel_Chart_DataSeries::GROUPING_STANDARD,   // plotGrouping
	  array(0,1),                                     // plotOrder
	  $labels,                                        // plotLabel
	  $categories,                                    // plotCategory
	  $values                                         // plotValues
	); 

}else {
	
	$labels = array(
	  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$B$1', null, 1),
	);
	$labels2 = array(
	  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$C$1', null, 1),
	);
	$values = array(
	  new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$B$2:$B$'.$nb_col, null, 4),
	);
	$values2 = array(
	  new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$C$2:$C$'.$nb_col, null, 4),
	);
	
	$series = new PHPExcel_Chart_DataSeries(
	  PHPExcel_Chart_DataSeries::TYPE_BARCHART,       // plotType
	  PHPExcel_Chart_DataSeries::GROUPING_STANDARD,   // plotGrouping
	  array(0),                                     // plotOrder
	  $labels,                                        // plotLabel
	  $categories,                                    // plotCategory
	  $values                                         // plotValues
	); 
	
}



$series2 = new PHPExcel_Chart_DataSeries(
  PHPExcel_Chart_DataSeries::TYPE_LINECHART,      // plotType
  PHPExcel_Chart_DataSeries::GROUPING_STANDARD,   // plotGrouping
  array(0),                                       // plotOrder
  $labels2,                                       // plotLabel
  NULL,                                    		  // plotCategory
  $values2                                        // plotValues
); 

$series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);
$plotarea = new PHPExcel_Chart_PlotArea(NULL, array($series, $series2));
$title = new PHPExcel_Chart_Title('FPY (Jours)');  
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
$chart->setTopLeftPosition('F10');
$chart->setBottomRightPosition('P25');
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