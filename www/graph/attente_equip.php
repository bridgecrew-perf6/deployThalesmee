<?php 
require('../conf/connexion_param.php');// connexion a la base
require ('jpgraph/jpgraph.php');
require ('jpgraph/jpgraph_bar.php');


if(!isset($_GET["idService"]) || !isset($_GET["fifo"]))
	echo "<div class='alert alert-danger'><strong>Erreur de récupération du service et du parametre fifo</strong></div>";
else
{
	$idService=$_GET["idService"];
	$fifo=$_GET["fifo"];
	if($idService==1)
		$labo="EMC";
	elseif($idService==2)
		$labo="VIB";
	else
		$labo="VTH";
	//on recupere les données des procedures, date de demande et date de besoin
	$str="select et.dateEtat, et.idEtat_etat
	from essai e, etatessai et
	where et.idEssai_essai=e.idEssai
	and (et.idEtat_etat=22 or idEtat_etat=23)
	and e.fifo=$fifo
	and e.idService_service=$idService
	and EXISTS 
		(select idEtat_etat from etatessai
		where idEtat_etat=25
		and idEssai_essai=e.idessai)
	order by e.idessai, et.idEtat_etat;";
	$req=mysqli_query($bdd,$str);
	if(!$req)
		echo "<div class='alert alert-danger'><strong>Erreur de récupération des infos des procédures</strong></div>";
	else{
		
		$j0=0;
		$j1=0;
		$j2_3=0;
		$j4_5=0;
		$jsup6=0;
		$tab = array();
		while($lg=mysqli_fetch_object($req))
		{
			$dateEtat=$lg->dateEtat;
			$idEtat=$lg->idEtat_etat;
			if($idEtat==23)//dateEtat contient la date de fin d'attente
			{
				$nbj=floor((strtotime($dateEtat) - strtotime($datePrecedente))/86400);
				array_push($tab,$dateEtat);
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
				
			}
			else //dateEtat contient la date de debut d'attente
				$datePrecedente=$dateEtat;
			
		}
		mysqli_close($bdd);

		//creation du tableau
		$data1y=array($j0,$j1,$j2_3,$j4_5,$jsup6);
	
		$graph = new Graph(1000,800, 'auto');
		$graph->SetScale("textlin");

		$theme_class=new UniversalTheme;
		$graph->SetTheme($theme_class);
		$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,9);
		$graph->yaxis->title->SetFont(FF_ARIAL,FS_BOLD,12);
		$graph->title->SetFont(FF_ARIAL,FS_BOLD, 16);
		if($fifo==0)
			$titre="Durée d'attente de l'equipement sur l'étagére $labo";
		else
			$titre="Durée d'attente de l'equipement sur l'étagére $labo FIFO";
		if (empty($tab)){
			$titre="Aucune donnée";
		}
		$graph->title->Set($titre);
		$graph->SetBox(false);

		$graph->ygrid->SetFill(false);
		$graph->xaxis->SetTickLabels(array('0 jour','1 jour','2 à 3 jours','4 à 5 jours','>5 jours'));

		$graph->yaxis->HideLine(false);
		$graph->yaxis->HideTicks(false,false);
		$graph->yaxis->title->Set("Nombre de tests");

		//Creation des barPlot
		$b1plot = new BarPlot($data1y);
		
		//Creation du groupe de barPlot
		$gbplot = new GroupBarPlot(array($b1plot));

		//ajout au graph
		$graph->Add($gbplot);
		
		//ajout de la legende
		$b1plot->SetLegend('Procédure validée');
		$graph->legend->SetFrameWeight(1);
		$graph->legend->SetColumns(6);
		$graph->legend->SetColor('#4E4E4E','black');
		$graph->legend->SetLayout(LEGEND_VERT);
		$graph->legend->SetFont(FF_ARIAL,FS_NORMAL,11);

		$b1plot->SetColor("white");
		$b1plot->SetFillColor("#6699FF");
		$b1plot->value->Show();
		$b1plot->value->SetFormat('%d');

		// Display the graph
		$graph->Stroke();
	}
}
