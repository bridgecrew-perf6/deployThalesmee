<?php 
require("top.php");
require('../conf/connexion_param.php');
?>
<script src="../js/swal.js"></script>
<?php
if (isset($_POST["numInstru"]))
{
	require('../fonction.php');
	if (!isset($_FILES["ficheTech"]))
	{
		echo '<script>swal({

			title : "Erreur de modification de la fiche instrument",
			text : "La fiche n\'a pas été transmise",
			icon : "warning"
			
		});</script>';
	}
	if (!isset($_FILES["certif"])) {
		echo '<script>swal({

			title : "Erreur de modification du certificat",
			text : "Le certificat n\'a pas été transmis",
			icon : "warning"
			
		});</script>';
	}

	$fiche = $_FILES["ficheTech"];
	$certif = $_FILES["certif"];
	$certifName = $_FILES["certif"]["name"];
	$nameClean=mysqli_real_escape_string($bdd,$certifName);
	$numInstru = $_POST["numInstru"];
	$firstNum = "";
	$chemin="C:\\Serveur\\metro\\ficheTech\\";
	for ($cpt=0; $cpt<count($numInstru); $cpt++)
	{
		$instru = $numInstru[$cpt];
		$str = "SELECT idInstruVib FROM instrument_vib WHERE numInstru_instrument = '$instru'";
		$req = mysqli_query($bdd, $str);

		if (mysqli_num_rows($req) == 0)
		{
			$str = "SELECT idInstruCapt FROM instrument_vib_capteur WHERE numInstru_instrument = '$instru'";
			$req = mysqli_query($bdd, $str);
			if (mysqli_num_rows($req) == 0)
			{

				echo '<script>swal({

					title : "Impossible d\'ajout de l\'instrument",
					text : "L\'instrument contient une erreur",
					icon : "error"
					
				});</script>';
			}else
			{
				if (isset ($fiche)){

					if ($cpt == 0) {
						uploadFicheTech($instru,$fiche);
						$firstNum = $instru;
					}else
					{
						copy($chemin.$firstNum.".pdf", $chemin.$instru.".pdf");
					}
				}

				if (isset($certif)){
					
					if ($cpt == 0) uploadCertif($instru,$certif,$bdd);
					else {

						
						//suppression de l'ancien certificat
						$str="SELECT certificat from instrument_vib_capteur
						where numInstru_instrument='$instru'";
						$req=mysqli_query($bdd, $str);
						$lg=mysqli_fetch_object($req);
						if($lg->certificat!= 'NULL')
						{
							$lien=$chemin.$lg->certificat;
							if (file_exists($lien)) {	
								@unlink($lien);
							}
						}

						$str="UPDATE instrument_vib_capteur set certificat='".$nameClean."'  
						where numInstru_instrument='$instru'";
						$req=mysqli_query($bdd, $str);
					}	
				}	
			}
		}else
		{
			if (isset ($fiche)){

				if ($cpt == 0) {
					uploadFicheTech($instru,$fiche);
					$firstNum = $instru;
				}else
				{
					copy($chemin.$firstNum.".pdf", $chemin.$instru.".pdf");
				}
			}

			if (isset($certif))
			{
				if ($cpt == 0) uploadInstruCertif($instru,$certif,$bdd);
				else {

					$str = "SELECT numInstru_INSTRUMENT, certificat FROM certificat_instrument WHERE numInstru_INSTRUMENT = '$instru'";
					$req = mysqli_query($bdd, $str);
					if (mysqli_num_rows($req) != 0)
					{

						$lg=mysqli_fetch_object($req);
						if($lg->certificat!= 'NULL')
						{
							$lien=$chemin.$lg->certificat;
							if (file_exists($lien)) {	
								@unlink($lien);
							}
						}

						$str="UPDATE certificat_instrument set certificat='".$nameClean."'  
						where numInstru_INSTRUMENT='$instru'";
						$req=mysqli_query($bdd, $str);

					}else
					{
						$str="INSERT INTO certificat_instrument VALUES ( '$instru','".$nameClean."')";
						$req=mysqli_query($bdd, $str);
					}
				}	

			}
		}
	}
	
}
?>

<link rel="stylesheet" href="../jquery-ui/css/redmond/jquery-ui.min.css">
<link href="../css/addons/datatables.min.css" rel="stylesheet">
<div class="container">
	<form method="post" enctype="multipart/form-data" role="form" action="associerDocument.php" >
	<div class="page-header">
		<h2>Associer un document</h2>
	</div>
	<div class="jumbotron">
		<div class="row">
			<div class="col-md-6">
				<div class="info fiche"><label>Fiche technique : </label><input title="Fiche technique"  class="form-control" style="height:auto;" id="fiche" type="file" name="ficheTech"/></div>
			</div>
			<div class="col-md-6">
				<div class="info certif"><label>Certificat d'étalonnage : </label><input title="Certificat d'étalonnage"  class="form-control" id="certif" style="height:auto;" type="file" name="certif"/></div>
			</div>
		</div>
	</div>
	<div class="jumbotron">
		<table class="table table-striped table-tri" id="tri">
			<thead>
				<tr>
					<th>Numéro</th>
					<th>Type</th>
					<th>Désignation</th>		
					<th>Fournisseur</th>
					<th>Modèle</th>
					<th>N°Série</th>
					<th>Date FI</th>
					<th>Localisation</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th>Numéro</th>
					<th>Type</th>
					<th>Désignation</th>		
					<th>Fournisseur</th>
					<th>Modèle</th>
					<th>N°Série</th>
					<th>Date FI</th>
					<th>Localisation</th>
				</tr>
			</tfoot>
			<tbody>
				
			</tbody>
		</table>
	</div>

	
		<div class="jumbotron" id="content">
			<table class="table table-striped table-tri" id=res>
				<thead>
					<tr>
						<th>Numéro</th>
						<th>Type</th>
						<th>Désignation</th>
						<th>Fournisseur</th>
						<th>Modèle</th>
						<th>N°Série</th>
						<th>Date FI</th>
						<th>Localisation</th>
						<th>Supprimer</th>
					</tr>
				</thead>
				<tbody id="body">
				</tbody>
			</table>
		</div>
		<center>
			<input type="button" value="Retour" class="btn btn-primary btn-lg" onclick="document.location.href='index.php'"/>
			<input type="submit" value="Valider" class="btn btn-success btn-lg"/>
		</center>
	</form>

</div>
<script src="../jquery-ui/js/jquery-ui.min.js"></script>
<script type="text/javascript" language="javascript" src="../DataTables/jquery.dataTables.js"></script>
<script type="text/javascript" src="../js/addons/datatables.min.js"></script>
<script type="text/javascript" src="../js/table.js"></script>
<script type="text/javascript" language="javascript" src="../DataTables/columnFilter.js"></script>	
<script type="text/javascript" src="../js/associerDocument.js" charset="utf-8"></script>

<?php
require("bottom.php");