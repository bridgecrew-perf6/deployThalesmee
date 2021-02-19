<?php
require("top.php");
require("../fonction.php");

if(isset($_POST["equip"]))
{
	require('../conf/connexion_param.php'); //connexion a la bdd
	
	$numInstru=$_POST["numInstru"];
	
	$model=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["model"]));
	$serie=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["serie"]));
	if(isset($_POST['etat']))$etat=$_POST['etat']; else $etat="";
	
	$ancienNum=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["aNum"]));
	$fab=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["fab"]));
	$lastCal=dateFrToSQL(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["lCal"])));
	$nextCal=dateFrToSQL(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["nCal"])));
	
	$equip=$_POST["equip"];
	$fonc=$_POST["fonc"];
	if(isset($_POST["loca"]))$loca=$_POST["loca"]; else $loca="";
	$cara=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["cara"]));
	$com=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["com"]));
	
	$str="update instrument_emc set caracteristique='$cara', idDes_designation_emc='$fonc' where numInstru_instrument='$numInstru';";
	$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
	$req=mysqli_query($bdd, $str);
	if(!$req)
		echo "<div class='alert alert-danger'><strong>Erreur de modification de l'instrument</strong></div>";
	else
	{
		$fichier=$_FILES['monfichier'];
		uploadPhoto($numInstru,$fichier);
		
		$str="update instrument set idLocal_localisation='$loca', modele='$model', numSerie='$serie', idEtat_etat='$etat', ancienNum='$ancienNum',
		marque='$fab', date_derniereInt='$lastCal', date_futureInt='$nextCal', commentaire='$com' where numInstru='$numInstru';";
		$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
		$req=mysqli_query($bdd, $str);
		echo "<div class='text-center'>";
			echo "<div class='alert alert-success'><strong>Modifications validées</strong></div>";
			echo "<input class='btn btn-lg btn-primary' type='button' value='Retour' onclick='document.location.href=\"detailsInstru.php?numInstru=$numInstru\"' />";
		echo "</div>";
	}
	
}
elseif(!isset($_GET["numInstru"]))
	echo "<div class='alert alert-danger'><strong>Erreur de réception des paramétres</strong></div>";
else
{
	if(!isset($bdd))//on evite les multiples inclusions
		require('../conf/connexion_param.php');
	if(!isset($numInstru))
		$numInstru=$_GET["numInstru"];

	$str="select idDes_designation_emc, ie.caracteristique, i.date_derniereInt,  i.date_futureInt, i.idLocal_localisation,
	do.nomDom, de.nomDes as desTres, i.modele, i.marque, i.numSerie, i.idEtat_etat, i.ancienNum, i.commentaire,
	i.trescalId, i.periodicite, i.affectF, d.idEquip_equipement_emc
	from statut t,
	instrument_emc ie LEFT OUTER JOIN designation_emc d ON ie.idDes_designation_emc=d.idDes,
	instrument i
	LEFT OUTER JOIN designation de ON i.idDes_designation=de.idDes
	LEFT OUTER JOIN domaine do ON do.idDom=de.idDom_domaine
	where ie.numInstru_instrument='$numInstru'
	and ie.numInstru_instrument=i.numInstru
	and i.idStatut_statut=t.idStatut;";
	$req=mysqli_query($bdd, $str);
	echo mysqli_error($bdd);
	if(!$req)
		echo "<div class='alert alert-danger'><strong>Erreur de récupération des infos sur l'instrument</strong></div>";
	else
	{
		$lg=mysqli_fetch_object($req);

		if($lg->date_derniereInt!="")
			$derniereInt=dateSQLToFr($lg->date_derniereInt);
		else
			$derniereInt="";
		if($lg->date_futureInt!="")
			$futureInt=dateSQLToFr($lg->date_futureInt);
		else
			$futureInt="";
		
		
		$str="select idEquip, nomEquip from equipement_emc;";
		$reqEquip=mysqli_query($bdd, $str);
		
		$str="select idDes, fonction from designation_emc where idEquip_equipement_emc='".$lg->idEquip_equipement_emc."';";
		$reqDes=mysqli_query($bdd, $str);
		
		$str="select idLocal, nomLocal from localisation where idLabo_labo=1 or idLabo_labo is null;";
		$reqLoc=mysqli_query($bdd, $str);
		
		$str="select idEtat, nomEtat from etat;";
		$reqEtat=mysqli_query($bdd, $str);
		
		?>
		<link href="../calendrier/calendrier.css" rel="stylesheet" />
		<div class="container">
			<form enctype="multipart/form-data" method="post" action="modifInstru.php">
				<div class="page-header">
					<h2>Modification de l'instrument <?php echo $numInstru ;?></h2> 
				</div>
				<h4>Informations générales</h4>
				<div class="jumbotron">
					<div class="row">
						<div class="col-md-4">
							<div class="info"><label>Numéro Immo:&nbsp </label><?php echo $numInstru; ?></div>
							<input id="model" name="model" title="Type/Modèle" type="text" class="form-control" value="<?php echo $lg->modele; ?>" placeholder="Type/Modèle" required />			
							<input id="serie" name="serie" title="Numéro de série" type="text" class="form-control" value="<?php echo $lg->numSerie; ?>" placeholder="Numéro de série" required />			
							<select id="etat" title="État" class="form-control" name="etat" required>
								<option value="" disabled selected>État</option>
								<?php
									while($lgEtat=mysqli_fetch_object($reqEtat))
									{
										if($lgEtat->idEtat==$lg->idEtat_etat)
											echo '<option value="'.$lgEtat->idEtat.'" selected>'.$lgEtat->nomEtat.'</option>';
										else
											echo '<option value="'.$lgEtat->idEtat.'">'.$lgEtat->nomEtat.'</option>';
										
									}
								?>
							</select>
						</div>
						<div class="col-md-4">
							<input id="aNum" name="aNum" title="Ancien immo" type="text" class="form-control" value="<?php echo $lg->ancienNum; ?>" placeholder="Ancien immo" />
							<input id="fab" name="fab" title="Fabricant" type="text" class="form-control" value="<?php echo $lg->marque; ?>" placeholder="Fabricant" required />
							<input placeholder="Dernière cal: JJ/MM/YYYY" type="text"  id="lCal" name="lCal" value="<?php echo $derniereInt; ?>" class="calendrier form-control" size="8" />
							<input placeholder="Prochaine cal: JJ/MM/YYYY" type="text"  id="nCal" name="nCal" value="<?php echo $futureInt; ?>" class="calendrier form-control" size="8" />
						</div>
						<div class="col-md-4">
							<select id="equip" title="Equipement" class="form-control" name="equip">
								<option value="-1" disabled selected>Equipement</option>
								<?php
									while($lgEquip=mysqli_fetch_object($reqEquip))
									{
										if($lgEquip->idEquip==$lg->idEquip_equipement_emc)
											echo '<option value="'.$lgEquip->idEquip.'" selected>'.$lgEquip->nomEquip.'</option>';
										else
											echo '<option value="'.$lgEquip->idEquip.'">'.$lgEquip->nomEquip.'</option>';
									}
								?>
							</select>
							<select id="fonc" title="Fonction" class="form-control" name="fonc">
								<option value="-1" disabled selected>Fonction</option>
								<?php
									while($lgDes=mysqli_fetch_object($reqDes))
									{
										if($lgDes->idDes==$lg->idDes_designation_emc)
											echo '<option value="'.$lgDes->idDes.'" selected>'.$lgDes->fonction.'</option>';
										else
											echo '<option value="'.$lgDes->idDes.'">'.$lgDes->fonction.'</option>';
									}
								?>
							</select>
							<select id="loca" title="Localisation" class="form-control" name="loca">
								<option value="-1" disabled selected>Localisation</option>
								<?php
									while($lgLoc=mysqli_fetch_object($reqLoc))
									{
										if($lgLoc->idLocal==$lg->idLocal_localisation)
											echo '<option value="'.$lgLoc->idLocal.'" selected>'.$lgLoc->nomLocal.'</option>';
										else
											echo '<option value="'.$lgLoc->idLocal.'">'.$lgLoc->nomLocal.'</option>';
									}
								?>
							</select>
							<input class="form-control" type="text" name="cara" value="<?php echo $lg->caracteristique; ?>" placeholder="Caracteristique" title="Caracteristique"/>
						</div>
					</div>
					<div>
						<textarea name="com" class="form-control" placeholder="Remarque" title="Remarque"><?php echo $lg->commentaire; ?></textarea> 
					</div>
					Photo de l'instrument:<input title="Photo de l'instrument"  class="form-control" style="height:auto;" id="file"  type="file" name="monfichier"/>
				</div>
				
				<input type="button" id="b_det" class="btn btn-success" value="+" />
				
				<div id="detailsSup" class="show_hide">
					<h4>Informations complémentaires</h4>
					
					<!-- détails -->
					<div class="jumbotron">
						<div class="row">
							<div class="col-md-4">
								<div class="info"><label>Trescal ID:&nbsp </label><?php echo $lg->trescalId; ?></div>
								<div  id="info_aff"><label>Affectation financière:&nbsp </label><?php echo $lg->affectF; ?></div>
								
							</div>
							<div class="col-md-4">
								<div class="info" id="info_des"><label>Désignation:&nbsp </label><?php echo $lg->desTres; ?></div>
							</div>
							<div class="col-md-4">
								<div class="info"><label>Périodicité:&nbsp </label><?php echo $lg->periodicite; ?></div>
								
							</div>
						</div>
					</div>
				</div>
				<div class="text-center">
					<input type="hidden" name="numInstru" value="<?php echo $numInstru; ?>" />
					<input type="submit" value="Valider" class="btn btn-primary btn-lg" />
					<input type="button" value="Annuler" class="btn btn-primary btn-lg" onclick="document.location.href='detailsInstru.php?numInstru=<?php echo $numInstru; ?>'"/>
				</div>
			</form>
		</div>
		<script src="../calendrier/calendrier.js"></script>
		<script src="../js/crudInstru.js"></script>
		<?php
	}
}
require("bottom.php");