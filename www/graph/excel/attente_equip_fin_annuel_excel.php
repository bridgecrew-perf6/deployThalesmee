<?php

/**
* Ce fichier contient la générattion du graphique pour l'export Excel
* Ce fichier crée un fichier Excel contanant un tableau et un graphique du temps d'attente 
* de l'équipement sur l'étagère après le test. Ce fichier .xlsx sera proposé au téléchargement à l'utilisateur
* @param GET
* Tous
* I2PT
* I2PA
* LPA
* LPH
* Autre
* LPE
* idService
* target
* Les commentaires sont disponible dans le fichier graph/excel/attente_equip_av_annuel_excel.php
*/

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
$annee_en_cours = date("Y");
$condVar=array('idService' => $idService);		
$mois = 0;
$nb_col = date("n") + 2;

$resultat = array();
array_push($resultat,array('', 'Temps d\'attente médian', 'Temps d\'attente moyen', 'target'));

for ($i=0; $i<$nb_col; $i++){
	
	if ($i == 0){
		
		$annee_prec = $annee_en_cours - 1 ;
		$dateDeb = $annee_prec."-01-01 00:00:00";
		$dateFin = $annee_prec."-12-31 23:59:59";
		$legende =  $annee_prec;
		
	}else if ($i == 1){
		
		$dateDeb = $annee_en_cours."-01-01 00:00:00";
		$dateFin = $annee_en_cours."-12-31 23:59:59";
		$legende = $annee_en_cours;
		
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
	//on recupere les données des essais (durée nattente avant test)
	$str="select distinct(idEssai), et.dateEtat, et.idEtat_etat, nomLigne
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
			where (idEtat_etat=25)
			and idEssai_essai=e.idessai
			)
		$condDate
		order by e.idessai, et.idEtat_etat;";

	$res=$dbh->prepare($str);
	$res->execute($condVar);
	
	$median = array();
	$essai = false;
	$nbjMoy=0;
	$nbTest=0;
	
	while($lg=$res->fetch(PDO::FETCH_OBJ))
	{
		$dateEtat=$lg->dateEtat;
		$idEtat=$lg->idEtat_etat;
		if($idEtat==25 and $correct == true and $idEs == $lg->idEssai)//dateEtat contient la date de fin d'attente
		{
			$nbj=(strtotime($dateEtat) - strtotime($datePrecedente))/86400;
			array_push($median,$nbj);
			$nbTest++;
			$nbjMoy+=$nbj;
			$correct = false;
			
		}else if ($idEtat==24) {//dateEtat contient la date de debut d'attente
			
			$idEs = $lg->idEssai;
			$correct = true;
			$datePrecedente=$dateEtat;
		}
		$essai = true;
		
	}
	
	if($nbTest!=0)
		$tmpAttMoy=round($nbjMoy/$nbTest,2);
	else
		$tmpAttMoy=0;
		
	sort($median);
	if ($essai != false){
		
		$middle = count($median)/2;
		if (is_numeric($middle)){
			
			$temps_median = round($median[$middle],2);
			
		}else {
			
			$temps_median = round(($median[ceil($middle)] + $median[floor($middle)]) / 2,2);
		}
		
	}else {
		
		$temps_median = 0;
	}
	array_push($resultat, array($legende,$temps_median,$tmpAttMoy,$val_target));

}

$dbh->connection = null;
$nb_col += 1;
$sheet->fromArray($resultat);

$labels = array(
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$B$1', null, 1),
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$C$1', null, 1),
);
$labels2 = array(
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$D$1', null, 1),
);

$categories = array(
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$A$2:$A$'.$nb_col, null, 4),   
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

$series2 = new PHPExcel_Chart_DataSeries(
  PHPExcel_Chart_DataSeries::TYPE_LINECHART,       // plotType
  PHPExcel_Chart_DataSeries::GROUPING_STANDARD,    // plotGrouping
  array(0),                                        // plotOrder
  $labels2,                                        // plotLabel
  NULL,                                    		   // plotCategory
  $values2                                         // plotValues
); 

$series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);
$plotarea = new PHPExcel_Chart_PlotArea(NULL, array($series, $series2));
$title = new PHPExcel_Chart_Title('Temps d\'attente après le test (Jours)');  
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