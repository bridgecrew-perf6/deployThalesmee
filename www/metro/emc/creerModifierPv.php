<?php
require("top.php");
require('../conf/connexion_param.php');

if(isset($_POST["titrePV"]))
{
	require('../conf/connexion_param.php');
	$titrePV=htmlspecialchars(mysqli_real_escape_string($bdd,$_POST["titrePV"]));
	
	//creation du pv s'il n'existe pas, date aujourd'hui et labo emc (1)
	if(!isset($_POST["idPV"]))
	{
		$str="insert into pv values (NULL,CURDATE(),'$titrePV',1);";
		$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
		$req=mysqli_query($bdd,$str);
		$idPV=mysqli_insert_id($bdd);
	}
	else
	{
		$idPV=$_POST["idPV"];
		$str="update pv set titrePv='$titrePV' where idPV=$idPV;";
		$str = str_replace("''", "null", $str);//on remplace tous les ,'', (du au fait d'une valeur null) par null
		$req=mysqli_query($bdd,$str);
	}
	
	//suppression des anciennes lignes
	$str="delete from pvtest where idPV_pv=$idPV;";
	$req=mysqli_query($bdd,$str);
	if(isset($_POST["numInstru"]))
	{
		$numInstru=$_POST["numInstru"];
		$test=$_POST["test"];
		$nb=count($numInstru);
		for($i=0;$i<$nb;$i++)
		{
			//recup des differents tests
			$tabIdTest=$test[$i];
			$tabIdTest=explode("$$",$tabIdTest);
			$nbTest=count($tabIdTest);
			for($j=0;$j<$nbTest;$j++)
			{
				$idTest=trim($tabIdTest[$j]);
				$str="insert into pvtest values ($idPV,'".$numInstru[$i]."', '$idTest');";
				$req=mysqli_query($bdd,$str);
				if(!$req)
					echo "<div class='alert alert-danger'><strong>Instrument n°".$numInstru[$i]." inconnu, ligne non ajouté au pv</strong></div>";
				
			}
		}
	}
	?>
	<div class='text-center'>
		<div class='alert alert-success'><strong>PV enregistré</strong></div>
		<a class='btn btn-lg btn-primary' type='button' href="./index.php" />Retour</a>
	</div>
	<?php
}
else
{
	$titrePV="";
	if(isset($_GET["idPV"]))
	{
		$idPV=$_GET["idPV"];
		
		$str="select titrePv from PV where idPV=$idPV;";
		$req=mysqli_query($bdd,$str);
		$titrePV=mysqli_fetch_object($req)->titrePv;
		
		$str="select i.numInstru, d.fonction, i.numSerie, i.modele, i.marque, i.date_futureInt, GROUP_CONCAT(t.idTest SEPARATOR ',') as cIDTest, GROUP_CONCAT(t.nomtest SEPARATOR ',') as cTest
		from pvtest p, test t, instrument i, instrument_emc ie, designation_emc d
		where idPV_pv=$idPV
		and ie.numInstru_instrument=i.numInstru
		and ie.idDes_designation_emc=d.idDes
		and p.numInstru_instrument=i.numInstru
		and t.idTest=p.idTest_test
		group by i.numInstru;";
		$reqInfo=mysqli_query($bdd,$str);
		$titre="PV n° $idPV";
	}
	else
		$titre="Créer un PV de Test";

	$str="select idTest, nomTest from test where idLabo_labo=1;";
	$req=mysqli_query($bdd,$str);
	?>
	<link rel="stylesheet" href="../jquery-ui/css/redmond/jquery-ui.min.css">
	<div class="container">
		<div class="page-header">
			<h2><?php echo $titre;?></h2>
		</div>
		<h4>Séléction des tests</h4>
		<div class="jumbotron">
			<div class="row" id="typeT">
				<div class="col-md-3" >
					<select title="Type de test" class="form-control selTypeT">
						<option value="-1" disabled selected>Choisir le type de test</option>
						<?php 
						while($lg=mysqli_fetch_object($req))
						{
							echo "<option value='".$lg->idTest."' >".$lg->nomTest."</option>";
						}
						?>
					</select>
				</div>
			</div>	
		</div>
		<div class="text-center">
			<input id="addT" type="button" class="btn  btn-success" value="Ajouter un type de test"/>	
			<input id="suppT" type="button" class="btn  btn-primary" value="Supprimer un type de test"/>	
		</div>
		<h4>Prévisualisation du PV</h4>
		<form method="post" action="./creerModifierPv.php" id="form">
			<div class="jumbotron">
				<textarea class="form-control" name="titrePV" placeholder="Titre du PV" required><?php echo $titrePV;?></textarea>
				<table class="table table-striped table-tri" >
					<thead>
						<tr>
							<th>N°</th>
							<th>Fonction</th>
							<th>Type/Modèle</th>
							<th>Manufacturer</th>
							<th>Numéro de série</th>
							<th>Next Cal</th>
							<th>Test</th>
							<th>Supprimer</th>
						</tr>
					</thead>
					<tbody id="tab">
					</tbody>
				</table>
			</div>
			<div class="text-center">
				<input id="addL" type="button" class="btn  btn-success btn-lg" value="Ajouter un instrument"/>
				<input type="submit" value="Valider" class="btn btn-primary btn-lg"/>
				<?php
				if(isset($idPV)) //si modification, on ajoute l'idprojet en hidden et le btn annuler renvoi vers la page detail
				{
					echo '<input type="hidden" name="idPV" value="'.$idPV.'" />'; 
					echo '<a href="detailsPv.php?idPV='.$idPV.'" class="btn btn-primary btn-lg" role="button">Annuler</a>';
				}
				else //sinon le btn annuler renvoi vers la page général
					echo '<a href="index.php" class="btn btn-primary btn-lg" role="button">Annuler</a>';
				?>
			</div>
		</form>
	</div>
	<div id="dialog_t" title="Veuillez saisir un numéro d'instrument">
		<input placeholder="Numéro d'instrument - de série - trescal id" class="form-control" type="text" id="nouvNum" title="Numéro d'instrument - de série - trescal id" />
	</div>
	<script src="../jquery-ui/js/jquery-ui.min.js"></script>
	<script type="text/javascript" language="javascript" src="../js/pvTest.js"></script>
	<?php
	if(isset($idPV))
	{
		while($lg=mysqli_fetch_object($reqInfo))
		{
			$output = array(
			"numInstru" => $lg->numInstru,
			"fonction" => $lg->fonction,
			"model" => $lg->modele,
			"manu" => $lg->marque,
			"numSerie" => $lg->numSerie,
			"cal" => $lg->date_futureInt,
			"test" => $lg->cTest,
			"idTest" => $lg->cIDTest
			);
			echo "<script>ajouterInstruPv(".json_encode( $output ).");</script>";
		}
	}
}
require("bottom.php");