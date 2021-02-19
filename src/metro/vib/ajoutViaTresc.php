<?php
require("top.php");
require('../conf/connexion_param.php');
require ('../fonction.php');

if(isset($_POST["num"]))
{

	$numInstru=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["num"]));
	$com=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["com"]));
	if(isset($_POST["capt"]))
	{
		//Si l'l'instrument est d'un type créé par l'utilisateur
		$typeC=$_POST["typeC"];

		$axeX= '';
		$axeY= '';
		$axeZ= '';
		$axeZs= '';
		$sensiX= '';
		$sensiY= '';
		$sensiZ= '';
		$sensiZs= '';
		$fic = '';
		$unite=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["unite"]));
		$dateSensi=dateFrToSQL(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["dSensi"])));
		
		//On insere l'instrument dans la table instru pour les vib_capteurs
		$str="INSERT into instrument_vib_capteur values (null,'$axeX','$sensiX','$axeY','$sensiY','$axeZ','$sensiZ','$axeZs','$sensiZs','$fic','$unite','$numInstru','$typeC');";
		$req=mysqli_query($bdd, $str);
		$idInstruCapt = mysqli_insert_id($bdd);

		$str="SELECT nomTypeC, colonne, libelle from typecapteur where idTypeC = $typeC";
		$req=mysqli_query($bdd, $str);
		$lg = mysqli_fetch_object($req);
		$colonne = explode("-",$lg->colonne);
		$libelle = explode("-",$lg->libelle);
		
		if (isset($_FILES['certif'])){
				
			$numInstru = $_POST["num"];
			$fichier=$_FILES['certif'];
			uploadCertif($numInstru,$fichier,$bdd);	
		}		

		$str="SELECT idInstruCapt,axeX, sensiX, axeY, sensiY, axeZ, sensiZ, axeZs, sensiZs,certificat
		from instrument_vib_capteur
		where idInstruCapt='$idInstruCapt'";
		$req=mysqli_query($bdd, $str);
		$lg=mysqli_fetch_object($req);

		//On insere l'instrument dans l'historique
		$str="insert into histo_vib_capteur values(NULL,'".$axeX."','".$sensiX."','".$axeY."','".$sensiY."',
			'".$axeZ."','".$sensiZ."','".$axeZs."','".$sensiZs."','$dateSensi','".$lg->idInstruCapt."','".$lg->certificat."')";
		$req=mysqli_query($bdd, $str);
		$id = mysqli_insert_id($bdd);

		foreach ($colonne as $col)
		{
			$str="UPDATE instrument_vib_capteur SET ".$col."='".$_POST[$col]."' WHERE numInstru_instrument = '$numInstru'";
			$req = mysqli_query($bdd, $str);

			$str="UPDATE histo_vib_capteur SET ".$col."='".$_POST[$col]."' WHERE idHistoCapt = $id";
			$req = mysqli_query($bdd, $str);
		}
	}
	else
	{
		//On insere l'instrument dans la table instru pour les vib
		$str="insert into instrument_vib values (NULL,'$numInstru');";
		$req=mysqli_query($bdd, $str);

		if (isset($_FILES['certif']))
		{
			$fichier=$_FILES['certif'];
			uploadInstruCertif($numInstru,$fichier,$bdd);

		}
	}
	if(!$req){
		echo '<div class="alert alert-danger"><strong>Erreur de spec de l\'instrument vib</strong></div>';
	}
	else
	{

		//photo
		$fichier=$_FILES['monfichier'];
		uploadPhoto($numInstru,$fichier);
		
		//fiche technique
		$fichier=$_FILES['ficheTech'];
		uploadFicheTech($numInstru,$fichier);
		//update des commentaires de l'instruments
		$str="update instrument set commentaire='$com' where numInstru='$numInstru';";
		$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
		$req=mysqli_query($bdd, $str);
		
		$str="delete from ajoutInstru where numInstru_instrument='$numInstru';";
		$req=mysqli_query($bdd, $str);

		echo '<script>function redirection(){
	
			document.location.href="listAjoutViaTresc.php";
		}

		swal({
			
			title : "Informations validées",
			text : "Redirection dans quelques instants",
			icon : "success"
			
		});
		setTimeout(redirection, 1250);</script>';
	}	
}
else{
	$numInstru=$_GET["numInstru"];
	$str="select i.numInstru, i.date_derniereInt,  i.date_futureInt, do.nomDom, 
	de.nomDes, i.modele, i.marque, i.numSerie, nomEtat, i.ancienNum, i.commentaire,
	i.trescalId, i.periodicite, i.affectF, nomStatut,idLocal_localisation, nomLocal
	from localisation, statut s, instrument_vib iv,
	instrument i LEFT OUTER JOIN etat e ON i.idEtat_etat=e.idEtat
	LEFT OUTER JOIN designation de ON i.idDes_designation=de.idDes
	LEFT OUTER JOIN domaine do ON do.idDom=de.idDom_domaine
	where
	idLocal_localisation = idLocal
	and i.idStatut_statut=s.idStatut
	and i.numInstru='$numInstru'";
	$req=mysqli_query($bdd, $str);
	$lg = mysqli_fetch_object($req);
	
	//on recupere les categories
	
	$str="select idTypeC, nomTypeC, colonne, libelle from typeCapteur;";
	$reqCapt=mysqli_query($bdd, $str);
	
	$str="select idUnite, nomUnite from unite;";
	$reqUnite=mysqli_query($bdd, $str);
?>
	<link href="../calendrier/calendrier.css" rel="stylesheet" />
	<div class="container">
		<div class="page-header">
			<h2>Renseigner un instrument : <?php echo $numInstru; ?></h2>
		</div>
		<h4>Informations générales</h4>
			<div class="jumbotron">
				<div class="row">
					<div class="col-md-4">
						<div class="info"><label>Ancien immo:&nbsp </label><?php echo $lg->ancienNum; ?></div>
						<div class="info"><label>Marque:&nbsp </label><?php echo $lg->marque; ?></div>
						<div class="info"><label>État:&nbsp </label><?php echo $lg->nomEtat; ?></div>
						
							
					</div>
					<div class="col-md-4">
							
						<div class="info"><label>Cal:&nbsp </label><?php echo $lg->date_derniereInt ?></div>
						<div class="info"><label>Next cal:&nbsp </label><?php echo $lg->date_futureInt ?></div>
						<div class="info"><label>Localisation:&nbsp </label><?php echo $lg->nomLocal; ?></div>
				
					</div>
					<div class="col-md-4">
					<div class="info"><label>Modele:&nbsp </label><?php echo $lg->modele; ?></div>
						<div class="info" id="info_des"><label>Désignation:&nbsp </label><?php echo $lg->nomDes; ?></div>
						<div class="info" id="info_des"><label>Domaine:&nbsp </label><?php echo $lg->nomDom; ?></div>

					</div>

				</div>
			</div>
		<form enctype="multipart/form-data" method="post" action="ajoutViaTresc.php" role="form" id="formAjout">
		<div class="jumbotron">
			<textarea id="com" name="com" title="Remarque" type="text" class="form-control" placeholder="Remarque" /></textarea>
		</div>
		<div class="jumbotron">
			<div class="row">
			<div class="col-md-4">
				<div class="info"><label>Photo de l'instrument : </label><input title="Photo de l'instrument"  class="form-control" style="height:auto;" id="monfichier"  type="file" name="monfichier"/></div>
			</div>
			<div class="col-md-4">
				<div class="info"><label>Fiche technique : </label><input title="Fiche technique"  class="form-control" style="height:auto;" id="ficheTech"  type="file" name="ficheTech"/></div>
			</div>
			<div class="col-md-4">
				<div class="info"><label>Certificat d'étalonnage : </label><input title="Certificat d'étalonnage"  class="form-control" style="height:auto;" id="certif"  type="file" name="certif"/></div>
			</div>
			
			</div>
		</div>
		
			<h4><label>Capteur</label> <input type="checkbox" id="capt" name="capt" /></h4>

			<div id="sensi">
			
				<h4>Sensibilitées: </h4>

				<div class="jumbotron">
					<div class="row">
						<div class="col-md-3">
							<select id="typeC" title="Type capteur" class="form-control" name="typeC">
								<option value="" disabled selected>Type capteur</option>
								<?php
									while($lgCapt=mysqli_fetch_object($reqCapt))
										echo '<option value="'.$lgCapt->idTypeC.'">'.$lgCapt->nomTypeC.'</option>';
								?>
							</select>
						</div>
						<div class="col-md-3">
							<select id="unite" title="Unité" class="form-control" name="unite">
								<option value="" disabled selected>Unité</option>
								<?php
									while($lgUnite=mysqli_fetch_object($reqUnite))
										echo '<option value="'.$lgUnite->idUnite.'">'.$lgUnite->nomUnite.'</option>';
								?>
							</select>
						</div>
						<div class="col-md-3">
							<input placeholder="Date de calibration: JJ/MM/YYYY" value="<?php echo date("d/m/Y"); ?>" type="text"  id="dSensi" name="dSensi" class="calendrier form-control" size="8" />
						</div>
					</div>
					<div class="row content">
						
					</div>
				</div>
				
			</div>
			<div class="text-center">
				<input type="hidden" name="num" value="<?php echo $numInstru;?>" />
				<input class='btn btn-lg btn-primary' type='submit' value="Valider" />
				<input class='btn btn-lg btn-primary' type='button' value='Annuler' onclick='document.location.href="index.php"'/>
			</div>
		</form>
	</div><!-- /.container -->
	<script src="../calendrier/calendrier.js"></script>
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
				 	echo 'widget += \'<div class="col-md-3"><input type="text" name="'.$colonne[$i].'" title="'.$libelle[$i].'" class="form-control" placeholder="'.$libelle[$i].'" value="" /></div>\';';
				}
				echo 'console.log(widget);';
				echo 'console.log($(".content"));';
				echo '$(".content").append(widget);';
			echo '}';
		}
	?>
	}
	</script>
	<script src="../js/crudInstru.js"></script>

<?php
}
require("bottom.php");