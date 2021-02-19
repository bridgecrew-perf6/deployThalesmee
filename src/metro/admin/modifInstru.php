<?php
require('top.php');
require('../fonction.php');
if(isset($_POST["etat_fiche"]))
{
	require('../conf/connexion_param.php'); //connexion a la bdd
	$numInstru=$_POST["numInstru"];
	
	//htmlspecialchars remplace les caracteres speciaux par leurs équivalent html, évite la plupart des erreurs/failles d'injection sql avec par exemple '		
	$etat_fiche=$_POST['etat_fiche'];
	$statut_fiche=$_POST['statut_fiche'];
	$derniereInt=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['derniereInt']));
	$futureInt=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['futureInt']));
	$periodicite=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['periodicite']));
	$etat=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['etat']));
	$statut=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['statut']));
	
	//construction des dates format sql
	$date_derniereInt=dateFrToSQL(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['date_derniereInt'])));
	$date_futureInt=dateFrToSQL(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST['date_futureInt'])));
	
	//FINIR UPDATE
	$str="update instrument set etat_fiche='$etat_fiche', statut_fiche='$statut_fiche', derniereInt='$derniereInt', futureInt='$futureInt',
	periodicite='$periodicite', date_derniereInt='$date_derniereInt', date_futureInt='$date_futureInt', idEtat_etat='$etat', idStatut_statut='$statut'
	where numInstru='$numInstru';";
	$req=mysqli_query($bdd, $str);
	if(!$req)
		echo "<div class='alert alert-danger'><strong>Erreur de modification de l'instrument</strong></div>";
	else
	{
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
	require('../conf/connexion_param.php');
	
	$numInstru=$_GET["numInstru"];
	$table="";
	$col="";
	$where="";
	$type=2; //variable pour savoir si l'instrument est un capteur sans avoir a faire plusieurs fois le test
	// 0 => classique | 1 => capteur | 2 => autre
	//test si l'instrument est un capteur (je choisi de tester dans cette table car elle est plus petite que celle des instru standard)
	$str="select idInstruCapt
	from instrument_capteur
	where numInstru_instrument='$numInstru';";
	$req=mysqli_query($bdd, $str);
	if(mysqli_num_rows($req)!=0) //l'instrument est un capteur
	{
		$table=",instrument_capteur ic";
		$col=",axeX, sensiX, axeY, sensiY, axeZ, sensiZ, axeZs, sensiZs";
		$where="and ic.numInstru_instrument=i.numInstru";
		$type=1;
	}
	else
	{
		//test si l'instrument est un classique
		$str="select idInstruClass
		from instrument_classique
		where numInstru_instrument='$numInstru';";
		$req=mysqli_query($bdd, $str);
		if(mysqli_num_rows($req)!=0) //l'instrument est un classique
		{
			$table=",instrument_classique ic";
			$col=",description, commentaire";
			$where="and ic.numInstru_instrument=i.numInstru";
			$type=0;
		}
	}
	
	
	$str="select numInstru, ancienNum, trescalId, nomDes, modele, marque, numSerie, etat_fiche, statut_fiche,periodicite, derniereInt, 
	date_derniereInt, futureInt, date_futureInt, af.filiere, entityURL, joursRetard, justification, d.domaine, d.famille, d.sous_famille,
	af.entreprise, af.idVille, af.centreComp, af. departement, i.idEtat_etat, i.idStatut_statut $col
	from instrument i, designation d, affectation_financiere af $table
	where i.idDes_designation=d.idDes
	and i.numInstru='$numInstru'
	and i.idAffectF_affectation_financiere=af.idAffectF
	$where;";
	$req=mysqli_query($bdd, $str);
	echo mysqli_error($bdd);
	if(!$req)
		echo "<div class='alert alert-danger'><strong>Erreur de récupération des infos sur l'instrument</strong></div>";
	else
	{
		$correspondant="";
		//correspondant
		$str="select nomEmp, prenomEmp from employe e, correspondant c
		where c.idEmp_employe=e.idEmp
		and c.numInstru_instrument='$numInstru';";
		$reqCor=mysqli_query($bdd, $str);
		if(mysqli_num_rows($reqCor)!=0)
		{
			while($lgCor=mysqli_fetch_object($reqCor))
				$correspondant.=$lgCor->prenomEmp." ".$lgCor->nomEmp." / ";
			$correspondant=substr($correspondant,0,-3); //supprime le dernier " / "
		}
		//destinataire
		$destinataire="";
		$str="select nomEmp, prenomEmp from employe e, destinataire d
		where d.idEmp_employe=e.idEmp
		and d.numInstru_instrument='$numInstru';";
		$reqCor=mysqli_query($bdd, $str);
		if(mysqli_num_rows($reqCor)!=0)
		{
			$lgCor=mysqli_fetch_object($reqCor);
			$destinataire=$lgCor->prenomEmp." ".$lgCor->nomEmp;
		}
		
		//liste des etats possibles
		$str="select idEtat ,nomEtat from etat;";
		$reqEtat=mysqli_query($bdd, $str);
		
		//liste des statuts possibles
		$str="select idStatut ,nomStatut from statut;";
		$reqStatut=mysqli_query($bdd, $str);
			
		$utilisateur="";
		//utilisateur_instrument
		$str="select nomEmp, prenomEmp from employe e, utilisateur_instrument u
		where u.idEmp_employe=e.idEmp
		and u.numInstru_instrument='$numInstru';";
		$reqCor=mysqli_query($bdd, $str);
		if(mysqli_num_rows($reqCor)!=0)
		{
			$lgCor=mysqli_fetch_object($reqCor);
			$utilisateur=$lgCor->prenomEmp." ".$lgCor->nomEmp;
		}

		$lg=mysqli_fetch_object($req);
		$nbRetard=$lg->joursRetard;
		
		if($lg->date_derniereInt!="")
			$derniereInt=dateSQLToFr($lg->date_derniereInt);
		else
			$derniereInt="";
		if($lg->date_futureInt!="")
			$futureInt=dateSQLToFr($lg->date_futureInt);
		else
			$futureInt="";

		?>
		<link href="../calendrier/calendrier.css" rel="stylesheet" />
		<div class="container">
			<form method="post" action="modifInstru.php">
				<div class="page-header">
					<h2>Modifier l'instrument <?php echo $numInstru; ?></h2> 
					<input type="button" value="Annuler" class="btn btn-primary btn-lg" onclick="document.location.href='detailsInstru.php?numInstru=<?php echo $numInstru; ?>'"/>
				</div>
				<div class="container theme-showcase" role="main">
					<h4>Informations générales</h4>
					
					<!-- bulle d'information sur la désignation -->
					<div class="div_info_des" id="div_info_des">
						<p><label>Domaine:&nbsp </label><?php echo $lg->domaine; ?></p>
						<p><label>Famille:&nbsp </label><?php echo $lg->famille; ?></p>
						<p><label>Sous famille:&nbsp </label><?php echo $lg->sous_famille; ?></p>
					</div>
					
					<!-- détails -->
					<div class="jumbotron">
						<div class="row">
							<div class="col-md-4">
								<div class="info"><label>Numéro Immo:&nbsp </label><?php echo $numInstru; ?></div>
								<select class="form-control" name="etat" title="État">
									<option value="-1" disabled>État</option>
									<?php 
									while($lg2=mysqli_fetch_object($reqEtat))
									{
										$id=$lg2->idEtat;
										$nom=$lg2->nomEtat;
										if($id==$lg->idEtat_etat)
											echo "<option value='$id' selected>$nom</option>";
										else
											echo "<option value='$id'>$nom</option>";
									}
									?>
								</select>
								<select class="form-control" name="etat_fiche" title="État fiche">
									<option value="-1" disabled>État fiche</option>
									<?php 
									if($lg->etat_fiche =="Conforme")
									{
										echo '<option value="Conforme" selected>Conforme</option>';
										echo '<option value="Non conforme" >Non conforme</option>';
									}
									else
									{
										echo '<option value="Conforme">Conforme</option>';
										echo '<option value="Non conforme" selected>Non conforme</option>';
									}
									?>
										
	
								</select>
								
								<select class="form-control" name="statut_fiche" title="Statut fiche">
									<option value="-1" disabled>Statut fiche</option>
									<?php 
									
									if($lg->statut_fiche =="Utilisable")
									{
										echo '<option value="Utilisable" selected>Utilisable</option>';
										echo '<option value="Non utilisable" >Non utilisable</option>';
									}
									else
									{
										echo '<option value="Utilisable">Utilisable</option>';
										echo '<option value="Non utilisable" selected>Non utilisable</option>';
									}
									?>
										
								</select>
								
							</div>
							<div class="col-md-4">
								<div class="info"><label>Trescal ID:&nbsp </label><?php echo $lg->trescalId; ?></div>
								<select class="form-control" name="statut" title="Statut">
									<option value="-1" disabled>Statut</option>
									<?php 
									while($lg2=mysqli_fetch_object($reqStatut))
									{
										$id=$lg2->idStatut;
										$nom=$lg2->nomStatut;
										if($id==$lg->idStatut_statut)
											echo "<option value='$id' selected>$nom</option>";
										else
											echo "<option value='$id'>$nom</option>";
									}
									?>
								</select>
								<input type="text" name="derniereInt" value="<?php echo $lg->derniereInt; ?>" class="form-control" placeholder="Dernière intervention" title="Dernière intervention" required/>
								<input type="text" name="futureInt" value="<?php echo $lg->futureInt; ?>" class="form-control" placeholder="Future intervention" title="Future intervention" required/>
							</div>
							<div class="col-md-4">
								<div class="info" id="info_des"><label>Désignation:&nbsp </label><?php echo $lg->nomDes; ?></div>
								<input type="text" name="periodicite" value="<?php echo $lg->periodicite; ?>" class="form-control" placeholder="Périodicite" title="Périodicite" required/>
								<div class="autre-form">
									<span title="Date dernière intervention"><label>Date DI:</label> <input placeholder="01/01/2014" value="<?php echo $derniereInt; ?>" type="text" name="date_derniereInt" class="calendrier"  size="8" required/></span>
								</div>
								<div class="autre-form">
									<span title="Date future intervention"><label>Date FI:</label> <input placeholder="01/01/2014" value="<?php echo $futureInt; ?>" type="text" name="date_futureInt" class="calendrier"  size="8" required/></span>
								</div>
							</div>
						</div>
					</div>
					
					
					<?php
					//si besoin on affiche ces informations
					if($type==0) // si instru classique
					{
						?>
						<h4>Précisions sur l'instruments</h4>
						<div class="jumbotron">
							<div class="row">
								<div class="col-md-6">
									<input type="text" name="description" value="<?php echo $lg->description; ?>" class="form-control" placeholder="Déscription" title="Déscription" />
								</div>
								<div class="col-md-6">
									<input type="text" name="commentaire" value="<?php echo $lg->commentaire; ?>" class="form-control" placeholder="Commentaire" title="Commentaire" />
								</div>
							</div>
						</div>
						<?php 
					}
					elseif($type==1) //sinon si capteur
					{
						?>
						<h4>Sensibilitées</h4>
						<div class="jumbotron">
							<div class="row">
								<div class="col-md-3">
									<input type="text" name="axeX" value="<?php echo $lg->axeX; ?>" class="form-control" placeholder="Axe X" title="Axe X" />
									<input type="text" name="sensiX" value="<?php echo $lg->sensiX; ?>" class="form-control" placeholder="Sensibilité X" title="Sensibilité X" />
								</div>
								<?php 
								if($lg->axeY!="") //si axe Y non null -> au moins tri-axe
								{
									?>
									<div class="col-md-3">
										<input type="text" name="axeY" value="<?php echo $lg->axeY; ?>" class="form-control" placeholder="Axe Y" title="Axe Y" />
										<input type="text" name="sensiY" value="<?php echo $lg->sensiY; ?>" class="form-control" placeholder="Sensibilité Y" title="Sensibilité Y" />
									</div>
									<div class="col-md-3">
										<input type="text" name="axeZ" value="<?php echo $lg->axeZ; ?>" class="form-control" placeholder="Axe Z" title="Axe Z" />
										<input type="text" name="sensiZ" value="<?php echo $lg->sensiZ; ?>" class="form-control" placeholder="Sensibilité Z" title="Sensibilité Z" />
									</div>
									<?php
									if($lg->axeZs!="") //quatres axes
									{
										?>
										<div class="col-md-3">
											<input type="text" name="axeZs" value="<?php echo $lg->axeZs; ?>" class="form-control" placeholder="Axe Zs" title="Axe Zs" />
											<input type="text" name="sensiZs" value="<?php echo $lg->sensiZs; ?>" class="form-control" placeholder="Sensibilité Zs" title="Sensibilité Zs" />
										</div>
										<?php
									}
									
								}
								?>
		
							</div>
						</div>
						<?php 
					}
					?>
					<h4>Informations complémentaires</h4>
					<!-- bulle d'information sur la désignation -->
					<div class="div_info_aff" id="div_info_aff">
						<p><label>Entreprise:&nbsp </label><?php echo $lg->entreprise; ?></p>
						<p><label>Id ville:&nbsp </label><?php echo $lg->idVille; ?></p>
						<p><label>CC:&nbsp </label><?php echo $lg->centreComp; ?></p>
						<p><label>Département:&nbsp </label><?php echo $lg->departement; ?></p>
					</div>
					
					<!-- détails -->
					<div class="jumbotron">
						<div class="row">
							<div class="col-md-4">
								<div class="info"><label>Numéro de série:&nbsp </label><?php echo $lg->numSerie; ?></div>
								<div class="info"><label>Modèle:&nbsp </label><?php echo $lg->modele; ?></div>
								<div class="info"><label>Correspondant:&nbsp </label><?php echo $correspondant; ?></div>
							</div>
							<div class="col-md-4">
								<div class="info"><label>Ancien numéro:&nbsp </label><?php echo $lg->ancienNum; ?></div>
								<div class="info"><label>Marque:&nbsp </label><?php echo $lg->marque; ?></div>
								<div class="info"><label>Destinataire:&nbsp </label><?php echo $destinataire; ?></div>
							</div>
							<div class="col-md-4">
								<div class="info" id="info_aff"><label>Affectation financière:&nbsp </label><?php echo $lg->filiere; ?></div>
								<div class="info"><label>EntityURL:&nbsp </label><?php echo $lg->entityURL; ?></div>
								<div class="info"><label>Utilisateur:&nbsp </label><?php echo $utilisateur; ?></div>					
							</div>
						</div>
						<?php
						if($nbRetard>0)
						{
							?>
							<div class="row">
								<div class="col-md-4">
									<div ><label>Jours de retards:&nbsp </label><?php echo $nbRetard; ?></div>
								</div>
								<div class="col-md-8">
									<div ><label>Justification:&nbsp </label><?php echo $lg->justification; ?></div>
								</div>
							</div>
							<?php
						}
						?>
					</div>
				</div>
				<div class="text-center">
					<input type="hidden" name="numInstru" value="<?php echo $numInstru; ?>" />
					<input type="button" value="Annuler" class="btn btn-primary btn-lg" onclick="document.location.href='detailsInstru.php?numInstru=<?php echo $numInstru; ?>'"/>
					<input type="submit" value="Valider" class="btn btn-primary btn-lg" />
				</div>
			</form>
		</div>
		<script src="../calendrier/calendrier.js"></script>
		<script src="../js/detailsInstru.js"></script>
		<?php
	}
}
require('bottom.php');