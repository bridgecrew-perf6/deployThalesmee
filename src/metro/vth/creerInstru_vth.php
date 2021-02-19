<?php 
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
	
	if(isset($_POST['des']))$des=$_POST['des']; else $des="";
	if(isset($_POST['loca']))$loca=$_POST['loca']; else $loca="";
	
	
	//ajout de l'instrument
	$str="INSERT INTO instrument VALUES ('$numInstru','$ancienNum',null,'$des','$fab','$model','$serie','024VIB',
	'$lastCal','$nextCal',null,'$etat','$loca','1',null)";
	$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
	$req=mysqli_query($bdd, $str);

	if(!$req) //une erreur dans la requete renvera false
		echo '<div class="alert alert-danger"><strong>Erreur d\'ajout de l\'instrument</strong></div>';
	else
	{
		//On insere l'instrument dans la table instru pour les vib
		$str="insert into instrument_vth values (NULL,'$numInstru');";
		$req=mysqli_query($bdd, $str);
		if(!$req)
		{
			//on supprime l'instrument ajouté précédemment
			$str="delete from instrument where numInstru ='$numInstru';";
			$req=mysqli_query($bdd, $str);
			echo '<div class="alert alert-danger"><strong>Erreur d\'ajout de l\'instrument vib</strong></div>';
		}
		else
		{
			echo "<div class='text-center'>";
				echo "<div class='alert alert-success'><strong>Instrument ajouté</strong></div>";
				echo "<input class='btn btn-lg btn-primary' type='button' value='Retour' onclick='document.location.href=\"index.php\"' />";
			echo "</div>";
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
		
?>
	<link href="../calendrier/calendrier.css" rel="stylesheet" />
	<div class="container">
		<div class="page-header">
			<h2>Ajouter un instrument</h2>
		</div>
		<form method="post" action="creerInstru.php" role="form">
			<h4>Informations générales</h4>
			<div class="jumbotron">
				<div class="row">
					<div class="col-md-4">
						<input id="num" name="num" title="Numéro de l'instrument" type="text" class="form-control" placeholder="Numéro de l'instrument" required autofocus />			
						<input id="model" name="model" title="Type/Modèle" type="text" class="form-control" placeholder="Type/Modèle" required />			
						<input id="serie" name="serie" title="Numéro de série" type="text" class="form-control" placeholder="Numéro de série" required />			
						<select id="etat" title="Equipement" class="form-control" name="etat">
							<option value="-1" disabled selected>État</option>
							<?php
								while($lgEtat=mysqli_fetch_object($reqEtat))
									echo '<option value="'.$lgEtat->idEtat.'">'.$lgEtat->nomEtat.'</option>';
							?>
						</select>
					</div>
					<div class="col-md-4">
						<input id="aNum" name="aNum" title="Ancien immo" type="text" class="form-control" placeholder="Ancien immo" />
						<input id="fab" name="fab" title="Fabricant" type="text" class="form-control" placeholder="Fabricant" required />
						<input placeholder="Dernière cal: JJ/MM/YYYY" value="" type="text"  id="lCal" name="lCal" class="calendrier form-control" size="8" />
						<input placeholder="Prochaine cal: JJ/MM/YYYY" value="" type="text"  id="nCal" name="nCal" class="calendrier form-control" size="8" />
					</div>
					<div class="col-md-4">
						<select id="dom" title="Domaine" class="form-control">
							<option value="-1" disabled selected>Domaine</option>
							<?php
								while($lgDom=mysqli_fetch_object($reqDom))
									echo '<option value="'.$lgDom->idDom.'">'.$lgDom->nomDom.'</option>';
							?>
						</select>
						<select id="des" title="Désignation" class="form-control" name="des">
							<option value="-1" disabled selected>Désignation</option>
						</select>
						<select id="loca" title="Localisation" class="form-control" name="loca">
							<option value="-1" disabled selected>Localisation</option>
							<?php
								while($lgLoc=mysqli_fetch_object($reqLoc))
									echo '<option value="'.$lgLoc->idLocal.'">'.$lgLoc->nomLocal.'</option>';
							?>
						</select>
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
	<script src="../js/creerModifierInstru.js"></script>
	<script>include="vib";</script>
<?php
}
