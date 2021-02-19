<?php
require('../conf/connexion_param.php');// connexion a la base
require ('jpgraph/jpgraph.php');
require ('jpgraph/jpgraph_bar.php');
//Vérification du paramètre
if(!isset($_GET["idService"]))
	echo "<div class='alert alert-danger'><strong>Erreur de récupération du service</strong></div>";
else
{	
	//Stockage du paramètre
	$idService=$_GET["idService"];
	//Sélection du labo
	if($idService==1) $labo="EMC";
	elseif($idService==2) $labo="VIB";
	else $labo="VTH";

	//Date de début date (date actuelle)
	$date_act = strftime("%d/%m/%Y", mktime(0,0,0,date('m'), date('d'), date('Y')));

	//Sélection des informations des procédure
	$str="select p.idProc, dp.delai, dateDemandeDP_redigerDP
	from procedures p, etatproc et, demande_procedure dp
	where et.idProc_procedures=p.idProc
	and et.idEtat_etat=17
	and p.idDP_demande_procedure=dp.idDP
	and p.idService_service=$idService;";
	$req=mysqli_query($bdd,$str);
	if(!$req)
		echo "<div class='alert alert-danger'><strong>Erreur de récupération des infos des procédures</strong></div>";
	else{
		//Initialisation des variables
		$datePrecedente=0;
		$tabNbJoursAtt=array();
		$tabNbJoursRedac=array();
		$tabNbJoursRelec=array();
		$tabNbJoursSign=array();
		$tabNbJoursDP=array();
		$i=0;
		//Pour chaque procédures
		while($lg=mysqli_fetch_object($req))
		{
			$idProc=$lg->idProc;
			//Calcul du nombre de jours demandés LP
			$tabNbJoursDP[$i]=floor((strtotime($lg->delai) - strtotime($lg->dateDemandeDP_redigerDP))/86400); //nombre de jours de la demande
			
			$str="select et.dateEtat, et.idEtat_etat
			from  procedures p, etatproc et
			where et.idProc_procedures=p.idProc
			and p.idProc=$idProc
			and et.idEtat_etat!=12
			and et.idEtat_etat!=11
			order by p.idProc, et.idEtat_etat;";
			$reqInfo=mysqli_query($bdd,$str);
			if(mysqli_num_rows($reqInfo)==5)
			{
				while($lgInfo=mysqli_fetch_object($reqInfo))
				{
					$idEtat=$lgInfo->idEtat_etat;
					$dateEtat=$lgInfo->dateEtat;
					if($idEtat!=13) //a l'état 13 on n'a pas de date précédente
					{
						if($idEtat==14)// date mise en redac - date mise en attente
							$tabNbJoursAtt[$i]=floor((strtotime($dateEtat) - strtotime($datePrecedente))/86400);
						elseif($idEtat==15)// date mise en relec - date mise en redac
							$tabNbJoursRedac[$i]=floor((strtotime($dateEtat) - strtotime($datePrecedente))/86400);
						elseif($idEtat==16)// date mise en signature - date mise en relec
							$tabNbJoursRelec[$i]=floor((strtotime($dateEtat) - strtotime($datePrecedente))/86400);
						elseif($idEtat==17)// date validation - date mise en signature
							$tabNbJoursSign[$i]=floor((strtotime($dateEtat) - strtotime($datePrecedente))/86400);
					}
					$datePrecedente=$dateEtat;	
				}
			}
			$i++;	
		}
		//Initialisation des variables
		$moyAtt=0;
		$moyRedac=0;
		$moyRelec=0;
		$moySign=0;
		if(mysqli_num_rows($req)!=0) //si un seul résultat dans la premiere requete, les tableaux ont été remplies (evite division par 0)
		{
			$moyAtt= array_sum($tabNbJoursAtt)/count($tabNbJoursAtt); //Calcul de moyenne
			$moyRedac= array_sum($tabNbJoursRedac)/count($tabNbJoursRedac); //Calcul de moyenne
			$moyRelec= array_sum($tabNbJoursRelec)/count($tabNbJoursRelec); //Calcul de moyenne
			$moySign= array_sum($tabNbJoursSign)/count($tabNbJoursSign); //Calcul de moyenne
			$moyDP= array_sum($tabNbJoursDP)/count($tabNbJoursDP); //Calcul de moyenne
		}
		mysqli_close($bdd);

		//Ajout dans le tableau
		$data1y=array($moyAtt,20,$moyDP);
		$data2y=array($moyRedac,0,0);
		$data3y=array($moyRelec,0,0);
		$data4y=array($moySign,0,0);

		// Create the graph. These two calls are always required
		$graph = new Graph(1000,800, 'auto');
		$graph->SetScale("textlin");
		$theme_class = new UniversalTheme;
		$graph->SetTheme($theme_class);

		$graph->xaxis->SetTickLabels(array('Procédures',"Engagement $labo",'Demande LP'));

		$graph->title->SetFont(FF_ARIAL,FS_BOLD, 16);
		$graph->title->Set("Durée moyenne des étapes de rédaction des procédures $labo\n\nÉdité le ".$date_act);

		$graph->yaxis->title->SetFont(FF_ARIAL,FS_BOLD,12);
		$graph->yaxis->HideLine(false);
		$graph->yaxis->HideTicks(false,false);
		$graph->yaxis->title->Set("Nombre de jours");

		$graph->SetBox(false);

		$graph->ygrid->SetFill(false);

		$graph->yaxis->HideLine(false);
		$graph->yaxis->HideTicks(false,false);
		// Setup month as labels on the X-axis

		// Create the bar plots
		$b1plot = new BarPlot($data1y);
		$b2plot = new BarPlot($data2y);
		$b3plot = new BarPlot($data3y);
		$b4plot = new BarPlot($data4y);

		// Create the grouped bar plot
		$gbbplot = new AccBarPlot(array($b1plot,$b2plot,$b3plot,$b4plot));

		$graph->Add($gbbplot);

		//Paramètres des barPlot
		$b1plot->SetColor("white");
		$b1plot->SetFillColor("#6699FF");
		$b1plot->SetLegend("Attente");
		$b1plot->SetValuePos("center");
		$b1plot->value->show();
		$b1plot->value->SetColor('black');
		$b1plot->value->SetFormat('%d');

		$b2plot->SetColor("white");
		$b2plot->SetFillColor("#FFCC66");
		$b2plot->SetLegend("Rédaction");
		$b2plot->SetValuePos("center");
		$b2plot->value->show();
		$b2plot->value->SetColor('black');
		$b2plot->value->SetFormat('%d');

		$b3plot->SetColor("white");
		$b3plot->SetFillColor("#EE0000");
		$b3plot->SetLegend("Relecture");
		$b3plot->SetValuePos("center");
		$b3plot->value->show();
		$b3plot->value->SetColor('black');
		$b3plot->value->SetFormat('%d');

		$b4plot->SetColor("white");
		$b4plot->SetFillColor("#DA70D6");
		$b4plot->SetLegend("Signature");
		$b4plot->SetValuePos("center");
		$b4plot->value->show();
		$b4plot->value->SetColor('black');
		$b4plot->value->SetFormat('%d');

		$gbbplot->value->show();
		$gbbplot->value->SetFormat('%d');

		$graph->legend->SetFrameWeight(1);
		$graph->legend->SetColumns(6);
		$graph->legend->SetColor('#4E4E4E','black');
		$graph->legend->SetLayout(LEGEND_VERT);
		$graph->legend->SetFont(FF_ARIAL,FS_NORMAL,11);
		$graph->legend->Pos(0.73,0.81);

		$band = new PlotBand(VERTICAL,BAND_RDIAG,11,"max",'khaki4');
		$band->ShowFrame(true);
		$band->SetOrder(DEPTH_BACK);
		$graph->Add($band);

		// Display the graph
		$graph->Stroke();
	}
}
