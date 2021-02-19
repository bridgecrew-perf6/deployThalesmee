<?php
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
$idService=$_GET["idService"];
if($idService==1)
	$labo="EMC";
elseif($idService==2)
	$labo="VIB";
else
	$labo="VTH";

$str="select p.idProc, dp.delai, dateDemandeDP_redigerDP
from procedures p, etatproc et, demande_procedure dp
where et.idProc_procedures=p.idProc
and et.idEtat_etat=17
and p.idDP_demande_procedure=dp.idDP
and p.idService_service=$idService;";
$req=mysqli_query($bdd,$str);

$datePrecedente=0;
$tabNbJoursAtt=array();
$tabNbJoursRedac=array();
$tabNbJoursRelec=array();
$tabNbJoursSign=array();
$tabNbJoursDP=array();
$i=0;
while($lg=mysqli_fetch_object($req))
{
	$idProc=$lg->idProc;

	$tabNbJoursDP[$i]=floor((strtotime($lg->delai) - strtotime($lg->dateDemandeDP_redigerDP))/86400); //nombre de jours de la demande
	
	$str="select et.dateEtat, et.idEtat_etat
	from  procedures p, etatproc et
	where et.idProc_procedures=p.idProc
	and p.idProc=$idProc
	and et.idEtat_etat!=12
	and et.idEtat_etat!=11
	order by p.idProc, et.idEtat_etat;";
	$reqInfo=mysqli_query($bdd,$str);
	if(mysqli_num_rows($reqInfo)==5)
	{
		while($lgInfo=mysqli_fetch_object($reqInfo))
		{
			$idEtat=$lgInfo->idEtat_etat;
			$dateEtat=$lgInfo->dateEtat;
			if($idEtat!=13) //a l'état 13 on n'a pas de date précédente
			{
				if($idEtat==14)// date mise en redac - date mise en attente
					$tabNbJoursAtt[$i]=floor((strtotime($dateEtat) - strtotime($datePrecedente))/86400);
				elseif($idEtat==15)// date mise en relec - date mise en redac
					$tabNbJoursRedac[$i]=floor((strtotime($dateEtat) - strtotime($datePrecedente))/86400);
				elseif($idEtat==16)// date mise en signature - date mise en relec
					$tabNbJoursRelec[$i]=floor((strtotime($dateEtat) - strtotime($datePrecedente))/86400);
				elseif($idEtat==17)// date validation - date mise en signature
					$tabNbJoursSign[$i]=floor((strtotime($dateEtat) - strtotime($datePrecedente))/86400);
			}
			$datePrecedente=$dateEtat;	
		}
	}
	$i++;	
}
$moyAtt=0;
$moyRedac=0;
$moyRelec=0;
$moySign=0;
if(mysqli_num_rows($req)!=0) //si un seul résultat dans la premiere requete, les tableaux ont été remplies (evite division par 0)
{
	$moyAtt= array_sum($tabNbJoursAtt)/count($tabNbJoursAtt);
	$moyRedac= array_sum($tabNbJoursRedac)/count($tabNbJoursRedac);
	$moyRelec= array_sum($tabNbJoursRelec)/count($tabNbJoursRelec);
	$moySign= array_sum($tabNbJoursSign)/count($tabNbJoursSign);
	$moyDP= array_sum($tabNbJoursDP)/count($tabNbJoursDP);
}
mysqli_close($bdd);


$sheet->fromArray(  
	array(
		array('','Attente','Rédaction','Relecture ', 'Signature'),
		array('Procédures',$moyAtt,$moyRedac,$moyRelec,$moySign),
		array('Engagement VIB',20,0,0,0),
		array('Demande LP',$moyDP,0,0,0),

		)  
);

$labels = array(
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$B$1', null, 1),
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$C$1', null, 1), 
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$D$1', null, 1),
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$E$1', null, 1), 

);
$categories = array(
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$A$2:$A$4', null, 4),   
);
$values = array(
  new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$B$2:$B$4', null, 4),
  new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$C$2:$C$4', null, 4),  
  new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$D$2:$D$4', null, 4),  
  new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$E$2:$E$4', null, 4),  
);
$series = new PHPExcel_Chart_DataSeries(
  PHPExcel_Chart_DataSeries::TYPE_BARCHART,     // plotType
  PHPExcel_Chart_DataSeries::GROUPING_STACKED,  // plotGrouping
  array(0,1,2,3),                                     // plotOrder
  $labels,                                        // plotLabel
  $categories,                                    // plotCategory
  $values                                         // plotValues
);  

$series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);
$plotarea = new PHPExcel_Chart_PlotArea(NULL, array($series));
$title    = new PHPExcel_Chart_Title('Durée moyenne des étapes de rédaction des procédures VIB');  
$legend   = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_BOTTOM, null, false);
$xTitle   = new PHPExcel_Chart_Title('');
$yTitle   = new PHPExcel_Chart_Title('Nombre de jours');
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
$chart->setTopLeftPosition('B7');
$chart->setBottomRightPosition('F25');
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