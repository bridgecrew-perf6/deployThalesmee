<?php 

if(!isset($_GET["idService"]) || !isset($_GET["target"]))
	echo "<div class='alert alert-danger'><strong>Erreur de récupération du service et du parametre fifo</strong></div>";
else
{
	require('../conf/connexion_param.php');// connexion a la base
	require ('jpgraph/jpgraph.php');
	require ('jpgraph/jpgraph_bar.php');
	require ('jpgraph/jpgraph_line.php');
	require('fonction.php');

	//Date du jour (date actuelle)
	$date_act = strftime("%d/%m/%Y", mktime(0,0,0,date('m'), date('d'), date('Y')));
	
	$idService=$_GET["idService"];
	
	$str ="SELECT nomMoyen FROM moyen WHERE idService_SERVICE = $idService";
	$req = mysqli_query($bdd, $str);
	$moyen = "";
	
	while ($lg=mysqli_fetch_object($req)){
		
		$moy = str_replace(" ", "-", $lg->nomMoyen);
		if (isset ($_GET[$moy])){
		
			$moyen .= "'".$lg->nomMoyen."',";
		}
	}
	
	//On enlève le dernier caractère qui est une virgule
	$moyen = substr($moyen,0,-1);
	
	$val_target = $_GET["target"];
		
	//Création d'un tableau des mois permettant de trouver la chaine de caractère grâce a un indice
	$mois_lettre = array("","Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");
	
	$annee_en_cours = explode("-", $_GET["dateDeb"])[0];	
	$mois = 0;
	$annee_actuel = date("Y");
	$nb_col = 14;

	
	$target=array();
	$legende = array();
	$correct = false;
	$data = array();
	
	for ($i=0; $i<$nb_col; $i++){
		
		$resultat = array();
		if ($i == 0){
			
			$annee_prec = $annee_en_cours - 1 ;
			$date_deb = $annee_prec."-01-01 00:00:00";
			$date_fin = $annee_prec."-12-31 23:59:59";
			array_push($legende, $annee_prec);
			
		}else if ($i == 1){
			
			$date_deb = $annee_en_cours."-01-01 00:00:00";
			$date_fin = $annee_en_cours."-12-31 23:59:59";
			array_push($legende, $annee_en_cours);
			
		}else{
			
			$mois += 1;
			$nb_jour = cal_days_in_month (CAL_GREGORIAN, $mois, $annee_en_cours);
			if ($mois < 10){
				
				$date_deb = $annee_en_cours."-0".$mois."-01 00:00:00";
				$date_fin = $annee_en_cours."-0".$mois."-".$nb_jour." 23:59:00";
				
			}else {
				
				$date_deb = $annee_en_cours."-".$mois."-01 00:00:00";
				$date_fin = $annee_en_cours."-".$mois."-".$nb_jour." 23:59:00";
				
			}
			array_push($legende, $mois_lettre[$mois]);
			
		}
		
		$str = "SELECT distinct(`idEssai`), nomMoyen, `date_debut`,`date_fin` FROM etatessai et, `essai` e, moyen WHERE (date_fin <= '$date_fin' and  date_fin >= '$date_deb' or date_debut >= '$date_deb' and date_debut <= '$date_fin' ) and nomMoyen in ($moyen) and date_debut < date_fin and idMoyen_MOYEN = idMoyen and e.idService_SERVICE = $idService and et.idEssai_ESSAI = idEssai and idEtat_ETAT = 23 ORDER BY nomMoyen, date_debut";
		$req = mysqli_query($bdd,  $str);

		$nomMoyen = "";
		$resultat = array();
		$prems = true;
		$first = false;
		$total = nb_demijour_ouvre ($date_deb,$date_fin,false);
		$dateFin = explode(" ", $date_fin);
		$jour_dateFin = $dateFin[0];
		$date_dateFin = explode ("-", $jour_dateFin);
		$horaire_dateFin = $dateFin[1];
		$heure_dateFin = explode (":", $horaire_dateFin);
		$timestamp_fin = mktime ($heure_dateFin[0],$heure_dateFin[1],0,$date_dateFin[1],$date_dateFin[2],$date_dateFin[0]);
		$date_fin_set = $timestamp_fin;

		$dateDeb = explode(" ", $date_deb);
		$jour_dateDeb = $dateDeb[0];
		$date_dateDeb = explode ("-", $jour_dateDeb);
		$horaire_dateDeb = $dateDeb[1];
		$heure_dateDeb = explode (":", $horaire_dateDeb);
		$timestamp_fin = mktime ($heure_dateDeb[0],$heure_dateDeb[1],0,$date_dateDeb[1],$date_dateDeb[2],$date_dateDeb[0]);
		$date_debut_set = $timestamp_fin;

		//echo nb_demijour_ouvre ("2019-08-08 11:08:00","2019-08-09 11:52:00",true)."<br>";
		//Boucle permettant de parcourir le résultat de la requête
		while ($lg = mysqli_fetch_object($req)){
			
			if ($lg->nomMoyen != $nomMoyen){
				
				$res[$lg->nomMoyen] = array();
				$first = true;
				if (!$prems) {
					
					$resultat[$nomMoyen] = array_sum($res[$nomMoyen])/$total*100;
				}
					
				$prems = false;
				$nomMoyen = $lg->nomMoyen;
				$dateDeb = explode(" ", $date_deb);
				$jour_dateDeb = $dateDeb[0];
				$date_dateDeb = explode ("-", $jour_dateDeb);
				$horaire_dateDeb = $dateDeb[1];
				$heure_dateDeb = explode (":", $horaire_dateDeb);
				$date_prec = $date_dateDeb;
				$horaire_prec = $heure_dateDeb;
				$timestamp_fin_sansHeure = mktime (0,0,0,$date_dateDeb[1],$date_dateDeb[2],$date_dateDeb[0]);
				$timestamp_fin = mktime ($heure_dateDeb[0],$heure_dateDeb[1],0,$date_dateDeb[1],$date_dateDeb[2],$date_dateDeb[0]);
				//echo $nomMoyen."<br>";
				
				
				
			}else $first = false;
						
			$dateCours = explode(" ", $lg->date_debut);
			$jour_dateCours = $dateCours[0];
			$date_dateCours = explode ("-", $jour_dateCours);
			$horaire_dateCours = $dateCours[1];
			$heureDeb_dateCours = explode (":", $horaire_dateCours);
			$timestamp_cours_deb_sansHeure = mktime (0,0,0,$date_dateCours[1],$date_dateCours[2],$date_dateCours[0]);
			$timestamp_cours_deb = mktime ($heureDeb_dateCours[0],$heureDeb_dateCours[1],0,$date_dateCours[1],$date_dateCours[2],$date_dateCours[0]);
			
			$dateCours = explode(" ", $lg->date_fin);
			$jour_dateCours = $dateCours[0];
			$date_dateCours = explode ("-", $jour_dateCours);
			$horaire_dateCours = $dateCours[1];
			$heure_dateCours = explode (":", $horaire_dateCours);
			$timestamp_cours_fin_sans_heure = mktime (0,0,0,$date_dateCours[1],$date_dateCours[2],$date_dateCours[0]);
			$timestamp_cours_fin = mktime ($heure_dateCours[0],$heure_dateCours[1],0,$date_dateCours[1],$date_dateCours[2],$date_dateCours[0]);
			
			$debut = $lg->date_debut;
			$fin = $lg->date_fin;
			if ($date_debut_set > $timestamp_cours_deb){

				$debut = date("Y-m-d H:i", strtotime($date_dateDeb[0]."-".$date_dateDeb[1]."-".$date_dateDeb[2]." ".$heure_dateDeb[0].":".$heure_dateDeb[1]));
				$dateCours = explode(" ", $debut);
				$jour_dateCours = $dateCours[0];
				$date_dateCours = explode ("-", $jour_dateCours);
				$horaire_dateCours = $dateCours[1];
				$heureDeb_dateCours = explode (":", $horaire_dateCours);
				$timestamp_cours_deb_sansHeure = mktime (0,0,0,$date_dateCours[1],$date_dateCours[2],$date_dateCours[0]);
				$timestamp_cours_deb = mktime ($heureDeb_dateCours[0],$heureDeb_dateCours[1],0,$date_dateCours[1],$date_dateCours[2],$date_dateCours[0]);
				
			}
			
			//echo "Fin date : ".$date_fin_set."<br>";
			//echo "Fin du test : ".$timestamp_cours_deb."<br>";
			if ($date_fin_set < $timestamp_cours_fin){
				
				$fin= date("Y-m-d H:i", strtotime($date_dateFin[0]."-".$date_dateFin[1]."-".$date_dateFin[2]." ".$heure_dateFin[0].":".$heure_dateFin[1]));
				
			}
			
			if ($date_debut_set == $timestamp_cours_deb ){
				
				//echo nb_demijour_ouvre($debut, $fin, false)."<br>";
				array_push ($res[$lg->nomMoyen], nb_demijour_ouvre($debut, $fin, false));
				$date_prec = $date_dateCours;
				$horaire_prec = $heure_dateCours;
				$timestamp_fin_sansHeure = $timestamp_cours_fin_sans_heure;
				$timestamp_fin = $timestamp_cours_fin;
				
			}else if ($timestamp_fin_sansHeure == $timestamp_cours_deb_sansHeure && $horaire_prec[0] < 13 && $heureDeb_dateCours[0] < 13 && $first == false){
				
				//echo  nb_demijour_ouvre($debut, $fin, true)."<br>";
				array_push ($res[$lg->nomMoyen], nb_demijour_ouvre($debut, $fin, true));
				$date_prec = $date_dateCours;
				$horaire_prec = $heure_dateCours;
				$timestamp_fin_sansHeure = $timestamp_cours_fin_sans_heure;
				$timestamp_fin = $timestamp_cours_fin;
				
			}else if ($timestamp_fin_sansHeure == $timestamp_cours_deb_sansHeure && $horaire_prec[0] > 13 && $heureDeb_dateCours[0] > 13){
				
				//echo nb_demijour_ouvre($debut, $fin, true)."<br>";
				array_push ($res[$lg->nomMoyen], nb_demijour_ouvre($debut, $fin, true));
				$date_prec = $date_dateCours;
				$horaire_prec = $heure_dateCours;
				$timestamp_fin_sansHeure = $timestamp_cours_fin_sans_heure;
				$timestamp_fin = $timestamp_cours_fin;
				
			}else if ($timestamp_fin <= $timestamp_cours_deb ){
				
				//echo nb_demijour_ouvre($debut, $fin, false)."<br>";
				array_push ($res[$lg->nomMoyen], nb_demijour_ouvre($debut, $fin, false));
				$date_prec = $date_dateCours;
				$horaire_prec = $heure_dateCours;
				$timestamp_fin_sansHeure = $timestamp_cours_fin_sans_heure;
				$timestamp_fin = $timestamp_cours_fin;

			}else if ($timestamp_cours_deb <= $timestamp_fin && $timestamp_cours_fin > $timestamp_fin){
						
				if ($horaire_prec[0] == "13" && $horaire_prec[1] == "00"){
					
					//echo nb_demijour_ouvre(date("Y-m-d H:i", strtotime($date_prec[0]."-".$date_prec[1]."-".$date_prec[2]." ".$horaire_prec[0].":".$horaire_prec[1]." -1 hour")), $fin, true)."<br>";
					array_push ($res[$lg->nomMoyen], nb_demijour_ouvre(date("Y-m-d H:i", strtotime($date_prec[0]."-".$date_prec[1]."-".$date_prec[2]." ".$horaire_prec[0].":".$horaire_prec[1]." -1 hour")), $fin, true));
					
				}else {
					
					//echo nb_demijour_ouvre(date("Y-m-d H:i", strtotime($date_prec[0]."-".$date_prec[1]."-".$date_prec[2]." ".$horaire_prec[0].":".$horaire_prec[1])), $fin, true)."<br>";
					array_push ($res[$lg->nomMoyen], nb_demijour_ouvre(date("Y-m-d H:i", strtotime($date_prec[0]."-".$date_prec[1]."-".$date_prec[2]." ".$horaire_prec[0].":".$horaire_prec[1])), $fin, true));
					
				}
				
				$date_prec = $date_dateCours;
				$horaire_prec = $heure_dateCours;
				$timestamp_fin_sansHeure = $timestamp_cours_fin_sans_heure;
				$timestamp_fin = $timestamp_cours_fin;
			}
		}
		
		if (isset($res[$nomMoyen])) $resultat[$nomMoyen] = array_sum($res[$nomMoyen])/$total*100;
		else $resultat[$nomMoyen] = 0;

		array_push ($data, $resultat);
		array_push($target,$val_target);
	}
	
	array_push($target,$val_target);

		
	//Booleen permettant de voir si les deux états sont etre les dates demandées. 
	//Dans certain cas il est possible que l'état 23 soit dans l'intervale de date mais pas l'état 22
	$correct = false;
	if(!$res)
		echo "<div class='alert alert-danger'><strong>Erreur de récupération des infos des procédures</strong></div>";
	else{

		//Initialisation du graphe
		$graph = new Graph(1000,800, 'auto');	
		$graph->SetScale("textlin");
		$theme_class=new UniversalTheme;
		$graph->SetTheme($theme_class);
		$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,9);
		$graph->yaxis->title->SetFont(FF_ARIAL,FS_BOLD,12);
		$graph->title->SetFont(FF_ARIAL,FS_BOLD, 16);

		$titre="Taux d'occupation des moyens (%)";
		$graph->title->Set($titre."\n\nÉdité le ".$date_act);

		$graph->SetBox(false);

		$graph->ygrid->SetFill(false);
		$graph->xaxis->SetTickLabels($legende);
		$graph->yaxis->HideLine(false);
		$graph->yaxis->HideTicks(false,false);
		$graph->yaxis->SetLabelAlign('right','center');
		
		$group = array();
		$tab = array ();

		$str ="SELECT nomMoyen FROM moyen WHERE idService_SERVICE = $idService";
		$req = mysqli_query($bdd, $str);
		$tabMoyen = array();
		while ($lg=mysqli_fetch_object($req)){
			
			$moy = str_replace(" ", "-", $lg->nomMoyen);
			if (isset($_GET[$moy])){
				
				array_push($tabMoyen, $lg->nomMoyen);
				$tab[$lg->nomMoyen] = array();
				
			}
		}
		
		//Parcours des différente périodes
		for ($i=0; $i<$nb_col; $i++){
			//Pour chaques moyens dans la base de données
			for ($moyen = 0; $moyen<count($tabMoyen); $moyen++){
				$find = false;
				//Si data[$i] existe (si le mois en cours est inférieur à décembre)
				if (isset($data[$i])){			
					//Pour chaque moyen de l'année
					foreach ($data[$i] as $key => $value){
						//Si il est égale au moyen dans la base de données
						if ($key == $tabMoyen[$moyen]){
							//On l'ajoute dans le tableau
							array_push($tab[$tabMoyen[$moyen]],$value);
							//On en a trouvé un
							$find = true;
						}
					}
				}
				//Replace l'espace par un tiret car la passage en GET ne prend pas en compte les espaces
				$moy = str_replace(" ", "-", $tabMoyen[$moyen]);
				//Si aucun moyen  n'a été trouvé 
				if (!$find && isset($_GET[$moy])){
					//Ajout d'un 0 pour compléter le graphe
					array_push($tab[$tabMoyen[$moyen]],0);
				}
			}				
		}

		//changer les couleurs
		$tab_couleur = array("#6699FF","#3ADF00","#F4F458","orange","#DD0000","#2ECCFA","#9AFE2E","#0000FF","#9A2EFE","#FE2EF7","#A4A4A4","#585858");
		$cpt = 0;
		$tab_final = array();
		//Pour chaque élement dans le tableau
		foreach ($tab as $key => $value){
			//Création d'un bar(plot
			$b1plot = new BarPlot($tab[$key]);
			$b1plot->SetLegend($key);
			$b1plot->SetFillColor($tab_couleur[$cpt]);
			//Ajout dans un tableau pour gérer la legende ert la couleur apres le Add dans le graphe
			$tab_final[$key] =  $b1plot;
			//Ajout dans le tableau dans le but de former le groupe
			array_push($group,$b1plot);
			$cpt += 1;

		}
		
		//Ligne rouge d'indication de "limite"
		$lplot = new LinePlot($target);
		
		//Creation du groupe de barPlot
		$gbplot = new GroupBarPlot($group);
		$graph->Add($gbplot);
		$graph->Add($lplot);
		
		//Faire un for pour remplir les coulerus
		$cpt = 0;
		foreach ($tab_final as $key => $value){
			
			$tab_final[$key]->SetFillColor($tab_couleur[$cpt]);
			$tab_final[$key]->SetColor("white");
			$cpt += 1;
		}
		
		//Affichage de la légence
		$lplot->SetLegend('Target');
		$lplot->SetWeight(5);
		$lplot->SetColor("#FF0000");
		
		$graph->legend->SetFrameWeight(1);	
		$graph->legend->Pos(0.5,0.925,'center','bottom');
		$graph->xaxis->SetLabelAngle(30);


		//Display the graph
		$graph->Stroke();
	}
}
