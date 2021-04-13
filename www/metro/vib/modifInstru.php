<script src="../jquery-ui/js/jquery.js"></script>
<script src="../js/swal.js"></script>
<?php
require("top.php");
require ('../fonction.php');
if(isset($_POST["numInstru"]))
{
	require('../conf/connexion_param.php'); //connexion a la bdd
	$numInstru=$_POST["numInstru"];
	$typeIns=$_POST["typeIns"];
	$newCal = $_POST["newCal"];
	$newCal = dateFrToSQL($newCal);
	$str="SELECT nomTypeC
		from instrument_vib_capteur ivc, typecapteur t
		where 
	ivc.numInstru_instrument='$numInstru'
		and ivc.idTypeC_typecapteur=t.idTypeC;";
	$req=mysqli_query($bdd, $str);
	$lg=mysqli_fetch_object($req);
	$com=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["com"]));
	if(isset($_POST["loca"]))$loca=$_POST["loca"]; else $loca="";
	
	if (isset ($_POST["trescalId"])) $trescalId = $_POST["trescalId"];
	if (isset ($_POST["affectF"])) $affectF = $_POST["affectF"];
	if (isset ($_POST["modele"])) $modele = $_POST["modele"];
	if (isset ($_POST["periodicite"])) $periodicite = $_POST["periodicite"];
	if (isset ($_POST["marque"])) $marque = $_POST["marque"];
	if (isset ($_POST["numSerie"])) $numSerie = $_POST["numSerie"];
	if (isset ($_POST["typeC"])) $typeC = $_POST["typeC"];
	
	$str="UPDATE instrument set date_derniereInt = '$newCal'
	where numInstru_instrument='$numInstru';";
	$req=mysqli_query($bdd, $str);	
	
	if($typeIns==1)
	{
		//Changement du type de capteur
		$str = "UPDATE instrument_vib_capteur set idTypeC_typeCapteur = '$typeC'
		where numInstru_instrument='$numInstru'; ";
		$req=mysqli_query($bdd, $str);
		//Sélection du nom du capteur
		$str = "SELECT nomTypeC FROM typecapteur WHERE idTypeC = $typeC";
		$req=mysqli_query($bdd, $str);
		$typeC = mysqli_fetch_object($req)->nomTypeC;
		//Initialisation des sensibilités et des axes
		$axeX='';
		$axeY='';
		$axeZ='';
		$axeZs= '';
		$sensiX= '';
		$sensiY= '';
		$sensiZ= '';
		$sensiZs= '';
		$unite=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["unite"]));
		$dateSensi=dateFrToSQL(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["dSensi"])));

		//Sélection de l'id et du certificat
		$str="SELECT idInstruCapt,certificat
		from instrument_vib_capteur
		where numInstru_instrument='$numInstru'";
		$req=mysqli_query($bdd, $str);
		$lg=mysqli_fetch_object($req);
		
		if(isset($_POST["histo"]))//case conserver historique cochée
		{
			//On initialise l'historique à vide avec l'id et le certificat précédent
			$str="INSERT into histo_vib_capteur values(NULL,'".$axeX."','".$sensiX."','".$axeY."','".$sensiY."',
			'".$axeZ."','".$sensiZ."','".$axeZs."','".$sensiZs."','$dateSensi','".$lg->idInstruCapt."','".$lg->certificat."')";
			$req=mysqli_query($bdd, $str);
			$id = mysqli_insert_id($bdd); //Récupération de l'id généré
		}
		
		//Récupération des colonne et du libelle correspondant autype de capteur
		$str="SELECT colonne, libelle from typecapteur where nomTypeC = '$typeC'";
		$req=mysqli_query($bdd, $str);
		$lg = mysqli_fetch_object($req);
		$colonne = explode("-",$lg->colonne); //Changement en tableau
		$libelle = explode("-",$lg->libelle); //Changement en tableau

		//On initialise l'update à vide
		$str="UPDATE instrument_vib_capteur set axeX='$axeX', axeY='$axeY', axeZ='$axeZ', axeZs='$axeZs',
		sensiX='$sensiX', sensiY='$sensiY', sensiZ='$sensiZ', sensiZs='$sensiZs'
		where numInstru_instrument='$numInstru';";
		$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
		$req=mysqli_query($bdd, $str);

		//Pour chaque colonne demandée on update le champ
		foreach ($colonne as $col)
		{
			//Update des sensibilité et des axes pour chaque élément caractérisant le capteur
			$str="UPDATE instrument_vib_capteur SET ".$col."='".$_POST[$col]."' WHERE numInstru_instrument = '$numInstru'";
			$req = mysqli_query($bdd, $str);
			//Si l'historique doit être conservé
			if (isset($id))
			{
				$str="UPDATE histo_vib_capteur SET ".$col."='".$_POST[$col]."' WHERE idHistoCapt = $id";
				$req = mysqli_query($bdd, $str);
			}
		}

		//Certificat d'étalonnage
		if (isset($_FILES['certif'])){
			
			$fichier=$_FILES['certif'];
			uploadCertif($numInstru,$fichier,$bdd);
		}		

		//Dans tous les cas, update de l'unité		
		$str="UPDATE instrument_vib_capteur set idUnite_unite='$unite'
		where numInstru_instrument='$numInstru';";
		$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
		$req=mysqli_query($bdd, $str);
	}
	else {
		//Cerificat d'étalonnage pour les instruments non capteurs
		if (isset($_FILES['certif']))
		{
			$fichier=$_FILES['certif'];
			uploadInstruCertif($numInstru,$fichier,$bdd); //Upload et changement dans la base

		}
		$req=true;
	}
	if(!$req)
		echo "<div class='alert alert-danger'><strong>Erreur de modification de l'instrument</strong></div>";
	else
	{
		//Photo de l'instrument
		if (isset($_FILES['monfichier'])){

			$fichier=$_FILES['monfichier'];
			uploadPhoto($numInstru,$fichier);
		}
		
		//Fiche technique
		if (isset($_FILES['ficheTech'])){
			
			$fichier=$_FILES['ficheTech'];
			$str = "SELECT fiche_technique FROM fichetechnique_instrument WHERE numInstru_INSTRUMENT = '$numInstru';";
			$req=mysqli_query($bdd, $str);
			if (mysqli_num_rows($req) != 0){
				$lg = mysqli_fetch_object($req);
				$str = "UPDATE fichetechnique_instrument SET  fiche_technique='".$lg->fiche_technique."' WHERE numInstru_instrument = '$numInstru';";
			}else {
				$str = "INSERT INTO fichetechnique_instrument VALUES ('$numInstru', '".$fichier['name']."');";
			}
			$req=mysqli_query($bdd, $str);
			uploadFicheTech($numInstru,$fichier);
		}
		
		//Numéro de série
		if (isset($_POST["numSerie"])){
			
			$str="UPDATE instrument set trescalId = $trescalId, affectF = '$affectF', modele = '$modele', periodicite = '$periodicite', marque = '$marque', numSerie = '$numSerie', idLocal_localisation='$loca', commentaire='$com' where numInstru='$numInstru';";
		}else{
			
			$str="UPDATE instrument set idLocal_localisation='$loca', commentaire='$com' where numInstru='$numInstru';";
		}
		
		$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
		$req=mysqli_query($bdd, $str);

		//Redirection
		/*echo '<script>function redirection(){
	
			document.location.href="listInstru.php";
		}

		swal({
			
			title : "Informations validées",
			text : "Redirection dans quelques instants",
			icon : "success"
			
		});
		setTimeout(redirection, 1250);</script>';*/
	}
	
}
elseif(!isset($_GET["numInstru"]))
	echo "<div class='alert alert-danger'><strong>Erreur de réception des paramétres</strong></div>";
else
{
	require('../conf/connexion_param.php');
	$numInstru=$_GET["numInstru"];
	
	$typeIns=0; //0 -> simple, 1-> capteur
	//Sélection des informations pour les instruments
	$str="select i.numInstru, i.date_derniereInt,  i.date_futureInt, do.nomDom, 
	de.nomDes, i.modele, i.marque, i.numSerie, nomEtat, i.ancienNum, i.commentaire,
	i.trescalId, i.periodicite, i.affectF, nomStatut,idLocal_localisation
	from statut s, instrument_vib iv,
	instrument i LEFT OUTER JOIN etat e ON i.idEtat_etat=e.idEtat
	LEFT OUTER JOIN designation de ON i.idDes_designation=de.idDes
	LEFT OUTER JOIN domaine do ON do.idDom=de.idDom_domaine
	where iv.numInstru_instrument=i.numInstru
	and i.idStatut_statut=s.idStatut
	and iv.numInstru_instrument='$numInstru'";
	$req=mysqli_query($bdd, $str);

	//Sinon c'est un capteur
	if(mysqli_num_rows($req)==0)
	{
		$typeIns=1;
		$str="select idInstruCapt, axeX, sensiX, axeY, sensiY, axeZ, sensiZ, axeZs, sensiZs, ivc.idUnite_unite,
		i.numInstru, i.date_derniereInt,  i.date_futureInt, do.nomDom, 
		de.nomDes, i.modele, i.marque, i.numSerie, nomEtat, i.ancienNum, i.commentaire, i.modele,
		i.trescalId, i.periodicite, i.affectF, nomStatut,idLocal_localisation, idTypeC, nomTypeC, ivc.certificat
		from statut s, instrument_vib_capteur ivc, typecapteur t,
		instrument i LEFT OUTER JOIN etat e ON i.idEtat_etat=e.idEtat
		LEFT OUTER JOIN designation de ON i.idDes_designation=de.idDes
		LEFT OUTER JOIN domaine do ON do.idDom=de.idDom_domaine
		where ivc.numInstru_instrument=i.numInstru
		and i.idStatut_statut=s.idStatut
		and ivc.numInstru_instrument='$numInstru'
		and ivc.idTypeC_typecapteur=t.idTypeC order by idInstruCapt;";
		$req=mysqli_query($bdd, $str);
		
		$str="select hvc.idHistoCapt, hvc.date_histo, hvc.axeX, hvc.sensiX, hvc.axeY, hvc.sensiY, hvc.axeZ, hvc.sensiZ, hvc.axeZs, hvc.sensiZs, hvc.certificat
		from instrument_vib_capteur ivc, histo_vib_capteur hvc
		where ivc.numInstru_instrument='$numInstru'
		and ivc.idInstruCapt=hvc.idInstruCapt_instrument_vib_capteur
		order by date_histo DESC ,`idHistoCapt` DESC;";
		$reqHisto=mysqli_query($bdd, $str);
		

	}else
	{
		//Récupération du certificat
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
		//convertion des dates format fr
		$derniereInt=dateSQLToFr($lg->date_derniereInt);
		$futureInt=dateSQLToFr($lg->date_futureInt);
		
		$str="select idLocal, nomLocal from localisation where idLabo_labo=2 or idLabo_labo is null;;";
		$reqLoc=mysqli_query($bdd, $str);
		
		$str = "SELECT * FROM `typecapteur`";
		$reqCapteur = mysqli_query($bdd, $str);
		
		$str="select idUnite, nomUnite from unite;";
		$reqUnite=mysqli_query($bdd, $str);
		
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
							<div class="info"><label>Ancien immo:&nbsp </label><?php echo $lg->ancienNum; ?></div>
							<div class="info"><label>Modèle:&nbsp </label><?php echo $lg->modele; ?></div>
							<div class="info"><label>État:&nbsp </label><?php echo $lg->nomEtat; ?></div>
							<div class="info" id="info_des"><label>Photo de l'instrument :&nbsp </label>&nbsp <img src="<?php 
							if(file_exists(repertoirePhoto().str_replace("/", "" ,$numInstru).".jpg")) 
								echo repertoirePhoto().str_replace("/", "" ,$numInstru).".jpg" 
						
							?>" alt="Pas de photo" width="200"/>&nbsp </br></br><input title="Photo de l'instrument"  class="form-control" style="height:auto;" id="file"  type="file" name="monfichier"/></div>
							
						</div>
						<div class="col-md-4">
							<div class="info"><label>Cal:&nbsp </label><input placeholder="Date de la modif: JJ/MM/YYYY" value="<?php echo $derniereInt; ?>" type="text"  id="newCal" name="newCal" class="calendrier form-control" size="8" required/></div>
							
							<div class="info"><label>Next cal:&nbsp </label><?php echo $futureInt ?></div>

							<select id="loca" title="Localisation" class="form-control" name="loca" required>
								<option value="" disabled selected>Localisation</option>
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
							<div class="info" id="info_des">
								<label>Fiche technique :&nbsp </label><?php 
								if(file_exists(repertoireficheTech().str_replace("/", "" ,$numInstru).".pdf")){
									$str = "SELECT fiche_technique FROM fichetechnique_instrument WHERE numInstru_INSTRUMENT = '$numInstru';";
									$req=mysqli_query($bdd, $str);
									if (mysqli_num_rows($req) != 0){
										$lg2 = mysqli_fetch_object($req);
										echo '<a target="_blank" href="'.repertoireficheTech().str_replace("/", "" ,$numInstru).'.pdf" >Fiche technique : '.$lg2->fiche_technique.'</a>';
									}else
										echo '<a target="_blank" href="'.repertoireficheTech().str_replace("/", "" ,$numInstru).'.pdf" >Fiche technique : '.$numInstru.'.pdf</a>';
									
								}
								else
									echo "Pas de fiche technique";

								?>
								</br>
								<input title="Fiche technique" class="form-control" style="height:auto;" id="file"  type="file" name="ficheTech"/>
							</div>
						</div>
						<div class="col-md-4">
							<div class="info" id="info_des"><label>Certificat d'étalonnage :&nbsp </label><?php 
						if(isset($lg->certificat))
							echo '<a target="_blank" href="'.repertoireCertificat().$lg->certificat.'" >'.$lg->certificat.'</a>';

						else if (isset($instru_certif))
							echo '<a target="_blank" href="'.repertoireCertificat().$instru_certif.'" >'.$instru_certif.'</a>';
						else
							echo "Pas de certificat d'étalonnage";
						?></br><input title="Certificat d'étalonnage"  class="form-control" style="height:auto;" id="file"  type="file" name="certif"/></div>
						</div>


					</div>
					<div>
						<textarea name="com" class="form-control" placeholder="Remarque" title="Remarque"><?php echo $lg->commentaire; ?></textarea> 
					</div>
						
				</div>
				<?php 
				if($typeIns==1)
				{
				?>
					<div class="row">
						<div class="col-md-2"><h4>Sensibilités:</h4></div>
						<div class="col-md-10">
							<select id="typeC" title="typeC" class="form-control dis" name="typeC" required>
								<?php
									while($lgCapteur=mysqli_fetch_object($reqCapteur))
									{
										if($lgCapteur->nomTypeC==$lg->nomTypeC)
											echo '<option value="'.$lgCapteur->idTypeC.'" selected>'.$lgCapteur->nomTypeC.'</option>';
										else
											echo '<option value="'.$lgCapteur->idTypeC.'">'.$lgCapteur->nomTypeC.'</option>';
									}
								?>
							</select>
						</div>
					</div>
					<div class="jumbotron">
						<label>Conserver historique: <input type="checkbox" name="histo" checked /></label>
						<div class="row">
							<div class="col-md-3">
								<input placeholder="Date de la modif: JJ/MM/YYYY" value="<?php echo date("d/m/Y"); ?>" type="text"  id="dSensi" name="dSensi" class="calendrier form-control dis" size="8" required/>
							</div>
							<div class="col-md-3">
								<select id="unite" title="Unite" class="form-control dis" name="unite" required>
									<option value="" disabled selected>Unité</option>
									<?php
										while($lgUnite=mysqli_fetch_object($reqUnite))
										{
											if($lgUnite->idUnite==$lg->idUnite_unite)
												echo '<option value="'.$lgUnite->idUnite.'" selected>'.$lgUnite->nomUnite.'</option>';
											else
												echo '<option value="'.$lgUnite->idUnite.'">'.$lgUnite->nomUnite.'</option>';
										}
									?>
								</select>
							</div>
						</div>
						<div class="row content">

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
									<th>Date modif</th>
									<?php

									$libelle = explode("-", $lg_typeC->libelle);

									for ($i=0; $i < count($libelle); $i++) { 
										
										echo '<th>'.$libelle[$i].'</th>';
									}
									
									?>
									<th>Certificat</th>

									<th></th>
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
										
										echo '<td>'.$lg->$colonne[$i].'</td>';
									}

									if($lgHisto->certificat!="")
									{
										echo '<td><a target="_blank" href="'.repertoireCertificat().$lgHisto->certificat.'" >'.$lgHisto->certificat.'</a></td>';
										echo "<td onclick='confirmSupprHisto(\"$idHistoCapt\");'><IMG style='cursor:pointer;float:right;max-height:20px' SRC='../img/supr.png'  /></td>";
									}
									else{
									
										echo "<td></td>";
										echo "<td onclick='confirmSupprHisto(\"$idHistoCapt\");'><IMG style='cursor:pointer;float:right;max-height:20px' SRC='../img/supr.png'  /></td>";
									}
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
								<div class="info"><label>Trescal ID:&nbsp; </label><input class="form-control dis" placeholder="Trescal ID" value="<?php echo $lg->trescalId; ?>" type="number"  id="trescalId" name="trescalId" size="11" required/></div>
								<div class="info"><label>Affectation financière:&nbsp; </label><input class="form-control dis" placeholder="Affectation Financière" value="<?php echo $lg->affectF; ?>" type="text"  id="affectF" name="affectF" size="10" required/></div>
								
							</div>
							<div class="col-md-4">
								<div class="info"><label>Type/Model:&nbsp; </label><input class="form-control dis" placeholder="Modele" value="<?php echo $lg->modele; ?>" type="text" name="modele" size="30" required/></div>
								<div class="info"><label>Périodicité:&nbsp; </label><input class="form-control dis" placeholder="Périodicité" value="<?php echo $lg->periodicite; ?>" type="text" name="periodicite" size="25" required/></div>								
							</div>
							<div class="col-md-4">
								<div class="info"><label>Serial number:&nbsp; </label><input class="form-control dis" placeholder="Numéro de série" value="<?php echo $lg->numSerie; ?>" type="text" name="numSerie" size="30" required/></div>
								<div class="info"><label>Fab:&nbsp; </label><input class="form-control dis" placeholder="Fabricant" value="<?php echo $lg->marque; ?>" type="text" name="marque" size="50" required/></div>	
							</div>

						</div>
					</div>
				</div>
				<div class="text-center">
					<input type="hidden" name="numInstru" value="<?php echo $numInstru; ?>" />
					<input type="hidden" name="typeIns" value="<?php echo $typeIns; ?>" />
					<input type="submit" value="Valider" onclick="if (confirm('Voulez-vous appliquer les modifications ?')) {return true} else {return false}" class="btn btn-primary btn-lg" />
					<input type="button" value="Annuler" class="btn btn-primary btn-lg" onclick="document.location.href='detailsInstru.php?numInstru=<?php echo $numInstru; ?>'"/>
				</div>
			</form>
		</div>
		<script src="../calendrier/calendrier.js"></script>
		<script src="../js/crudInstru.js"></script>
		<script>
		$('#typeC').change(function(){
		
			$(".content").empty();
			update();
		});
		function update(){
		<?php
			$str="select idTypeC, nomTypeC, colonne, libelle from typeCapteur;";
			$reqCapt=mysqli_query($bdd, $str);
			while($lgCapt=mysqli_fetch_object($reqCapt))
			{
				$colonne = $lgCapt->colonne;
				$colonne = explode("-", $colonne);
				$libelle = $lgCapt->libelle;
				$libelle = explode("-", $libelle);
				echo 
				'if($("#typeC option:selected").text() == "'.$lgCapt->nomTypeC.'"){';

					echo 'var widget = "";';
					for ($i = 0; $i < count($colonne); $i++) {
					 	echo 'widget += \'<div class="col-md-3"><input type="text" name="'.$colonne[$i].'" title="'.$libelle[$i].'" class="form-control value" placeholder="'.$libelle[$i].'" value="';
					 	if ($lgCapt->nomTypeC == $lg->nomTypeC) echo $lg->$colonne[$i].'" /></div>\';';
					 	else echo '" /></div>\';';
					}
					echo 'console.log(widget);';
					echo 'console.log($(".content"));';
					echo '$(".content").append(widget);';
				echo '}';
			}
		?>
		}
		</script>
		<script>update()</script>
		<?php
		if ($_SESSION["metro"]["categUser"]==2)
		{
			echo "<script>disable()</script>";
		}
	}
}
require("bottom.php");