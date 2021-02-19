<?php 
if(!isset($_GET["idService"]) || !isset($_GET["target"]))
	echo "<div class='alert alert-danger'><strong>Erreur de récupération du service et de la valeur de la target</strong></div>";
else
{
	error_reporting(E_ALL);
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);
	date_default_timezone_set('Europe/Paris');
	date_default_timezone_set('Europe/Paris');
	require('../../conf/connexion_param.php'); // connexion a la base
	set_include_path(get_include_path() . PATH_SEPARATOR . '../../Classes/');

	include 'PHPExcel.php';

	//Initialisation du classeur Excel
	$workbook = new PHPExcel();
	$sheet = $workbook->getActiveSheet();

	//Numéro du service
	$idService = $_GET["idService"];

	//Valeur de la target
	$val_target = $_GET["target"];
	$dateDeb = $_GET["dateDeb"];
	$dateFin = $_GET["dateFin"];

	//Date du jour (date actuelle)
	$date_act = strftime("%d/%m/%Y", mktime(0,0,0,date('m'), date('d'), date('Y')));

	$value = array();
	$res = array(); //Tableau resultat pour la création du fichier Excel
	$entete = array('Cause', 'Nombre d\'anomalie', 'Pourcentage cumulatif', 'Target'); //Entete pour le tableau Excel
	array_push($res, $entete);
	$labels = array();
	$values = array();

	$target=array();
	$legende = array();
	$pareto = array();

	//Nombre d'anomalie totale
	$total = 0;
	//Titre par défault
	$titre="Cause des anomalies";

	//Selection du nombre d'anomalie regroupées par cause
	$str="SELECT count(nomCause) as nb, nomCause FROM `cause_anomalie`, essai e WHERE idEssai_ESSAI = idEssai and e.idService_SERVICE='$idService' and e.date_debut >= '$dateDeb' and e.date_fin <= '$dateFin' GROUP BY nomCause ORDER BY nb DESC;";
	$req=mysqli_query($bdd, $str);

	//Si le nombre d'anomalie est égale à zéro
	if (mysqli_num_rows($req) == 0)
	{
		$total = 1;
		array_push($value, 0);
		array_push($target,0);
		array_push($legende, "");
		$titre="AUCUNE DONNÉE";
	}

	//Pour chaque anomalie
	while ($lg = mysqli_fetch_object($req))
	{
		$total += $lg->nb;
		array_push($value, $lg->nb);
		array_push($legende, str_replace(" ", "\n", $lg->nomCause));
	}

	//Nombre d'anomalie par défault (utile pour le calcul des pourcentage cumulatif)
	$prec = 0;
	//Pour chaque anomalie
	for ($i = 0; $i < count($value); $i++)
	{
		array_push($pareto, $value[$i]/$total*100 + $prec); //Ajout dans le tableau le pourcentage cumulatif
		//La valeur devint la valeur précédente
		$prec = $pareto[$i];
	}

	$col = 'B';
	for ($cpt = 0; $cpt < count($legende); $cpt++)
	{
		array_push($res, array($legende[$cpt], $value[$cpt], $pareto[$cpt], $val_target));
		array_push($labels, new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$'.$col.'$1', null, 1));
		$col++;
		array_push($values, new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$'.$col.'$2:$'.$col.'$5', null, 4));
	}

	$sheet->fromArray($res);

	$categories = array(
	  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$A$2:$A$5', null, 4),   
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
	$title    = new PHPExcel_Chart_Title('Nombre d\'anomalie');  
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
}