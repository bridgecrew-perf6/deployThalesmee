<?php
require("top.php");
if(isset($_POST["num"]))
{
	require('../conf/connexion_param.php');
	$idPret=$_POST["idPret"];
	$str="select idPret from histo_pret where idPret='$idPret';";
	$req=mysqli_query($bdd,$str);
	if(mysqli_num_rows($req)==0) //si le pret n'est pas deja historisé
	{
		$str="insert into histo_pret select idPret, nomPret, nomCorresp, typeES, idLabo_labo, idLocal_localisation from pret where idPret='$idPret';";
		$req=mysqli_query($bdd,$str);
	}
	foreach($_POST["num"] as $num)
	{
		$dateRetour=date('y-m-d');
		//ajout des intruments séléctionnées dans l'historique
		$str="insert into histo_concernepret SELECT idPret_pret, numInstru_instrument, datePret, '$dateRetour'
		FROM concernepret 
		where idPret_pret='$idPret'
		and numInstru_instrument='$num';";
		$req=mysqli_query($bdd,$str);
		
		//update du statut et de la localisation des intrument retourner
		$str="update instrument set idStatut_statut=1, idLocal_localisation='1' where numInstru='$num';";
		$req=mysqli_query($bdd,$str);
		//suppression de la table actuelle
		$str="delete from concernepret
		where idPret_pret='$idPret'
		and numInstru_instrument='$num';";
		$req=mysqli_query($bdd,$str);
	}
	
	//on verifi qu'il reste des lignes pour ce pret
	$str="select idPret_pret from concernepret where idPret_pret='$idPret';";
	$req=mysqli_query($bdd,$str);
	if(mysqli_num_rows($req)==0)//si 0 on supprime le pret (histo terminé)
	{
		$str="delete from pret where idPret='$idPret';";
		$req=mysqli_query($bdd,$str);
		
		echo "<div class='text-center'>";
			echo "<div class='alert alert-success'><strong>Tous les instruments ont été retournées</strong></div>";
			echo "<input class='btn btn-lg btn-primary' type='button' value='Retour' onclick='document.location.href=\"./index.php\"' />";
		echo "</div>";
	}
	else
	{
		echo "<div class='text-center'>";
			echo "<div class='alert alert-success'><strong>Instruments correctements retournées</strong></div>";
			echo "<input class='btn btn-lg btn-primary' type='button' value='Retour' onclick='document.location.href=\"./index.php\"' />";
		echo "</div>";
	}
}
elseif(!isset($_GET["idPret"]))
{
	echo "<div class='alert alert-danger'><strong>Erreur de réception des paramétres</strong></div>";
}
else
{
	require('../conf/connexion_param.php');
	require('../fonction.php');
	$idPret=$_GET["idPret"];
	
	$str="select i.numInstru, i.marque, i.numSerie, i.date_futureInt,c.datePret,c.dateRetour
	from instrument i, concernePret c
	where i.numInstru=c.numInstru_instrument
	and c.idPret_pret=$idPret;";
	$reqInfo=mysqli_query($bdd,$str);
?>

	<div class="container">
		<div class="page-header">
			<h2>Séléctionnez les instruments retournés</h2>
		</div>
		<form action="retourPret.php" method="POST" id="form" onSubmit="return validForm()">
			<div class="jumbotron">
				<table class="table table-striped table-tri" id="tab">
					<thead>
						<tr>
							<th><input type='checkbox' id="allC" /></th>
							<th>N°Immo</th>						
							<th>Marque</th>
							<th>N°série</th>
							<th>Prochain etalonnage</th>
							<th>Date de sortie</th>
							<th>Date de retour</th>
						</tr>
					</thead>
					<tbody>
					<?php
						while($lg=mysqli_fetch_object($reqInfo))
						{
							$num=$lg->numInstru;
							$marque=$lg->marque;
							$numSerie=$lg->numSerie;
							//date format fr
							$nextEtal=dateSQLToFr($lg->date_futureInt);
							$datePret=dateSQLToFr($lg->datePret);
							$dateRetour=dateSQLToFr($lg->dateRetour);		
							
							echo "<tr>";
								echo '<td><input type="checkbox" name="num[]" value="'.$num.'" class="check" /></td>';
								echo "<td>$num</td>";
								echo "<td>$marque</td>";
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
			<input type="hidden" name="idPret" value="<?php echo $idPret; ?>"/>	
			<div class="text-center">
				<input type="submit" class="btn btn-primary btn-lg" value="Valider" />
				<input type="button" value="Annuler" class="btn btn-primary btn-lg" onclick="document.location.href='detailsPret.php?idPret=<?php echo $idPret;?>'"/>
			</div>
		</form>
	</div>
	<script type="text/javascript" language="javascript" src="../js/retourPret.js"></script>	
<?php
}
require("bottom.php");