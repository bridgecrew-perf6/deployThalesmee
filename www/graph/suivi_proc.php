<?php 
require('../conf/connexion_param.php');// connexion a la base
require ('jpgraph/jpgraph.php');
require ('jpgraph/jpgraph_bar.php');
require ('fonction.php');

//On recupere les données des procedures, date de demande et date de besoin
$str="select dp.delai, et.dateEtat, p.idService_service, et.idEtat_etat
from demande_procedure dp, procedures p, etatproc et
where p.idDP_demande_procedure=dp.idDP
and et.idProc_procedures=p.idProc
and (et.idEtat_etat=17 or et.idEtat_etat=16 or et.idEtat_etat=15)
and EXISTS 
	(select idEtat_etat from etatproc
	where idEtat_etat=17
	and idProc_procedures=p.idProc)";
$req=mysqli_query($bdd,$str);

$str="select distinct dp.delai, dp.dateDemandeDP_redigerDP, p.idService_service
from demande_procedure dp, procedures p, etatproc et
where p.idDP_demande_procedure=dp.idDP
and et.idProc_procedures=p.idProc";
$reqDP=mysqli_query($bdd,$str);
if(!$req || !$reqDP)
	echo "<div class='alert alert-danger'><strong>Erreur de récupération des infos des procédures</strong></div>";
else{
	/*
	1 -> date Validation <= date besoin
	2 -> date mis en signature <= date besoin
	3 -> durée demandée < engagement labo (20 jours)
	4 -> date mis en relecture <=date besoin -1 semaine
	*/
	$nb1EMC=0;
	$nb2EMC=0;
	$nb3EMC=0;
	$nb4EMC=0;
	$nb1VIB=0;
	$nb2VIB=0;
	$nb3VIB=0;
	$nb4VIB=0;
	$nb1VTH=0;
	$nb2VTH=0;
	$nb3VTH=0;
	$nb4VTH=0;

	$nb1TotalEMC=0;
	$nb2TotalEMC=0;
	$nb3TotalEMC=0;
	$nb4TotalEMC=0;
	$nb1TotalVIB=0;
	$nb2TotalVIB=0;
	$nb3TotalVIB=0;
	$nb4TotalVIB=0;
	$nb1TotalVTH=0;
	$nb2TotalVTH=0;
	$nb3TotalVTH=0;
	$nb4TotalVTH=0;

	//Date de début date (date actuelle)
	$date_act = strftime("%d/%m/%Y", mktime(0,0,0,date('m'), date('d'), date('Y')));
	
	while($lg=mysqli_fetch_object($req))
	{
		$dateBesoin=$lg->delai;
		$dateValidation=$lg->dateEtat;
		$service=$lg->idService_service;
		$idEtat=$lg->idEtat_etat;
		
		if($idEtat==17) //procédures terminé
		{
			//Attribution en fonction du service
			if($service==1) $nb1TotalEMC++;
			elseif($service==2) $nb1TotalVIB++;
			elseif($service==3) $nb1TotalVTH++;
			
			if(strtotime($dateValidation) <= strtotime($dateBesoin))
			{
				if($service==1) $nb1EMC++;
				elseif($service==2) $nb1VIB++;
				elseif($service==3) $nb1VTH++;
			}			
		}
		elseif($idEtat==16) //procédures en signature
		{
			//Attribution en fonction du service
			if($service==1) $nb2TotalEMC++;
			elseif($service==2) $nb2TotalVIB++;
			elseif($service==3) $nb2TotalVTH++;
			
			if(strtotime($dateValidation) <= strtotime($dateBesoin))
			{
				if($service==1) $nb2EMC++;
				elseif($service==2) $nb2VIB++;
				elseif($service==3) $nb2VTH++;		
			}
		}
		elseif($idEtat==15) //procédures en relecture
		{
			//Attribution en fonction du service
			if($service==1) $nb4TotalEMC++;
			elseif($service==2) $nb4TotalVIB++;
			elseif($service==3) $nb4TotalVTH++;
			
			if(strtotime($dateValidation) <= (strtotime($dateBesoin) -604800)) //604800 7 jours en seconde
			{
				if($service==1) $nb4EMC++;
				elseif($service==2) $nb4VIB++;
				elseif($service==3) $nb4VTH++;		
			}
		}
	}

	//Pour chaque demande de procedures
	while($lg=mysqli_fetch_object($reqDP))
	{
		$dateBesoin=$lg->delai; //>Date de besoin
		$dateDP=$lg->dateDemandeDP_redigerDP; //Date de demande de la procédure
		$service=$lg->idService_service; //Laboratoire
		//Attribution en fonction du service
		if($service==1) $nb3TotalEMC++;
		elseif($service==2) $nb3TotalVIB++;
		elseif($service==3) $nb3TotalVTH++;
			
		if(floor((strtotime($dateBesoin) - strtotime($dateDP))/86400) < 20)
		{
			if($service==1) $nb3EMC++;
			elseif($service==2) $nb3VIB++;
			elseif($service==3) $nb3VTH++;
		}
	}
	mysqli_close($bdd);

	//les test servent à éviter les divisions par 0
	if($nb1TotalEMC!=0) $nb1EMC=(($nb1EMC*100)/$nb1TotalEMC);
	if($nb2TotalEMC!=0) $nb2EMC=(($nb2EMC*100)/$nb2TotalEMC);
	if($nb3TotalEMC!=0) $nb3EMC=(($nb3EMC*100)/$nb3TotalEMC);
	if($nb4TotalEMC!=0) $nb4EMC=(($nb4EMC*100)/$nb4TotalEMC);
	if($nb1TotalVIB!=0) $nb1VIB=(($nb1VIB*100)/$nb1TotalVIB);
	if($nb2TotalVIB!=0) $nb2VIB=(($nb2VIB*100)/$nb2TotalVIB);
	if($nb3TotalVIB!=0) $nb3VIB=(($nb3VIB*100)/$nb3TotalVIB);
	if($nb4TotalVIB!=0) $nb4VIB=(($nb4VIB*100)/$nb4TotalVIB);
	if($nb1TotalVTH!=0) $nb1VTH=(($nb1VTH*100)/$nb1TotalVTH);	
	if($nb2TotalVTH!=0) $nb2VTH=(($nb2VTH*100)/$nb2TotalVTH);
	if($nb3TotalVTH!=0) $nb3VTH=(($nb3VTH*100)/$nb3TotalVTH);
	if($nb4TotalVTH!=0) $nb4VTH=(($nb4VTH*100)/$nb4TotalVTH);

	//creation du tableau
	$data1y=array($nb1EMC,$nb1VIB,$nb1VTH);
	$data2y=array($nb2EMC,$nb2VIB,$nb2VTH);
	$data3y=array($nb3EMC,$nb3VIB,$nb3VTH);
	$data4y=array($nb4EMC,$nb4VIB,$nb4VTH);


	$graph = new Graph(1000,800, 'auto');
	$graph->SetScale("textlin");

	$titre = "Livraison des procédures par laboratoire \n\nÉdité le ".$date_act;
	$graph = afficheGraphe($graph, array('Labo EMC','Labo VIB','Labo VTH'), $titre);

	//Creation des barPlot
	$b1plot = new BarPlot($data1y);
	$b2plot = new BarPlot($data2y);
	$b3plot = new BarPlot($data3y);
	$b4plot = new BarPlot($data4y);
	
	//Creation du groupe de barPlot
	$gbplot = new GroupBarPlot(array($b1plot,$b4plot,$b2plot,$b3plot));
	//ajout au graph
	$graph->Add($gbplot);
	
	//ajout de la legende
	$b1plot->SetLegend('Date validation <= Date besoin');
	$b1plot->SetColor("white");
	$b1plot->SetFillColor("#00CC33");
	$b1plot->value->Show();

	$b2plot->SetLegend('Date mise en signature <= Date besoin');
	$b2plot->SetColor("white");
	$b2plot->SetFillColor("#FFCC66");
	$b2plot->value->Show();

	$b3plot->SetLegend('Durée demandée < Durée engagemen t laboratoire');
	$b3plot->SetColor("white");
	$b3plot->SetFillColor("#DD0000");
	$b3plot->value->Show();

	$b4plot->SetLegend('Date mise en relecture <= Date besoin - 1semaine');
	$b4plot->SetColor("white");
	$b4plot->SetFillColor("#6699FF");
	$b4plot->value->Show();
	
	$graph->legend->Pos(0.5,0.90,'center','bottom');

	$graph->legend->SetColumns(6);
	$graph->legend->SetColor('#4E4E4E','black');
	$graph->legend->SetLayout(LEGEND_VERT);
	$graph->legend->SetFont(FF_ARIAL,FS_NORMAL,11);

	// Display the graph
	$graph->Stroke();
}
