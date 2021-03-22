<?php 
if(!isset($_GET["idService"]))
	echo "<div class='alert alert-danger'><strong>Erreur de récupération du service et du parametre fifo</strong></div>";
else
{
	require('../conf/connexionPDO_param.php');// connexion a la base
	require('../conf/connexion_param.php');// connexion a la base
	require ('jpgraph/jpgraph.php');
	require ('jpgraph/jpgraph_bar.php');
	require ('jpgraph/jpgraph_line.php');

	$ligne = "";
	$condDate="";
	$idService=$_GET["idService"];

	//Date du jour (date actuelle)
	$date_act = strftime("%d/%m/%Y", mktime(0,0,0,date('m'), date('d'), date('Y')));

	//Permet de gérer les lignes de produit passée(s) en paramètre
	if (isset ($_GET['I2PT'])) $ligne .= "'I2PT',";
	
	if (isset ($_GET['I2PA'])) $ligne .= "'I2PA',";
	
	if (isset ($_GET['LPA'])) $ligne .= "'LPA',";
	
	if (isset ($_GET['LPH'])) $ligne .= "'LPH',";
		
	if (isset ($_GET['Autre'])) $ligne .= "'Autre',";
	
	if (isset ($_GET['LPE'])) $ligne .= "'LPE',";
	
	//On enlève le dernier caractère qui est une virgule
	$ligne = substr($ligne,0,-1);
	
	if($idService==1) $labo="EMC";
	elseif($idService==2) $labo="VIB";
	else $labo="VTH";
	
	$val_target = $_GET["target"];
	$mois_lettre = array("","Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");
	$annee_en_cours = explode("-", $_GET["dateDeb"])[0];
	$mois = 0;
	$annee_actuel = date("Y");
	if ($annee_en_cours < $annee_actuel) $nb_col = 14;
	else $nb_col = date("n") + 2;

	if (isset($_GET["public"]))
	{
		$mois_limite = explode("-", $_GET["dateFin"])[1];
		$mois_limite = intval($mois_limite);
		$nb_col = $mois_limite + 2;
	}
	
	$tab_total = array();
	$tab_i2pt = array();
	$target=array();
	$legende = array();

	$array_total = array();
	$array_anomalie = array();
	$array_i2pt = array();
	
	for ($i=0; $i<$nb_col; $i++)
	{		
		if ($i == 0)
		{			
			$annee_prec = $annee_en_cours - 1 ;
			$dateDeb = $annee_prec."-01-01 00:00:00";
			$dateFin = $annee_prec."-12-31 23:59:59";
			array_push($legende, $annee_prec);
			
		}else if ($i == 1)
		{			
			$dateDeb = $annee_en_cours."-01-01 00:00:00";
			$dateFin = $annee_en_cours."-12-31 23:59:59";
			array_push($legende, $annee_en_cours);
			
		}else
		{			
			$mois += 1;
			$nb_jour = cal_days_in_month (CAL_GREGORIAN, $mois, $annee_en_cours);
			if ($mois < 10)
			{
				$dateDeb = $annee_en_cours."-0".$mois."-01";
				$dateFin = $annee_en_cours."-0".$mois."-".$nb_jour;
				
			}else 
			{				
				$dateDeb = $annee_en_cours."-".$mois."-01";
				$dateFin = $annee_en_cours."-".$mois."-".$nb_jour;
			}

			if (isset($_GET["public"]))
			{
				$mois_limite = explode("-", $_GET["dateFin"])[1];
				$mois_limite = intval($mois_limite);
				if ($mois == $mois_limite) $dateFin = explode(" ", $_GET["dateFin"])[0];
				elseif ($mois > $mois_limite)break;
			}
			
			array_push($legende, $mois_lettre[$mois]);
			
		}
		
		//Affichage de la condition pour le choix de la date
		$condDate="and e.date_debut >='$dateDeb' and e.date_debut<='$dateFin'";
		//on recupere le nombre dtotal d'essai sur la période en question
		$str="select distinct(idEssai) from essai e, etatessai et, ligneproduit
		where et.idEssai_essai=e.idEssai";
		if (isset ($_GET['Tous']))
		{			
			$str .= " and ((idLigne = ligneProd
			and nomLigne in ($ligne)) or ligneProd is NULL)";
		}else
		{			
			$str .= " and idLigne = ligneProd
			and nomLigne in ($ligne)";
		}
		
		$str .= "and e.idService_service=$idService
		and idEtat_etat >= 23 
		and EXISTS 
			(select idEtat_etat from etatessai et
			where (idEtat_etat=23)
			and idEssai_essai=e.idessai
			)
		$condDate
		order by e.idessai, et.idEtat_etat;";
		
		$req = mysqli_query($bdd, $str);
		$total = mysqli_num_rows($req);	
		array_push($array_total, $total);	
		
		//on recupere le nombre d'anomalie total
		$str="select distinct(a.idEssai) from essai e, etatessai et, ligneproduit, anomalie a
		where et.idEssai_essai=e.idEssai and a.idEssai = e.idEssai";
		if (isset ($_GET['Tous']))
		{			
			$str .= " and ((idLigne = ligneProd
			and nomLigne in ($ligne)) or ligneProd is NULL)";
		}else
		{			
			$str .= " and idLigne = ligneProd
			and nomLigne in ($ligne)";
		}
		
		$str .= "and e.idService_service=$idService
		and idEtat_etat >= 23 
		and EXISTS 
			(select idEtat_etat from etatessai et
			where (idEtat_etat=23)
			and idEssai_essai=e.idessai
			)
		$condDate
		order by e.idessai, et.idEtat_etat;";
		
		$req = mysqli_query($bdd, $str);
		$total_anomalie = mysqli_num_rows($req);
		array_push($array_anomalie, $total_anomalie);
		
		//on recupere le nombre d'anomalie i2pt
		$str="select distinct(a.idEssai) from essai e, etatessai et, ligneproduit, anomalie a
		where et.idEssai_essai=e.idEssai and a.idEssai = e.idEssai and autre=1";
		if (isset ($_GET['Tous']))
		{			
			$str .= " and ((idLigne = ligneProd
			and nomLigne in ($ligne)) or ligneProd is NULL)";
		}else
		{			
			$str .= " and idLigne = ligneProd
			and nomLigne in ($ligne)";
		}
		
		$str .= "and e.idService_service=$idService
		and idEtat_etat >= 23 
		and EXISTS 
			(select idEtat_etat from etatessai et
			where (idEtat_etat=23)
			and idEssai_essai=e.idessai
			)
		$condDate
		order by e.idessai, et.idEtat_etat;";
		
		$req = mysqli_query($bdd, $str);
		$anomalie_i2pt = mysqli_num_rows($req);
		array_push($array_i2pt, $anomalie_i2pt);
		
		//Calcul du ratio en %
		if ($total == 0)
		{			
			$fpy_global = '';
			$fpy_i2pt  = '';
			
		}else 
		{			
			$fpy_global = round(($total - $total_anomalie) / $total * 100, 0);
			$fpy_i2pt = round(($total - $anomalie_i2pt) / $total * 100, 0);
		}
		
		array_push($tab_total, $fpy_global);
		array_push($tab_i2pt, $fpy_i2pt);
		array_push($target,$val_target);

	}
	
	array_push($target,$val_target);
	
	$nb_col -= 1;
	$mini_total = min($tab_total);
	$mini_i2pt = min($tab_i2pt);
	while ($nb_col <= 12 )
	{		
		array_push($tab_total, '');
		array_push($tab_i2pt, '');
		array_push($target,$val_target);
		array_push($legende, $mois_lettre[$nb_col]);
		$nb_col += 1;
		
	}

	if(!$req)
		
		echo "<div class='alert alert-danger'><strong>Erreur de récupération des infos</strong></div>";
		
	else
	{
		if (isset($_GET["tableau"])){

			$output = array(
				"legende"=>$legende,
				"tests"=>$array_total,
				"anomalie"=>$array_anomalie,
				"i2pt"=>$array_i2pt
			);
			echo json_encode($output);
		}else 
		{
			if (isset($_GET["public"])) $graph = new Graph(1000,400, 'auto');	
			else $graph = new Graph(1000,800, 'auto');
			
			if ($mini_total < 50 || $mini_i2pt < 50) $graph->SetScale("textlin");
			else $graph->SetScale("textlin", 50, 100);

			//creation du tableau
			$data1y=$tab_total;
			$data2y=$tab_i2pt;

			$theme_class=new UniversalTheme;
			$graph->SetTheme($theme_class);
			$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,9);
			$graph->yaxis->title->SetFont(FF_ARIAL,FS_BOLD,12);
			$graph->title->SetFont(FF_ARIAL,FS_BOLD, 16);

			$titre="FPY (%)";
			$graph->title->Set($titre."\n\nÉdité le ".$date_act);

			$graph->SetBox(false);

			$graph->ygrid->SetFill(false);
			$graph->xaxis->SetTickLabels($legende);
			$graph->yaxis->HideLine(false);
			$graph->yaxis->HideTicks(false,false);

			$group = array();
			//Creation des barPlot
			if (isset($_GET["global"]))
			{			
				$b1plot = new BarPlot($data1y);
				array_push($group,$b1plot);
				//ajout de la legende
				$b1plot->SetLegend('FPY global (%)');
				$b1plot->SetValuePos("center");
			}
			
			if (isset($_GET["i2pt"]))
			{			
				$b2plot = new BarPlot($data2y);
				array_push($group,$b2plot);
				//ajout de la legende
				$b2plot->SetLegend('FPY I2PT (%)');
				$b2plot->SetValuePos("center");	
			}
			
			$lplot = new LinePlot($target);
			
			//Creation du groupe de barPlot
			$gbplot = new GroupBarPlot($group);
			$gbplot->SetWidth(0.9);
			//ajout au graph
			$graph->Add($gbplot);
			$graph->Add($lplot);
			
			if (isset($_GET["global"]))
			{			
				$b1plot->SetColor("white");
				$b1plot->SetFillColor("#6699FF");
				$b1plot->value->SetColor("white");
				$b1plot->value->Show();
				$b1plot->value->SetFormat("%d");
				$b1plot->value->SetFont(FF_ARIAL, FS_BOLD, 10);
			}
			
			if (isset($_GET["i2pt"]))
			{
				$b2plot->SetColor("white");
				$b2plot->SetFillColor("#00CC33");
				$b2plot->value->SetColor("white");
				$b2plot->value->Show();
				$b2plot->value->SetFormat("%d");
				$b2plot->value->SetFont(FF_ARIAL, FS_BOLD, 10);
			}
			
			$graph->legend->SetFrameWeight(1);
			$graph->legend->SetColumns(6);
			$graph->legend->SetColor('#4E4E4E','black');
			$graph->legend->SetLayout(LEGEND_VERT);
			$graph->legend->SetFont(FF_ARIAL,FS_NORMAL,11);

			$lplot->SetLegend('Target');
			$lplot->SetWeight(5);
			$lplot->SetColor("#FF0000");

			// Display the graph
			$graph->Stroke();
		}		
	}
}
