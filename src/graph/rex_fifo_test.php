<?php 
if(!isset($_GET["idService"]))
	echo "<div class='alert alert-danger'><strong>Erreur de récupération du service</strong></div>";
else
{
	require('../conf/connexionPDO_param.php');// connexion a la base
	require ('jpgraph/jpgraph.php');
	require ('jpgraph/jpgraph_bar.php');
	$ligne = "";
	if (isset ($_GET['I2PT'])) $ligne .= "'I2PT',";
	if (isset ($_GET['I2PA'])) $ligne .= "'I2PA',";
	if (isset ($_GET['LPA'])) $ligne .= "'LPA',";
	if (isset ($_GET['LPH'])) $ligne .= "'LPH',";
	if (isset ($_GET['Autre'])) $ligne .= "'Autre',";
	if (isset ($_GET['LPE'])) $ligne .= "'LPE',";
	
	//On enlève le dernier caractère qui est une virgule
	$ligne = substr($ligne,0,-1);
	$condDate="";
	$idService=$_GET["idService"];
	
	if(isset($_GET["dateDeb"])) //si on passe des dates en parametres (ex pour le rex) on les prends en comptes
	{
		$dateDeb=$_GET["dateDeb"];
		$dateFin=$_GET["dateFin"];
		//Date du jour (date actuelle)
		$date_act = strftime("%d/%m/%Y", mktime(0,0,0,date('m'), date('d'), date('Y')));
		$condDate="and et.dateEtat >=:dateDeb and et.dateEtat <=:dateFin";
		$condVar=array('idService' => $idService,'dateDeb' => $dateDeb,'dateFin' => $dateFin);
	}
	else
		$condVar=array('idService' => $idService);
	
	//on recupere les données des essais en fifo
	$str="select et.dateEtat, nomLigne
	from essai e, etatessai et, ligneproduit
	where et.idEssai_essai=e.idEssai";
	if (isset ($_GET['Tous'])){
			
		$str .= " and ((idLigne = ligneProd
		and nomLigne in ($ligne)) or ligneProd is NULL)";
	}else{
		
		$str .= " and idLigne = ligneProd
		and nomLigne in ($ligne)";
	}
	
	$str .= " and et.idEtat_etat=25
	and e.fifo=1
	and e.idService_service=:idService
	$condDate
	order by et.dateEtat;";
	
	$res=$dbh->prepare($str);
	$res->execute($condVar);
	if(!$res)
		echo "<div class='alert alert-danger'><strong>Erreur de récupération des infos des procédures</strong></div>";
	else{
		$numSemPrec=0;
		$numSemAct=0;
		$cptSemDiff=0;
		$nbTest=0;
		$tabSemaine=array(); //tableau contenant le nombre de test reunis par semaines
		$tabNumSemLegende=array(); //tableau pour construire la legende
		while($lg=$res->fetch(PDO::FETCH_OBJ))
		{
			$numSemAct=date("W",(strtotime($lg->dateEtat)));
			if($numSemPrec!=$numSemAct)
			{
				if($numSemPrec !=0) //si ce n'est pas le premier passage -> On incremente le compteur (au premier passage on conserve le 0 pour la premiere case du tableau)
					$cptSemDiff++;
				$numSemPrec=$numSemAct;
				$tabSemaine[$cptSemDiff]=0;
				$tabNumSemLegende[$cptSemDiff]=date("y",(strtotime($lg->dateEtat))).$numSemAct;
			}
			$tabSemaine[$cptSemDiff]++;
			$nbTest++;
			
			
		}		
		$dbh->connection = null;

		//creation du tableau
		
		
		$graph = new Graph(1000,800, 'auto');
		$graph->SetScale("textlin");

		$theme_class=new UniversalTheme;
		$graph->SetTheme($theme_class);
		$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,9);
		$graph->yaxis->title->SetFont(FF_ARIAL,FS_BOLD,12);
		$graph->title->SetFont(FF_ARIAL,FS_BOLD, 16);
		if (empty($tabSemaine)){
			
			array_push($tabSemaine,0);
			$titre = "Aucune donnée";
			
		}else $titre = "Retour d'experience FIFO";

		$graph->title->Set($titre."\n\nÉdité le ".$date_act);
		$data1y=$tabSemaine;
		$graph->SetBox(false);

		$graph->ygrid->SetFill(false);
		$graph->xaxis->SetTickLabels($tabNumSemLegende);
		$graph->xaxis->SetLabelAngle(90);

		$graph->yaxis->HideLine(false);
		$graph->yaxis->HideTicks(false,false);

		//Creation des barPlot
		$b1plot = new BarPlot($data1y);

		//Creation du groupe de barPlot
		$gbplot = new GroupBarPlot(array($b1plot));

		//ajout au graph
		$graph->Add($gbplot);
		
		//ajout de la legende
		$b1plot->SetLegend('Nombre de tests réalisés');
		
		$graph->legend->SetFrameWeight(1);
		$graph->legend->SetColumns(6);
		$graph->legend->SetColor('#4E4E4E','black');
		$graph->legend->SetLayout(LEGEND_VERT);
		$graph->legend->SetFont(FF_ARIAL,FS_NORMAL,11);

		
		$b1plot->SetColor("white");
		$b1plot->SetFillColor("#6699FF");
		$b1plot->value->Show();
		$b1plot->value->SetFormat('%d');

		$txt=new Text("Total tests: ".$nbTest);
		$txt->SetPos(450,725);
		$txt->SetColor('#4E4E4E','black');
		$txt->SetFont(FF_ARIAL,FS_NORMAL,11);
		$graph->AddText($txt);
		
		$graph->Stroke();
	}
}

