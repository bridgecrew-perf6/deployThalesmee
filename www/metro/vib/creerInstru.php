<?php
/*
Ce fichier contient le code pour la création d'un instrument ou d'un capteur.
Il possède un formulaire pour saisir les différente informations.
Le script ../js/creerInstru.js lui est associé pour, entre autre, 
l'affichage des sensibilités pour un capteurs en fonction du type de capteur
*/
?>
<script src="../jquery-ui/js/jquery.js"></script>
<script src="../js/swal.js"></script>
<?php 
require("top.php");
require('../conf/connexion_param.php');

if(isset($_POST["num"]))
{
	require('../fonction.php');
	//recup des parametres
	$numInstru=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["num"]));
	$model=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["model"]));
	$serie=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["serie"]));
	if(isset($_POST['etat']))$etat=$_POST['etat']; else $etat="";
	
	$ancienNum=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["aNum"]));
	$fab=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["fab"]));
	$lastCal=dateFrToSQL(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["lCal"])));
	$nextCal=dateFrToSQL(htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["nCal"])));
	$com=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["com"]));
	
	if(isset($_POST['des']))$des=$_POST['des']; else $des="";
	if(isset($_POST['loca']))$loca=$_POST['loca']; else $loca="";
	
	
	//ajout de l'instrument
	$str="INSERT INTO instrument VALUES ('$numInstru','$ancienNum',null,'$des','$fab','$model','$serie','024VIB',
	'$lastCal','$nextCal',null,'$etat','$loca','1','$com')";
	
	$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
	$req=mysqli_query($bdd, $str);

	if(!$req){ //une erreur dans la requete renvera false
		echo '<div class="alert alert-danger"><strong>Erreur d\'ajout de l\'instrument</strong></div>';
		
	}
	else
	{
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
		if(!$req)
		{
			//on supprime l'instrument ajouté précédemment
			$str="delete from instrument where numInstru ='$numInstru';";
			$req=mysqli_query($bdd, $str);
			echo '<div class="alert alert-danger"><strong>Erreur d\'ajout de l\'instrument vib</strong></div>';
		}
		else
		{
			if (isset ($_FILES['monfichier'])){
				
				//photo
				$fichier=$_FILES['monfichier'];
				uploadPhoto($numInstru,$fichier);
			}
			
			if (isset ($_FILES['ficheTech'])){
				
				//fiche technique
				$fichier=$_FILES['ficheTech'];
				$str = "INSERT INTO fichetechnique_instrument VALUES ('$numInstru', '".$fichier['name']."');";
				$req=mysqli_query($bdd, $str);
				uploadFicheTech($numInstru,$fichier);
			}
			
			echo '<script src="../js/success.js"></script>';
		}
	}
	
}
else{
	//on recupere les categories
	$str="select idDom, nomDom from domaine;";
	$reqDom=mysqli_query($bdd, $str);

	$str="select idLocal, nomLocal from localisation where idLabo_labo=2;";
	$reqLoc=mysqli_query($bdd, $str);
	
	$str="select idEtat, nomEtat from etat;";
	$reqEtat=mysqli_query($bdd, $str);
	
	$str="select idTypeC, nomTypeC from typecapteur;";
	$reqCapt=mysqli_query($bdd, $str);
	
	$str="select idUnite, nomUnite from unite;";
	$reqUnite=mysqli_query($bdd, $str);
		
?>
	<link href="../calendrier/calendrier.css" rel="stylesheet" />
	<div class="container">
		<div class="page-header">
			<h2>Ajouter un instrument</h2>
		</div>
		<form enctype="multipart/form-data" method="post" action="creerInstru.php" role="form" id="formAjout">
			<h4>Informations générales</h4>
			<div class="jumbotron">
				<div class="row">
					<div class="col-md-4">
						<input id="num" name="num" title="Numéro de l'instrument" type="text" class="form-control" placeholder="Numéro de l'instrument" required autofocus />			
						<input id="model" name="model" title="Type/Modèle" type="text" class="form-control" placeholder="Type/Modèle" required />			
						<input id="serie" name="serie" title="Numéro de série" type="text" class="form-control" placeholder="Numéro de série" required />			
						<select id="etat" title="Equipement" class="form-control" name="etat" required>
							<option value="" disabled selected>État</option>
							<?php
								while($lgEtat=mysqli_fetch_object($reqEtat))
									echo '<option value="'.$lgEtat->idEtat.'">'.$lgEtat->nomEtat.'</option>';
							?>
						</select>
					</div>
					<div class="col-md-4">
						<input id="aNum" name="aNum" title="Ancien immo" type="text" class="form-control" placeholder="Ancien immo" />
						<input id="fab" name="fab" title="Fabricant" type="text" class="form-control" placeholder="Fabricant" required />
						<input placeholder="Dernière cal: JJ/MM/YYYY" value="" type="text"  id="lCal" name="lCal" class="calendrier form-control" size="8" required/>
						<input placeholder="Prochaine cal: JJ/MM/YYYY" value="" type="text"  id="nCal" name="nCal" class="calendrier form-control" size="8" />
					</div>
					<div class="col-md-4">
						<select id="dom" title="Domaine" class="form-control" required>
							<option value="" disabled selected>Domaine</option>
							<?php
								while($lgDom=mysqli_fetch_object($reqDom))
									echo '<option value="'.$lgDom->idDom.'">'.$lgDom->nomDom.'</option>';
							?>
						</select>
						<select id="des" title="Désignation" class="form-control" name="des" required>
							<option value="" disabled selected>Désignation</option>
						</select>
						<select id="loca" title="Localisation" class="form-control" name="loca" required>
							<option value="" disabled selected>Localisation</option>
							<?php
								while($lgLoc=mysqli_fetch_object($reqLoc))
									echo '<option value="'.$lgLoc->idLocal.'">'.$lgLoc->nomLocal.'</option>';
							?>
						</select>
					</div>
				</div>
				<textarea id="com" name="com" title="Remarque" type="text" class="form-control" placeholder="Remarque" /></textarea>
			</div>
			<div class="jumbotron">
				<div class="row">
			<div class="col-md-4">
				<div class="info"><label>Fiche technique : </label><input title="Fiche technique"  class="form-control" style="height:auto;" id="ficheTech"  type="file" name="ficheTech"/></div>
			</div>
			<div class="col-md-4">
				<div class="info"><label>Photo de l'instrument : </label><input title="Photo de l'instrument"  class="form-control" style="height:auto;" id="monfichier"  type="file" name="monfichier"/></div>
			</div>
			<div class="col-md-4">
				<div class="info"><label>Certificat d'étalonnage : </label><input title="Certificat d'étalonnage"  class="form-control" style="height:auto;" id="certif"  type="file" name="certif"/></div>
				</div>
			</div>
			</div>
			
			<div><label>Capteur</label> <input type="checkbox" id="capt" name="capt" /></div>
			

			<div id="sensi">			
				<h4>Sensibilités: </h4>
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
				<input class='btn btn-lg btn-primary' type='submit' value="Valider" />
				<input class='btn btn-lg btn-primary' type='button' value='Annuler' onclick='document.location.href="index.php"'/>
			</div>
		</form>
	</div><!-- /.container -->
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
<?php
}
require("bottom.php");