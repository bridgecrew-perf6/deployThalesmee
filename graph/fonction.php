<?php
/*
* Fonction qui permet à partir d'un mois de calculer le suivant
* @param
* mois : mois de base
* @return 
* String : mois suivant
*/
function moisSuivant ($mois)
{	
	$mois ++; //Incrémentation de 1
	if ($mois == 13) return '01'; //Si égale 13 -> Janvier
	else if ($mois < 10) return '0'.$mois; //Si inférieur à 10 il faut ajouter un 0
	else return "".$mois; //Sinon le mois est correct (concaténation pour conversion en String
}

/*
* Fonction qui permet de calculer la différence entre deux date
* date1 : date
* date2 : date
* @return
* diff : array contenant la nombre de minutes, de secondes et d'heure entre les deux dates
*/
function dateDiff($date1, $date2)
{
	$diff = array(); // Initialisation du retour
	$tmp = $date2 - $date1;
	
	// Nombre de secondes entre les 2 dates
	$diff["sec"] = $tmp % 60;  // Extraction du nombre de secondes

	$tmp = round(($tmp-$diff["sec"])/60); // Nombre de minutes (partie entière)
	$diff["min"] = $tmp % 60; // Extraction du nombre de minutes
 
	$tmp = round(($tmp-$diff["min"])/60); // Nombre d'heures (entières)
	$diff["hour"] = $tmp % 24; // Extraction du nombre d'heures
	 
	$tmp = round(($tmp-$diff["hour"])/24); // Nombre de jours restants
	$diff["day"] = $tmp;
	return $diff;
}

/*
* Fonction qui permet de configurer le graphe avec des configurations par défaults
* @param
* graph : la graph à configurer
* legend : la légende à afficher
* titre : le titre du graphique
* @return
* graph : Le graphe à afficher
*/
function afficheGraphe($graph, $legend, $titre)
{
	$theme_class=new UniversalTheme;
	$graph->SetTheme($theme_class);
	$graph->xaxis->SetFont(FF_ARIAL,FS_BOLD,8);
	$graph->yaxis->title->SetFont(FF_ARIAL,FS_BOLD,12);
	$graph->title->SetFont(FF_ARIAL,FS_BOLD, 16);
	$graph->title->Set($titre);
	$graph->img->SetAntiAliasing(false);
	$graph->SetBox(false);
	$graph->ygrid->SetFill(false);
	$graph->xaxis->SetTickLabels($legend);
	$graph->xaxis->SetLabelAngle(90);
	$graph->yaxis->HideLine(false);
	$graph->yaxis->HideTicks(false,false);
	$graph->legend->Pos(0,0,'left','top');
	$graph->legend->SetFrameWeight(1);
	return $graph;
}

/*
* Fonction permettant de compter le nombre de demi-journées entre deux dates
* @param 
* date_deb_ini : date de départ
* date_fin_ini : date de fin
* sim : booléen qui exprime si le test est en simultané
* @return
* nb_demijour : nombre de dmi journées
*/
function nb_demijour_ouvre ($date_deb_ini, $date_fin_ini, $sim)
{	
	$nb_demijour = 0; //Initialisation
	//Découpage pour récupérer la jour et l'heure
	$dateDeb = explode(" ", $date_deb_ini); //Découpage pour récupérer la jour et l'heure
	$jour_dateDeb = $dateDeb[0];
	$date_dateDeb = explode ("-", $jour_dateDeb);
	$horaire_dateDeb = $dateDeb[1];
	$heure_dateDeb = explode (":", $horaire_dateDeb);
	//Découpage pour récupérer la jour et l'heure
	$dateFin = explode(" ", $date_fin_ini);
	$jour_dateFin = $dateFin[0];
	$date_dateFin = explode ("-", $jour_dateFin);
	$horaire_dateFin = $dateFin[1];
	$heure_dateFin = explode (":", $horaire_dateFin);
	//Temps depuis la création de UNIX
	$timestamp_deb = mktime (0,0,0,$date_dateDeb[1],$date_dateDeb[2],$date_dateDeb[0]);
	$timestamp_fin = mktime (0,0,0,$date_dateFin[1],$date_dateFin[2],$date_dateFin[0]);
	//Si le jour est différent de dimanche et de samedi
	if ((date("w", $timestamp_fin) != 0) && (date("w", $timestamp_fin) != 6)){
			
		if (intval($heure_dateFin[0]) <= 13) $nb_demijour -= 1; //Si l'heure est avant 13h
	}
	//Si le jour est différent de dimanche et de samedi
	if ((date("w", $timestamp_deb) != 0) && (date("w", $timestamp_deb) != 6)){
			
		if (intval($heure_dateDeb[0]) >= 13) $nb_demijour -= 1; //Si l'heure est avant 13h
	}
	//Tant que le date de début est inférieur à la date de fin
	while ($timestamp_deb <= $timestamp_fin)
	{
		//Si le jour est différent de dimanche et de samedi 
		if ((date("w", $timestamp_deb) != 0) && (date("w", $timestamp_deb) != 6)) $nb_demijour += 2;
		//Changement de la date pour ajouter 1 jour
		$date_deb = date("Y-m-d H:i", strtotime($date_dateDeb[0]."-".$date_dateDeb[1]."-".$date_dateDeb[2]." ".$heure_dateDeb[0].":".$heure_dateDeb[1]." +1 day"));
		//Découpage pour récupérer le jour et l'heure
		$dateDeb = explode(" ", $date_deb);
		$jour_dateDeb = $dateDeb[0];
		$date_dateDeb = explode ("-", $jour_dateDeb);
		$horaire_dateDeb = $dateDeb[1];
		$heure_dateDeb = explode (":", $horaire_dateDeb);
		$timestamp_deb = mktime (0,0,0,$date_dateDeb[1],$date_dateDeb[2],$date_dateDeb[0]);
		
	}	
	//Si simultané
	if ($sim) $nb_demijour -= 1;
	return $nb_demijour;
}

/*
* Divise une chaine de caractère en tableau
* @param
* delimiters : les points où il faut découper
* string : la chaîne de caractère
*/
function multiexplode($delimiters, $string){
	
	$ready = str_replace($delimiters, $delimiters[0], $string);
	$launch = explode ($delimiters[0], $ready);
	return $launch;
}

/*
* Fonction qui donne la différence entre deux mois
* @param
* date1 : une date
* date 2 : un autre date
* NB : si date1 < date 2 => nombre négatif
*/
function diff_mois ($date1, $date2){
	
	//Mois de la date1 en entier
	$moisCours = $date1[1];
	//Année de la date1 en entier
	$anneCours = intval($date1[0]);
	if (intval($moisCours[0]) != 0){
		$moisCours = intval($moisCours);
	}else{
		$moisCours = intval($moisCours[1]);
	}
	//Mois de la date2 en entier
	$moisPrec = $date2[1];
	//Année de la date2 en entier
	$anneePrec = intval($date2[0]);
	if (intval($moisCours[0]) != 0){
		$moisPrec = intval($moisPrec);
	}else{
		$moisPrec = intval($date2[1]);
	}
	//Si les deux dates sont égales
	if ($moisCours == $moisPrec && $anneCours == $anneePrec){
		return 0;
	//Si les deux années sont égales mais pas le numéro du mois
	}else if ($anneCours == $anneePrec && $moisCours > $moisPrec){
		$res = 0;
		//Tant que le mois précedent est strictement inférieur au mois actuel
		while ($moisPrec < $moisCours){	
			$moisPrec++;
			$res ++;
		}
		return $res;
	//Si les années sont différentes
	}else if ($anneCours > $anneePrec){
		$res = 0;
		//Tant que la première année est strictement inférieur
		while ($anneePrec < $anneCours){
			$moisPrec++;
			$res ++;
			//Si le mois est à 13 -> Janvier
			//Increment le nombre d'année
			if ($moisPrec == 13){	
				$moisPrec = 1;
				$anneePrec++;
			}
		}
		//Si le mois précedent est inférieur au mois en cours
		if ($moisCours > $moisPrec){
			//Tant que le mois précedent est strictement inférieur au mois actuel
			while ($moisPrec < $moisCours){
				$moisPrec++;
				$res ++;
			}
		}
		return $res;
	//Sinon 0
	}else{
		return 0;
	}
}

/*
* Donne le nombre de mois entre 2 mois (Ex: Janvier et Mars ->1 Mois)
* @param
* date1 : une date
* date 2 : un autre date
* NB : si date1 < date 2 => nombre négatif
*/
function ecart_mois ($date1, $date2){
	
	//Mois de la date1 en entier
	$moisCours = $date1[1];
	//Année de la date1 en entier
	$anneCours = intval($date1[0]);
	if (intval($moisCours[0]) != 0){
		$moisCours = intval($moisCours);
	}else{
		$moisCours = intval($moisCours[1]);
	}
	//Mois de la date2 en entier
	$moisPrec = $date2[1];
	//Année de la date2 en entier
	$anneePrec = intval($date2[0]);
	if (intval($moisCours[0]) != 0){
		$moisPrec = intval($moisPrec);
	}else{
		$moisPrec = intval($date2[1]);
	}
	//Si les deux dates sont égales
	if ($moisCours == $moisPrec && $anneCours == $anneePrec){
		return 0;
	//Si les deux années sont égales mais le mois precedent +1 est inferieur au mois actuel
	}else if ($anneCours == $anneePrec && $moisCours > $moisPrec+1){
		$res = 0;
		//Tant que le mois précedent +1 est strictement inférieur au mois actuel
		while ($moisPrec+1 < $moisCours){	
			$moisPrec++;
			$res ++;
		}
		return $res;
	//Si les années sont différentes
	}else if ($anneCours > $anneePrec){
		$res = 0;
		//Tant que la première année est strictement inférieur
		while ($anneePrec < $anneCours){
			//Si le mois est à 12 -> Janvier
			//Increment le nombre d'année
			if ($moisPrec == 12){	
				$moisPrec = 1;
				$anneePrec++;
			//Sinon incrementation du nombre de mois
			}else{
				$moisPrec++;
				$res ++;
			}
		}
		//Si le mois précendent est inférieur au mois en cours
		if ($moisCours > $moisPrec){
			//Tant que le mois précedent est strictement inférieur au mois actuel
			while ($moisPrec < $moisCours){
			
			$moisPrec++;
			$res ++;
			}
		}
		return $res;
	//Sinon 0
	}else{
		return 0;
	}
}
?>