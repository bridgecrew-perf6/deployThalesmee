<?php 

require('../conf/connexion_param.php');// connexion a la base
require ('../graph/jpgraph/jpgraph.php');
require ('../graph/jpgraph/jpgraph_bar.php');
require ('fonction.php');

if(isset($_GET["dateDeb"]) && isset($_GET["dateFin"]) && isset($_GET["idService"]))
{	
	//Récupération du numéro de laboratoire
	$idService = $_GET['idService'];
	//Récupération des dates de début et de fin
	$dateDeb=$_GET["dateDeb"];
	$dateFin=$_GET["dateFin"];
	//Date du jour (date actuelle)
	$date_act = strftime("%d/%m/%Y", mktime(0,0,0,date('m'), date('d'), date('Y')));

	$ligne = "";
	//Récupération des lignes de produit choisies par l'utilisateur
	if (isset ($_GET['I2PT'])) $ligne .= "'I2PT',";
	if (isset ($_GET['I2PA'])) $ligne .= "'I2PA',";
	if (isset ($_GET['LPA'])) $ligne .= "'LPA',";
	if (isset ($_GET['LPH'])) $ligne .= "'LPH',";
	if (isset ($_GET['Autre'])) $ligne .= "'Autre',";
	if (isset ($_GET['LPE'])) $ligne .= "'LPE',";

	//On enlève le dernier caractère qui est une virgule
	$ligne = substr($ligne,0,-1);	

	//Sélection des essais avec la date de début prévu et la date de passage dans l'état «En attente»	
	$str = "Select e.idEssai, e.date_debut_prevu, et.dateEtat, nomLigne from essai e, etatessai et , ligneproduit where idService_SERVICE='$idService'";

	if (isset ($_GET['Tous'])) 
	{
		$str .= " and ((idLigne = ligneProd and nomLigne in ($ligne)) or ligneProd is NULL)";

	}else
	{
		$str .= " and idLigne = ligneProd and nomLigne in ($ligne)";
	}

	$str .= " and e.date_debut >'$dateDeb' and date_debut < '$dateFin' and e.idEssai = et.idEssai_ESSAI and idEtat_ETAT >= 22 and e.date_debut_prevu != 'NULL' and e.affaire != 'MAINT' group by e.idEssai ";
	$req = mysqli_query($bdd, $str);

	//Initialisation des variables
	$avant = 0;
	$Plusde2Heures =0;
	$Plusde6h = 0;
	$plusde1jours = 0;
	$plusde3jours = 0;
	$tab = array();
	
	//Parcours de chaque essais
	while ($lg=mysqli_fetch_object($req)){

		$dateDebPrevu = $lg->date_debut_prevu;
		$dateLivraison = $lg->dateEtat;
		array_push($tab,$dateDebPrevu);
		//Calcul de la différence entre la date de début prévues et la date de livraison
		$diff = dateDiff (strtotime($dateDebPrevu), strtotime($dateLivraison));
		//Incrémentation de la bonne variable en fonction de la différence
		if ($diff["day"] > 3) $plusde3jours += 1; //Plus de 3 jours
		else if ($diff["day"] >= 1 && $diff["day"] <= 3) $plusde1jours += 1; //Entre 1 et 3 jours
		else if ($diff["day"] == 0 && $diff["hour"] > 6 ) $Plusde6h += 1; //Plus de 6 heures
		else if ($diff["day"] == 0 && $diff["hour"] >= 2 && $diff["min"] > 0 && $diff["hour"] <= 6 ) $Plusde2Heures += 1; //Plusdee 2 heurs
		else $avant += 1; //Avant l'heure prévue
	}

	$data1y=array($avant,$Plusde2Heures,$Plusde6h,$plusde1jours,$plusde3jours);
	
	$graph = new Graph(1000,800, 'auto'); //Génération du graphe
	$graph->SetScale("textlin");

	if (empty($tab)) $titre = "AUCUNE DONNÉE"; //Titre si vide
	else $titre = "Retard de livraison des équipements avant test"; //Vide

	$titre .= "\n\nÉdité le ".$date_act;

	$b1plot = new BarPlot($data1y); //Creation des barPlot
	$gbplot = new GroupBarPlot(array($b1plot)); //Creation du groupe de barPlot	
	$graph->Add($gbplot); //ajout au graph
	$b1plot->SetColor("white"); //Création de la barre
	$b1plot->SetFillColor(array ("#6699FF", '#3ADF00', "#F7FE2E", 'orange', "#DD0000"));
	$b1plot->value->Show();
	$b1plot->value->SetFormat('%d');

	afficheGraphe($graph, array('< 2h','2h à 6h','6h à 1 jours','1 jours à 3 jours',' > 3 jours'), $titre)->Stroke();

}
?>