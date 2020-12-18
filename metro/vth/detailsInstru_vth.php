<?php
if(!isset($_GET["numInstru"]))
	echo "<div class='alert alert-danger'><strong>Erreur de réception des paramétres</strong></div>";
else
{
	require('../fonction.php');
	if(!isset($bdd))//on evite les multiples inclusions
		require('../conf/connexion_param.php');
	if(!isset($numInstru))
		$numInstru=$_GET["numInstru"];
	
	$str="select i.numInstru, i.date_derniereInt,  i.date_futureInt, l.nomLocal,
	do.nomDom, de.nomDes, i.modele, i.marque, i.numSerie, nomEtat, i.ancienNum,
	i.trescalId, i.periodicite, i.affectF, nomStatut
	from statut s, instrument_vth iv,
	instrument i LEFT OUTER JOIN localisation l ON i.idLocal_localisation=l.idLocal
	LEFT OUTER JOIN etat e ON i.idEtat_etat=e.idEtat
	LEFT OUTER JOIN designation de ON i.idDes_designation=de.idDes
	LEFT OUTER JOIN domaine do ON do.idDom=de.idDom_domaine
	where iv.numInstru_instrument=i.numInstru
	and i.idStatut_statut=s.idStatut
	and iv.numInstru_instrument='$numInstru';";
	$req=mysqli_query($bdd, $str);
	
	if(!$req)
		echo "<div class='alert alert-danger'><strong>Erreur de récupération des infos sur l'instrument</strong></div>";
	else
	{
		$lg=mysqli_fetch_object($req);
		$numInstru=$lg->numInstru; //si scan l'ancien numInstru était le trescalId
		if($lg->date_derniereInt!="")
			$derniereInt=date('d/m/Y',strtotime($lg->date_derniereInt));
		else
			$derniereInt="";
		if($lg->date_futureInt!="")
			$futureInt=date('d/m/Y',strtotime($lg->date_futureInt));
		else
			$futureInt="";
		?>
		<div class="container">
			<div class="page-header">
				<h2>Détails de l'instrument <?php echo $numInstru.": ".$lg->nomStatut;?></h2> 
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
						<div ><label>Localisation:&nbsp </label><?php echo $lg->nomLocal ?></div>
						
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
		</div>
		<div class="text-center">
			<input type="button" value="Modifier" class="btn btn-primary btn-lg" onclick="document.location.href='modifInstru.php?numInstru=<?php echo $numInstru; ?>'"/>
			<input type="button" value="Retour" class="btn btn-primary btn-lg" onclick="document.location.href='listInstru.php'"/>
		</div>
		<script src="../js/detailsInstru.js"></script>
		<?php
	}
}