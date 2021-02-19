<?php
require("top.php");
if(!isset($_GET["idPret"]))
	echo "<div class='alert alert-danger'><strong>Erreur de récupération de l'entrée sortie</strong></div>";
else
{
	require('../conf/connexion_param.php');
	require('../fonction.php');
	
	$idPret=$_GET["idPret"];
	$str="select nomPret,nomCorresp,nomLocal, typeES from pret p 
	LEFT JOIN localisation l ON p.idLocal_localisation=l.idLocal 
	where p.idPret=$idPret;";
	$req=mysqli_query($bdd,$str);
	$lg=mysqli_fetch_object($req);
	$nomPret=$lg->nomPret;
	$nomCorresp=$lg->nomCorresp;
	$lieu=$lg->nomLocal;
	$typeES=$lg->typeES;
	
	$str="select i.numInstru, de.nomDes, i.modele, i.marque, i.numSerie, i.date_futureInt,c.datePret,c.dateRetour
	from concernePret c, instrument i
	LEFT OUTER JOIN designation de ON i.idDes_designation=de.idDes
	where i.numInstru=c.numInstru_instrument
	and c.idPret_pret=$idPret;";
	$reqInfo=mysqli_query($bdd,$str);
	$titre="";
	if($typeES==0)
		$titre="Détails de la calibration n°".$idPret;
	else
		$titre="Détails du prêt n°".$idPret;
	
	?>
	<link href="../calendrier/calendrier.css" rel="stylesheet" />
	<div class="container">
		<div class="page-header">
			<h2><?php echo $titre; ?></h2>
		</div>
		<h4>Informations</h4>
		<div class="jumbotron">
			<div class="row" id="typeT">
				<div class="col-md-4" >
					<div class="info"><label>Correspondant:&nbsp </label><?php echo $nomCorresp; ?></div>
					
				</div>
				<div class="col-md-4" >
					<div class="info"><label>Lieu:&nbsp </label><?php echo $lieu; ?></div>
				</div>
			</div>
			<div><?php echo $nomPret; ?></div>
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
				<tbody>
				<?php
					while($lg=mysqli_fetch_object($reqInfo))
					{
						$num=$lg->numInstru;
						$nomDes=$lg->nomDes;
						$marque=$lg->marque;
						$modele=$lg->modele;
						$numSerie=$lg->numSerie;
						//date format fr
						$nextEtal=dateSQLToFr($lg->date_futureInt);
						$datePret=dateSQLToFr($lg->datePret);
						$dateRetour=dateSQLToFr($lg->dateRetour);
						
						echo "<tr>";
							echo "<td>$num</td>";
							echo "<td>$nomDes</td>";
							echo "<td>$marque</td>";
							echo "<td>$modele</td>";
							echo "<td>$numSerie</td>";
							echo "<td>$nextEtal</td>";
							echo "<td>$datePret</td>";
							echo "<td>$dateRetour</td>";	
						echo "</tr>";
					}
				?>
				</tbody>
			</table>
		</div>
		
		<div class="text-center">
			<input class='btn btn-lg btn-primary' type='button' value='Excel' onclick='document.location.href="./pretExcel.php?idPret=<?php echo $idPret;?>"' />
			<input type="button" value="Modifier" class="btn btn-primary btn-lg" onclick="document.location.href='creerModifierPret.php?idPret=<?php echo $idPret; ?>'"/>
			<input type="button" value="Retour d'instruments" class="btn btn-primary btn-lg" onclick="document.location.href='retourPret.php?idPret=<?php echo $idPret; ?>'"/>
			<input type="button" value="Retour" class="btn btn-primary btn-lg" onclick="document.location.href='index.php'"/>
		</div>
	</div>
<?php
}
require("bottom.php");