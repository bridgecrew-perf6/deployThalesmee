<?php
require("top.php");
require('../conf/connexion_param.php');

if(isset($_GET["idPV"]))
{
	$idPV=$_GET["idPV"];
	$str="select titrePv from PV where idPV=$idPV;";
	$req=mysqli_query($bdd,$str);
	$titrePV=mysqli_fetch_object($req)->titrePv;
	
	$str="select i.numInstru, d.fonction, i.numSerie, i.modele, i.marque, i.date_futureInt, GROUP_CONCAT(t.nomtest SEPARATOR ',') as cTest
	from pvtest p, test t, instrument i, instrument_emc ie, designation_emc d
	where idPV_pv=$idPV
	and ie.numInstru_instrument=i.numInstru
	and ie.idDes_designation_emc=d.idDes
	and p.numInstru_instrument=i.numInstru
	and t.idTest=p.idTest_test
	group by i.numInstru
	order by cTest DESC;";
	$reqInfo=mysqli_query($bdd,$str);
	$titre="PV n° $idPV";

	?>
	<link rel="stylesheet" href="../jquery-ui/css/redmond/jquery-ui.min.css">
	<div class="container">
		<div class="page-header">
			<h2><?php echo $titre;?></h2>
		</div>
		<h4>Titre du PV</h4>
		<div class="jumbotron">
			<?php echo $titrePV; ?>
		</div>
		
		<h4>Prévisualisation du PV</h4>
		<div class="jumbotron">
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
					</tr>
				</thead>
				<tbody id="tab">
				</tbody>
			</table>
		</div>
		<div class="text-center">
			<a href="creerModifierPv.php?idPV=<?php echo $idPV; ?>" class="btn btn-primary btn-lg" role="button">Modifier</a>
			<a href="pvExcel.php?idPV=<?php echo $idPV; ?>" class="btn btn-primary btn-lg" role="button">Excel</a>
			<form  style="display:inline;" method="post" action="suppPv.php" onsubmit="return confirmSupp();">
				<input type="hidden" name="idPV" value="<?php echo $idPV;?>"/>
				<input type="submit" class="btn btn-lg btn-primary" value="Supprimer"/>
			</form>
			<a href="listPvTest.php" class="btn btn-primary btn-lg" role="button">Retour</a>
		</div>
	</div>
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
			"test" => $lg->cTest
			);
			echo "<script>detailsPvInstru(".json_encode( $output ).");</script>";
		}
	}
}
else
	echo "Erreur de reception des parametres";
require("bottom.php");