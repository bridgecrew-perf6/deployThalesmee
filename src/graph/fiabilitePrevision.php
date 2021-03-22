<?php 
/*
Ce fichier permet de génerer le graphique de fiabilité prévisions. Toutes les données récupérées sont saisies par l'utilisateur dans la page 
plannification.php
@param GET
idService
dateDeb
dateFin
*/
if(!isset($_GET["idService"]))
	echo "<div class='alert alert-danger'><strong>Erreur de récupération du service et du parametre fifo</strong></div>";
else
{
	require('../conf/connexionPDO_param.php');// connexion a la base
	require('../conf/connexion_param.php');// connexion a la base
	require ('jpgraph/jpgraph.php');
	require ('jpgraph/jpgraph_bar.php');
	require ('jpgraph/jpgraph_line.php');
	
	$condDate="";
	$idService=$_GET["idService"];
	
	//Récuperation du service
	if($idService==1) $labo="EMC";
	elseif($idService==2) $labo="VIB";
	else $labo="VTH";

	//Date de début date (date actuelle)
	$date_act = strftime("%d/%m/%Y", mktime(0,0,0,date('m'), date('d'), date('Y')));
	
	//Tableau pour l'affichage des mois en lettres
	$mois_lettre = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");
	$annee_en_cours = explode("-", $_GET["dateDeb"])[0];
	$annee_actuel = date("Y");
	if ($annee_en_cours < $annee_actuel){
		
		$nb_col = 12;
	}else {
		
		$nb_col = date("n") ;
	}
	
	//Initialisation des variables
	$res = array();
	$tab_annee_prec = array();
	$tab_annee_prec_vu = array();
	$tab_annee_en_cours = array();
	$tab_annee_en_cours_vu = array();
	$legende = array();
	$annee_prec = $annee_en_cours - 1;
	
	/////Année précedente/////
	//Sélection des informations a partir de la table plannification : cette table est remplie a l'aide du script plannification.php
	$str = "SELECT * FROM plannification WHERE annee = $annee_prec";
	$req = mysqli_query($bdd, $str);
	if (mysqli_num_rows($req) == 0){
		
		array_push($res, '');
	}else {
		
		//Sélection des informations a partir de la table plannification_vu : cette table est remplie a l'aide du script plannification.php
		$str_vu = "SELECT * FROM plannification_vu WHERE annee = $annee_prec";
		$req_vu = mysqli_query($bdd, $str_vu);
		
		if (mysqli_num_rows($req_vu) == 0){
		
			array_push($res, '');
			
		}else {
			
			$lg = mysqli_fetch_object ($req);
			array_push ($tab_annee_prec, $lg->janvier);
			array_push ($tab_annee_prec, $lg->fevrier);
			array_push ($tab_annee_prec, $lg->mars);
			array_push ($tab_annee_prec, $lg->avril);
			array_push ($tab_annee_prec, $lg->mai);
			array_push ($tab_annee_prec, $lg->juin);
			array_push ($tab_annee_prec, $lg->juillet);
			array_push ($tab_annee_prec, $lg->aout);
			array_push ($tab_annee_prec, $lg->septembre);
			array_push ($tab_annee_prec, $lg->octobre);
			array_push ($tab_annee_prec, $lg->novembre);
			array_push ($tab_annee_prec, $lg->decembre);
			
			$lg_vu = mysqli_fetch_object ($req_vu);
			array_push ($tab_annee_prec_vu, $lg_vu->janvier);
			array_push ($tab_annee_prec_vu, $lg_vu->fevrier);
			array_push ($tab_annee_prec_vu, $lg_vu->mars);
			array_push ($tab_annee_prec_vu, $lg_vu->avril);
			array_push ($tab_annee_prec_vu, $lg_vu->mai);
			array_push ($tab_annee_prec_vu, $lg_vu->juin);
			array_push ($tab_annee_prec_vu, $lg_vu->juillet);
			array_push ($tab_annee_prec_vu, $lg_vu->aout);
			array_push ($tab_annee_prec_vu, $lg_vu->septembre);
			array_push ($tab_annee_prec_vu, $lg_vu->octobre);
			array_push ($tab_annee_prec_vu, $lg_vu->novembre);
			array_push ($tab_annee_prec_vu, $lg_vu->decembre);
			
			array_push($res, (array_sum($tab_annee_prec_vu)/array_sum($tab_annee_prec))*100);
		
		}
		
	}
	//Ajout dans le tableau de la légende
	array_push($legende, $annee_prec);
	
	/////Année en cours/////
	//Sélection des informations a partir de la table plannification : cette table est remplie a l'aide du script plannification.php
	$str = "SELECT * FROM plannification WHERE annee = $annee_en_cours";
	$req = mysqli_query($bdd, $str);
	if (mysqli_num_rows($req) == 0){
		
		array_push($res, '');
	}else {
		
		//Sélection des informations a partir de la table plannification_vu : cette table est remplie a l'aide du script plannification.php
		$str_vu = "SELECT * FROM plannification_vu WHERE annee = $annee_en_cours";
		$req_vu = mysqli_query($bdd, $str_vu);
		
		if (mysqli_num_rows($req_vu) == 0){
		
			array_push($res, '');
			
		}else {
			
			$lg = mysqli_fetch_object ($req);
			array_push ($tab_annee_en_cours, $lg->janvier);
			array_push ($tab_annee_en_cours, $lg->fevrier);
			array_push ($tab_annee_en_cours, $lg->mars);
			array_push ($tab_annee_en_cours, $lg->avril);
			array_push ($tab_annee_en_cours, $lg->mai);
			array_push ($tab_annee_en_cours, $lg->juin);
			array_push ($tab_annee_en_cours, $lg->juillet);
			array_push ($tab_annee_en_cours, $lg->aout);
			array_push ($tab_annee_en_cours, $lg->septembre);
			array_push ($tab_annee_en_cours, $lg->octobre);
			array_push ($tab_annee_en_cours, $lg->novembre);
			array_push ($tab_annee_en_cours, $lg->decembre);
			
			$lg_vu = mysqli_fetch_object ($req_vu);
			array_push ($tab_annee_en_cours_vu, $lg_vu->janvier);
			array_push ($tab_annee_en_cours_vu, $lg_vu->fevrier);
			array_push ($tab_annee_en_cours_vu, $lg_vu->mars);
			array_push ($tab_annee_en_cours_vu, $lg_vu->avril);
			array_push ($tab_annee_en_cours_vu, $lg_vu->mai);
			array_push ($tab_annee_en_cours_vu, $lg_vu->juin);
			array_push ($tab_annee_en_cours_vu, $lg_vu->juillet);
			array_push ($tab_annee_en_cours_vu, $lg_vu->aout);
			array_push ($tab_annee_en_cours_vu, $lg_vu->septembre);
			array_push ($tab_annee_en_cours_vu, $lg_vu->octobre);
			array_push ($tab_annee_en_cours_vu, $lg_vu->novembre);
			array_push ($tab_annee_en_cours_vu, $lg_vu->decembre);
			
			array_push($res, (array_sum($tab_annee_en_cours_vu)/array_sum($tab_annee_en_cours))*100);
		
		}
		
	}
	
	//Ajout dans le tableau de la légende
	array_push($legende, $annee_en_cours);
	
	//Boucle permettant d'ajouter les informations dans le tableau de résultat pour les mois
	for ($i=0; $i<$nb_col; $i++){
		
		if (isset($tab_annee_en_cours[$i]) && isset ($tab_annee_en_cours_vu[$i])){
			
			array_push($res, ($tab_annee_en_cours_vu[$i]/$tab_annee_en_cours[$i])*100);
			array_push($legende, $mois_lettre[$i]);

		}else {
			array_push($res, '');
			//Ajout dans le tableau de la légende
			array_push($legende, $mois_lettre[$i]);
		}
	}
	
	while ($nb_col < 12 ){
		
		array_push($res, '');
		//Ajout dans le tableau de la légende
		array_push($legende, $mois_lettre[$nb_col]);
		$nb_col += 1;
		
	}		
	//Vérification de la requete
	if(!$req)
		
		echo "<div class='alert alert-danger'><strong>Erreur de récupération des infos</strong></div>";
		
	else{
		
		//Configuration du graphique
		$graph = new Graph(1000,800, 'auto');
		
		$graph->SetScale("textlin");
		//creation du tableau
		$data1y=$res;

		$theme_class=new UniversalTheme;
		$graph->SetTheme($theme_class);
		$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,9);
		$graph->yaxis->title->SetFont(FF_ARIAL,FS_BOLD,12);
		$graph->title->SetFont(FF_ARIAL,FS_BOLD, 16);
		
		$graph->title->Set("Efficience plannification (%)\n\nÉdité le ".$date_act);
		$graph->SetBox(false);

		$graph->ygrid->SetFill(false);
		$graph->xaxis->SetTickLabels($legende);

		$graph->yaxis->HideLine(false);
		$graph->yaxis->HideTicks(false,false);

		//Creation des barPlot
		$b1plot = new BarPlot($data1y);

		//ajout de la legende
		$b1plot->SetLegend('ratio (%)');
		$b1plot->SetColor("white");
		$b1plot->SetFillColor("#6699FF");
		$b1plot->value->Show();

		//ajout au graph
		$graph->Add($b1plot);

		$b1plot->SetColor("white");
		$b1plot->SetFillColor("#6699FF");
		$b1plot->value->Show();
		$b1plot->value->SetFormat("%d");
				
		$graph->legend->SetFrameWeight(1);
		$graph->legend->SetColumns(6);
		$graph->legend->SetColor('#4E4E4E','black');
		$graph->legend->SetLayout(LEGEND_VERT);
		$graph->legend->SetFont(FF_ARIAL,FS_NORMAL,11);

		// Display the graph
		$graph->Stroke();
	}
}
