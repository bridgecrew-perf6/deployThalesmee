<?php
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/Paris');

date_default_timezone_set('Europe/Paris');
require('../../conf/connexionPDO_param.php');// connexion a la base
set_include_path(get_include_path() . PATH_SEPARATOR . '../../Classes/');
/** PHPExcel */
include 'PHPExcel.php';

$workbook = new PHPExcel();
$sheet = $workbook->getActiveSheet();

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
	
$condDate="";
$idService=$_GET["idService"];


if(isset($_GET["dateDeb"])) //si on passe des dates en parametres (ex pour le rex) on les prends en comptes
{
	$dateDeb=$_GET["dateDeb"];
	$dateFin=$_GET["dateFin"];
	$condDate="and et.dateEtat >=:dateDeb and et.dateEtat <=:dateFin";
	$condVar=array('idService' => $idService,'dateDeb' => $dateDeb,'dateFin' => $dateFin);
}
else
	$condVar=array('idService' => $idService);

//on recupere les données des essais en fifo
$str="select et.dateEtat, nomLigne
from essai e, etatessai et, ligneproduit
where et.idEssai_essai=e.idEssai";
if (isset ($_GET['Tous'])){
		
	$str .= " and ((idLigne = ligneProd
	and nomLigne in ($ligne)) or ligneProd is NULL)";
}else{
	
	$str .= " and idLigne = ligneProd
	and nomLigne in ($ligne)";
}

$str .= " and et.idEtat_etat=25
and e.fifo=1
and e.idService_service=:idService
$condDate
order by et.dateEtat;";
$res=$dbh->prepare($str);
$res->execute($condVar);
$numSemPrec=0;
$numSemAct=0;
$cptSemDiff=0;
$nbTest=0;
$tabSemaine=array(); //tableau contenant le nombre de test reunis par semaines
$tabNumSemLegende=array(); //tableau pour construire la legende
while($lg=$res->fetch(PDO::FETCH_OBJ))
{
	$numSemAct=date("W",(strtotime($lg->dateEtat)));
	if($numSemPrec!=$numSemAct)
	{
		if($numSemPrec !=0) //si ce n'est pas le premier passage -> On incremente le compteur (au premier passage on conserve le 0 pour la premiere case du tableau)
			$cptSemDiff++;
		$numSemPrec=$numSemAct;
		$tabSemaine[$cptSemDiff]=0;
		$tabNumSemLegende[$cptSemDiff]=date("y",(strtotime($lg->dateEtat))).$numSemAct;
	}
	$tabSemaine[$cptSemDiff]++;
	$nbTest++;
	
	
}		
$dbh->connection = null;

$res = array(array('', 'Nombres de test réalisés'));

for ($i = 0; $i<count($tabSemaine); $i ++){
	
	array_push($res, array($tabNumSemLegende[$i],$tabSemaine[$i]));
}

$size = count($tabSemaine) +1;

$sheet->fromArray($res);

$labels = array(
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$B$1', null, 1),

);
$categories = array(
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$A$2:$A$'.$size, null, 4),   
);
$values = array(
  new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$B$2:$B$'.$size, null, 4),

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
$title = new PHPExcel_Chart_Title('Retour d\'expérience FIFO');  
$legend   = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_BOTTOM, null, false);
$xTitle   = new PHPExcel_Chart_Title('Nombre de tests : '.$nbTest);
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
$chart->setTopLeftPosition('D3');
$chart->setBottomRightPosition('Y35');
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