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

$idService=$_GET["idService"];
$date_deb = $_GET["dateDeb"]." 00:00:00";
$date_fin = $_GET["dateFin"]." 23:59:59";
$str = "SELECT distinct(`idEssai`), nomMoyen, `date_debut`,`date_fin` FROM etatessai et, `essai` e, moyen WHERE (date_fin <= '$date_fin' and  date_fin >= '$date_deb' or date_debut >= '$date_deb' and date_debut <= '$date_fin' ) and date_debut < date_fin and idMoyen_MOYEN = idMoyen and e.idService_SERVICE = $idService and et.idEssai_ESSAI = idEssai and idEtat_ETAT = 23 ORDER BY nomMoyen, date_debut";
$req = mysqli_query($bdd,  $str);
$res = array();

function nb_demijour_ouvre ($date_deb_ini, $date_fin_ini, $sim){
	
	$nb_demijour = 0;
	
	$dateDeb = explode(" ", $date_deb_ini);
	$jour_dateDeb = $dateDeb[0];
	$date_dateDeb = explode ("-", $jour_dateDeb);
	$horaire_dateDeb = $dateDeb[1];
	$heure_dateDeb = explode (":", $horaire_dateDeb);
	
	$dateFin = explode(" ", $date_fin_ini);
	$jour_dateFin = $dateFin[0];
	$date_dateFin = explode ("-", $jour_dateFin);
	$horaire_dateFin = $dateFin[1];
	$heure_dateFin = explode (":", $horaire_dateFin);
	
	$timestamp_deb = mktime (0,0,0,$date_dateDeb[1],$date_dateDeb[2],$date_dateDeb[0]);
	$timestamp_fin = mktime (0,0,0,$date_dateFin[1],$date_dateFin[2],$date_dateFin[0]);
	
	if ((date("w", $timestamp_fin) != 0) && (date("w", $timestamp_fin) != 6)){
			
		if (intval($heure_dateFin[0]) <= 13){

			$nb_demijour -= 1;
		
		}

	}
	
	if ((date("w", $timestamp_deb) != 0) && (date("w", $timestamp_deb) != 6)){
			
		if (intval($heure_dateDeb[0]) >= 13){
			
			$nb_demijour -= 1;
			
		}
	}

	while ($timestamp_deb <= $timestamp_fin)
	{

		if ((date("w", $timestamp_deb) != 0) && (date("w", $timestamp_deb) != 6)){
			
			$nb_demijour += 2;			
		}

		
		$date_deb = date("Y-m-d H:i", strtotime($date_dateDeb[0]."-".$date_dateDeb[1]."-".$date_dateDeb[2]." ".$heure_dateDeb[0].":".$heure_dateDeb[1]." +1 day"));
		$dateDeb = explode(" ", $date_deb);
		$jour_dateDeb = $dateDeb[0];
		$date_dateDeb = explode ("-", $jour_dateDeb);
		$horaire_dateDeb = $dateDeb[1];
		$heure_dateDeb = explode (":", $horaire_dateDeb);
		$timestamp_deb = mktime (0,0,0,$date_dateDeb[1],$date_dateDeb[2],$date_dateDeb[0]);
		
		
	}	
	
	if ($sim) $nb_demijour -= 1;
	return $nb_demijour;

}

$legende = array();
$nomMoyen = "";
$nbMoyen = 1;
$resultat = array();
array_push($resultat, array('Moyen','Taux d\'occupation'));
$prems = true;
$first = false;
$total = nb_demijour_ouvre ($date_deb,$date_fin,false);
$dateFin = explode(" ", $date_fin);
$jour_dateFin = $dateFin[0];
$date_dateFin = explode ("-", $jour_dateFin);
$horaire_dateFin = $dateFin[1];
$heure_dateFin = explode (":", $horaire_dateFin);
$timestamp_fin = mktime ($heure_dateFin[0],$heure_dateFin[1],0,$date_dateFin[1],$date_dateFin[2],$date_dateFin[0]);
$date_fin_set = $timestamp_fin;

$dateDeb = explode(" ", $date_deb);
$jour_dateDeb = $dateDeb[0];
$date_dateDeb = explode ("-", $jour_dateDeb);
$horaire_dateDeb = $dateDeb[1];
$heure_dateDeb = explode (":", $horaire_dateDeb);
$timestamp_fin = mktime ($heure_dateDeb[0],$heure_dateDeb[1],0,$date_dateDeb[1],$date_dateDeb[2],$date_dateDeb[0]);
$date_debut_set = $timestamp_fin;


while ($lg = mysqli_fetch_object($req)){
	
	if ($lg->nomMoyen != $nomMoyen){
		
		$res[$lg->nomMoyen] = array();
		$first = true;
		if (!$prems) {
			
			array_push($resultat, array($nomMoyen, (array_sum($res[$nomMoyen])/$total)*100));
			$nbMoyen += 1;
			//array_push($legende, $nomMoyen);
		}
			
		$prems = false;
		$nomMoyen = $lg->nomMoyen;
		$dateDeb = explode(" ", $date_deb);
		$jour_dateDeb = $dateDeb[0];
		$date_dateDeb = explode ("-", $jour_dateDeb);
		$horaire_dateDeb = $dateDeb[1];
		$heure_dateDeb = explode (":", $horaire_dateDeb);
		$date_prec = $date_dateDeb;
		$horaire_prec = $heure_dateDeb;
		$timestamp_fin_sansHeure = mktime (0,0,0,$date_dateDeb[1],$date_dateDeb[2],$date_dateDeb[0]);
		$timestamp_fin = mktime ($heure_dateDeb[0],$heure_dateDeb[1],0,$date_dateDeb[1],$date_dateDeb[2],$date_dateDeb[0]);
		//echo $nomMoyen."<br>";

	}else {
		
		$first = false;
		
	}
	
	$dateCours = explode(" ", $lg->date_debut);
	$jour_dateCours = $dateCours[0];
	$date_dateCours = explode ("-", $jour_dateCours);
	$horaire_dateCours = $dateCours[1];
	$heureDeb_dateCours = explode (":", $horaire_dateCours);
	$timestamp_cours_deb_sansHeure = mktime (0,0,0,$date_dateCours[1],$date_dateCours[2],$date_dateCours[0]);
	$timestamp_cours_deb = mktime ($heureDeb_dateCours[0],$heureDeb_dateCours[1],0,$date_dateCours[1],$date_dateCours[2],$date_dateCours[0]);
	
	$dateCours = explode(" ", $lg->date_fin);
	$jour_dateCours = $dateCours[0];
	$date_dateCours = explode ("-", $jour_dateCours);
	$horaire_dateCours = $dateCours[1];
	$heure_dateCours = explode (":", $horaire_dateCours);
	$timestamp_cours_fin_sans_heure = mktime (0,0,0,$date_dateCours[1],$date_dateCours[2],$date_dateCours[0]);
	$timestamp_cours_fin = mktime ($heure_dateCours[0],$heure_dateCours[1],0,$date_dateCours[1],$date_dateCours[2],$date_dateCours[0]);
	
	$debut = $lg->date_debut;
	$fin = $lg->date_fin;
	if ($date_debut_set > $timestamp_cours_deb){

		$debut = date("Y-m-d H:i", strtotime($date_dateDeb[0]."-".$date_dateDeb[1]."-".$date_dateDeb[2]." ".$heure_dateDeb[0].":".$heure_dateDeb[1]));
		$dateCours = explode(" ", $debut);
		$jour_dateCours = $dateCours[0];
		$date_dateCours = explode ("-", $jour_dateCours);
		$horaire_dateCours = $dateCours[1];
		$heureDeb_dateCours = explode (":", $horaire_dateCours);
		$timestamp_cours_deb_sansHeure = mktime (0,0,0,$date_dateCours[1],$date_dateCours[2],$date_dateCours[0]);
		$timestamp_cours_deb = mktime ($heureDeb_dateCours[0],$heureDeb_dateCours[1],0,$date_dateCours[1],$date_dateCours[2],$date_dateCours[0]);
		
	}
	
	//echo "Fin date : ".$date_fin_set."<br>";
	//echo "Fin du test : ".$timestamp_cours_deb."<br>";
	if ($date_fin_set < $timestamp_cours_fin){
		
		$fin= date("Y-m-d H:i", strtotime($date_dateFin[0]."-".$date_dateFin[1]."-".$date_dateFin[2]." ".$heure_dateFin[0].":".$heure_dateFin[1]));
		
	}
	
	if ($date_debut_set == $timestamp_cours_deb ){
		
		//echo nb_demijour_ouvre($debut, $fin, false)."<br>";
		array_push ($res[$lg->nomMoyen], nb_demijour_ouvre($debut, $fin, false));
		$date_prec = $date_dateCours;
		$horaire_prec = $heure_dateCours;
		$timestamp_fin_sansHeure = $timestamp_cours_fin_sans_heure;
		$timestamp_fin = $timestamp_cours_fin;
		
	}else if ($timestamp_fin_sansHeure == $timestamp_cours_deb_sansHeure && $horaire_prec[0] < 13 && $heureDeb_dateCours[0] < 13 && $first == false){
		
		//echo  nb_demijour_ouvre($debut, $fin, true)."<br>";
		array_push ($res[$lg->nomMoyen], nb_demijour_ouvre($debut, $fin, true));
		$date_prec = $date_dateCours;
		$horaire_prec = $heure_dateCours;
		$timestamp_fin_sansHeure = $timestamp_cours_fin_sans_heure;
		$timestamp_fin = $timestamp_cours_fin;
		
	}else if ($timestamp_fin_sansHeure == $timestamp_cours_deb_sansHeure && $horaire_prec[0] > 13 && $heureDeb_dateCours[0] > 13){
		
		//echo nb_demijour_ouvre($debut, $fin, true)."<br>";
		array_push ($res[$lg->nomMoyen], nb_demijour_ouvre($debut, $fin, true));
		$date_prec = $date_dateCours;
		$horaire_prec = $heure_dateCours;
		$timestamp_fin_sansHeure = $timestamp_cours_fin_sans_heure;
		$timestamp_fin = $timestamp_cours_fin;
		
	}else if ($timestamp_fin <= $timestamp_cours_deb ){
		
		//echo nb_demijour_ouvre($debut, $fin, false)."<br>";
		array_push ($res[$lg->nomMoyen], nb_demijour_ouvre($debut, $fin, false));
		$date_prec = $date_dateCours;
		$horaire_prec = $heure_dateCours;
		$timestamp_fin_sansHeure = $timestamp_cours_fin_sans_heure;
		$timestamp_fin = $timestamp_cours_fin;

	}else if ($timestamp_cours_deb <= $timestamp_fin && $timestamp_cours_fin > $timestamp_fin){
				
		if ($horaire_prec[0] == "13" && $horaire_prec[1] == "00"){
			
			//echo nb_demijour_ouvre(date("Y-m-d H:i", strtotime($date_prec[0]."-".$date_prec[1]."-".$date_prec[2]." ".$horaire_prec[0].":".$horaire_prec[1]." -1 hour")), $fin, true)."<br>";
			array_push ($res[$lg->nomMoyen], nb_demijour_ouvre(date("Y-m-d H:i", strtotime($date_prec[0]."-".$date_prec[1]."-".$date_prec[2]." ".$horaire_prec[0].":".$horaire_prec[1]." -1 hour")), $fin, true));
			
		}else {
			
			//echo nb_demijour_ouvre(date("Y-m-d H:i", strtotime($date_prec[0]."-".$date_prec[1]."-".$date_prec[2]." ".$horaire_prec[0].":".$horaire_prec[1])), $fin, true)."<br>";
			array_push ($res[$lg->nomMoyen], nb_demijour_ouvre(date("Y-m-d H:i", strtotime($date_prec[0]."-".$date_prec[1]."-".$date_prec[2]." ".$horaire_prec[0].":".$horaire_prec[1])), $fin, true));
			
		}
		
		$date_prec = $date_dateCours;
		$horaire_prec = $heure_dateCours;
		$timestamp_fin_sansHeure = $timestamp_cours_fin_sans_heure;
		$timestamp_fin = $timestamp_cours_fin;
	}

	
}

array_push($resultat, array($nomMoyen,(array_sum($res[$nomMoyen])/$total)*100));
$nbMoyen += 1;

$sheet->fromArray($resultat);

$labels = array(
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$B$1', null, 1),

);
$categories = array(
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$A$2:$A$'.$nbMoyen, null, 4),   
);
$values = array(
  new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$B$2:$B$'.$nbMoyen, null, 4),

);
$series = new PHPExcel_Chart_DataSeries(
  PHPExcel_Chart_DataSeries::TYPE_BARCHART,       // plotType
  PHPExcel_Chart_DataSeries::GROUPING_STANDARD,   // plotGrouping
  array(0),                                       // plotOrder
  $labels,                                        // plotLabel
  $categories,                                    // plotCategory
  $values                                         // plotValues
);  

$series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);
$plotarea = new PHPExcel_Chart_PlotArea(NULL, array($series));
$title = new PHPExcel_Chart_Title('Taux d\'occupation des moyens (%)');  
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
$chart->setTopLeftPosition('E10');
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