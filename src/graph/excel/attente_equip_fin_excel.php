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
$fifo=$_GET["fifo"];


if($idService==1)
	$labo="EMC";
elseif($idService==2)
	$labo="VIB";
else
	$labo="VTH";

if(isset($_GET["dateDeb"])) //si on passe des dates en parametres (ex pour le rex) on les prends en comptes
{
	$dateDeb=$_GET["dateDeb"];
	$dateFin=$_GET["dateFin"];
	$condDate="and et.dateEtat >='$dateDeb' and et.dateEtat <='$dateFin'";		

}


//fifo vaut 2 si l'utilisateur veut la somme de fifo et de non fifo
if ($fifo == 2){
	
	//on recupere les données des essais (durée attente aprés test)
	$str="select distinct(idEssai),et.dateEtat, et.idEtat_etat, nomLigne
	from essai e, etatessai et, ligneproduit
	where et.idEssai_essai=e.idEssai";
	
	if (isset ($_GET['Tous'])){
		
		$str .= " and ((idLigne = ligneProd
		and nomLigne in ($ligne)) or ligneProd is NULL)";
	}else{
		
		$str .= " and idLigne = ligneProd
		and nomLigne in ($ligne)";
	}
	
	$str .= " and (et.idEtat_etat=24 or idEtat_etat=25)
	and e.idService_service=$idService
	and EXISTS 
		(select idEtat_etat from etatessai et
		where idEtat_etat=25
		and idEssai_essai=e.idessai
		)
	$condDate
	order by e.idessai, et.idEtat_etat;";
	
	
}else {
	
	//on recupere les données des essais (durée attente aprés test)
	$str="select distinct(idEssai),et.dateEtat, et.idEtat_etat, nomLigne
	from essai e, etatessai et, ligneproduit
	where et.idEssai_essai=e.idEssai";
	if (isset ($_GET['Tous'])){
		
		$str .= " and ((idLigne = ligneProd
		and nomLigne in ($ligne)) or ligneProd is NULL)";
	}else{
		
		$str .= " and idLigne = ligneProd
		and nomLigne in ($ligne)";
	}
	
	$str .= " and (et.idEtat_etat=24 or idEtat_etat=25)
	and e.fifo=$fifo
	and e.idService_service=$idService
	and EXISTS 
		(select idEtat_etat from etatessai et
		where (idEtat_etat=25)
		and idEssai_essai=e.idessai
		)
	$condDate
	order by e.idessai, et.idEtat_etat;";
}


$res=$dbh->prepare($str);
$res->execute();
	
$j0=0;
$j1=0;
$j2_3=0;
$j4_5=0;
$jsup6=0;
$nbjMoy=0;
$nbTest=0;
$tab = array();
$correct = false;
while($lg=$res->fetch(PDO::FETCH_OBJ))
{
	$dateEtat=$lg->dateEtat;
	$idEtat=$lg->idEtat_etat;
	if($idEtat==25 and $correct == true and $idEs == $lg->idEssai)//dateEtat contient la date de fin d'attente
	{
		$nbj=floor((strtotime($dateEtat) - strtotime($datePrecedente))/86400);
		array_push($tab,$dateEtat);
		$nbjMoy+=$nbj;
		$nbTest++;
		if($nbj==0)
			$j0++;
		elseif($nbj==1)
			$j1++;
		elseif($nbj <4)
			$j2_3++;
		elseif($nbj<6)
			$j4_5++;
		else
			$jsup6++;
		$correct = false;
		
	}
	else if ($idEtat==24) {
		
		$idEs = $lg->idEssai;
		$correct = true;
		$datePrecedente=$dateEtat;//dateEtat contient la date de debut d'attente
	}
	
}
$dbh->connection = null;

if($nbTest!=0)
	$tmpAttMoy=round($nbjMoy/$nbTest,2);
else
	$tmpAttMoy=0;


//creation du tableau
$data1y=array($j0,$j1,$j2_3,$j4_5,$jsup6);
	

$sheet->fromArray(  
	array(
		array('','Test terminés'),
		array('0 jour',$j0),
		array('1 jour',$j1),
		array('2 à 3 jours',$j2_3),
		array('4 à 5 jours',$j4_5),
		array('> 5 jours',$jsup6),

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
if ($fifo == 2)	$title = new PHPExcel_Chart_Title('Temps d\'attente tout tests confondus après le test');  
else $title = new PHPExcel_Chart_Title('Temps d\'attente après le test (FIFO)'); 
$legend   = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_BOTTOM, null, false);
$xTitle   = new PHPExcel_Chart_Title('Temps d\'attente moyen : '.$tmpAttMoy.'j');
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