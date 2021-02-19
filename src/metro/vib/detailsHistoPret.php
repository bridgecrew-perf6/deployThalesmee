<?php
require("top.php");
if(!isset($_GET["idPret"]))
	echo "<div class='alert alert-danger'><strong>Erreur de récupération du prêt</strong></div>";
else
{
	require('../conf/connexion_param.php');
	require('../fonction.php');
	
	$idPret=$_GET["idPret"];
	$str="select nomPret,nomCorresp,nomLocal, typeES from histo_pret p LEFT JOIN localisation l ON p.idLocal_localisation=l.idLocal where p.idPret=$idPret;";
	$req=mysqli_query($bdd,$str);
	
	$lg=mysqli_fetch_object($req);
	$nomPret=$lg->nomPret;
	$nomCorresp=$lg->nomCorresp;
	$lieu=$lg->nomLocal;
	$typeES=$lg->typeES;
	
	
	if($typeES==0)
	{
		$titre="Historique de la calibration n°".$idPret ;
	}
	else
	{
		$titre="Historique du prêt n°".$idPret ;
	}
	
	$str="select i.numInstru, i.marque, i.numSerie, i.date_futureInt,c.datePret,c.dateRetour,c.responsable
	from instrument i, histo_concernePret c
	where i.numInstru=c.numInstru_instrument
	and c.idPret_histo_pret=$idPret;";
	$reqInfo=mysqli_query($bdd,$str);
	
	?>
	<link href="../calendrier/calendrier.css" rel="stylesheet" />
	<div class="container">
		<div class="page-header">
			<h2><?php echo $titre; ?></h2>
		</div>
		<h4>Informations</h4>
		<div class="jumbotron">
			<div class="row" id="typeT">
				<div class="col-md-3" >
					<div class="info"><label>Correspondant:&nbsp </label><?php echo $nomCorresp; ?></div>
					<div class="info"><label>Lieu:&nbsp </label><?php echo $lieu; ?></div>
				</div>
			</div>
		</div>
		<h4>Prévisualisation</h4>
		<div class="jumbotron">
			<div class="info"><label>Nom du prêt:&nbsp </label><?php echo $nomPret; ?></div>
			
			<table class="table table-striped table-tri" id="tab">
				<thead>
					<tr>
						<th>N°Immo</th>						
						<th>Fournisseur</th>
						<th>N°série</th>
						<th>Prochain etalonnage</th>
						<th>Date de sortie</th>
						<th>Date de retour</th>
						<th>Validé par</th>
					</tr>
				</thead>
				<tbody>
				<?php
					while($lg=mysqli_fetch_object($reqInfo))
					{
						$num=$lg->numInstru;
						$marque=$lg->numInstru;
						$numSerie=$lg->numSerie;
						//date format fr
						$nextEtal=dateSQLToFr($lg->date_futureInt);
						$datePret=dateSQLToFr($lg->datePret);
						$dateRetour=dateSQLToFr($lg->dateRetour);
						$resp = $lg->responsable;
						
						
						
						echo "<tr>";
							echo "<td>$num</td>";
							echo "<td>$marque</td>";
							echo "<td>$numSerie</td>";
							echo "<td>$nextEtal</td>";
							echo "<td>$datePret</td>";
							echo "<td>$dateRetour</td>";	
							echo "<td>$resp</td>";
						echo "</tr>";

					}
				?>
				</tbody>
			</table>
		</div>
		
		<div class="text-center">
			<input class='btn btn-lg btn-primary' type='button' value='Excel' onclick='document.location.href="./pretExcel.php?histo_idPret=<?php echo $idPret;?>"' />
			<!-- Formulaire pour le bouton suppression, plus de sécurité en passant l'id en post et non en get (empeche une suppression juste en passant par l'url) -->
			<form  style="display:inline;" method="post" action="suppPret.php" onsubmit="return confirmSupp();">
				<input type="hidden" name="idPret" value="<?php echo $idPret;?>"/>
				<input type="submit" class="btn btn-lg btn-primary" value="Supprimer"/>
			</form>
			<input type="button" value="Retour" class="btn btn-primary btn-lg" onclick="document.location.href='index.php'"/>
		</div>
	</div>
	<script>
	function confirmSupp()
	{
		if(confirm("Voulez vous vraiment supprimer ce prêt ?"))
			return true;
		return false;

	}


	</script>
<?php
}
require("bottom.php");