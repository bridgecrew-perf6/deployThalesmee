<?php 
if(!isset($_GET["idService"]) || !isset($_GET["target"]))
	echo "<div class='alert alert-danger'><strong>Erreur de récupération du service et de la valeur de la target</strong></div>";
else
{
	require('../conf/connexionPDO_param.php');// connexion a la base
	require ('jpgraph/jpgraph.php');
	require ('jpgraph/jpgraph_bar.php');
	require ('jpgraph/jpgraph_line.php');
	require ('../fonction.php');
	
	//Permet de stocker une chaine de caractère contenant les lignes de produit
	$ligne = "";
	//Variable permettant de stocker la condition de filter par rapport à la date
	$condDate="";
	//Numéro du service
	$idService=$_GET["idService"];
	//Valeur de la target
	$val_target = $_GET["target"];
	//Date du jour (date actuelle)
	$date_act = strftime("%d/%m/%Y", mktime(0,0,0,date('m'), date('d'), date('Y')));

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

	//Création d'un tableau des mois permettant de trouver la chaine de caractère grâce a un indice
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
	
	$tab = array();
	$target=array();
	$legende = array();
	$correct = false;
	
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
		
		$condDate="and e.date_debut >='$dateDeb' and e.date_fin <='$dateFin'";
		//on recupere les données des essais (durée nattente avant test)
		$str="SELECT DISTINCT(idEssai), e.date_debut, e.date_fin, e.date_debut_prevu, e.date_fin_prevu, e.duree_planifie, e.duree_actuelle
		from essai e, etatessai et, ligneproduit
		where et.idEssai_essai=e.idEssai";
		
		if (isset ($_GET['Tous'])){
			
			$str .= " and ((idLigne = ligneProd
			and nomLigne in ($ligne)) or ligneProd is NULL)";
		}else{
			
			$str .= " and idLigne = ligneProd
			and nomLigne in ($ligne)";
		}
		
		$str .= " and e.idService_service=$idService
		and EXISTS 
			(SELECT idEtat_etat from etatessai et
			where (idEtat_etat=24)
			and idEssai_essai=e.idessai
			)
		$condDate
		order by e.idessai, et.idEtat_etat;";


		$res=$dbh->prepare($str);
		$res->execute($condVar);
		
		$planifie = 0;
		$actuelle = 0;	
		$duree_planifie = 0;
		$duree_actuelle = 0;

		while($lg=$res->fetch(PDO::FETCH_OBJ))
		{
			$duree_planifie = $lg->duree_planifie;
			if ($duree_planifie == 0 ) $duree_planifie = dureePrimavera($lg->date_debut_prevu, $lg->date_fin_prevu);

			$duree_actuelle = $lg->duree_actuelle;
			if ($duree_actuelle == 0) $duree_actuelle = dureePrimavera($lg->date_debut, $lg->date_fin);

			$actuelle += $duree_actuelle;
			$planifie += $duree_planifie;
	
		}

		if ($actuelle == 0 || $planifie == 0) array_push($tab, '');
		else {
			$retard = $planifie / $actuelle * 100;
			array_push($tab, $retard);
		}
		array_push($target,$val_target);
		

	}
	
	array_push($target,$val_target);
	
	$nb_col -= 1;
	while ($nb_col <= 12 ){
		
		array_push($tab, '');
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
		$data1y=$tab;

		$theme_class=new UniversalTheme;
		$graph->SetTheme($theme_class);
		$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,9);
		$graph->yaxis->title->SetFont(FF_ARIAL,FS_BOLD,12);
		$graph->title->SetFont(FF_ARIAL,FS_BOLD, 16);

		$titre="Efficacité (tenue des cycles)";
		
		$graph->title->Set($titre."\n\nÉdité le ".$date_act);
		$graph->SetBox(false);

		$graph->ygrid->SetFill(false);
		$graph->xaxis->SetTickLabels($legende);

		$graph->yaxis->HideLine(false);
		$graph->yaxis->HideTicks(false,false);
		
		$group = array();

		$b1plot = new BarPlot($data1y);
		array_push($group,$b1plot);
		
		//Ligne rouge d'indication de "limite"
		$lplot = new LinePlot($target);
		
		//Creation du groupe de barPlot
		$gbplot = new GroupBarPlot($group);
		if (isset($_GET["public"])) $gbplot->SetWidth(0.9);

		//ajout au graph
		$graph->Add($gbplot);
		$graph->Add($lplot);	
		
		$b1plot->SetColor("white");
		$b1plot->SetFillColor("#00CC33");
		$b1plot->value->Show();
		$b1plot->value->SetFormat("%01.1f");
		$b1plot->SetLegend('Durée théorique / Durée réalisée');
				
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