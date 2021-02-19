<?php
require("top.php");
require('../fonction.php');
require('../conf/connexion_param.php');
if(isset($_POST["numInstru"]))
{
	$labo=$_SESSION['metro']['labo'];
	$nomCor=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["nomCor"]));
	$nomPret=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["nomPret"]));
	$typeES=$_POST["typeES"];
	$tabNum=$_POST["numInstru"];
	$tabDateP=$_POST["dateP"];
	$tabDateR=$_POST["dateR"];
	$loca="";
	$idStatut="";
	if($typeES==0)
	{
		$loca=2;
		$idStatut=2;
	}
	else
	{
		$loca=$_POST["loca"];
		$idStatut=3;
	}
	
	$erreur="";
	
	//creation du pret s'il n'existe pas 
	if(!isset($_POST["idPret"]))
	{
		$str="insert into pret values (NULL,'$nomPret','$nomCor','$typeES','$labo','$loca');";
		$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
		$req=mysqli_query($bdd,$str);
		if(!$req)
			$erreur="Erreur d'ajout du prêt";
		else
			$idPret=mysqli_insert_id($bdd);
	}
	else
	{
		$idPret=$_POST["idPret"];
		$str="update pret set nomPret='$nomPret',typeES='$typeES', nomCorresp='$nomCor', idLocal_localisation='$loca' where idPret=$idPret;";
		$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
		$req=mysqli_query($bdd,$str);
		if(!$req)
			$erreur="Erreur de mise à jour du prêt";
	}
	if($erreur!="")
		echo "<div class='alert alert-danger'><strong>$erreur</strong></div>";
	else
	{
		//suppression des anciennes lignes
		$str="delete from concernePret where idPret_pret='$idPret';";
		$req=mysqli_query($bdd,$str);
		$nbNum=count($tabNum);
		//ajout des nouvelles
		for($i=0;$i<$nbNum;$i++)
		{
			$num=htmlspecialchars(mysqli_real_escape_string($bdd,$tabNum[$i]));
			$dateP=htmlspecialchars(mysqli_real_escape_string($bdd,$tabDateP[$i]));
			$dateR=htmlspecialchars(mysqli_real_escape_string($bdd,$tabDateR[$i]));
			
			//date en format sql
			$dateP=dateFrToSQL(htmlspecialchars(mysqli_real_escape_string($bdd,$dateP)));
			$dateR=dateFrToSQL(htmlspecialchars(mysqli_real_escape_string($bdd,$dateR)));
			
			$str="insert into concernePret values ('$idPret','$num','$dateP','$dateR');";
			$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
			$req=mysqli_query($bdd,$str);
			if(!$req)
				echo "<div class='alert alert-warning'><strong>Instrument n°:$num inconnu ou déja emprunté, non ajouté au prêt</strong></div>";
			else
			{
				//update des info de l'instrument
				$str="update instrument set idStatut_statut='$idStatut', idLocal_localisation='$loca' where numInstru='$num';";
				$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
				$req=mysqli_query($bdd,$str);
			}
		}
		echo "<div class='text-center'>";
			echo "<div class='alert alert-success'><strong>Entrée/sortie n°$idPret enregistré</strong></div>";
			echo "<input class='btn btn-lg btn-primary' type='button' value='Retour' onclick='document.location.href=\"./detailsPret.php?idPret=$idPret\"' />";
		echo "</div>";
	}
}
else
{
	if(isset($_GET["idPret"]))
	{
		$idPret=$_GET["idPret"];
		$lienRetour="detailsPret.php?idPret=".$idPret;
		$str="select nomPret,nomCorresp,idLocal_localisation, typeES from pret where idPret=$idPret;";
		$req=mysqli_query($bdd,$str);
		if(mysqli_num_rows($req)!=0)
		{
			$lg=mysqli_fetch_object($req);
			$nomPret=$lg->nomPret;
			$nomCorresp=$lg->nomCorresp;
			$lieu=$lg->idLocal_localisation;
			$typeES=$lg->typeES;
			
			$str="select i.numInstru, de.nomDes, i.modele, i.marque, i.numSerie, i.date_futureInt,c.datePret,c.dateRetour
			from concernePret c, instrument i
			LEFT OUTER JOIN designation de ON i.idDes_designation=de.idDes
			where i.numInstru=c.numInstru_instrument
			and c.idPret_pret=$idPret;";
			$reqInfo=mysqli_query($bdd,$str);
			$titre="Entrée/sortie n° $idPret";
		}
		else //pret inconnu, cas interdit
		{
			echo "<div class='alert alert-warning text-center'><strong>Prêt $idPret inconnu, création d'un nouveau prêt</strong></div>";
			unset($idPret);
			$titre="Ajouter une entrée/sortie";
			$lienRetour="index.php";
		}
	}
	else
	{
		$titre="Ajouter une entrée/sortie";
		$lienRetour="index.php";
	}
	
	$str="select idLocal, nomLocal from localisation where idLabo_labo=2;";
	$reqLoc=mysqli_query($bdd, $str);
	
	?>
	<link rel="stylesheet" href="../jquery-ui/css/redmond/jquery-ui.min.css">
	<link href="../calendrier/calendrier.css" rel="stylesheet" />
	<div class="container">
		<form method="post" action="./creerModifierPret.php" id="form">
			<div class="page-header">
				<h2><?php echo $titre;?></h2>
			</div>
			<h4>Informations</h4>
			<div class="jumbotron">
				<div class="row" id="typeT">
					<div class="col-md-4" >
						<input type="text" class="form-control" id="nomCor" name="nomCor" value="<?php if(isset($nomCorresp))echo $nomCorresp;?>" title="Nom du correspondant" placeholder="Nom du correspondant" required/>
						<input placeholder="Date de sortie: JJ/MM/YYYY" value="<?php echo date("d/m/Y"); ?>" type="text"  id="pretGen" class="calendrier form-control" size="8" />
					</div>
					<div class="col-md-4" >
						<select id="typeES" title="Type d'entrée/sortie" class="form-control" name="typeES" required>
							<option value="" disabled>Type d'entrée/sortie</option>
							<?php 
							echo '<option value="0" selected>Calibration</option>';
							if(isset($typeES) && $typeES!=0)
								echo '<option value="1" selected>Prêt</option>';
							else
								echo '<option value="1">Prêt</option>';
							?>
						</select>
						<input placeholder="Date de retour: JJ/MM/YYYY" value="" type="text" id="retourGen" class="calendrier form-control" size="8" />
					</div>
					<div class="col-md-4" >
						<select id="loca" title="Localisation" class="form-control" name="loca">
							<option value="" disabled selected>Localisation</option>
							<?php
								while($lgLoc=mysqli_fetch_object($reqLoc))
								{
									if(isset($lieu) && $lgLoc->idLocal==$lieu)
										echo '<option value="'.$lgLoc->idLocal.'" selected>'.$lgLoc->nomLocal.'</option>';
									else
										echo '<option value="'.$lgLoc->idLocal.'">'.$lgLoc->nomLocal.'</option>';
								}
							?>
						</select>
					</div>
				</div>
				<textarea class="form-control" name="nomPret" placeholder="Nom de l'entrée/sortie"><?php if(isset($nomPret))echo $nomPret;?></textarea>
			</div>
			<h4>Prévisualisation</h4>
			<div class="jumbotron">
				<table class="table table-striped table-tri" id="tab">
					<thead>
						<tr>
							<th>N°Immo</th>						
							<th>Désignation</th>						
							<th>Fournisseur</th>						
							<th>Modèle</th>
							<th>N°série</th>
							<th>Date FI</th>
							<th>Date de sortie</th>
							<th>Date de retour</th>
						</tr>
					</thead>
					<tbody id="tabBody">
					</tbody>
				</table>
			</div>
			<input id="addL" type="button" class="btn  btn-success btn-lg" value="Ajouter un instrument"/>
			<div class="text-center">
				<?php if(isset($idPret)){ echo "<input type='hidden' value='$idPret' name='idPret' />";}?>
				<input type="submit" value="Valider" class="btn btn-primary btn-lg"/>
				<input type="button" value="Retour" class="btn btn-primary btn-lg" onclick="document.location.href='<?php echo $lienRetour;?>'"/>
			</div>
		</form>
	</div>
	<div id="dialog_t" title="Veuillez saisir un numéro de capteur">
		<input placeholder="Numéro d'instrument - de série - trescal id" class="form-control" type="text" id="nouvNumCapt" title="Numéro d'instrument - de série - trescal id" />
	</div>
	<script src="../jquery-ui/js/jquery-ui.min.js"></script>
	<script src="../calendrier/calendrier.js"></script>
	<script type="text/javascript" language="javascript" src="../js/creerModifierPret.js"></script>
	<?php
	if(isset($idPret))
	{
		while($lg=mysqli_fetch_object($reqInfo))
		{
			$output = array(
			"numInstru" => $lg->numInstru,
			"nomDes" => $lg->nomDes,
			"marque" => $lg->marque,
			"modele" => $lg->modele,
			"numSerie" => $lg->numSerie,
			"nextEtal" => dateSQLToFr($lg->date_futureInt),
			"datePret" => $lg->datePret,
			"dateRetour" => $lg->dateRetour,
			);
			echo "<script>ajouterInstru(".json_encode( $output ).");</script>";
		}
	}
}
require("bottom.php");