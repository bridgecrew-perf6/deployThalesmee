<?php

if(isset($_POST["numInstru"]))
{
	require('../conf/connexion_param.php'); //connexion a la bdd
	
	$numInstru=$_POST["numInstru"];
	if(isset($_POST["loca"]))$loca=$_POST["loca"]; else $loca="";
	
	$str="update instrument set idLocal_localisation='$loca' where numInstru='$numInstru';";
	$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
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
	require('../fonction.php');
	if(!isset($bdd))//on evite les multiples inclusions
		require('../conf/connexion_param.php');
	if(!isset($numInstru))
		$numInstru=$_GET["numInstru"];
	
	
	
	$str="select i.numInstru, i.date_derniereInt,  i.date_futureInt, do.nomDom, 
	de.nomDes, i.modele, i.marque, i.numSerie, nomEtat, i.ancienNum,
	i.trescalId, i.periodicite, i.affectF, nomStatut,idLocal_localisation
	from statut s, instrument_vth iv,
	instrument i LEFT OUTER JOIN etat e ON i.idEtat_etat=e.idEtat
	LEFT OUTER JOIN designation de ON i.idDes_designation=de.idDes
	LEFT OUTER JOIN domaine do ON do.idDom=de.idDom_domaine
	where iv.numInstru_instrument=i.numInstru
	and i.idStatut_statut=s.idStatut
	and iv.numInstru_instrument='$numInstru'";
	$req=mysqli_query($bdd, $str);
	if(!$req)
		echo "<div class='alert alert-danger'><strong>Erreur de récupération des infos sur l'instrument</strong></div>";
	else
	{
		$lg=mysqli_fetch_object($req);

		if($lg->date_derniereInt!="")
			$derniereInt=date('d/m/Y',strtotime($lg->date_derniereInt));
		else
			$derniereInt="";
		if($lg->date_futureInt!="")
			$futureInt=date('d/m/Y',strtotime($lg->date_futureInt));
		else
			$futureInt="";
		
		$str="select idLocal, nomLocal from localisation where idLabo_labo=2;";
		$reqLoc=mysqli_query($bdd, $str);
		
		
		?>
		<div class="container">
			<form method="post" action="modifInstru.php">
				<div class="page-header">
					<h2>Modification de l'instrument <?php echo $numInstru ;?></h2> 
				</div>
				<h4>Informations générales</h4>
				<div class="jumbotron">
					<div class="row">
						<div class="col-md-4">
							<div class="info"><label>Numéro Immo:&nbsp </label><?php echo $numInstru; ?></div>
							<div class="info"><label>Ancien immo:&nbsp </label><?php echo $lg->ancienNum; ?></div>
							<div ><label>État:&nbsp </label><?php echo $lg->nomEtat; ?></div>
						</div>
						<div class="col-md-4">
							
							<div class="info"><label>Cal:&nbsp </label><?php echo $derniereInt ?></div>
							<div class="info"><label>Next cal:&nbsp </label><?php echo $futureInt ?></div>
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
							
						</div>
						<div class="col-md-4">
							<div class="info" id="info_des"><label>Désignation:&nbsp </label><?php echo $lg->nomDes; ?></div>
							<div class="info" id="info_des"><label>Domaine:&nbsp </label><?php echo $lg->nomDom; ?></div>
							
						</div>
					</div>
				</div>
				<input type="button" id="b_det" class="btn btn-success" value="+" />
				
				<div id="detailsSup" class="show_hide">
					<h4>Informations complémentaires</h4>
					
					<!-- détails -->
					<div class="jumbotron">
						<div class="row">
							<div class="col-md-4">
								<div class="info"><label>Trescal ID:&nbsp </label><?php echo $lg->trescalId; ?></div>
								<div ><label>Affectation financière:&nbsp </label><?php echo $lg->affectF; ?></div>
								
							</div>
							<div class="col-md-4">
								<div class="info"><label>Type/Model:&nbsp </label><?php echo $lg->modele; ?></div>
								<div ><label>Périodicité:&nbsp </label><?php echo $lg->periodicite; ?></div>
								
							</div>
							<div class="col-md-4">
								<div class="info"><label>Serial number:&nbsp </label><?php echo $lg->numSerie; ?></div>
								<div ><label>Fab:&nbsp </label><?php echo $lg->marque; ?></div>
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
		<script src="../js/detailsInstru.js"></script>
		<?php
	}
}
