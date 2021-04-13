<?php 
if(!isset($_GET["idService"]) || !isset($_GET["fifo"]))
	echo "<div class='alert alert-danger'><strong>Erreur de récupération du service et du parametre fifo</strong></div>";
else
{
	require('../conf/connexionPDO_param.php');// connexion a la base
	require ('jpgraph/jpgraph.php');
	require ('jpgraph/jpgraph_bar.php');
	
	//Permet de stocker une chaine de caractère contenant les lignes de produit
	$ligne = "";
	//FIFO ou NON
	$fifo=$_GET["fifo"];
	//Variable permettant de stocker la condition de filter par rapport à la date
	$condDate="";
	//Numéro du service
	$idService=$_GET["idService"];
	//Date du jour (date actuelle)
	$date_act = strftime("%d/%m/%Y", mktime(0,0,0,date('m'), date('d'), date('Y')));

	//Choix de la famille de l'équipement suivant les cases cochées par l'utilisateur
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

	if(isset($_GET["dateDeb"])) //si on passe des dates en parametres (ex pour le rex) on les prends en comptes
	{
		$dateDeb=$_GET["dateDeb"];
		$dateFin=$_GET["dateFin"];
		$condDate="and et.dateEtat >='$dateDeb' and et.dateEtat <='$dateFin'";		

	}

	//fifo vaut 2 si l'utilisateur veut la somme de fifo et de non fifo
	if ($fifo == 2){
		
		//on recupere les données des essais (durée attente aprés test)
		$str="select distinct(idEssai),et.dateEtat, et.idEtat_etat, nomLigne
		from essai e, etatessai et, ligneproduit
		where et.idEssai_essai=e.idEssai";
		
		if (isset ($_GET['Tous'])){
			
			$str .= " and ((idLigne = ligneProd
			and nomLigne in ($ligne)) or ligneProd is NULL)";
		}else{
			
			$str .= " and idLigne = ligneProd
			and nomLigne in ($ligne)";
		}
		
		$str .= " and (et.idEtat_etat=24 or idEtat_etat=25)
		and e.idService_service=$idService
		and EXISTS 
			(select idEtat_etat from etatessai et
			where idEtat_etat=25
			and idEssai_essai=e.idessai
			)
		$condDate
		order by e.idessai, et.idEtat_etat;";
		
		
	}else {
		
		//on recupere les données des essais (durée attente aprés test)
		$str="select distinct(idEssai),et.dateEtat, et.idEtat_etat, nomLigne
		from essai e, etatessai et, ligneproduit
		where et.idEssai_essai=e.idEssai";
		if (isset ($_GET['Tous'])){
			
			$str .= " and ((idLigne = ligneProd
			and nomLigne in ($ligne)) or ligneProd is NULL)";
		}else{
			
			$str .= " and idLigne = ligneProd
			and nomLigne in ($ligne)";
		}
		
		$str .= " and (et.idEtat_etat=24 or idEtat_etat=25)
		and e.fifo=$fifo
		and e.idService_service=$idService
		and EXISTS 
			(select idEtat_etat from etatessai et
			where (idEtat_etat=25)
			and idEssai_essai=e.idessai
			)
		$condDate
		order by e.idessai, et.idEtat_etat;";
	}
	
	$res=$dbh->prepare($str);
	$res->execute();
	
	if(!$res)
		echo "<div class='alert alert-danger'><strong>Erreur de récupération des infos des procédures</strong></div>";
	else{
		
		$j0=0;
		$j1=0;
		$j2_3=0;
		$j4_5=0;
		$jsup6=0;
		$nbjMoy=0;
		$nbTest=0;
		$tab = array();
		$median= array();
		$correct = false;
		$essai = false;
		while($lg=$res->fetch(PDO::FETCH_OBJ))
		{
			$dateEtat=$lg->dateEtat;
			$idEtat=$lg->idEtat_etat;
			if($idEtat==25 and $correct == true and $idEs == $lg->idEssai)//dateEtat contient la date de fin d'attente
			{
				$nbj=(strtotime($dateEtat) - strtotime($datePrecedente))/86400;
				array_push($tab,$dateEtat);
				$nbjMoy+=$nbj;
				array_push($median,$nbj);
				$nbj = floor($nbj);
				$nbTest++;
				if($nbj==0) $j0++;
				elseif($nbj==1) $j1++;
				elseif($nbj <4) $j2_3++;
				elseif($nbj<6) $j4_5++;
				else $jsup6++;
				$correct = false;
				
			}
			else if ($idEtat==24) 
			{//dateEtat contient la date de debut d'attente				
				$idEs = $lg->idEssai;
				$correct = true;
				$datePrecedente=$dateEtat;
			}
			$essai = true;
			
		}
		//Trier le tableau pour trouver la valeur médiane
		sort($median);
		$dbh->connection = null;
		
		$graph = new Graph(1000,800, 'auto');
		if($nbTest!=0) $tmpAttMoy=round($nbjMoy/$nbTest,2);
		else $tmpAttMoy=0;
		
		if ($essai != false)
		{
			$middle = count($median)/2;
			if (is_numeric($middle)) $temps_median = round($median[$middle],2);
			else  $temps_median = round(($median[ceil($middle)] + $median[floor($middle)]) / 2,2);
			$graph->SetScale("textlin");
			
		}else 
		{			
			$graph->SetScale("textlin",0,10);
			$temps_median = 0;
			$j0 = 0;
			$j1 = 0;
			$j2_3 = 0;
			$j4_5 = 0;
			$jsup6 = 0;
		}
		
		//creation du tableau
		$data1y=array($j0,$j1,$j2_3,$j4_5,$jsup6);

		$theme_class=new UniversalTheme;
		$graph->SetTheme($theme_class);
		$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,9);
		$graph->yaxis->title->SetFont(FF_ARIAL,FS_BOLD,12);
		$graph->title->SetFont(FF_ARIAL,FS_BOLD, 16);

		if($fifo==0) $titre="Durée d'attente de l'equipement sur l'étagére $labo après le test";
		else if ( $fifo ==1) $titre="Durée d'attente de l'equipement sur l'étagére $labo FIFO après le test";
		else $titre="Temps d'attente tout tests confondus après le test";
		
		if (empty($tab)) $titre="AUCUNE DONNEE";

		$graph->title->Set($titre."\n\nÉdité le ".$date_act);
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
		
		$graph->legend->SetFrameWeight(1);
		$graph->legend->SetColumns(6);
		$graph->legend->SetColor('#4E4E4E','black');
		$graph->legend->SetLayout(LEGEND_VERT);
		$graph->legend->SetFont(FF_ARIAL,FS_NORMAL,11);
		
		//ajout de la legende
		$b1plot->SetLegend('Test terminés');
		$b1plot->SetColor("white");
		$b1plot->SetFillColor("#6699FF");
		$b1plot->value->Show();
		$b1plot->value->SetFormat('%d');

		$txt=new Text("Temps d'attente moyen: ".$tmpAttMoy."j");
		$txt->SetPos(200,700);
		$txt->SetColor('#4E4E4E','black');
		$txt->SetFont(FF_ARIAL,FS_NORMAL,11);

		$txt2=new Text("Temps d'attente médian: ".$temps_median."j");
		$txt2->SetPos(600,700);
		$txt2->SetColor('#4E4E4E','black');
		$txt2->SetFont(FF_ARIAL,FS_NORMAL,11);

		$graph->AddText($txt);
		$graph->AddText($txt2);
		
		// Display the graph
		$graph->Stroke();
	}
}
