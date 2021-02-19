<?php
require('top.php');
if(!isset($_SESSION['idDP']))
	echo '<div class="alert alert-danger"><strong>Erreur de récuperation de la demande</strong></div>';
else
{
	$idDP=$_SESSION["idDP"];
	require('../conf/connexion_param.php'); //connexion a la bdd
	//on verifie l'avancement de l'etape
	$str="select validiteDP from DEMANDE_PROCEDURE where idDP=$idDP;";
	$req=mysqli_query($bdd,$str);
	if(!$req)
		echo '<div class="alert alert-danger"><strong>Erreur de récupération du numéro de l\'étape</strong></div>';
	else
	{
		$etape=(mysqli_fetch_object($req)->validiteDP);
		if ($etape!=1)
			echo '<div class="alert alert-danger"><strong>L\'étape précédente n\'a pas correctement été validé</strong></div>';
		else
		{
		?>
			<div class="container">
				<div class="page-header">
					<h2>Demande de procédure: n° <?php echo $idDP; ?></h2>
				</div>
				<p>
					<iframe src="genPDFDemande_rapide.php?idDP=<?php echo $idDP;?>" class="iframe_resu"></iframe>
				</p>	
				<center>
					<input type="button" class="btn btn-lg btn-primary" style="float:left;" value="Précédent" onclick="document.location.href='DProc_1.php?idDP=<?php echo $idDP; ?>'"/>
					<input type="button" class="btn btn-lg btn-primary"  value="Enregister et Quitter" id="valider" onclick="document.location.href='index.php';"/>
					<form style="display:inline;" onsubmit="confirmEnvoi()" method="post" id='form' action="traitement_validation.php" >
						<input type="hidden" value="0" name="valid" />
						<input type="submit" class="btn btn-lg btn-primary" style="float:right;" value="Envoyer la demande" id="valider" />
					</form>
				</center>
			</div>
			<script>
			function confirmEnvoi()
			{
				if(confirm("Voulez vous envoyer cette demande ?"))
					return true;
				return false;
			
			}
			</script>
		<?php
		}			
	}
}
require('bottom.php');
?>
