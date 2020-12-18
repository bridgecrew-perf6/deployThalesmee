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
require('../../fonction.php'); // connexion a la base
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

//Permet de stocker une chaine de caractère contenant les lignes de produit
$ligne = "";

if (isset ($_GET['I2PT'])) $ligne .= "'I2PT',";

if (isset ($_GET['I2PA'])) $ligne .= "'I2PA',";

if (isset ($_GET['LPA'])) $ligne .= "'LPA',";

if (isset ($_GET['LPH'])) $ligne .= "'LPH',";
	
if (isset ($_GET['Autre'])) $ligne .= "'Autre',";

if (isset ($_GET['LPE'])) $ligne .= "'LPE',";

//On enlève le dernier caractère qui est une virgule
$ligne = substr($ligne,0,-1);

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
$tab = array();

array_push($entete,'Efficacité (tenue des cycles)');

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
	
	$condDate="and e.date_debut >='$dateDeb' and e.date_fin <='$dateFin'";
	//on recupere les données des essais (durée nattente avant test)
	$str="SELECT DISTINCT(idEssai), e.date_debut, e.date_fin, e.date_debut_prevu, e.date_fin_prevu, e.duree_planifie, e.duree_actuelle
	from essai e, etatessai et, ligneproduit
	where et.idEssai_essai=e.idEssai";
	
	if (isset ($_GET['Tous'])){
		
		$str .= " and ((idLigne = ligneProd
		and nomLigne in ($ligne)) or ligneProd is NULL)";
	}else{
		
		$str .= " and idLigne = ligneProd
		and nomLigne in ($ligne)";
	}
	
	$str .= " and e.idService_service=$idService
	and EXISTS 
		(SELECT idEtat_etat from etatessai et
		where (idEtat_etat=24)
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
	
	$planifie = 0;
	$actuelle = 0;	
	$duree_planifie = 0;
	$duree_actuelle = 0;	

	while($lg=$res->fetch(PDO::FETCH_OBJ))
	{
		$duree_planifie = $lg->duree_planifie;
		if ($duree_planifie == 0 ) $duree_planifie = dureePrimavera($lg->date_debut_prevu, $lg->date_fin_prevu);

		$duree_actuelle = $lg->duree_actuelle;
		if ($duree_actuelle == 0) $duree_actuelle = dureePrimavera($lg->date_debut, $lg->date_fin);

		$actuelle += $duree_actuelle;
		$planifie += $duree_planifie;

	}

	if ($actuelle == 0) array_push($resultat, array($legende ,0 ,$val_target));
	else {
		$retard = $planifie / $actuelle * 100;
		array_push($resultat, array($legende ,$retard ,$val_target));
	}
}

$dbh->connection = null;	
$nb_col += 1;

$sheet->fromArray($resultat);


$categories = array(
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$A$2:$A$'.$nb_col, null, 4),   
);


$labels = array(
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$B$1', null, 1),
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$C$1', null, 1),
);

$values = array(
  new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$B$2:$B$'.$nb_col, null, 4),
  new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$C$2:$C$'.$nb_col, null, 4),
);

$series = new PHPExcel_Chart_DataSeries(
  PHPExcel_Chart_DataSeries::TYPE_BARCHART,       // plotType
  PHPExcel_Chart_DataSeries::GROUPING_STANDARD,   // plotGrouping
  array(0,1),                                     // plotOrder
  $labels,                                        // plotLabel
  $categories,                                    // plotCategory
  $values                                         // plotValues
); 


$series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);
$plotarea = new PHPExcel_Chart_PlotArea(NULL, array($series));
$title = new PHPExcel_Chart_Title('Efficacité (tenu des cycles)');  
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