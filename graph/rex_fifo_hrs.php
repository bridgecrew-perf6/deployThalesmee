<?php 

function echanger($tabDate,$tabHrs,$a,$b)
{
	$temp=$tabDate[$a];
	$tabDate[$a]=$tabDate[$b];
	$tabDate[$b]=$temp;
	
	$temp=$tabHrs[$a];
	$tabHrs[$a]=$tabHrs[$b];
	$tabHrs[$b]=$temp;
	$tab=array();
	$tab[0]=$tabDate;
	$tab[1]=$tabHrs;
	return $tab;
}

function quickSort($tabDate,$tabHrs,$deb,$fin)
{
	//si tabDate de longueur nulle -> rien a faire
	if($deb < $fin)
	{	
		$gauche=$deb-1;
		$droite=$fin+1;
		$pivot=$tabDate[$deb];
		
		while($gauche < $droite)
		{
			do{ $droite--;} while($tabDate[$droite] > $pivot);
			do{ $gauche++;} while($tabDate[$gauche] < $pivot);
			
			if($gauche < $droite)
			{
				$tab=echanger($tabDate,$tabHrs,$gauche,$droite);
				$tabDateFin=$tab[0];
				$tabNbHeures=$tab[1];
				unset($tab);
			}
		}
		
		quickSort($tabDate,$tabHrs,$deb,$droite);
		quickSort($tabDate,$tabHrs,$droite+1,$fin);
	}
	$tab=array();
	$tab[0]=$tabDate;
	$tab[1]=$tabHrs;
	return $tab;
}
if(!isset($_GET["idService"]))
	echo "<div class='alert alert-danger'><strong>Erreur de récupération du service</strong></div>";
else
{
	require('../conf/connexionPDO_param.php');// connexion a la base
	require ('jpgraph/jpgraph.php');
	require ('jpgraph/jpgraph_bar.php');
	
	$condDate="";
	$idService=$_GET["idService"];
	if($idService==1)
		$labo="EMC";
	elseif($idService==2)
		$labo="VIB";
	else
		$labo="VTH";
	
	if(isset($_GET["dateDeb"])) //si on passe des dates en parametres (ex pour le rex) on les prends en comptes
	{
		$dateDeb=$_GET["dateDeb"];
		$dateFin=$_GET["dateFin"];
		//Formatage pour l'affichage des dates sur le graphe
		$dateDebForm=explode("-",explode(" ",$_GET["dateDeb"])[0]);
		$dateDebForm=$dateDebForm[2]."/".$dateDebForm[1]."/".$dateDebForm[0];
		$dateFinForm=explode("-",explode(" ",$_GET["dateFin"])[0]);
		$dateFinForm=$dateFinForm[2]."/".$dateFinForm[1]."/".$dateFinForm[0];
		$condDate="and et.dateEtat >=:dateDeb and et.dateEtat <=:dateFin";
		$condVar=array('idService' => $idService,'dateDeb' => $dateDeb,'dateFin' => $dateFin);
	}
	else
		$condVar=array('idService' => $idService);
	
	//on recupere les données des essais (durée du test
	$str="select et.dateEtat, et.idEtat_etat, e.idEssai
	from essai e, etatessai et
	where et.idEssai_essai=e.idEssai
	and (et.idEtat_etat=23 or idEtat_etat=24)
	and e.fifo=1
	and e.idService_service=:idService
	and EXISTS 
		(select idEtat_etat from etatessai et
		where idEtat_etat=25
		and idEssai_essai=e.idessai
		$condDate)
	order by e.idessai, et.idEtat_etat;";
	$res=$dbh->prepare($str);
	$res->execute($condVar);

   print_r($dbh->errorInfo());
	if(!$res)
		echo "<div class='alert alert-danger'><strong>Erreur de récupération des infos des procédures</strong></div>";
	else{
		
		$numSemPrec=0;
		$numSemAct=0;
		$cptSemDiff=0;
		$nbTestSemaine=0;
		
		//$tabSemaine=array(); //tableau contenant le nombre de test reunis par semaines
		//$tabNumSemLegende=array(); //tableau pour construire la legende
		$tabNbHeures=array();
		$tabDateFin=array();
		
		$nbTest=0;
		/*
		Ici il faut corriger les heures de la base de données pour obtenir les vrai heures de test, car il n'y a pas de pause pour la nuit sur le logiciel
		or les moyens ne fonctionnenent pas, donc il faut retirer les heures de nuit, de pause midi etc...
		Par midi: -1.5h
		Par nuit: -14.5h
		Par week end: -2j
		*/
		
		while($lg=$res->fetch(PDO::FETCH_OBJ))
		{
			$dateEtat=$lg->dateEtat;
			$idEtat=$lg->idEtat_etat;
			$idEssai=$lg->idEssai;
			if($idEtat==24)//dateEtat contient la date de fin d'attente
			{
				$dateDeb=new DateTime($datePrecedente);
				$dateFin=new DateTime($dateEtat);
				//$dateFin->modify('+1 day');
				$dateFinCorrige=clone $dateFin;
				
				$interval= $dateFin->diff($dateDeb);
				$days= $interval->days;
				//uniquement les tests de plus de 3h
				if($interval->days !=0 || $interval->h >3)
				{
					$period= new DatePeriod($dateDeb, new DateInterval('P1D'), $dateFin);
					
					foreach($period as $dt)
					{
						//suppression des week end
						$curr = $dt->format('w'); //numéro du jours de la semaine
						//6 -> samedi / 0 -> dimanche
						if($curr==6 || $curr==0)
							$dateFinCorrige->sub(new DateInterval('P1D'));
							
						else
						{
							/*
							Pour prendre en compte toutes les données (parfois début a 7h56 par ex), on étent les tests, par ex le début entre 7h00 et 12h30 
							alors qu'en corrigeant on fera comme si c'etait entre 8h00 et 12h00
							*/
							
							//on joint les heures et les minutes pour manipuler des entier -> plus simple a comparer pour un programme
							$dtH=$dt->format('Hi');
							//pour savoir si date actuel = date debut/fin on utilise on joints les dates en entier et on compare
							$dtJ=$dt->format('ymd');
							$debJ=$dateDeb->format('ymd');
							$finJ=$dateFin->format('ymd');
							
							//si debut et fin du test le meme jours
							if($debJ==$dtJ && $finJ==$dtJ)
							{
								//si apres 7h00 et avant avant 12h30 -> on enleve le midi
								if($dtH>=700 && $dtH <= 1230)
									$dateFinCorrige->sub(new DateInterval('PT1H30M'));
							}							
							//si on travaille sur la date de début
							elseif($debJ==$dtJ)
							{
								
								//si apres 7h00 et avant avant 12h30 -> on enleve la nuit et midi
								if($dtH>=700 && $dtH <= 1230)
								{
									//on retire 1h30 pour la pause de midi, et 14h30 pour la nuit -> 16h en tout
									$dateFinCorrige->sub(new DateInterval('PT16H'));
								}
								//si apres midi  -> on enleve la nuit
								elseif($dtH >= 1230 && $dtH <= 1900)
								{
									//on retire les heures de nuits -> 14h30
									$dateFinCorrige->sub(new DateInterval('PT14H30M'));
									
								}
								//theoriquement ne devrait jamais se produire -> on genere un message pour produire une erreur sur le diagramme et avertir d'un probleme de données
								else
									echo "Problème de données:".$dateDeb->format('Y-m-d')."--- ".$dt->format('Y-m-d')."--- ".$dateFin->format('Y-m-d')."--<br/>";
							}
							//si on travaille sur la date de fin
							elseif($finJ==$dtJ)
							{
								//corrige un bug, DateInterval ne va pas jusqu'a l'heure de fin exacte mais celle de la date de début
								$dtH=$dateFin->format("Hi");
								//si apres 7h00 et avant avant 12h30 -> pas de correction 
								//si apres midi (13h30) -> on enleve le midi
								if($dtH >= 1230 && $dtH <= 1900)
								{
									// midi = 1h30
									$dateFinCorrige->sub(new DateInterval('PT1H30M'));
								}
							}
							//jours au millieu -> on retire toujours le midi et la nuit -> 16h
							else
							{							
								$dateFinCorrige->sub(new DateInterval('PT16H'));
							}
						
						}
					}
					//valeur corrige
					$interCorrige=$dateFinCorrige->diff($dateDeb);
					$nbHeureCorrige=round(($interCorrige->d)*24 + $interCorrige->h + ($interCorrige->i)/60,2);
					$tabNbHeures[$nbTest]=$nbHeureCorrige;
					$tabDateFin[$nbTest]=$dateFin;
					$nbTest++;
					
					//echo $dateFin->format("W")."--".$nbHeureCorrige."---".$idEssai."<br/>";
				}	
			}
			else //dateEtat contient la date de debut d'attente
				$datePrecedente=$dateEtat;
			
		}
		$dbh->connection = null;
		//tri des tableaux par date de fin
		$tab=quickSort($tabDateFin,$tabNbHeures,0,$nbTest-1);
		$tabDateFin=$tab[0];
		$tabNbHeures=$tab[1];
		
		unset($tab);
		
		$dateSuivante=null;
		$datePrecedente=null;
		$dateAct=null;
		$moyHrs=0;
		$cptSem=0;
		$tabHrsSem=array();
		$tabSem=array();
		
		$nbSem=0;
		$moyHrsTotal=0;
		//on reunie les heures par semaines pour faire une moyenne par semaine
		for($i=0;$i<$nbTest;$i++)
		{	
			
			$dateAct=$tabDateFin[$i];
			//echo $tabNbHeures[$i].'----'.$dateAct->format('W')."<br/>";

			if($i+1<$nbTest && $dateAct->format('W')== $tabDateFin[$i+1]->format('W'))
			{
				
				$moyHrs+=$tabNbHeures[$i];
				$cptSem++;
			}
			else
			{
				//on oublie pas de prendre en compte l'heure parcourue actuellement
				$moyHrs+=$tabNbHeures[$i];
				$cptSem++;			
				
				$moyHrs=round($moyHrs/$cptSem,2);
				$tabHrsSem[$nbSem]=$moyHrs;
				$tabSem[$nbSem]=$dateAct->format('y').$dateAct->format('W');
				
				$moyHrsTotal+=$moyHrs;
				$nbSem++;
				
				$moyHrs=0;
				$cptSem=0;
			}
		}
		/*echo "<br/>";
		for($i=0;$i<$nbSem;$i++)
			echo $tabHrsSem[$i]."---".$tabSem[$i]."<br/>";*/
		
	
		$moyHrsTotal=round($moyHrsTotal/$nbSem,2);
		
		//creation du tableau
		$data1y=$tabHrsSem;
		
		$graph = new Graph(1000,800, 'auto');
		$graph->SetScale("textlin");

		$theme_class=new UniversalTheme;
		$graph->SetTheme($theme_class);
		$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,9);
		$graph->yaxis->title->SetFont(FF_ARIAL,FS_BOLD,12);
		$graph->title->SetFont(FF_ARIAL,FS_BOLD, 16);
		$graph->title->Set("Durée moyenne des tests par semaines");
		
		$graph->SetBox(false);

		$graph->ygrid->SetFill(false);
		$graph->xaxis->SetTickLabels($tabSem);
		$graph->xaxis->SetLabelAngle(90);

		$graph->yaxis->HideLine(false);
		$graph->yaxis->HideTicks(false,false);
		
		if (empty($data1y)){
			
			array_push($dataly,0);
			$graph->title->Set("Aucune donnée");
			
		}else{
			
			$titre = "Retour d'experience FIFO";
			$graph->title->Set($titre."\n\nDu ".$dateDebForm." au ".$dateFinForm);
		}
		//Creation des barPlot
		$b1plot = new BarPlot($data1y);
		
		//Creation du groupe de barPlot
		$gbplot = new GroupBarPlot(array($b1plot));

		//ajout au graph
		$graph->Add($gbplot);
		
		//ajout de la legende
		$b1plot->SetLegend('Durée moyenne en heure');
		
		$graph->legend->SetFrameWeight(1);
		$graph->legend->SetColumns(6);
		$graph->legend->SetColor('#4E4E4E','black');
		$graph->legend->SetLayout(LEGEND_VERT);
		$graph->legend->SetFont(FF_ARIAL,FS_NORMAL,11);

		$b1plot->SetColor("white");
		$b1plot->SetFillColor("#6699FF");
		$b1plot->value->Show();
		$b1plot->value->SetFormat('%01.2f');
		$b1plot->value->SetFont(FF_ARIAL,FS_NORMAL,7);
		
		$txt=new Text("Moyenne générale: ".$moyHrsTotal."h");
		$txt->SetPos(320,550);
		$txt->SetColor('#4E4E4E','black');
		$txt->SetFont(FF_ARIAL,FS_NORMAL,11);
		$graph->AddText($txt);
		
		// Display the graph
		$graph->Stroke();
	}
}

