<?php 
if(!isset($_GET["idService"]) || !isset($_GET["target"]))
	echo "<div class='alert alert-danger'><strong>Erreur de récupération du service et de la valeur de la target</strong></div>";
else
{
	require('../conf/connexionPDO_param.php');// connexion a la base
	require ('jpgraph/jpgraph.php');
	require ('jpgraph/jpgraph_bar.php');
	require ('jpgraph/jpgraph_line.php');
	//Permet de stocker une chaine de caractère contenant les lignes de produit
	$ligne = "";
	//Valeur de la target
	$val_target = $_GET["target"];
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
	
	$mois_lettre = array("","Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");
	$annee_en_cours = explode("-", $_GET["dateDeb"])[0];
	$condVar=array('idService' => $idService);		
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
	
	$tab_median = array();
	$tab_moyenne = array();
	$target=array();
	$legende = array();
	
	for ($i=0; $i<$nb_col; $i++){
		
		if ($i == 0){
			
			$annee_prec = $annee_en_cours - 1 ;
			$dateDeb = $annee_prec."-01-01 00:00:00";
			$dateFin = $annee_prec."-12-31 23:59:59";
			array_push($legende, $annee_prec);
			
		}else if ($i == 1){
			
			$dateDeb = $annee_en_cours."-01-01 00:00:00";
			$dateFin = $annee_en_cours."-12-31 23:59:59";
			array_push($legende, $annee_en_cours);
			
		}else{
			
			$mois += 1;
			$nb_jour = cal_days_in_month (CAL_GREGORIAN, $mois, $annee_en_cours);
			if ($mois < 10){
				
				$dateDeb = $annee_en_cours."-0".$mois."-01";
				$dateFin = $annee_en_cours."-0".$mois."-".$nb_jour;
				
			}else {
				
				$dateDeb = $annee_en_cours."-".$mois."-01";
				$dateFin = $annee_en_cours."-".$mois."-".$nb_jour;
				
			}

			if (isset($_GET["public"]))
			{
				$mois_limite = explode("-", $_GET["dateFin"])[1];
				$mois_limite = intval($mois_limite);
				if ($mois == $mois_limite) $dateFin = explode(" ", $_GET["dateFin"])[0];
				elseif ($mois > $mois_limite) break;
			}
			array_push($legende, $mois_lettre[$mois]);
			
		}
		
		$condDate="and et.dateEtat >='$dateDeb' and et.dateEtat <='$dateFin'";
		//on recupere les données des essais (durée nattente avant test)
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
			where (idEtat_etat=25)
			and idEssai_essai=e.idessai
			)
		$condDate
		order by e.idessai, et.idEtat_etat;";

		$res=$dbh->prepare($str);
		$res->execute($condVar);
		
		$median = array();
		$essai = false;
		$nbjMoy=0;
		$nbTest=0;

		while($lg=$res->fetch(PDO::FETCH_OBJ))
		{
			$dateEtat=$lg->dateEtat;
			$idEtat=$lg->idEtat_etat;
			
			if($idEtat==25 and $correct == true and $idEs == $lg->idEssai)//dateEtat contient la date de fin d'attente
			{
				$nbj=(strtotime($dateEtat) - strtotime($datePrecedente))/86400;
				array_push($median,$nbj);
				$nbTest++;
				$nbjMoy+=$nbj;
				$correct = false;
				
			}
			else if ($idEtat==24) {//dateEtat contient la date de debut d'attente
				
				$idEs = $lg->idEssai;
				$correct = true;
				$datePrecedente=$dateEtat;
				
			}
			$essai = true;
			
		}
		
		if($nbTest!=0) $tmpAttMoy=round($nbjMoy/$nbTest,2);
		else $tmpAttMoy='';
		
		sort($median);
		
		if ($essai != false){
			
			$middle = count($median)/2;
			if (is_numeric($middle)){
				
				if (isset($median[$middle])) $temps_median = round($median[$middle],2);
				
			}else $temps_median = round(($median[ceil($middle)] + $median[floor($middle)]) / 2,2);
			
		}else $temps_median = '';
		
		array_push($tab_median, $temps_median);
		array_push($tab_moyenne, $tmpAttMoy);
		array_push($target,$val_target);

	}
	
	array_push($target,$val_target);
	
	$nb_col -= 1;
	while ($nb_col <= 12 ){
		
		array_push($tab_median, '');
		array_push($tab_moyenne, '');
		array_push($target,$val_target);
		array_push($legende, $mois_lettre[$nb_col]);
		$nb_col += 1;
		
	}
	
	$dbh->connection = null;
	
	//Booleen permettant de voir si les deux états sont etre les dates demandées. 
	//Dans certain cas il est possible que l'état 23 soit dans l'intervale de date mais pas l'état 22
	$correct = false;
	if(!$res)
		
		echo "<div class='alert alert-danger'><strong>Erreur de récupération des infos des procédures</strong></div>";
		
	else{

		if (isset($_GET["public"])) $graph = new Graph(1000,400, 'auto');	
		else $graph = new Graph(1000,800, 'auto');
		
		$graph->SetScale("textlin");
		//creation du tableau
		$data1y=$tab_median;
		$data2y=$tab_moyenne;

		$theme_class=new UniversalTheme;
		$graph->SetTheme($theme_class);
		$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,9);
		$graph->yaxis->title->SetFont(FF_ARIAL,FS_BOLD,12);
		$graph->title->SetFont(FF_ARIAL,FS_BOLD, 16);

		$titre="Temps d'attente après le test (Jours)";
		
		$graph->title->Set($titre."\n\nÉdité le ".$date_act);
		$graph->SetBox(false);

		$graph->ygrid->SetFill(false);
		$graph->xaxis->SetTickLabels($legende);

		$graph->yaxis->HideLine(false);
		$graph->yaxis->HideTicks(false,false);
		$graph->yaxis->SetLabelAlign('right','center');

		$group = array();
		//Creation des barPlot
		if (isset($_GET["median"])){
			
			$b1plot = new BarPlot($data1y);
			array_push($group,$b1plot);
			//ajout de la legende
			$b1plot->SetLegend('Temps d\'attente médian');
			$b1plot->SetColor("white");
			$b1plot->SetFillColor("#00CC33");

			$b1plot->value->SetFormat("%01.2f");
		}
		
		if (isset($_GET["moyenne"])){
			
			$b2plot = new BarPlot($data2y);
			array_push($group,$b2plot);
			//ajout de la legende
			$b2plot->SetLegend('Temps d\'attente moyen');
			$b2plot->SetColor("white");
			$b2plot->SetFillColor("#6699FF");
			$b2plot->value->Show();
			$b2plot->value->SetFormat("%01.2f");
		}
		
		$lplot = new LinePlot($target);
		
		//Creation du groupe de barPlot
		$gbplot = new GroupBarPlot($group);
		if (isset($_GET["public"])) $gbplot->SetWidth(0.9);
		
		//ajout au graph
		$graph->Add($gbplot);
		$graph->Add($lplot);
		
		if (isset($_GET["median"])){
			
			$b1plot->SetColor("white");
			$b1plot->SetFillColor("#00CC33");
			$b1plot->value->SetAlign('center', 'center');
			$b1plot->value->Show();
			
			$b1plot->value->SetFormat("%01.1f");
		}
		
		if (isset($_GET["moyenne"])){

			$b2plot->SetColor("white");
			$b2plot->SetFillColor("#6699FF");
			$b2plot->value->Show();
			$b2plot->value->SetFormat("%01.1f");
		}
		
		$graph->legend->SetFrameWeight(1);
		$graph->legend->SetColumns(6);
		$graph->legend->SetColor('#4E4E4E','black');
		$graph->legend->SetLayout(LEGEND_VERT);
		$graph->legend->SetFont(FF_ARIAL,FS_NORMAL,11);
		$graph->xaxis->SetLabelAngle(30);

		$lplot->SetLegend('Target');
		$lplot->SetWeight(5);
		$lplot->SetColor("#FF0000");

		// Display the graph
		$graph->Stroke();
	}
}
