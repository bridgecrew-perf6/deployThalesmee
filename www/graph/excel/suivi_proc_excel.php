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

$str="select dp.delai, et.dateEtat, p.idService_service, et.idEtat_etat
from demande_procedure dp, procedures p, etatproc et
where p.idDP_demande_procedure=dp.idDP
and et.idProc_procedures=p.idProc
and (et.idEtat_etat=17 or et.idEtat_etat=16 or et.idEtat_etat=15)
and EXISTS 
	(select idEtat_etat from etatproc
	where idEtat_etat=17
	and idProc_procedures=p.idProc)";
$req=mysqli_query($bdd,$str);

$str="select distinct dp.delai, dp.dateDemandeDP_redigerDP, p.idService_service
from demande_procedure dp, procedures p, etatproc et
where p.idDP_demande_procedure=dp.idDP
and et.idProc_procedures=p.idProc";
$reqDP=mysqli_query($bdd,$str);

$nb1EMC=0;
$nb2EMC=0;
$nb3EMC=0;
$nb4EMC=0;
$nb1VIB=0;
$nb2VIB=0;
$nb3VIB=0;
$nb4VIB=0;
$nb1VTH=0;
$nb2VTH=0;
$nb3VTH=0;
$nb4VTH=0;

$nb1TotalEMC=0;
$nb2TotalEMC=0;
$nb3TotalEMC=0;
$nb4TotalEMC=0;
$nb1TotalVIB=0;
$nb2TotalVIB=0;
$nb3TotalVIB=0;
$nb4TotalVIB=0;
$nb1TotalVTH=0;
$nb2TotalVTH=0;
$nb3TotalVTH=0;
$nb4TotalVTH=0;

while($lg=mysqli_fetch_object($req))
{
	$dateBesoin=$lg->delai;
	$dateValidation=$lg->dateEtat;
	$service=$lg->idService_service;
	$idEtat=$lg->idEtat_etat;
	
	if($idEtat==17) //proc terminé
	{
		if($service==1)
			$nb1TotalEMC++;
		elseif($service==2)
			$nb1TotalVIB++;
		elseif($service==3)
			$nb1TotalVTH++;
		
		if(strtotime($dateValidation) <= strtotime($dateBesoin))
		{
			if($service==1)
				$nb1EMC++;
			elseif($service==2)
				$nb1VIB++;
			elseif($service==3)
				$nb1VTH++;
		}			
	}
	elseif($idEtat==16) //proc en signature
	{
		if($service==1)
			$nb2TotalEMC++;
		elseif($service==2)
			$nb2TotalVIB++;
		elseif($service==3)
			$nb2TotalVTH++;
		
		if(strtotime($dateValidation) <= strtotime($dateBesoin))
		{
			if($service==1)
				$nb2EMC++;
			elseif($service==2)
				$nb2VIB++;
			elseif($service==3)
				$nb2VTH++;		
		}
	}
	elseif($idEtat==15) //proc en relecture
	{
		if($service==1)
			$nb4TotalEMC++;
		elseif($service==2)
			$nb4TotalVIB++;
		elseif($service==3)
			$nb4TotalVTH++;
		
		if(strtotime($dateValidation) <= (strtotime($dateBesoin) -604800)) //604800 7 jours en seconde
		{
			if($service==1)
				$nb4EMC++;
			elseif($service==2)
				$nb4VIB++;
			elseif($service==3)
				$nb4VTH++;		
		}
	}
}
while($lg=mysqli_fetch_object($reqDP))
{
	$dateBesoin=$lg->delai;
	$dateDP=$lg->dateDemandeDP_redigerDP;
	$service=$lg->idService_service;
	
	if($service==1)
		$nb3TotalEMC++;
	elseif($service==2)
		$nb3TotalVIB++;
	elseif($service==3)
		$nb3TotalVTH++;
		
	if(floor((strtotime($dateBesoin) - strtotime($dateDP))/86400) < 20)
	{
		if($service==1)
			$nb3EMC++;
		elseif($service==2)
			$nb3VIB++;
		elseif($service==3)
			$nb3VTH++;
	}
}
mysqli_close($bdd);
//les test servent à éviter les divisions par 0
if($nb1TotalEMC!=0)
	$nb1EMC=(($nb1EMC*100)/$nb1TotalEMC);
if($nb2TotalEMC!=0)
	$nb2EMC=(($nb2EMC*100)/$nb2TotalEMC);
if($nb3TotalEMC!=0)
	$nb3EMC=(($nb3EMC*100)/$nb3TotalEMC);
if($nb4TotalEMC!=0)
	$nb4EMC=(($nb4EMC*100)/$nb4TotalEMC);
if($nb1TotalVIB!=0)
	$nb1VIB=(($nb1VIB*100)/$nb1TotalVIB);
if($nb2TotalVIB !=0)
	$nb2VIB=(($nb2VIB*100)/$nb2TotalVIB);
if($nb3TotalVIB !=0)	
	$nb3VIB=(($nb3VIB*100)/$nb3TotalVIB);
if($nb4TotalVIB !=0)	
	$nb4VIB=(($nb4VIB*100)/$nb4TotalVIB);
if($nb1TotalVTH)
	$nb1VTH=(($nb1VTH*100)/$nb1TotalVTH);	
if($nb2TotalVTH!=0)
	$nb2VTH=(($nb2VTH*100)/$nb2TotalVTH);
if($nb3TotalVTH!=0)
	$nb3VTH=(($nb3VTH*100)/$nb3TotalVTH);
if($nb4TotalVTH!=0)
	$nb4VTH=(($nb4VTH*100)/$nb4TotalVTH);

$sheet->fromArray(  
	array(
		array('','Date validation <= Date besoin','Date mise en relecture <= Date besoin - 1 semaine','Date mise en signature <= Date besoin ', 'Durée demandée < Duree engagement laboratoire'),
		array('EMC',$nb1EMC,$nb4EMC,$nb2EMC,$nb3EMC),
		array('VIB',$nb1VIB,$nb4VIB,$nb2VIB,$nb3VIB),
		array('VTH',$nb1VTH,$nb4VTH,$nb2VTH,$nb3VTH),

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
  PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,  // plotGrouping
  array(0,1,2,3),                                     // plotOrder
  $labels,                                        // plotLabel
  $categories,                                    // plotCategory
  $values                                         // plotValues
);  

$series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);
$plotarea = new PHPExcel_Chart_PlotArea(NULL, array($series));
$title    = new PHPExcel_Chart_Title('Livraison des procédures par laboratoire');  
$legend   = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_BOTTOM, null, false);
$xTitle   = new PHPExcel_Chart_Title('');
$yTitle   = new PHPExcel_Chart_Title('Nombre de procédures en %');
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
$chart->setBottomRightPosition('D25');
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
