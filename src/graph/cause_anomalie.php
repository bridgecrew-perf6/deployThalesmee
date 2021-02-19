<?php 
if(!isset($_GET["idService"]) || !isset($_GET["target"]))
	echo "<div class='alert alert-danger'><strong>Erreur de récupération du service et de la valeur de la target</strong></div>";
else
{
	require('../conf/connexion_param.php');// connexion a la base
	require ('jpgraph/jpgraph.php');
	require ('jpgraph/jpgraph_bar.php');
	require ('jpgraph/jpgraph_line.php');

	//Numéro du service
	$idService = $_GET["idService"];

	//Valeur de la target
	$val_target = $_GET["target"];
	$dateDeb = $_GET["dateDeb"];
	$dateFin = $_GET["dateFin"];

	//Date du jour (date actuelle)
	$date_act = strftime("%d/%m/%Y", mktime(0,0,0,date('m'), date('d'), date('Y')));

	$value = array();
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
		array_push($target,$val_target);
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

	//Création du graphe
	$graph = new Graph(1000,800,'auto');

	$graph->SetMargin(40,40,40,40);
	$graph->SetScale('textlin');
	//Deuxième axe Y
	$graph->SetYScale(0, "lin", 0, 100);

	$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,9);
	$graph->yaxis->title->SetFont(FF_ARIAL,FS_BOLD,12);
	$graph->title->SetFont(FF_ARIAL,FS_BOLD, 16);

	$datay=$value;

	$graph->title->Set($titre."\n\nÉdité le ".$date_act);
	$graph->SetBox(false);
	$graph->ygrid->SetFill(false);
	$graph->xaxis->SetTickLabels($legende);
	$graph->yaxis->HideLine(false);
	$graph->yaxis->HideTicks(false,false);

	$group = array();

	$b1plot = new BarPlot($datay);
	$b1plot->SetLegend('Nombre d\'anomalie');
	$b1plot->SetColor("white");
	$b1plot->SetFillColor("#00CC33");
	$b1plot->value->Show();
	$b1plot->value->SetFormat("%01.2f");

	//Ligne rouge d'indication de "limite"
	$lplot = new LinePlot($target);
	$l2plot = new LinePlot($pareto);
	
	//Ajout au graph
	$graph->Add($b1plot);
	//Ajout sur le deuxième axe
	$graph->AddY(0,$lplot);
	$graph->AddY(0,$l2plot);
	
	$lplot->SetLegend('Target');
	$lplot->SetWeight(8);
	$lplot->SetColor('#FF0000');
	$lplot->SetBarCenter();

	$l2plot->SetColor('black');
	$l2plot->SetLegend('Pourcentage cumulatif');
	$l2plot->SetWeight(10);
	$l2plot->mark->SetType(MARK_UTRIANGLE,'',1.0);
	$l2plot->mark->SetColor('black');
	$l2plot->mark->SetFillColor('black');
	$l2plot->SetBarCenter();
	
	$graph->legend->SetFrameWeight(1);
	$graph->legend->SetColumns(6);
	$graph->legend->SetColor('#4E4E4E','black');
	$graph->legend->SetLayout(LEGEND_VERT);
	$graph->legend->SetFont(FF_ARIAL,FS_NORMAL,11);
	
	// Display the graph
	$graph->Stroke();
}
