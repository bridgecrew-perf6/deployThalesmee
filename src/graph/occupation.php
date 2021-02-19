<?php 
require('../conf/connexion_param.php');// connexion a la base
require ('jpgraph/jpgraph.php');
require ('jpgraph/jpgraph_bar.php');
require ('jpgraph/jpgraph_line.php');
require('fonction.php');

$idService=$_GET["idService"];
$date_deb = $_GET["dateDeb"]." 00:00:00";
$date_fin = $_GET["dateFin"]." 23:59:59";
$str = "SELECT distinct(`idEssai`), nomMoyen, `date_debut`,`date_fin` FROM etatessai et, `essai` e, moyen WHERE (date_fin <= '$date_fin' and  date_fin >= '$date_deb' or date_debut >= '$date_deb' and date_debut <= '$date_fin' ) and date_debut < date_fin and idMoyen_MOYEN = idMoyen and e.idService_SERVICE = $idService and et.idEssai_ESSAI = idEssai and idEtat_ETAT = 23 ORDER BY nomMoyen, date_debut";
$req = mysqli_query($bdd,  $str);
$res = array();

//Date du jour (date actuelle)
$date_act = strftime("%d/%m/%Y", mktime(0,0,0,date('m'), date('d'), date('Y')));

$legende = array();
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
			
			array_push($resultat, (array_sum($res[$nomMoyen])/$total)*100);
			array_push($legende, $nomMoyen);
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


array_push($resultat, (array_sum($res[$nomMoyen])/$total)*100);
array_push($legende, $nomMoyen);

//Initialisation du graphe
$graph = new Graph(1000,800, 'auto');	
$graph->SetScale("textlin");
//creation du tableau
$data1y=$resultat;

$theme_class=new UniversalTheme;
$graph->SetTheme($theme_class);
$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,9);
$graph->yaxis->title->SetFont(FF_ARIAL,FS_BOLD,12);
$graph->title->SetFont(FF_ARIAL,FS_BOLD, 16);

$titre="Taux d'occupation des moyens (%)";

$graph->title->Set($titre);
$graph->title->Set($titre."\n\nÉdité le ".$date_act);

$graph->SetBox(false);
$graph->ygrid->SetFill(false);
$graph->xaxis->SetTickLabels($legende);
$graph->yaxis->HideLine(false);
$graph->yaxis->HideTicks(false,false);
$graph->yaxis->SetLabelAlign('right','center');

$b1plot = new BarPlot($data1y);

//ajout au graph
$graph->Add($b1plot);

//Initialisation du barplot
$b1plot->SetLegend('Taux d\'occupation (%)');
$b1plot->SetColor("white");
$b1plot->SetFillColor("#6699FF");
$b1plot->value->Show();
$b1plot->value->SetFormat('%d');

$graph->legend->SetFrameWeight(1);
$graph->legend->SetColumns(6);
$graph->legend->SetColor('#4E4E4E','black');
$graph->legend->SetLayout(LEGEND_VERT);
$graph->legend->SetFont(FF_ARIAL,FS_NORMAL,11);
$graph->xaxis->SetLabelAngle(45);


//Display the graph
$graph->Stroke();
?>