<?php 
if(!isset($_GET["idService"]) || !isset($_GET["fifo"]))
	echo "<div class='alert alert-danger'><strong>Erreur de récupération du service et du parametre fifo</strong></div>";
else
{
	require('../conf/connexionPDO_param.php');// connexion a la base
	require ('jpgraph/jpgraph.php');
	require ('jpgraph/jpgraph_bar.php');
	require ('fonction.php');

	//Permet de stocker une chaine de caractère contenant les lignes de produit
	$ligne = "";
	//FIFO ou NON
	$fifo=$_GET["fifo"];
	//Variable permettant de stocker la condition de filter par rapport à la date
	$condDate="";
	//Numéro du service
	$idService=$_GET["idService"];
	//Date du jour
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
	//Choix du labortoire
	if($idService==1) $labo="EMC";
	elseif($idService==2) $labo="VIB";
	else $labo="VTH";
	
	if(isset($_GET["dateDeb"])) //si on passe des dates en parametres (ex pour le rex) on les prends en comptes
	{
		$dateDeb=$_GET["dateDeb"]; //Date de début
		$dateFin=$_GET["dateFin"]; //Date de fin
		$condDate="and et.dateEtat >='$dateDeb' and et.dateEtat <='$dateFin'";
		$condVar=array('fifo' => $fifo, 'idService' => $idService,'dateDeb' => $dateDeb,'dateFin' => $dateFin);
	}
	else $condVar=array('fifo' => $fifo, 'idService' => $idService);	
	
	//Fifo vaut 2 si l'utilisateur veut la somme de fifo et de non fifo
	if ($fifo == 2){
		
		//On recupere les données des essais (durée attente avant test)
		$str="select distinct(idEssai),et.dateEtat, et.idEtat_etat
		from essai e, etatessai et, ligneproduit
		where et.idEssai_essai=e.idEssai";
		
		if (isset ($_GET['Tous'])){ //Ajout des ligne NULL si TOUS est coché
			
			$str .= " and ((idLigne = ligneProd
			and nomLigne in ($ligne)) or ligneProd is NULL)";
		}else{
			
			$str .= " and idLigne = ligneProd 
			and nomLigne in ($ligne)"; // Sinon pas de NULL
		}
		
		$str .= " and (et.idEtat_etat=22 or idEtat_etat=23)
		and e.idService_service=$idService
		and EXISTS 
			(select idEtat_etat from etatessai et
			where (idEtat_etat=25)
			and idEssai_essai=e.idessai
			)
		$condDate
		order by e.idessai, et.idEtat_etat;";
	
	
	}else {
		
		//on recupere les données des essais (durée attente avant test)
		$str="select distinct(idEssai),et.dateEtat, et.idEtat_etat
		from essai e, etatessai et, ligneproduit
		where et.idEssai_essai=e.idEssai";
		if (isset ($_GET['Tous'])){ //Ajout des ligne NULL si TOUS est coché
			
			$str .= " and ((idLigne = ligneProd
			and nomLigne in ($ligne)) or ligneProd is NULL)";
		}else{
			
			$str .= " and idLigne = ligneProd
			and nomLigne in ($ligne)"; // Sinon pas de NULL
		}
		//Traitement de fifo
		$str .= " and (et.idEtat_etat=22 or idEtat_etat=23)
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
	//MYsql PDO
	$res=$dbh->prepare($str);
	$res->execute($condVar);
	//Booleen permettant de voir si les deix états sont etre les dates demandées. 
	//Dans certain cas il est possible que l'état 23 soit dans l'intervale de date mais pas l'état 22
	$correct = false;
	if(!$res)
		echo "<div class='alert alert-danger'><strong>Erreur de récupération des infos des procédures</strong></div>";
	else{
		//Initialisation des variables
		$j0=0;
		$j1=0;
		$j2_3=0;
		$j4_5=0;
		$jsup6=0;
		$nbjMoy=0; //Moyenne
		$nbTest=0; //Nombre d'essais
		$tab = array(); //Tableau des dates
		$median = array(); //Médian
		$essai = false; //Booléen pour connaître s'il y a des essais
		//Pour chaque essais
		while($lg=$res->fetch(PDO::FETCH_OBJ))
		{
			$dateEtat=$lg->dateEtat; //Date de passage dans l'état
			$idEtat=$lg->idEtat_etat; //Identifiant de l'état
			if($idEtat==23 and $correct == true and $idEs == $lg->idEssai)//dateEtat contient la date de fin d'attente
			{
				array_push($tab,$dateEtat); //Ajout au tableau
				$nbj=(strtotime($dateEtat) - strtotime($datePrecedente))/86400; //Calcul du nombre de jours
				$nbjMoy+=$nbj; //Addition pour le calcul de l amoyenne
				array_push($median,$nbj); //Ajout dans le tableau de la médiane
				$nbj = floor($nbj);
				$nbTest++; //Incrémentation du nombre de test
				if($nbj==0) $j0++;
				elseif($nbj==1) $j1++;
				elseif($nbj <4) $j2_3++;
				elseif($nbj<6) $j4_5++;
				else $jsup6++;
				$correct = false;
				
			}
			else if ($idEtat==22) {//dateEtat contient la date de debut d'attente
				$idEs = $lg->idEssai;
				$correct = true;
				$datePrecedente=$dateEtat; //Changement de la date précédente
			}
			$essai = true;
		}
		sort($median); //Triage du tableau
		$dbh->connection = null;
		//Création du graphe
		$graph = new Graph(1000,800, 'auto');
		if($nbTest!=0) $tmpAttMoy=round($nbjMoy/$nbTest,2); //Temps d'attente moyen 
		else $tmpAttMoy=0;
		//S'il y a des essais
		if ($essai != false)
		{
			$middle = count($median)/2;
			if (is_numeric($middle)) $temps_median = round($median[$middle],2); //Temps d'attente médian
			else  $temps_median = round(($median[ceil($middle)] + $median[floor($middle)]) / 2,2);
		//Sinon 	
		}else 
		{	
			$temps_median = 0; //Mise à zéro
			$j0 = 0; //Mise à zéro
			$j1 = 0; //Mise à zéro
			$j2_3 = 0; //Mise à zéro
			$j4_5 = 0; //Mise à zéro
			$jsup6 = 0; //Mise à zéro
		}
		
		//creation du tableau
		$data1y=array($j0,$j1,$j2_3,$j4_5,$jsup6);
		$graph->SetScale("textlin");

		//Affichage du titre qui convient
		if($fifo==0) $titre="Durée d'attente de l'equipement sur l'étagére $labo avant le test";
		else if ($fifo == 1) $titre="Durée d'attente de l'equipement sur l'étagére $labo FIFO avant le test";
		else $titre="Temps d'attente tout tests confondus avant le test";
		
		if (empty($tab)) $titre="AUCUNE DONNEE";
		$titre .= "\n\nÉdité le ".$date_act;

		$graph = afficheGraphe($graph, array('0 jour','1 jour','2 à 3 jours','4 à 5 jours','>5 jours'), $titre);

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
		$graph->legend->SetFrameWeight(1);
		$graph->legend->SetColumns(6);
		$graph->legend->SetColor('#4E4E4E','black');
		$graph->legend->SetLayout(LEGEND_VERT);
		$graph->legend->SetFont(FF_ARIAL,FS_NORMAL,11);

		$b1plot->SetLegend('Test terminés');
		$b1plot->SetColor("white");
		$b1plot->SetFillColor("#6699FF");
		$b1plot->value->Show();
		$b1plot->value->SetFormat('%d');

		//Ajout du temps d'attente moyen
		$txt=new Text("Temps d'attente moyen: ".$tmpAttMoy."j");
		$txt->SetPos(200,700);
		$txt->SetColor('#4E4E4E','black');
		$txt->SetFont(FF_ARIAL,FS_NORMAL,11);
		//Ajout du temps d'attente médian
		$txt2=new Text("Temps d'attente médian: ".$temps_median."j");
		$txt2->SetPos(600,700);
		$txt2->SetColor('#4E4E4E','black');
		$txt2->SetFont(FF_ARIAL,FS_NORMAL,11);
		//Ajout des texts
		$graph->AddText($txt);
		$graph->AddText($txt2);
		
		// Display the graph
		$graph->Stroke();
	}
}
