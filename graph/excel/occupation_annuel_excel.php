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

//Fonction permettant de compter le nombre de demi-journées entre deux dates
function nb_demijour_ouvre ($date_deb_ini, $date_fin_ini, $sim){
	
	$nb_demijour = 0;
	//Explode des dates pour avoir le bon format
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
	
	//Calcul de la durée pour chaque date 
	$timestamp_deb = mktime (0,0,0,$date_dateDeb[1],$date_dateDeb[2],$date_dateDeb[0]);
	$timestamp_fin = mktime (0,0,0,$date_dateFin[1],$date_dateFin[2],$date_dateFin[0]);
	
	//Si le jour de la semaine n'est pas un week end
	if ((date("w", $timestamp_fin) != 0) && (date("w", $timestamp_fin) != 6)){
		
		//Si l'heure de fin est inférieur ou égale à 13 => une demi journée de moins
		if (intval($heure_dateFin[0]) <= 13){

			$nb_demijour -= 1;
		
		}

	}
	
	//Si le jour de la semaine n'est pas un week end
	if ((date("w", $timestamp_deb) != 0) && (date("w", $timestamp_deb) != 6)){
		
		//Si l'heure de dfébut est supérieur ou égale à 13 => une demi journée de moins
		if (intval($heure_dateDeb[0]) >= 13){
			
			$nb_demijour -= 1;
			
		}
	}
	
	//Tant que la date de début est inférieur à la date de fin
	while ($timestamp_deb <= $timestamp_fin)
	{
		//Si la jour n'est pas un week end => ajout de deux demi-journées
		if ((date("w", $timestamp_deb) != 0) && (date("w", $timestamp_deb) != 6)){
			
			$nb_demijour += 2;			
		}

		//Ajout de 1 jour et création des dates au bon format
		$date_deb = date("Y-m-d H:i", strtotime($date_dateDeb[0]."-".$date_dateDeb[1]."-".$date_dateDeb[2]." ".$heure_dateDeb[0].":".$heure_dateDeb[1]." +1 day"));
		$dateDeb = explode(" ", $date_deb);
		$jour_dateDeb = $dateDeb[0];
		$date_dateDeb = explode ("-", $jour_dateDeb);
		$horaire_dateDeb = $dateDeb[1];
		$heure_dateDeb = explode (":", $horaire_dateDeb);
		$timestamp_deb = mktime (0,0,0,$date_dateDeb[1],$date_dateDeb[2],$date_dateDeb[0]);
		
		
	}	
	
	//Si l'essai est réalisé en simultané => moins une demi journée
	if ($sim) $nb_demijour -= 1;

	return $nb_demijour;

}


$idService=$_GET["idService"];

$str ="SELECT nomMoyen FROM moyen WHERE idService_SERVICE = $idService";
$req = mysqli_query($bdd, $str);
$moyen = "";

while ($lg=mysqli_fetch_object($req)){
	
	$moy = str_replace(" ", "-", $lg->nomMoyen);
	if (isset ($_GET[$moy])){
	
		$moyen .= "'".$lg->nomMoyen."',";
	}
}

//On enlève le dernier caractère qui est une virgule
$moyen = substr($moyen,0,-1);

$val_target = $_GET["target"];
	
//Création d'un tableau des mois permettant de trouver la chaine de caractère grâce a un indice
$mois_lettre = array("","Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");

$annee_en_cours = explode("-", $_GET["dateDeb"])[0];	
$mois = 0;
$annee_actuel = date("Y");
$nb_col = 14;


$target=array();
$legende = array();
$correct = false;
$data = array();

for ($i=0; $i<$nb_col; $i++){
	
	$resultat = array();
	if ($i == 0){
		
		$annee_prec = $annee_en_cours - 1 ;
		$date_deb = $annee_prec."-01-01 00:00:00";
		$date_fin = $annee_prec."-12-31 23:59:59";
		array_push($legende, $annee_prec);
		
	}else if ($i == 1){
		
		$date_deb = $annee_en_cours."-01-01 00:00:00";
		$date_fin = $annee_en_cours."-12-31 23:59:59";
		array_push($legende, $annee_en_cours);
		
	}else{
		
		$mois += 1;
		$nb_jour = cal_days_in_month (CAL_GREGORIAN, $mois, $annee_en_cours);
		if ($mois < 10){
			
			$date_deb = $annee_en_cours."-0".$mois."-01 00:00:00";
			$date_fin = $annee_en_cours."-0".$mois."-".$nb_jour." 23:59:00";
			
		}else {
			
			$date_deb = $annee_en_cours."-".$mois."-01 00:00:00";
			$date_fin = $annee_en_cours."-".$mois."-".$nb_jour." 23:59:00";
			
		}
		array_push($legende, $mois_lettre[$mois]);
		
	}
	
	$str = "SELECT distinct(`idEssai`), nomMoyen, `date_debut`,`date_fin` FROM etatessai et, `essai` e, moyen WHERE (date_fin <= '$date_fin' and  date_fin >= '$date_deb' or date_debut >= '$date_deb' and date_debut <= '$date_fin' ) and nomMoyen in ($moyen) and date_debut < date_fin and idMoyen_MOYEN = idMoyen and e.idService_SERVICE = $idService and et.idEssai_ESSAI = idEssai and idEtat_ETAT = 23 ORDER BY nomMoyen, date_debut";
	$req = mysqli_query($bdd,  $str);

	$nomMoyen = "";
	$resultat = array();
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

	//echo nb_demijour_ouvre ("2019-08-08 11:08:00","2019-08-09 11:52:00",true)."<br>";
	//Boucle permettant de parcourir le résultat de la requête
	while ($lg = mysqli_fetch_object($req)){
		
		if ($lg->nomMoyen != $nomMoyen){
			
			$res[$lg->nomMoyen] = array();
			$first = true;
			if (!$prems) {
				
				$resultat[$nomMoyen] = array_sum($res[$nomMoyen])/$total*100;
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
		//Si la date de début de l'intervalle est supérieur à la date de début de l'essai
		if ($date_debut_set > $timestamp_cours_deb){
			//La date de début de l'essai devient la date de début de l'intervalle
			$debut = date("Y-m-d H:i", strtotime($date_dateDeb[0]."-".$date_dateDeb[1]."-".$date_dateDeb[2]." ".$heure_dateDeb[0].":".$heure_dateDeb[1]));
			$dateCours = explode(" ", $debut);
			$jour_dateCours = $dateCours[0];
			$date_dateCours = explode ("-", $jour_dateCours);
			$horaire_dateCours = $dateCours[1];
			$heureDeb_dateCours = explode (":", $horaire_dateCours);
			$timestamp_cours_deb_sansHeure = mktime (0,0,0,$date_dateCours[1],$date_dateCours[2],$date_dateCours[0]);
			$timestamp_cours_deb = mktime ($heureDeb_dateCours[0],$heureDeb_dateCours[1],0,$date_dateCours[1],$date_dateCours[2],$date_dateCours[0]);
			
		}
		
		//Si la date de fin de l'intervalle est inferieur à la date de fin de l'essai
		if ($date_fin_set < $timestamp_cours_fin){
			//La date de fin de l'essai devient la date de fin de l'intervalle
			$fin= date("Y-m-d H:i", strtotime($date_dateFin[0]."-".$date_dateFin[1]."-".$date_dateFin[2]." ".$heure_dateFin[0].":".$heure_dateFin[1]));
			
		}
		
		//Si la date de début de l'intervalle est égale à la date de début de l'essai
		if ($date_debut_set == $timestamp_cours_deb ){
			
			//Calcul entre les deux dates de l'essai avec simultané à false
			array_push ($res[$lg->nomMoyen], nb_demijour_ouvre($debut, $fin, false));
			$date_prec = $date_dateCours;
			$horaire_prec = $heure_dateCours;
			$timestamp_fin_sansHeure = $timestamp_cours_fin_sans_heure;
			$timestamp_fin = $timestamp_cours_fin;
		
		//Si le jour de l'essai précédent est la même que celui de l'essai en cours et que l'heure de l'essai précedent et de l'essai en cours est < 13 et que ce n'est pas le premier
		}else if ($timestamp_fin_sansHeure == $timestamp_cours_deb_sansHeure && $horaire_prec[0] < 13 && $heureDeb_dateCours[0] < 13 && $first == false){
			
			//Calcul entre les deux dates de l'essai avec simultané à true
			array_push ($res[$lg->nomMoyen], nb_demijour_ouvre($debut, $fin, true));
			$date_prec = $date_dateCours;
			$horaire_prec = $heure_dateCours;
			$timestamp_fin_sansHeure = $timestamp_cours_fin_sans_heure;
			$timestamp_fin = $timestamp_cours_fin;
		
		//Si le jour de fin de l'essai précedent et l'essai en cours sont identique et que l'heure de l'essai precedent et de l'essai en cours est > 13
		}else if ($timestamp_fin_sansHeure == $timestamp_cours_deb_sansHeure && $horaire_prec[0] > 13 && $heureDeb_dateCours[0] > 13){
			
			//echo nb_demijour_ouvre($debut, $fin, true)."<br>";
			array_push ($res[$lg->nomMoyen], nb_demijour_ouvre($debut, $fin, true));
			$date_prec = $date_dateCours;
			$horaire_prec = $heure_dateCours;
			$timestamp_fin_sansHeure = $timestamp_cours_fin_sans_heure;
			$timestamp_fin = $timestamp_cours_fin;
		
		//Si la date de fin de l'essai précedent est inferieur ou égale à la date de début de l'essai en cours
		}else if ($timestamp_fin <= $timestamp_cours_deb ){
			
			//echo nb_demijour_ouvre($debut, $fin, false)."<br>";
			array_push ($res[$lg->nomMoyen], nb_demijour_ouvre($debut, $fin, false));
			$date_prec = $date_dateCours;
			$horaire_prec = $heure_dateCours;
			$timestamp_fin_sansHeure = $timestamp_cours_fin_sans_heure;
			$timestamp_fin = $timestamp_cours_fin;
		
		//Si la date de debut de l'essai est inferieur à la date de fin de l'essai précedent et que la date de fin de l'essai est supérieur à celle de l'essai précédent
		}else if ($timestamp_cours_deb <= $timestamp_fin && $timestamp_cours_fin > $timestamp_fin){
			
			//Si l'horaire de l'essai précendent est minuit ou 13h
			if ($horaire_prec[0] == "13" && $horaire_prec[1] == "00"){
				
				//Recul de une heure pour ne pas prendre une demi journée de trop
				array_push ($res[$lg->nomMoyen], nb_demijour_ouvre(date("Y-m-d H:i", strtotime($date_prec[0]."-".$date_prec[1]."-".$date_prec[2]." ".$horaire_prec[0].":".$horaire_prec[1]." -1 hour")), $fin, true));
				
			}else {
				
				array_push ($res[$lg->nomMoyen], nb_demijour_ouvre(date("Y-m-d H:i", strtotime($date_prec[0]."-".$date_prec[1]."-".$date_prec[2]." ".$horaire_prec[0].":".$horaire_prec[1])), $fin, true));
				
			}
			
			$date_prec = $date_dateCours;
			$horaire_prec = $heure_dateCours;
			$timestamp_fin_sansHeure = $timestamp_cours_fin_sans_heure;
			$timestamp_fin = $timestamp_cours_fin;
		}
	}
	
	if (isset($res[$nomMoyen])){
		
		$resultat[$nomMoyen] = array_sum($res[$nomMoyen])/$total*100;
	}else{
		
		$resultat[$nomMoyen] = 0;
	}
	
	array_push ($data, $resultat);
	array_push($target,$val_target);

}

array_push($target,$val_target);

$group = array();
$tab = array ();
//Récupération des moyens de la bdd
$str ="SELECT nomMoyen FROM moyen WHERE idService_SERVICE = $idService";
$req = mysqli_query($bdd, $str);
$tabMoyen = array();
//Initialisation du tableau qui permet de trier par moyen
while ($lg=mysqli_fetch_object($req)){
	//Replace à cause des paramètres de l'url qui n'accepte pas les espaces
	$moy = str_replace(" ", "-", $lg->nomMoyen);
	if (isset($_GET[$moy])){
		
		array_push($tabMoyen, $lg->nomMoyen);
		$tab[$lg->nomMoyen] = array();
		
	}
}

//Parcours des différente périodes
for ($i=0; $i<$nb_col; $i++){
	//Pour chaques moyens dans la base de données
	for ($moyen = 0; $moyen<count($tabMoyen); $moyen++){
		$find = false;
		//Si data[$i] existe (si le mois en cours est inférieur à décembre)
		if (isset($data[$i])){			
			//Pour chaque moyen de l'année
			foreach ($data[$i] as $key => $value){
				//Si il est égale au moyen dans la base de données
				if ($key == $tabMoyen[$moyen]){
					//On l'ajoute dans le tableau
					array_push($tab[$tabMoyen[$moyen]],$value);
					//On en a trouvé un
					$find = true;
				}
			}
		}
		//Replace l'espace par un tiret car la passage en GET ne prend pas en compte les espaces
		$moy = str_replace(" ", "-", $tabMoyen[$moyen]);
		//Si aucun moyen  n'a été trouvé 
		if (!$find && isset($_GET[$moy])){
			//Ajout d'un 0 pour compléter le graphe
			array_push($tab[$tabMoyen[$moyen]],0);
		}
	}				
}

//changer les couleurs
$tab_couleur = array("#6699FF","#3ADF00","#F4F458","orange","#DD0000","#2ECCFA","#9AFE2E","#0000FF","#9A2EFE","#FE2EF7","#A4A4A4","#585858");
//Pour chaque élement dans le tableau
$resultat = array();
$lettre = 'A';
//Ajout de la ligne pour la légende
array_push($resultat, array("",'2018','2019',"Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"));
//Pour chaque moyen dans le teableau
foreach ($tab as $key => $value){
	//Création d'un tableau qui contient chaque ligne du tableur Excel
	$tableau = array();
	//Ajout de la légende par ligne
	array_push($tableau, $key);
	//Ajout de chaques valeurs
	for ($cpt = 0; $cpt < count($value); $cpt ++){

		array_push($tableau, $value[$cpt]);
		
	}
	//Incrémentation de la lettre
	$lettre ++;
	//Ajout du tableau dans le tableau finale
	array_push($resultat, $tableau);
}
//+1 car on ne compte pas la première ligne (légende)
$nbMoyen = count($tab)+1;
$sheet->fromArray($resultat);

$categories = array(
  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$B$1:$O$1', null, 4),   
);

$val = array();
$lab = array();
for ($cpt = 2; $cpt <= $nbMoyen; $cpt ++){
	
	array_push($val, new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$B$'.$cpt.':$O$'.$cpt, null, 4));
	array_push($lab, new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$A$'.$cpt, null, 1));
}

$labels = $lab;
$values = $val;

$series = new PHPExcel_Chart_DataSeries(
  PHPExcel_Chart_DataSeries::TYPE_BARCHART,       // plotType
  PHPExcel_Chart_DataSeries::GROUPING_STANDARD,   // plotGrouping
  array(0,1,2,3,4,5,6,7,8,9,10,11),                                       // plotOrder
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
$chart->setTopLeftPosition('B20');
$chart->setBottomRightPosition('H35');
$sheet->addChart($chart);

for($col = 'A'; $col !== 'P'; $col++) {
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