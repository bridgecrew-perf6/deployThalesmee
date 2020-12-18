<?php
require("top.php"); 
require('../conf/connexion_param.php');

if(isset($_POST["num"]))
{
	require('../fonction.php');
	//recup des parametres
	$numInstru=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["num"]));

	if(isset($_POST['fonc']))$fonc=$_POST['fonc']; else $fonc="";
	if(isset($_POST['loca']))$loca=$_POST['loca']; else $loca="";
	$cara=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["cara"]));
	$com=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["com"]));
	
	//On insere l'instrument dans la table instru pour les emc
	$str="insert into instrument_emc values (NULL,'$cara','$numInstru','$fonc');";
	$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
	$req=mysqli_query($bdd, $str);
	if(!$req)
		echo '<div class="alert alert-danger"><strong>Erreur de spec de l\'instrument emc</strong></div>';
	else
	{
		//update des commentaires de l'instruments
		$str="update instrument set commentaire='$com' where numInstru='$numInstru';";
		$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
		$req=mysqli_query($bdd, $str);
		
		$str="delete from ajoutInstru where numInstru_instrument='$numInstru';";
		$req=mysqli_query($bdd, $str);
		echo "<div class='text-center'>";
			echo "<div class='alert alert-success'><strong>Instrument ajouté</strong></div>";
			echo "<input class='btn btn-lg btn-primary' type='button' value='Retour' onclick='document.location.href=\"index.php\"' />";
		echo "</div>";
	}	
}
else{
	$numInstru=$_GET["numInstru"];
	//on recupere les categories
	$str="select idEquip, nomEquip from equipement_emc;";
	$reqEquip=mysqli_query($bdd, $str);
	
	$str="select idLocal, nomLocal from localisation where idLabo_labo=1;";
	$reqLoc=mysqli_query($bdd, $str);
		
?>
	<div class="container">
		<div class="page-header">
			<h2>Renseigner un instrument : <?php echo $numInstru; ?></h2>
		</div>
		<form method="post" action="ajoutViaTresc.php" role="form">
			<h4>Informations générales</h4>
			<div class="jumbotron">
				<div class="row">
					<div class="col-md-4">
						<select id="equip" title="Equipement" class="form-control">
							<option value="-1" disabled selected>Equipement</option>
							<?php
								while($lgEquip=mysqli_fetch_object($reqEquip))
									echo '<option value="'.$lgEquip->idEquip.'">'.$lgEquip->nomEquip.'</option>';
							?>
						</select>
						<input id="cara" name="cara" title="Caractéristiques" type="text" class="form-control" placeholder="Caractéristiques" />
					</div>
					<div class="col-md-4">
						<select id="fonc" title="Fonction" class="form-control" name="fonc">
							<option value="-1" disabled selected>Fonction</option>
						</select>
					</div>
					<div class="col-md-4">
						<select id="loca" title="Localisation" class="form-control" name="loca">
							<option value="-1" disabled selected>Localisation</option>
							<?php
								while($lgLoc=mysqli_fetch_object($reqLoc))
									echo '<option value="'.$lgLoc->idLocal.'">'.$lgLoc->nomLocal.'</option>';
							?>
						</select>					
					</div>
				</div>
				<textarea id="com" name="com" title="Remarque" type="text" class="form-control" placeholder="Remarque" /></textarea>
			</div>
			<div class="text-center">
				<input type="hidden" name="num" value="<?php echo $numInstru;?>" />
				<input class='btn btn-lg btn-primary' type='submit' value="Valider" />
				<input class='btn btn-lg btn-primary' type='button' value='Annuler' onclick='document.location.href="index.php"'/>
			</div>
		</form>
	</div><!-- /.container -->
	<script src="../js/crudInstru.js"></script>
<?php
}
require("bottom.php");