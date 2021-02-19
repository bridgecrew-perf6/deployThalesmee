<?php 
//header('Content-Type: text/html; charset=UTF-8');
require('../conf/connexion_param.php');
require('top.php');

// Chargement du fichier Excel
if(isset($_FILES['file']) && is_uploaded_file($_FILES['file']['tmp_name']))
{
	$mimes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv','application/force-download');
	if(in_array($_FILES['file']['type'],$mimes))
	{
		$jour=date('w');
		if($jour!=6 && $jour!=7)
		{
			$date = date('Ymd');
			// Chargement du fichier csv
			$fic = fopen($_FILES['file']['tmp_name'], "r");
			//$fic = fopen("\\\\172.16.2.20\\p\\cos\\pe\\save\\deca20160427.csv", "r");
			if($fic!=false)
			{
				$ligne = 0; // compteur de ligne
				$tabKey=array();
				$tab = array();
				$erreur="";
				$nbchamps=0;

				$cpt = 0;
				while($tabCSV=fgetcsv($fic,1024,';'))
				{
					$cpt+=1;
					if($ligne==0)
					{
						$nbchamps = count($tabCSV);//nombre de champ dans la ligne en question
						for($i=0;$i<$nbchamps;$i++)
						{
							$tabKey[$i]=htmlspecialchars(mysqli_real_escape_string($bdd,$tabCSV[$i]));
						}
					}
					else
					{
						for($i=0;$i<$nbchamps;$i++)
						{
							if(trim($tabCSV[$i])=="")
								$tab[$tabKey[$i]]=""; 
							else
								$tab[$tabKey[$i]]=htmlspecialchars(mysqli_real_escape_string($bdd,$tabCSV[$i]));
						}
						//on verifi si l'affectation financiere correspond a un des trois labo
						$affectFI=$tab["AFFECTATION"];
						
						$labo="";
						if($affectFI=="024EMC")
						{
							$labo="instrument_emc";
							$idLabo=1;
							$champs="(idInstruEmc,numInstru_instrument)";
						}
						elseif($affectFI=="024VIB")
						{
							$labo="instrument_vib";
							$idLabo=2;
							$champs="(idInstruVib,numInstru_instrument)";
						}
						elseif($affectFI=="024VTH")
						{
							$labo="instrument_vth";
							$idLabo=3;
							$champs="(idInstruVth,numInstru_instrument)";
						}
						
						if($labo!="")
						{
							$numInstru=$tab["﻿MARQUAGE"];			
							$etat=$tab["ETAT"];
							if(isset($tab["DERNIERE_OP"])) $dateDerInt=$tab["DERNIERE_OP"]; else $dateDerInt="";
							if(isset($tab["PROCHAINE_OP"])) $dateFutInt=$tab["PROCHAINE_OP"]; else $dateFutInt="";
							if(isset($tab["NSERIE"]) && strlen($tab["NSERIE"]) < 30) $numSerie=$tab["NSERIE"]; else $numSerie="";
							
							if(isset($tab["CONSTRUCTEUR"]))$marque=$tab["CONSTRUCTEUR"]; else $marque="";
							$modele=$tab["REFERENCE_CONSTRUCTEUR"];
							if(isset($tab["PERIODE"])) $periode=$tab["PERIODE"]; else $periode="";
							if(isset($tab["DOMAINE"]))$dom=$tab["DOMAINE"];	else $dom="";
							$trescId=$tab["CODE_BARRE"];
							$desi=$tab["INTITULE_DESIGNATION"];
							$statut=$tab["SITUATION_METROLOGIQUE"];
							$ancNum=$tab["ANCIEN_MARQUAGE"];
							
							//designation
							//on verifie si elle existe deja dans la base
							$str="select idDes from designation where nomDes='$desi';";
							$req=mysqli_query($bdd, $str);
							if(!$req)
								$erreur.="Erreur de test des désignations. ";
							if(mysqli_num_rows($req)==0) //designation inconue
							{
								$str="select idDom from domaine where nomDom='$dom';";
								$req=mysqli_query($bdd, $str);
								if(mysqli_num_rows($req)==0) //domaine inconnue
								{
									$str="INSERT INTO domaine VALUES(null,'$dom');";
									$req=mysqli_query($bdd, $str);
									$idDom=mysqli_insert_id($bdd);
								}
								else
									$idDom=mysqli_fetch_object($req)->idDom;
								
								$str="INSERT INTO designation VALUES(null,'$desi','$idDom');";
								$req=mysqli_query($bdd, $str);
								if(!$req)
									$erreur.="Erreur de d'ajout des désignations. ";
								else
									$idDes=mysqli_insert_id($bdd);
							}
							else
								$idDes=mysqli_fetch_object($req)->idDes;
							
							
							//etat
							//on verifie si il existe deja dans la base
							$str="select idEtat from etat where nomEtat='$etat';";
							$req=mysqli_query($bdd, $str);
							if(!$req)
								$erreur.="Erreur de test des etats. ";
							if(mysqli_num_rows($req)==0) //etat inconu
							{
								
								$str="INSERT INTO etat VALUES 
								(NULL,'$etat');";
								$req=mysqli_query($bdd, $str);
								if(!$req)
									$erreur.="Erreur de d'ajout des désignations. ";
								$idEtat=mysqli_insert_id($bdd);
							}
							else
								$idEtat=mysqli_fetch_object($req)->idEtat;
							
							//statut
							$idLocal="";
							if($statut=="Disponible")
							{
								$idStatut=1;
								$idLocal=1;
							}
							elseif($statut=="En cours")
							{
								$idStatut=2;
								$idLocal=2;
							}
							else //normalement n'arrivera pas
								$idStatut="";
							
							//on ajuste le statut (non disponible si perdu,rebu etc)
							if($idEtat==3 || $idEtat==5 || $idEtat==6)
								$idStatut=4;
							
							//on adapte le format des dates au format sql
							if($dateDerInt!="")
							{
								$ddi=explode("/",str_replace(' ','/',$dateDerInt));
								$dateDerInt=$ddi[2]."-".$ddi[1]."-".$ddi[0];
							}
							
							if($dateFutInt!="")
							{
								$dfi=explode("/",str_replace(' ','/',$dateFutInt));
								$dateFutInt=$dfi[2]."-".$dfi[1]."-".$dfi[0];
							}
							
							
							
							//instruments
							$str="select numInstru, idLocal_localisation, idStatut_statut from instrument where numInstru='$numInstru';";
							$req=mysqli_query($bdd, $str);
							if(mysqli_num_rows($req)==0) //instrument inconue
							{
								$str="INSERT INTO instrument VALUES 
								('$numInstru','$ancNum','$trescId','$idDes','$marque','$modele','$numSerie','$affectFI','$dateDerInt',
								'$dateFutInt','$periode',$idEtat,'$idLocal','$idStatut',null);";
								$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
								$req=mysqli_query($bdd, $str);
								if(!$req)
									$erreur.="Erreur de d'ajout des instruments. ";
								
								//verification en cas de changement des numInstru
								$str="SELECT i1.numInstru FROM `instrument` i1, instrument i2 WHERE i1.trescalid=i2.trescalid
								and i1.`numInstru` != i2.`numInstru`
								and i1.`numInstru` = '$numInstru';";
								$req=mysqli_query($bdd, $str);
								if(!$req)
									echo mysqli_error($bdd);
								if(mysqli_num_rows($req)!=0) //besoin d'adapter
								{	
									echo "ihghgh";
									$str="update $labo set numInstru_instrument='$numInstru' where numInstru_instrument='$ancNum';";
									$req=mysqli_query($bdd, $str);
									echo mysqli_error($bdd);
									$str="delete from instrument where numInstru='$ancNum'";
									$req=mysqli_query($bdd, $str);
									echo mysqli_error($bdd);
									
								}
								else
								{
									if($idLabo==3)
									{
										$str="insert into $labo $champs values(NULL,'$numInstru');";
										$req=mysqli_query($bdd, $str);
										echo mysqli_error($bdd);
									}
									else
									{
										//ajout a la table pour demande de spec
										$str="INSERT INTO ajoutInstru VALUES ('$numInstru','$idLabo');";
										$req=mysqli_query($bdd, $str);
									}
								}
							}
							else
							{
								$lg=mysqli_fetch_object($req);
								/*
								On modifie l'ancienne Localisation si : 
									null / vide
									en calib
									en stock
									passage en calib
								*/
								$idLocalAct=$lg->idLocal_localisation;
								$nouvLoc=0;
								if($idLocalAct==null || $idLocalAct=="" || $idLocalAct==1 || $idLocalAct==2 || $idLocal==2)
								{
									$nouvLoc=$idLocal;
								}
								else
								{
									$nouvLoc=$idLocalAct;
								}
								
								//si statut sur prété en local, on ne modifie pas ce statut
								if($lg->idStatut_statut==3)
									$idStatut=3;
								
								
								$str="update instrument set ancienNum='$ancNum', trescalId=COALESCE('$trescId',trescalId), idDes_designation=COALESCE('$idDes',idDes_designation), 
								marque=COALESCE('$marque',marque), modele=COALESCE('$modele',modele), numSerie=COALESCE('$numSerie',numSerie), affectF=COALESCE('$affectFI',affectF),
								date_derniereInt=COALESCE('$dateDerInt',date_derniereInt), `date_futureInt`=COALESCE('$dateFutInt',date_futureInt), `periodicite`=COALESCE('$periode',periodicite),
								idetat_etat=COALESCE('$idEtat',idetat_etat), idStatut_statut=COALESCE('$idStatut',idStatut_statut), idLocal_localisation=COALESCE('$nouvLoc',idLocal_localisation)
								where numInstru='$numInstru'";
								
								/*
								$str="update instrument set ancienNum='$ancNum', trescalId='$trescId', idDes_designation='$idDes', marque='$marque', modele='$modele',
								numSerie='$numSerie', affectF='$affectFI', date_derniereInt='$dateDerInt', `date_futureInt`='$dateFutInt',
								`periodicite`='$periode', idetat_etat='$idEtat', idStatut_statut='$idStatut', idLocal_localisation='$nouvLoc'
								where numInstru='$numInstru'";
								*/
								
								$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
								$req=mysqli_query($bdd, $str);
								if(!$req)
									$erreur.="Erreur de de modif des instruments. ";
							}					
						}
					}
					$ligne++;
				}

				echo $cpt;
				if($erreur!="")
				{
					echo "<div class='alert alert-danger'><strong>$erreur</strong></div>";
				}
			}
		}
	}
}else{
?>
	<script type="text/javascript">//lance une animation le temps du chargement
	function lancerAnnim()
	{
		$("#se-pre-con-load").show();
		return true;
	}
	</script>
	<div id="se-pre-con-load" ></div>
	<div class="container">
		<div class="page-header">
			<h2>Mise à jour réservations Trescal</h2>
		</div>
		<div class="container theme-showcase" role="main">
			<form enctype="multipart/form-data" method="post" action="majTrescal.php" onSubmit="return lancerAnnim()" role="form">
				<div class="jumbotron">
					<input title="Fichier Excel Export Trescal"  class="form-control" style="height:auto;"  type="file" name="file" required/>
				</div>	
				<div class="text-center">
					<button class="btn  btn-primary " >Effectuer la mise à jour</button>
				</div>
			</form>
			
			
		</div>
	</div>
<?php
}
require('bottom.php');