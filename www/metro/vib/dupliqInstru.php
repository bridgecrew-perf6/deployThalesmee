<?php
/*
Ce fichier contient le code pour la création d'un instrument ou d'un capteur.
Il possède un formulaire pour saisir les différente informations.
Le script ../js/creerInstru.js lui est associé pour, entre autre, 
l'affichage des sensibilités pour un capteurs en fonction du type de capteur
*/
?>
<script src="../js/swal.js"></script>
<?php 
require("top.php");
require('../conf/connexion_param.php');

if(isset($_POST["num"]))
{
	require('../fonction.php');
	$numInstru = $_POST["numInstru"];

	$str="SELECT i.numInstru, i.date_derniereInt,  i.date_futureInt, idLocal_localisation,
	 idDes_designation, i.modele, i.marque, idEtat_etat, i.commentaire
	from instrument i 
	WHERE numInstru='$numInstru';";
	$req=mysqli_query($bdd, $str);

	$des = "";
	$fab = "";
	$model = "";
	$lastCal = "";
	$nextCal = "";
	$etat = "";
	$loca = "";
	$com = "";

	while($lg = mysqli_fetch_object($req))
	{
		$des = $lg->idDes_designation;
		$fab = $lg->marque;
		$model = $lg->modele;
		$lastCal = $lg->date_derniereInt;
		$nextCal = $lg->date_futureInt;
		$etat = $lg->idEtat_etat;
		$loca = $lg->idLocal_localisation;
		$com = htmlspecialchars(mysqli_real_escape_string($bdd,$lg->commentaire));

	}
	
	$error = false;
	$num = $_POST["num"];
	$serie = $_POST["serie"];
	

	for ($i = 0; $i < count($num); $i++){
		$str="INSERT INTO instrument VALUES ('$num[$i]',null,null,'$des','$fab','$model','$serie[$i]','024VIB',
		'$lastCal','$nextCal',null,'$etat','$loca','1','$com')";
		$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
		$req=mysqli_query($bdd, $str);
		if(!$req){ //une erreur dans la requete renvera false
			echo '<div class="alert alert-danger"><strong>Erreur d\'ajout de l\'instrument n°'.$num[$i].'</strong></div>';
			$error = true;
		}

		if($error == false)
		{
			//recup des parametres
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
			if(mysqli_num_rows($req)==0) //capteur
			{		

				$str="SELECT idInstruCapt,axeX, sensiX, axeY, sensiY, axeZ, sensiZ, axeZs, sensiZs,certificat, idUnite_unite, idTypeC_typeCapteur
				from instrument_vib_capteur
				where numInstru_instrument='$numInstru'";
				$req=mysqli_query($bdd, $str);

				$idInstruCapt_modele = "";
				$axeX = "";
				$axeY = "";
				$axeZ = "";
				$axeZs = "";
				$sensiX = "";
				$sensiY = "";
				$sensiZ = "";
				$sensiZs = "";
				$fic = '';
				$unite = "";
				$typeC = "";
				while($lg=mysqli_fetch_object($req)){
					$idInstruCapt_modele = $lg->idInstruCapt;
					$axeX = $lg->axeX;
					$axeY = $lg->axeY;
					$axeZ = $lg->axeZ;
					$axeZs = $lg->axeZs;
					$sensiX = $lg->sensiX;
					$sensiY = $lg->sensiY;
					$sensiZ = $lg->sensiZ;
					$sensiZs = $lg->sensiZs;
					$unite = $lg->idUnite_unite;
					$typeC = $lg->idTypeC_typeCapteur;
				}

				$str="INSERT into instrument_vib_capteur values (null,'$axeX','$sensiX','$axeY','$sensiY','$axeZ','$sensiZ','$axeZs','$sensiZs','$fic','$unite','$num[$i]','$typeC');";
				$req=mysqli_query($bdd, $str);
				$idInstruCapt = mysqli_insert_id($bdd);

				if (isset($_FILES["certif".$i])) {

					$certif = $_FILES["certif".$i];
					
					uploadCertif($num[$i],$certif,$bdd);


					$str="SELECT axeX, sensiX, axeY, sensiY, axeZ, sensiZ, axeZs, sensiZs, date_histo
					from histo_vib_capteur
					where idInstruCapt_instrument_vib_capteur ='$idInstruCapt_modele' ORDER BY date_histo LIMIT 1";
					$req=mysqli_query($bdd, $str);
					$lg=mysqli_fetch_object($req);
					$dateSensi = $lg->date_histo;

					//On insere l'instrument dans l'historique
					$str="INSERT into histo_vib_capteur values(NULL,'".$axeX."','".$sensiX."','".$axeY."','".$sensiY."',
						'".$axeZ."','".$sensiZ."','".$axeZs."','".$sensiZs."','$dateSensi','".$idInstruCapt."','".$certif['name']."')";
					$req=mysqli_query($bdd, $str);
					$id = mysqli_insert_id($bdd);

				}else{

					$str="SELECT axeX, sensiX, axeY, sensiY, axeZ, sensiZ, axeZs, sensiZs, date_histo
					from histo_vib_capteur
					where idInstruCapt_instrument_vib_capteur ='$idInstruCapt_modele' ORDER BY date_histo LIMIT 1";
					$req=mysqli_query($bdd, $str);
					$lg=mysqli_fetch_object($req);
					$dateSensi = $lg->date_histo;

					//On insere l'instrument dans l'historique
					$str="INSERT into histo_vib_capteur values(NULL,'".$axeX."','".$sensiX."','".$axeY."','".$sensiY."',
						'".$axeZ."','".$sensiZ."','".$axeZs."','".$sensiZs."','$dateSensi','".$idInstruCapt."','')";
					$req=mysqli_query($bdd, $str);
					$id = mysqli_insert_id($bdd);
				}



			}else{

				//On insere l'instrument dans la table instru pour les vib
				$str="INSERT into instrument_vib values (NULL,'$num[$i]');";
				$req=mysqli_query($bdd, $str);
				if(isset($_FILES["certif".$i])) uploadInstruCertif($num[$i],$_FILES["certif".$i],$bdd);
				

			}
			$cheminFiche="C:\\Serveur\\www\\metro\\ficheTech\\";
			$cheminPhoto="C:\\Serveur\\www\\metro\\photoInstrument\\";

			if (file_exists($cheminFiche.$numInstru.".pdf")) {	
				copy($cheminFiche.$numInstru.".pdf", $cheminFiche.$num[$i].".pdf");
			}

			if (file_exists($cheminPhoto.$numInstru.".jpg")) {	
				$str = "SELECT fiche_technique FROM fichetechnique_instrument WHERE numInstru_INSTRUMENT = '$numInstru';";
				$req=mysqli_query($bdd, $str);
				if (mysqli_num_rows($req) != 0){
					$lg = mysqli_fetch_object($req);
					$str = "INSERT INTO fichetechnique_instrument VALUES ('$num[$i]', '".$lg->fiche_technique."');";
					$req=mysqli_query($bdd, $str);
				}
				
				copy($cheminPhoto.$numInstru.".jpg", $cheminPhoto.$num[$i].".jpg");
			}

			echo '<script src="../js/success.js"></script>';
		}
	}

}
else{
	
	if (isset($_GET["numInstru"]))
	{
		
?>
	<link href="../calendrier/calendrier.css" rel="stylesheet" />
	<div class="container">
		<div class="page-header">
			<h2>Dupliquer l'instrument <?php echo $_GET["numInstru"]; ?></h2>
		</div>
		<form enctype="multipart/form-data" method="post" action="dupliqInstru.php" role="form" id="formAjout">
			<input type='hidden' value="<?php echo $_GET["numInstru"]; ?>" name="numInstru" />
			<div id="duplication">
				<div class="jumbotron">
					<div class="row">
						<div class="col-md-4">
							<div class="info"><label>Numéro de l'instrument </label><input id="num" name="num[]" title="Numéro de l'instrument" type="text" class="form-control" placeholder="Numéro de l'instrument" required autofocus /></div>							
						</div>
						<div class="col-md-4">
							<div class="info"><label>Numéro de série</label><input id="serie" name="serie[]" title="Numéro de série" type="text" class="form-control" placeholder="Numéro de série" required /></div>
						</div>
						
						<div class="col-md-4">
							<div class="info"><label>Certificat d'étalonnage : </label><input title="Certificat d'étalonnage"  class="form-control certif" style="height:auto;"  type="file" name="certif0"/></div>
						</div>
					</div>
				</div>
			</div>
			<div class="row text-center">
				<input class='btn btn-lg btn-success' type='button' value="Ajouter une duplication" onclick="ajouterDuplication()" />
			</div>
			<div class="row text-center" style="margin-top : 10px">
				<input class='btn btn-lg btn-primary' type='submit' value="Valider" />
				<input class='btn btn-lg btn-primary' type='button' value='Annuler' onclick='document.location.href="index.php"'/>
			</div>
		</form>
	</div><!-- /.container -->
	<script src="../js/dupliqInstru.js"></script>


<?php
	}elseif(!isset($_GET["numInstru"]))
		echo "<div class='alert alert-danger'><strong>Erreur de réception des paramétres</strong></div>";
}
require("bottom.php");
?>