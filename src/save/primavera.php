<?php 
require('top.php');

function majOf($tabnOf,$type,$idEs,$bdd, $idPrim, $article)
{
	//ajout des of manquant
	$str="SELECT idModele FROM type_modele WHERE nomModele='$type';";
	$req=@mysqli_query($bdd,$str);
	if(mysqli_num_rows($req)!=0)
	{
		$idType=mysqli_fetch_object($req)->idModele;
		//on rajoute les nouveaux match
		$nb=count($tabnOf);
		for($i=0; $i<$nb ;$i++){
			$noOF=$tabnOf[$i];
			
			//on verifie que l'OF existe
			$str="SELECT noOF as nb from EQUIPEMENT_OF where noOF='$noOF';";
			$req=@mysqli_query($bdd,$str);
			
			if(!$req)
				echo '<div class="alert alert-danger"><strong>Erreur de verification de l\'of</strong></div>';
			else
			{
				if(mysqli_num_rows($req)==0) //nouvel of
				{ 
					$str="INSERT into EQUIPEMENT_OF values('$noOF',$idType,'$article');";
					$req=mysqli_query($bdd,$str);
				}else
				{
					$str="UPDATE EQUIPEMENT_OF SET article='$article' WHERE noOF='$noOF';";
					$req=mysqli_query($bdd,$str);
				}
				
				//insertion du match essai OF
				$str="INSERT into tester values('$noOF','$idEs') ON DUPLICATE KEY UPDATE noOF_EQUIPEMENT_OF='$noOF';";
				$req=mysqli_query($bdd,$str);		
			}
		}
	}	
	
	//On enleve les of en trop de la base
	//Selection de l'essai concerné
	$str = "SELECT idEssai FROM `essai` WHERE `idTachePrim`='$idPrim';";
	$req=@mysqli_query($bdd,$str);
	$idEssai=mysqli_fetch_object($req)->idEssai;
		
	//Selection des of concernés par l'essai
	$str="SELECT noOF_EQUIPEMENT_OF as no FROM `tester` WHERE `idEssai_ESSAI`=$idEssai;";
	$req=@mysqli_query($bdd,$str);
	$i = 0;
	$ofParTest = array();
		
	//Remplissage d'un tableau regroupant tous les of d'un essai
	while($lg=mysqli_fetch_object($req))
	{
		$ofParTest[$i] = $lg->no;
		$i +=1;
	}			
					
	for ($i=0; $i<count($ofParTest); $i += 1){
			
		//Si l'of dans la base n'est pas dans la nouvelle liste d'of 
		if (!in_array($ofParTest[$i], $tabnOf))
		{
			//on le supprime
			$str = "DELETE From tester WHERE noOF_EQUIPEMENT_OF = '$ofParTest[$i]' and idEssai_Essai=$idEssai;";
			$req=@mysqli_query($bdd,$str);
		}
	}
	
}

function ajouterEssai($tab,$tabMoyenInconnue,$bdd)
{
	$tabDateFin=explode(" ",$tab["end_date"]);
	$date_fin=dateFrToSQL($tabDateFin[0]);
	if(isset($tabDateFin[1]))
			$date_fin.=" ".$tabDateFin[1];
	
	$today = date("Y-m-d H:i:s");
	
	//if ($date_fin >= $today)
	//{
		//emc 1 / vib 2 / vth 3 / 0 pas pris en compte
		$labo=0;
		if($tab["user_field_108057"]=="EMC")
			$labo=1;
		elseif($tab["user_field_108057"]=="CHOC" || $tab["user_field_108057"]=="VIB")
			$labo=2;
		elseif($tab["user_field_108057"]=="VTH")
			$labo=3;
		
		if($labo!=0)
		{
			//recup des param
			$idPrim=$tab["task_code"];

			$moyen="oui";
			$dejaTrouv=false;
			//faire la relation moyen - nom
			//on regarde d'abord le dernier champs (le definitif)
			if($tab["actv_code_tmm_moyen_dessai_id"]!="")
			{
				$t=explode(".",$tab["actv_code_tmm_moyen_dessai_id"]);
				if(isset($t[2]))
					$moyen=$t[2];
				elseif(isset($t[1]))
					$moyen=$t[1];
				else
					$moyen=$t[0];
					
				$str="select idMoyen from moyen 
				where LOWER(REPLACE(nomMoyen, ' ', '')) = LOWER(REPLACE('$moyen', ' ', ''))
				or LOWER(REPLACE(nomMoyen2, ' ', '')) = LOWER(REPLACE('$moyen', ' ', ''))
				or LOWER(REPLACE(nomMoyen3, ' ', '')) = LOWER(REPLACE('$moyen', ' ', ''))
				or LOWER(REPLACE(nomMoyen4, ' ', '')) = LOWER(REPLACE('$moyen', ' ', ''))
				or LOWER(REPLACE(nomMoyen5, ' ', '')) = LOWER(REPLACE('$moyen', ' ', ''));";
				$req=mysqli_query($bdd, $str);
				
				if(mysqli_num_rows($req)!=0)
				{
					$moyen=mysqli_fetch_object($req)->idMoyen;
					$dejaTrouv=true;
				}
				else
				{
					if(!in_array($moyen,$tabMoyenInconnue))
					{
						$tabMoyenInconnue[count($tabMoyenInconnue)]=$moyen;
					}
				}
			}
			//si non concluant on regarde le champs avant
			if(!$dejaTrouv && $tab["user_field_108014"]!="")
			{
				$moyen=$tab["user_field_108014"];
				$str="select idMoyen from moyen 
				where LOWER(REPLACE(nomMoyen, ' ', '')) = LOWER(REPLACE('$moyen', ' ', ''))
				or LOWER(REPLACE(nomMoyen2, ' ', '')) = LOWER(REPLACE('$moyen', ' ', ''))
				or LOWER(REPLACE(nomMoyen3, ' ', '')) = LOWER(REPLACE('$moyen', ' ', ''))
				or LOWER(REPLACE(nomMoyen4, ' ', '')) = LOWER(REPLACE('$moyen', ' ', ''))
				or LOWER(REPLACE(nomMoyen5, ' ', '')) = LOWER(REPLACE('$moyen', ' ', ''));";
				$req=mysqli_query($bdd, $str);
				
				if(mysqli_num_rows($req)!=0)
				{
					$moyen=mysqli_fetch_object($req)->idMoyen;
					$dejaTrouv=true;
				}
				else{
					if(!in_array($moyen,$tabMoyenInconnue))
					{
						$tabMoyenInconnue[count($tabMoyenInconnue)]=$moyen;
					}
				}
				
			}
			//si non concluant on regarde le premier champs
			if(!$dejaTrouv && $tab["user_field_108015"]!="")
			{
				$moyen=$tab["user_field_108015"];
				$str="select idMoyen from moyen 
				where LOWER(REPLACE(nomMoyen, ' ', '')) = LOWER(REPLACE('$moyen', ' ', ''))
				or LOWER(REPLACE(nomMoyen2, ' ', '')) = LOWER(REPLACE('$moyen', ' ', ''))
				or LOWER(REPLACE(nomMoyen3, ' ', '')) = LOWER(REPLACE('$moyen', ' ', ''))
				or LOWER(REPLACE(nomMoyen4, ' ', '')) = LOWER(REPLACE('$moyen', ' ', ''))
				or LOWER(REPLACE(nomMoyen5, ' ', '')) = LOWER(REPLACE('$moyen', ' ', ''));";
				$req=mysqli_query($bdd, $str);
				
				if(mysqli_num_rows($req)!=0)
				{
					$moyen=mysqli_fetch_object($req)->idMoyen;
					$dejaTrouv=true;
				}
				else{
					if(!in_array($moyen,$tabMoyenInconnue))
					{
						$tabMoyenInconnue[count($tabMoyenInconnue)]=$moyen;
					}
				}
			}
			
			//si non concluant aucun moyen affecté
			if(!$dejaTrouv)
			{					
				$moyen='';
				//echo  $tab["user_field_108015"]."  ".$tab["user_field_108014"]."  ".$tab["actv_code_tmm_moyen_dessai_id"]."  ".$ligne."<br/>";
			}
					
			
			
			$affaire=$tab["user_field_108008"];
			$equip=$tab["user_field_108010"];
			$of = $tab["user_field_108017"];
			$type_equip=preg_replace("/[^a-zA-Z]+/", "", $tab["user_field_107087"]);
			
			
			$tabOf=$tab["user_field_108017"];
			$tabOf=array_filter(explode("_",$tabOf));
			
			$os=$tab["user_field_108018"];
			
			$flag = $tab["user_field_108002"];
			
			$tabDateDebut=explode(" ",$tab["start_date"]);
			$date_debut=dateFrToSQL($tabDateDebut[0]);
			if(isset($tabDateDebut[1]))
			{				
				$date_debut.=" ".$tabDateDebut[1];
				$heure = explode(":",$tabDateDebut[1])[0];
			}
			else{
				$date_debut.=" 08:00:00";
				$heure = "08";
			}
			/*if (strtolower($flag)=="yellow"){
				
				$tabDateDebut=explode(" ",$tab["user_field_108022"]);
				$date_debut=dateFrToSQL($tabDateDebut[0]);
				if(isset($tabDateDebut[1])){
					
					$date_debut.=" ".$tabDateDebut[1];
					$heure = explode(":",$tabDateDebut[1])[0];
				}
				else{
					$date_debut.=" 08:00:00";
					$heure = "08";
				}
				
			}else if (strtolower($flag)=="blue"){
				
				$tabDateDebut=explode(" ",$tab["user_field_108024"]);
				$date_debut=dateFrToSQL($tabDateDebut[0]);
				if(isset($tabDateDebut[1])){
					$date_debut.=" ".$tabDateDebut[1];
					$heure = explode(":",$tabDateDebut[1])[0];
				}
				else{
					$date_debut.=" 08:00:00";
					$heure = "08";
				}
				
			}else if($tab["act_start_date"]=="")
			{
				$tabDateDebut=explode(" ",$tab["cstr_date"]);
				$date_debut=dateFrToSQL($tabDateDebut[0]);
				if(isset($tabDateDebut[1])){
					
					$date_debut.=" ".$tabDateDebut[1];
					$heure = explode(":",$tabDateDebut[1])[0];
				}
				else{
					$date_debut.=" 08:00:00";
					$heure = "08";
				}
			}
			else
			{
				$tabDateDebut=explode(" ",$tab["act_start_date"]);
				$date_debut=dateFrToSQL($tabDateDebut[0]);
				if(isset($tabDateDebut[1])){
					
					$date_debut.=" ".$tabDateDebut[1];
					$heure = explode(":",$tabDateDebut[1])[0];
				}
				else{
					$date_debut.=" 08:00:00";
					$heure = "08";
				}
			}*/			
			
			//Gestion des essais qui ont une heure de début superieur à la date de fin
			//Convention de 1 jour si c'est la cas (1 jour d'essai pas 24h)
			//On regarde si l'heure est inferueur à 12h
			if ($date_debut > $date_fin)
			{
				//Vérification que le premier caractède la date de début est un 0
				if ($heure[0] == '0')
				{					
					//Dans ce cas la l'heure est donc le deuxième caractère
					$heure = intval($heure[1]);
				
				//Sinon l'heure ne change pas
				}else $heure = intval($heure);
				
				//Si l'heure est inférieur à 13 cela signifie que l'essai commence le matin
				if ($heure < 13)
				{					
					//Saisie de la date de fin de l'essai
					$date_fin = dateFrToSQL($tabDateDebut[0]);
					$date_fin.=" 17:00:00";
				
				//Sinon l'essai commence l'après midi donc date de fin à midi le jour suivant
				}else $date_fin = date("Y-m-d 12:00:00", strtotime($date_debut . '+1 day'));

			}

			$str="SELECT idEssai, max(et.idEtat_etat) as idEtat FROM essai e, etatessai et
			where et.idEssai_ESSAI=e.idEssai 
			and idTachePrim='$idPrim';";
			$req=mysqli_query($bdd, $str);
			$lg=mysqli_fetch_object($req);
			
			$str="SELECT max(et.idEtat_etat) as idEtatPrecedent FROM essai e, etatessai et
			where et.idEssai_ESSAI=e.idEssai and idEtat_Etat != 27
			and idTachePrim='$idPrim';";
			$req=mysqli_query($bdd, $str);
			$etat=mysqli_fetch_object($req);
			if(isset($lg->idEssai) && $lg->idEssai != NULL) //la tache est deja connu
			{
				$idEssai=$lg->idEssai;
				$ligne = substr($idPrim, 0, 3);
				$check = substr($idPrim, 0, 2);
				if ($check == "LP")
				{					
					$str = "SELECT idLigne FROM ligneproduit WHERE nomLigne = '$ligne'";
					$req = mysqli_query($bdd, $str);
					$lg_ligne = mysqli_fetch_object($req);
					$ligne = $lg_ligne->idLigne;
					$str="UPDATE essai set ligneProd = $ligne  where idTachePrim='$idPrim';";
					 
				}else
				{					
					$str="UPDATE essai set ligneProd = null  where idTachePrim='$idPrim';";
				}
				$req = mysqli_query($bdd, $str);
				
				if($lg->idEtat < 22 || ($lg->idEtat == 27 && $etat->idEtatPrecedent < 22))//si essai max en attente ou maintenance
				{
					//champs mis à jours ?
					$str="update essai set affaire='$affaire', equipement='$equip', os='$os', idMoyen_moyen='$moyen', date_debut='$date_debut', date_fin='$date_fin', date_debut_prevu='$date_debut', date_fin_prevu='$date_fin'
					where idTachePrim='$idPrim';";
					$str = str_replace("''", "NULL", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
					$req=mysqli_query($bdd, $str);
					
					//forcément déja plannifié, mais si besoin on ajoute l'état reservé
					if($tab["user_field_108024"]!="" && $lg->idEtat ==20)
					{
						$str="insert into etatEssai values (date_format(now(),'%Y-%m-%d %H:%i:%s'),$idEssai,21);";
						$req=mysqli_query($bdd,$str);
						if(!$req)
						{
							echo "<div class='text-center'>";
								echo "<div class='alert alert-warning'><strong>Essai non mis à jours (tache primavera n°$idPrim)</strong></div>";
							echo "</div>";
						}
						
					}
					elseif($tab["user_field_108024"]=="" && $lg->idEtat ==21) //si date vide on retrograde l'etat de l'essai
					{
						$str="delete from etatEssai where idEssai_ESSAI='$idEssai' and idEtat_etat='21';";
						$req=mysqli_query($bdd,$str);
					}
					
					//corrige des erreurs de données dans le fichier excel
					if(strtolower($affaire)=="maint")
					{
						$str="insert into etatEssai values (date_format(now(),'%Y-%m-%d %H:%i:%s'),$idEssai,27);";
						$req=mysqli_query($bdd,$str);
					}
					else
					{
						$str="delete from etatEssai where idEssai_ESSAI='$idEssai' and idEtat_etat='27';";
						$req=mysqli_query($bdd,$str);
					}
					majOf($tabOf,$type_equip,$lg->idEssai,$bdd,$idPrim, substr($tab["user_field_108009"], 0,7));
				}else {
					
					if ($lg->idEtat >= 22 && $lg->idEtat <= 25 && strtolower($affaire)=="maint")
					{
						$str="insert into etatEssai values (date_format(now(),'%Y-%m-%d %H:%i:%s'),$idEssai,27);";
						$req=mysqli_query($bdd,$str);
						$str="update essai set affaire='$affaire', equipement='$equip', os='$os', idMoyen_moyen='$moyen', date_debut='$date_debut', date_fin='$date_fin', date_debut_prevu='$date_debut', date_fin_prevu='$date_fin'
						where idTachePrim='$idPrim';";
						$str = str_replace("''", "NULL", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
						$req=mysqli_query($bdd, $str);
					}else
					{						
						echo "<div class='text-center'>";
								echo "<div class='alert alert-warning'><strong>Essai non ajouté (tache primavera n°$idPrim, affaire : $affaire, of : $of)</br>Identifiant déjà existant</strong></div>";
						echo "</div>";
					}
				}
				
			}
			else //création de l'essai
			{
				//Récuperation de la ligne de produit
				$ligne = substr($idPrim, 0, 3);
				$check = substr($idPrim, 0, 2);
				if ($check == "LP")
				{					
					$str = "SELECT idLigne FROM ligneproduit WHERE nomLigne = '$ligne'";
					$req = mysqli_query($bdd, $str);
					$lg_ligne = mysqli_fetch_object($req);
					$ligne = $lg_ligne->idLigne;
					$str="insert into essai values (null, '$idPrim',null,'$affaire','$equip','$os',null,$labo,'$moyen','0','0','1',null,'$date_debut','$date_fin','$date_debut','$date_fin',0,0,0, '$ligne', 0);";
				}else
				{					
					$str="insert into essai values (null, '$idPrim',null,'$affaire','$equip','$os',null,$labo,'$moyen','0','0','1',null,'$date_debut','$date_fin','$date_debut','$date_fin',0,0,0, null, 0);";
				}
				
				$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
				$req=mysqli_query($bdd, $str);
				$idEssai=mysqli_insert_id($bdd);
				//ajout de l'etat de l'essai
				if(strtolower($affaire)!="maint")//si ce n'est pas une maintenance
				{
					//sur fichier primavera = plannifié au minimum = 20
					$str="insert into etatEssai values (date_format(now(),'%Y-%m-%d %H:%i:%s'),$idEssai,20);";
					$req=mysqli_query($bdd,$str);
					//si ce champs rempli = réservé = 21
					if($tab["user_field_108024"]!="")
					{
						$str="insert into etatEssai values (date_format(now(),'%Y-%m-%d %H:%i:%s'),$idEssai,21);";
						$req=mysqli_query($bdd,$str);
					}
					if(!$req)
					{
						echo "<div class='text-center'>";
							echo "<div class='alert alert-warning'><strong>Essai non ajouté (tache primavera n°$idPrim)</strong></div>";
						echo "</div>";
					}
					
				}
				else
				{
					$str="insert into etatEssai values (date_format(now(),'%Y-%m-%d %H:%i:%s'),$idEssai,27);";
					$req=mysqli_query($bdd,$str);
					if(!$req)
					{
						echo "<div class='text-center'>";
							echo "<div class='alert alert-warning'><strong>Maintenance non ajouté (tache primavera n°$idPrim)</strong></div>";
						echo "</div>";
					}
				}
				majOf($tabOf,$type_equip,$idEssai,$bdd,$idPrim,substr($tab["user_field_108009"], 0,7));
			}
		}	
	//}
	return $tabMoyenInconnue;	
}

if(isset($_FILES['file']) && is_uploaded_file($_FILES['file']['tmp_name']))
{
	//header('Content-Type: text/html; charset=UTF-8');
	require('../conf/connexion_param.php');
	require('../fonction.php');
	
	//test si le fichier est bien un csv
	$mimes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');
	if(in_array($_FILES['file']['type'],$mimes))
	{	
		// Chargement du fichier csv
		$fic = fopen($_FILES['file']['tmp_name'], "r");
		$date = date('Ymd');
		
		if($fic!=false)
		{
			$filename = $_FILES['file']['name'];
			$ext = pathinfo($filename, PATHINFO_EXTENSION);
			$allowed =  array('xls','xlsx');
			$ligne = 0; // compteur de ligne
			$tabKey=array();
			$tab = array();
			$nbchamps=0;
			$tabMoyenInconnue=array(); //tableau affichant les moyens encore inconnue
			if($_FILES['file']['type']=='application/vnd.ms-excel' && in_array($ext,$allowed)) //fichier excel
			{
				include '../Classes/PHPExcel.php';
				include '../Classes/PHPExcel/Writer/Excel2007.php';
 
				// Chargement du fichier Excel
				$objPHPExcel = PHPExcel_IOFactory::load($_FILES['file']['tmp_name']);
				$sheet = $objPHPExcel->getSheet(0);
				 
				foreach($sheet->getRowIterator() as $row)
				{
					if($ligne==0)
					{

						foreach ($row->getCellIterator() as $cell) {
							$tabKey[$nbchamps]=htmlspecialchars(mysqli_real_escape_string($bdd,$cell->getValue()));
							$nbchamps++;
						}
					}
					elseif($ligne!=1)
					{
						$cellIterator = $row->getCellIterator();
						$cellIterator->setIterateOnlyExistingCells(false); // Boucle sur toutes les cellules, meme vide
						$i=0;
						foreach ($cellIterator as $cell)
						{
								$val=trim($cell->getValue());
								if($val=="")
									$tab[$tabKey[$i]]=""; 
								else
									$tab[$tabKey[$i]]=htmlspecialchars(mysqli_real_escape_string($bdd,$val));
							
							$i++;
						}
						$tabMoyenInconnue=ajouterEssai($tab,$tabMoyenInconnue,$bdd);
					}
					$ligne++;
				}	
			}
			else //csv
			{
				while($tabCSV=fgetcsv($fic,1024,';'))
				{
					if($ligne==0)
					{
						$nbchamps = count($tabCSV);//nombre de champ dans la ligne en question
						for($i=0;$i<$nbchamps;$i++)
						{
							$tabKey[$i]=htmlspecialchars(mysqli_real_escape_string($bdd,$tabCSV[$i]));
						}
					}
					
					elseif($ligne!=1) //on saute la deuxieme ligne
					{
						for($i=0;$i<$nbchamps;$i++)
						{
							$tabCSV[$i]=trim($tabCSV[$i]);
							if($tabCSV[$i]=="")
								$tab[$tabKey[$i]]=""; 
							else
								$tab[$tabKey[$i]]=htmlspecialchars(mysqli_real_escape_string($bdd,$tabCSV[$i]));
						}
						$tabMoyenInconnue=ajouterEssai($tab,$tabMoyenInconnue,$bdd);
						
						
					}
					$ligne++;
				}
			}
			echo "<div class='text-center'>";
				echo "<div class='alert alert-success'><strong>Mise à jour effectuée</strong></div>";
				echo "<input class='btn btn-lg btn-primary' type='button' value='Retour' onclick='document.location.href=\"index.php\"' />";
			echo "</div>";
			?>
			<div class="container">
				<h4>Veuillez ajouter ces moyens dans la base de données, ou les ajouter en tant qu'alias de moyens déja existant</h4>
				<div class="jumbotron">
					<table class="table table-striped table-tri" id="tri">
						<thead>
							<tr >
								<th>Nom du moyen inconnu</th>
								
							</tr>
						</thead>
						<tbody>
							<?php
							foreach($tabMoyenInconnue as $moyenInconnu)
								echo '<tr><td>'.$moyenInconnu.'</td></tr>';
							?>
						</tbody>
						<tfoot>
							<th>Nom du moyen inconnu</th>
						</tfoot>
					</table>
				</div>
			</div>
			<script type="text/javascript" language="javascript" src="../DataTables/jquery.dataTables.js"></script>
			<script type="text/javascript" language="javascript" src="../DataTables/columnFilter.js"></script>
			<script type="text/javascript" charset="utf-8">
				$(document).ready(function() {
					$('#tri').dataTable().columnFilter();
					$('#tri_filter input').attr("placeholder", "Rechercher");
					$('#tri_filter input').attr("class", "form-control");
					$('#tri_filter input').attr("style", "font-weight:normal;");
					$('#tri_length select').attr("class", "form-control");
				} );
			</script>
			<?php
			
		}
		else
			echo "<div class='alert alert-danger'><strong>Erreur lors de l'ouverture du fichier</strong></div>";

		fclose($fic);
		//echo "<script>document.location.href = './primavera.php'</script>";
	}
	else {
		echo "<div class='text-center'>";
			echo "<div class='alert alert-warning'><strong>Le fichier doit être de type csv</strong></div>";
			echo "<input class='btn btn-lg btn-primary' type='button' value='Retour' onclick='document.location.href=\"primavera.php\"' />";
		echo "</div>";
	}
}
else
{
?>
	<div id="se-pre-con-load" ></div>
	<div class="container">
		<div class="page-header">
			<h2>Mise à jour réservations Primavera</h2>
		</div>
		<div class="container theme-showcase" role="main">
			<form enctype="multipart/form-data" method="post" action="primavera.php" onSubmit="return lancerAnnim()" role="form">
				<div class="jumbotron">
					<input title="Fichier Excel Export Primavera"  class="form-control" style="height:auto;"  type="file" name="file" required/>
				</div>	
				<div class="text-center">
					<button class="btn  btn-primary " >Effectuer la mise à jour</button>
				</div>
			</form>
			
			
		</div>
	</div><!-- /.container -->
	<script src="../js/loading.js"></script>
	<?php
}
require('bottom.php');
