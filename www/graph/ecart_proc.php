<?php 
require('../conf/connexion_param.php');// connexion a la base
require ('jpgraph/jpgraph.php');
require ('jpgraph/jpgraph_bar.php');

//Vérirication du paramètre
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

	//on recupere les données des procedures, date de demande et date de besoin
	$str="select dp.delai, et.dateEtat, et.idEtat_etat
	from demande_procedure dp, procedures p, etatproc et
	where p.idDP_demande_procedure=dp.idDP
	and et.idProc_procedures=p.idProc
	and (et.idEtat_etat=17 or idEtat_etat=16)
	and idService_service=$idService
	and EXISTS 
		(select idEtat_etat from etatproc
		where idEtat_etat=17
		and idProc_procedures=p.idProc)";
	$req=mysqli_query($bdd,$str);
	if(!$req)
		echo "<div class='alert alert-danger'><strong>Erreur de récupération des infos des procédures</strong></div>";
	else{
		
		//Initialisation des variables
		$nbValInf5=0;
		$nbVal5_0=0;
		$nbVal1_5=0;
		$nbVal6_10=0;
		$nbValSup10=0;
		$nbSignInf5=0;
		$nbSign5_0=0;
		$nbSign1_5=0;
		$nbSign6_10=0;
		$nbSignSup10=0;
		
		//Pour chaques procédures
		while($lg=mysqli_fetch_object($req))
		{
			//Stockage de la date de besoin
			$dateBesoin=$lg->delai;
			//Stockage de la date de validation
			$dateValidation=$lg->dateEtat;
			//Stockage de la date d'état
			$idEtat=$lg->idEtat_etat;
			//Calcul de la date de livraison
			$dateLiv=floor((strtotime($dateValidation) - strtotime($dateBesoin))/86400);
			//Si la procédure est validée => date de Validation sinon date de signature
			if($idEtat==17)
			{
				if($dateLiv<-5) //plus de 5 jours d'avance
					$nbValInf5++;
				elseif($dateLiv<=0) //Pile dans les temps
					$nbVal5_0++;
				elseif($dateLiv<=5) //moins de 5 jours de retard
					$nbVal1_5++;
				elseif($dateLiv<=10) //moins de 10 jours de ratrd
					$nbVal6_10++;
				else
					$nbValSup10++; //Plus de 10
			}
			else
			{
				if($dateLiv<-5)
					$nbSignInf5++;
				elseif($dateLiv<=0)
					$nbSign5_0++;
				elseif($dateLiv<=5)
					$nbSign1_5++;
				elseif($dateLiv<=10)
					$nbSign6_10++;
				else
					$nbSignSup10++;
			}		
		}
		mysqli_close($bdd);
		
		//creation des tableaux
		$data1y=array($nbValInf5,$nbVal5_0,$nbVal1_5,$nbVal6_10,$nbValSup10);
		$data2y=array($nbSignInf5,$nbSign5_0,$nbSign1_5,$nbSign6_10,$nbSignSup10);

		$graph = new Graph(1000,800, 'auto');
		$graph->SetScale("textlin");

		$theme_class=new UniversalTheme;
		$graph->SetTheme($theme_class);
		$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,9);
		$graph->yaxis->title->SetFont(FF_ARIAL,FS_BOLD,12);
		$graph->title->SetFont(FF_ARIAL,FS_BOLD, 16);
		$graph->title->Set("Écart de livraison des procédures $labo par rapport à la date de besoin\n\nÉdité le ".$date_act);
		
		$graph->SetBox(false);
		$graph->ygrid->SetFill(false);
		$graph->xaxis->SetTickLabels(array('< -5jours','de -5 à 0 jours','de 1 à 5 jours','de 6 à 10 jours','>10 jours'));
		$graph->yaxis->HideLine(false);
		$graph->yaxis->HideTicks(false,false);
		$graph->yaxis->title->Set("Nombre de procédures");

		//Creation des barPlot
		$b1plot = new BarPlot($data1y);
		$b2plot = new BarPlot($data2y);

		//Creation du groupe de barPlot
		$gbplot = new GroupBarPlot(array($b1plot,$b2plot));

		//ajout au graph
		$graph->Add($gbplot);
		
		//ajout de la legende
		$b1plot->SetLegend('Procédure validée');
		$b2plot->SetLegend('Procédure mise en signature');
		$graph->legend->SetFrameWeight(1);
		$graph->legend->SetColumns(6);
		$graph->legend->SetColor('#4E4E4E','black');
		$graph->legend->SetLayout(LEGEND_VERT);
		$graph->legend->SetFont(FF_ARIAL,FS_NORMAL,11);

		//Couleur
		$b1plot->SetColor("white");
		$b1plot->SetFillColor("#6699FF");
		$b1plot->value->Show();
		$b1plot->value->SetFormat('%d');
		$b2plot->SetColor("white");
		$b2plot->SetFillColor("#FFCC66");
		$b2plot->value->Show();
		$b2plot->value->SetFormat('%d');
		
		// Display the graph
		$graph->Stroke();
	}
}
