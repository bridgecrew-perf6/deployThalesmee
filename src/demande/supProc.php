<?php 
require('top.php');

//Si suppression deja validé -> on supprime la proc
if (isset($_POST['idDemProc'])){
	require('../conf/connexion_param.php'); //connexion a la bdd
	$idDP=$_POST['idDemProc'];
	
	//On supprime la demande -> le delete se fait en cascade sur les autres tables liés a la demande
	$req=mysqli_query($bdd,"delete from demande_procedure where iddp=$idDP;");
	if(!$req)
		echo '<div class="alert alert-danger"><strong>Erreur de suppression de la demande</strong></div>';
	else
	{
		echo "<center><div class='alert alert-success'><strong>La demande $idDP a bien été supprimée</strong></div>";
		echo '<input type="button" class="btn btn-lg btn-primary"  value="Accueil" id="valider" onclick="document.location.href=\'index.php\';"/></center>';
	}
}
elseif (isset($_GET['idDP'])){ //sinon on affiche le formulaire de confirmation
	// on connait la demande a charger
	$idDP=$_GET['idDP'];
	require('../conf/connexion_param.php'); //connexion a la bdd
	require('../fonction.php'); //page contenant verifDPRapide
	if(verifDPRapide($idDP,$bdd)) //fonction qui test si la demande est en une étape (true) ou trois (false)
		$recap="genPDFDemande_rapide.php?idDP=$idDP";
	else
		$recap="genPDFDemande.php?idDP=$idDP";
	
?>
<div class="container">
	<div class="page-header">
		<h2>Suppression de procédure</h2>
	</div>
	<p>
		<iframe src="<?php echo $recap;?>" class="iframe_resu"></iframe>
	</p>	
	<center>
		<form style="display:inline;" method="post" id='form' action="supProc.php" onSubmit="return confirmSupr()">
			<input type="hidden" value="<?php echo $idDP; ?>" name="idDemProc" />
			<input type="submit" class="btn btn-lg btn-primary" value="Supprimer" id="valider" onclick="sendDP()"/>
		</form>
		<input type="button" class="btn btn-lg btn-primary"  value="Annuler" id="valider" onclick="document.location.href='listDP.php?mode=1';"/>
	</center>
</div>
<script>
	function confirmSupr()
	{
		if(confirm("Voulez vous vraiment supprimer cette demande ?"))
			return true;
		return false;
	}
</script>
<?php
}
else
	echo '<div class="alert alert-danger"><strong>Erreur de réception de la demande</strong></div>';
require('bottom.php');
?>