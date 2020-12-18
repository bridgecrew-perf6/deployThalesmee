<?php
require("top.php");
require('../fonction.php');
if(!isset($_GET["numInstru"]))
	echo "<div class='alert alert-danger'><strong>Erreur de réception des paramétres</strong></div>";
else
{
	
	if(!isset($bdd))//on evite les multiples inclusions
		require('../conf/connexion_param.php');
	if(!isset($numInstru))
		$numInstru=$_GET["numInstru"];
	
	//provenance de l'utilisateurs
	if(isset($_GET["prov"]))
	{
		if($_GET["prov"]==1) //listCapteur
			$pagePrec="listCapteur.php";
		else //defaut -> listInstru
			$pagePrec="listInstru.php";
	}
	else //defaut -> listInstru
		$pagePrec="listInstru.php";
	
	$typeIns=0; //0 -> simple, 1-> capteur
	
	$str="SELECT i.numInstru, i.date_derniereInt,  i.date_futureInt, l.nomLocal,
	do.nomDom, de.nomDes, i.modele, i.marque, i.numSerie, nomEtat, i.ancienNum, i.commentaire,
	i.trescalId, i.periodicite, i.affectF, nomStatut
	from statut s, instrument_vib iv,
	instrument i LEFT OUTER JOIN localisation l ON i.idLocal_localisation=l.idLocal
	LEFT OUTER JOIN etat e ON i.idEtat_etat=e.idEtat
	LEFT OUTER JOIN designation de ON i.idDes_designation=de.idDes
	LEFT OUTER JOIN domaine do ON do.idDom=de.idDom_domaine
	where iv.numInstru_instrument=i.numInstru
	and i.idStatut_statut=s.idStatut
	and iv.numInstru_instrument='$numInstru';";
	$req=mysqli_query($bdd, $str);
	if(mysqli_num_rows($req)==0)
	{
		$typeIns=1;
		$str="SELECT axeX, sensiX, axeY, sensiY, axeZ, sensiZ, axeZs, sensiZs, u.nomUnite,
		i.numInstru, i.date_derniereInt,  i.date_futureInt, l.nomLocal,i.modele,
		do.nomDom, de.nomDes, i.modele, i.marque, i.numSerie, nomEtat, i.ancienNum, i.commentaire,
		i.trescalId, i.periodicite, i.affectF, nomStatut, idTypeC, nomTypeC, colonne, libelle, ivc.certificat
		from statut s, typecapteur t, instrument_vib_capteur ivc LEFT OUTER JOIN unite u ON u.idUnite=ivc.idUnite_unite,
		instrument i LEFT OUTER JOIN localisation l ON i.idLocal_localisation=l.idLocal
		LEFT OUTER JOIN etat e ON i.idEtat_etat=e.idEtat
		LEFT OUTER JOIN designation de ON i.idDes_designation=de.idDes
		LEFT OUTER JOIN domaine do ON do.idDom=de.idDom_domaine
		where ivc.numInstru_instrument=i.numInstru
		and i.idStatut_statut=s.idStatut
		and ivc.numInstru_instrument='$numInstru'
		and ivc.idTypeC_typecapteur=t.idTypeC;";
		$req=mysqli_query($bdd, $str);
		
		
		$str="SELECT hvc.idHistoCapt, hvc.date_histo, hvc.axeX, hvc.sensiX, hvc.axeY, hvc.sensiY, hvc.axeZ, hvc.sensiZ, hvc.axeZs, hvc.sensiZs, hvc.certificat
		from instrument_vib_capteur ivc, histo_vib_capteur hvc
		where ivc.numInstru_instrument='$numInstru'
		and ivc.idInstruCapt=hvc.idInstruCapt_instrument_vib_capteur
		order by date_histo DESC,`idHistoCapt` DESC;";
		$reqHisto=mysqli_query($bdd, $str);

		$str = "SELECT idPret_pret FROM concernepret WHERE numInstru_instrument ='$numInstru'";
		$req_sortie = mysqli_query($bdd, $str);
		if (mysqli_num_rows($req_sortie)>=1){

			$sortie = mysqli_fetch_object($req_sortie)->idPret_pret;
		}


	}else
	{
		$str = "SELECT certificat FROM certificat_instrument WHERE numInstru_INSTRUMENT = '$numInstru'";
		$req_certif = mysqli_query($bdd, $str);
		if (mysqli_num_rows($req_certif) != 0)
		{
			$instru_certif = mysqli_fetch_object($req_certif)->certificat;
		}

	}
	if(!$req)
		echo "<div class='alert alert-danger'><strong>Erreur de récupération des infos sur l'instrument</strong></div>";
	else
	{
		$lg=mysqli_fetch_object($req);
		$numInstru=$lg->numInstru; //si scan l'ancien numInstru était le trescalId
		//convertion des dates format fr
		$derniereInt=dateSQLToFr($lg->date_derniereInt);
		$futureInt=dateSQLToFr($lg->date_futureInt);

		?>
		<div class="container">
			<div class="page-header">
				<h2>Détails de l'instrument 
					<?php 
						echo $numInstru.": ".$lg->nomStatut;
						if (isset($sortie)){
							echo "<a style='margin-left:10px' class='btn btn-primary' href='./detailsPret.php?idPret=".$sortie."'>Entrée / Sortie</a>";
						} 
					?>	
				</h2>

			</div>
			<h4>Informations générales</h4>
			<div class="jumbotron">
				<div class="row">
					<div class="col-md-4">
					
						<div class="info"><label>Numéro Immo:&nbsp </label><?php echo $numInstru; ?></div>
						<div class="info"><label>Ancien immo:&nbsp </label><?php echo $lg->ancienNum; ?></div>
						<div class="info"><label>État:&nbsp </label><?php echo $lg->nomEtat; ?></div>
						<div class="info"><label>Modèle:&nbsp </label><?php echo $lg->modele; ?></div>
						
						
					</div>
					<div class="col-md-4">
						
						<div class="info"><label>Cal:&nbsp </label><?php echo $derniereInt ?></div>
						<div class="info"><label>Next cal:&nbsp </label><?php echo $futureInt ?></div>
						<div class="info"><label>Localisation:&nbsp </label><?php echo $lg->nomLocal ?></div>
						<?php if (isset($instru_certif)){
							echo '<div class="info"><label>Certificat d\'étalonnage :&nbsp </label><a target="_blank" href="'.repertoireCertificat().$instru_certif.'" >'.$instru_certif.'</a></div>';
						}?>
						
						
						
					</div>
					<div class="col-md-4">
						<div class="info" id="info_des"><label>Désignation:&nbsp </label><?php echo $lg->nomDes; ?></div>
						<div class="info" id="info_des"><label>Domaine:&nbsp </label><?php echo $lg->nomDom; ?></div>
						<div class="info">
						<?php 
							if(file_exists(repertoireficheTech().str_replace("/", "" ,$numInstru).".pdf"))
								echo '<a target="_blank" href="'.repertoireficheTech().str_replace("/", "" ,$numInstru).'.pdf" >Fiche technique : '.$numInstru.'.pdf</a>';
							else
								echo "Pas de fiche technique";
						?>
						</div>
						
					</div>
				</div>

				<div class="info"><label>Remarque:&nbsp </label><?php echo $lg->commentaire;?></div>
				
				<img src="<?php if(file_exists(repertoirePhoto().str_replace("/", "" ,$numInstru).".jpg")) echo repertoirePhoto().str_replace("/", "" ,$numInstru).".jpg" ?>" alt="Pas de photo" width="200"/>
			</div>
			<?php 
			if($typeIns==1)
			{
				$unite=$lg->nomUnite;
			?>
				<h4>Sensibilités: <?php echo $lg->nomTypeC; ?></h4>
				<div class="jumbotron">
					<div class="row">
						<?php
						$colonne = explode("-", $lg->colonne);
						$libelle = explode("-", $lg->libelle);
						for ($i = 0; $i< count($colonne); $i++) {
							
							echo '<div class="col-md-3"><div class="info"><label>'.$libelle[$i].':&nbsp; </label>'.$lg->$colonne[$i].' '.$unite.'</div></div>';
						}
						?>
					</div>
					<div>
						<?php 
						if(isset($lg->certificat) && $lg->certificat != "")
							echo '<a target="_blank" href="'.repertoireCertificat().$lg->certificat.'" >Certificat d\'étalonnage: '.$lg->certificat.'</a>';
						else
							echo "Pas de certificat d'étalonnage";
						?>
					</div>
				</div>
			<?php 
			}
			?>
			
			<input type="button" id="b_det" class="btn btn-success" value="+" />
			
			<div id="detailsSup" class="show_hide">
				<?php 
				if($typeIns==1 && mysqli_num_rows($reqHisto)!=0)
				{
					$idTypeC = $lg->idTypeC;
					$str = "SELECT colonne, libelle FROM typecapteur WHERE idTypeC =$idTypeC";
					$req= mysqli_query($bdd,$str);
					$lg_typeC = mysqli_fetch_object($req);
				?>
					<h4>Historique des sensibilités</h4>
					<div class="jumbotron">
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Date modif</th>
									
									<?php

									$libelle = explode("-", $lg_typeC->libelle);

									for ($i=0; $i < count($libelle); $i++) { 
										
										echo '<th>'.$libelle[$i].'</th>';
									}
									
									?>
									<th>Certificat</th>
								</tr>
							</thead>
							<tbody>
							<?php
								while($lgHisto=mysqli_fetch_object($reqHisto))
								{
									$idHistoCapt=$lgHisto->idHistoCapt;
									echo "<tr id='$idHistoCapt'>";
										echo "<td>".dateSQLToFr($lgHisto->date_histo)."</td>";

									$colonne = explode("-", $lg_typeC->colonne);

									for ($i=0; $i < count($colonne); $i++) { 
										
										echo '<td>'.$lg->$colonne[$i].' '.$unite.'</td>';
									}

									if($lgHisto->certificat!="")
									{
										echo '<td><a target="_blank" href="'.repertoireCertificat().$lgHisto->certificat.'" >'.$lgHisto->certificat.'</a></td>';

									}
									else echo "<td></td>";

									echo "</tr>";
								}
								?>
							</tbody>
						</table>
					</div>
				
				
				<?php
				}
				?>
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
			<input type="button" value="Supprimer" class="btn btn-primary btn-lg" onclick="confirmSupp('<?php echo $numInstru; ?>')"/>
			<input type="button" value="Retour" class="btn btn-primary btn-lg" onclick="document.location.href='<?php echo $pagePrec;?>'"/>
		</div>
		<script src="../js/crudInstru.js"></script>

		<?php
	}
}
require("bottom.php");