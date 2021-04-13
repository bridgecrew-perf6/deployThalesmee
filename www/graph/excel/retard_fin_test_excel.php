<?php

/**
* Ce fichier contient la générattion du graphique pour l'export Excel
* Ce fichier crée un fichier Excel contanant un tableau et un graphique du temps d'attente 
* de l'équipement sur l'étagère avant le test. Ce fichier .xlsx sera proposé au téléchargement à l'utilisateur
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
*/

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/Paris');
date_default_timezone_set('Europe/Paris');
require('../../conf/connexionPDO_param.php'); // connexion a la base
require('../../conf/connexion_param.php'); // connexion a la base
set_include_path(get_include_path() . PATH_SEPARATOR . '../../Classes/');

include 'PHPExcel.php';

//Initialisation du classeur Excel
$workbook = new PHPExcel();
$sheet = $workbook->getActiveSheet();

//Récupération des lignes de produit passés en paramètre
$moyen = "";
	
//Récupération du service
$idService=$_GET["idService"];
if($idService==1) $labo="EMC";
elseif($idService==2) $labo="VIB";
else $labo="VTH";

$str = "SELECT idMoyen, nomMoyen FROM moyen WHERE idService_SERVICE = $idService";
$req = mysqli_query($bdd,  $str);
while ($lg = mysqli_fetch_object($req)){
	if (isset($_GET[$lg->nomMoyen])) $moyen .= "'".$lg->nomMoyen."',";
}

//On enlève le dernier caractère qui est une virgule
$moyen = substr($moyen,0,-1);

//Récupération de la target
$val_target = $_GET["target"];

$mois_lettre = array("","Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");
$annee_en_cours = date("Y");
$condVar=array('idService' => $idService);
$condDate="";	
$mois = 0;
$nb_col = date("n") + 2;

//Initialisation des en-tete du tableau Excel
$resultat = array();
$entete = array('');

if (isset($_GET["medianRetard"])){
	
	array_push($entete,'Temps d\'attente médian');
}

if (isset($_GET["moyenneRetard"])){
	
	array_push($entete,'Temps d\'attente moyen');
}

array_push($entete,'target');

array_push($resultat,$entete);

//Boucle permettant d'itérer sur le nombre de mois nécessaire
for ($i=0; $i<$nb_col; $i++){
	
	//Première colonne du graohique -> Annèe précedente
	if ($i == 0){
		
		$annee_prec = $annee_en_cours - 1 ;
		$dateDeb = $annee_prec."-01-01 00:00:00";
		$dateFin = $annee_prec."-12-31 23:59:59";
		$legende =  $annee_prec;
	
	//Deuxième colonne du graohique -> Annèe en cours
	}else if ($i == 1){
		
		$dateDeb = $annee_en_cours."-01-01 00:00:00";
		$dateFin = $annee_en_cours."-12-31 23:59:59";
		$legende = $annee_en_cours;
	
	//Tous les mois jusqu'au mois actuel
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
	$str="select distinct(idEssai),et.dateEtat, et.idEtat_etat, e.date_debut_prevu, e.date_fin_prevu 
		from essai e, etatessai et, ligneproduit, moyen
		where et.idEssai_essai=e.idEssai";
		
		if (isset ($_GET['Tous'])){
			
			$str .= " and ((idMoyen_MOYEN = idMoyen
			and nomMoyen in ($moyen)) or idMoyen_MOYEN is NULL)";
		}else{
			
			$str .= " and idMoyen_MOYEN = idMoyen
			and nomMoyen in ($moyen)";
		}
		
		$str .= " and (et.idEtat_etat=23 or idEtat_etat=24)
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
	
	//Boucle permettant de parcourir tous les essais dans la période choisie
	while($lg=$res->fetch(PDO::FETCH_OBJ))
	{
		$dateEtat=$lg->dateEtat;
		$idEtat=$lg->idEtat_etat;
		if($idEtat==24 and $correct == true and $idEs == $lg->idEssai)//dateEtat contient la date de fin d'attente
		{

			$duree_planifie = (strtotime($lg->date_fin_prevu) - strtotime($lg->date_debut_prevu));
			$duree_reel = strtotime($dateEtat) - strtotime($datePrecedente);
			$nbj=($duree_reel - $duree_planifie)/86400;
			if ($nbj < 0) $nbj = 0;
			array_push($median,$nbj);
			$nbTest++;
			$nbjMoy+=$nbj;
			$correct=false;
		}
		else if ($idEtat==23) {//dateEtat contient la date de debut d'attente
			$correct = true;
			$datePrecedente=$dateEtat;
			$idEs = $lg->idEssai;
		}
		$essai = true;
	}
	
	//Calcul de la moyenne
	if($nbTest!=0) $tmpAttMoy=round($nbjMoy/$nbTest,2);
	else $tmpAttMoy=0;
	
	//Calcul de la médiane
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
	
	if (isset($_GET["moyenneRetard"]) && isset($_GET["medianRetard"])){
		
		array_push($resultat, array($legende,$temps_median, $tmpAttMoy,$val_target));
		
	}else if (isset($_GET["medianRetard"])){
		
		array_push($resultat, array($legende,$temps_median,$val_target));
		
	}else if (isset($_GET["moyenneRetard"])){
		
		array_push($resultat, array($legende,$tmpAttMoy,$val_target));
		
	}

}

$dbh->connection = null;	
$nb_col += 1;

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
$title = new PHPExcel_Chart_Title('Retard fin de test (Jours)');  
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