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

//on recupere les données des procedures, date de demande et date de besoin

$idService=$_GET["idService"];
if($idService==1)
	$labo="EMC";
elseif($idService==2)
	$labo="VIB";
else
	$labo="VTH";
//on recupere les données des procedures, date de demande et date de besoin
$str="select dp.delai, et.dateEtat, et.idEtat_etat
from demande_procedure dp, procedures p, etatproc et
where p.idDP_demande_procedure=dp.idDP
and et.idProc_procedures=p.idProc
and (et.idEtat_etat=17 or idEtat_etat=16)
and idService_service=$idService
and EXISTS 
	(select idEtat_etat from etatproc
	where idEtat_etat=17
	and idProc_procedures=p.idProc)";
$req=mysqli_query($bdd,$str);
	
$nbValInf5=0;
$nbVal5_0=0;
$nbVal1_5=0;
$nbVal6_10=0;
$nbValSup10=0;

$nbSignInf5=0;
$nbSign5_0=0;
$nbSign1_5=0;
$nbSign6_10=0;
$nbSignSup10=0;

while($lg=mysqli_fetch_object($req))
{
	$dateBesoin=$lg->delai;
	$dateValidation=$lg->dateEtat;
	$idEtat=$lg->idEtat_etat;
	$dateLiv=floor((strtotime($dateValidation) - strtotime($dateBesoin))/86400);
	if($idEtat==17)
	{
		if($dateLiv<-5)
			$nbValInf5++;
		elseif($dateLiv<=0)
			$nbVal5_0++;
		elseif($dateLiv<=5)
			$nbVal1_5++;
		elseif($dateLiv<=10)
			$nbVal6_10++;
		else
			$nbValSup10++;
	}
	else
	{
		if($dateLiv<-5)
			$nbSignInf5++;
		elseif($dateLiv<=0)
			$nbSign5_0++;
		elseif($dateLiv<=5)
			$nbSign1_5++;
		elseif($dateLiv<=10)
			$nbSign6_10++;
		else
			$nbSignSup10++;
	}		
}
mysqli_close($bdd);

$sheet->fromArray(  
	array(
		array('','Procédure validée','Procédure mise en signature'),
		array('< -5 jours',$nbValInf5,$nbSignInf5),
		array('de -5 à 0 jours',$nbVal5_0,$nbSign5_0),
		array('de 1 à 5 jours',$nbVal1_5,$nbSign1_5),
		array('de 6 à 10 jours',$nbVal6_10,$nbSign6_10),
		array('> 10 jours',$nbValSup10,$nbSignSup10),

		)  
);

$labels = array(
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$B$1', null, 1),
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$C$1', null, 1), 
);
$categories = array(
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$A$2:$A$6', null, 4),   
);
$values = array(
  new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$B$2:$B$6', null, 4),
  new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$C$2:$C$6', null, 4),  

);
$series = new PHPExcel_Chart_DataSeries(
  PHPExcel_Chart_DataSeries::TYPE_BARCHART,     // plotType
  PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,  // plotGrouping
  array(0,1),                                     // plotOrder
  $labels,                                        // plotLabel
  $categories,                                    // plotCategory
  $values                                         // plotValues
);  

$series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);
$plotarea = new PHPExcel_Chart_PlotArea(NULL, array($series));
$title    = new PHPExcel_Chart_Title('Écart de livraison des procédures VIB par rapport à la date de besoin');  
$legend   = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_BOTTOM, null, false);
$xTitle   = new PHPExcel_Chart_Title('');
$yTitle   = new PHPExcel_Chart_Title('Nombre de procédures');
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
