<?php
require('top.php');
?>

<div class="container index">
	<div class="row">
		<div class="col-md-4">
			<div class="page-header">
				<h2>Suivi MEE</h2>
			</div>
			<p><a href="../redacteur/suiviRedacLabo.php" class="btn btn-primary btn-lg" role="button">Suivi rédaction procédure</a></p>
			<p><a href="../redacteur/suiviEssaiLabo.php" class="btn btn-primary btn-lg" role="button">Suivi essai</a></p>
		</div>
		<div class="col-md-4">
			<div class="page-header">
				<h2>Suivi Activité Laboratoire</h2>
			</div>
			<p><a href="visuLabo.php?idLabo=1" class="btn btn-primary btn-lg" role="button">Laboratoire EMC</a></p>
			<p><a href="visuLabo.php?idLabo=2" class="btn btn-primary btn-lg" role="button">Laboratoire VIB</a></p>
			<p><a href="visuLabo.php?idLabo=3" class="btn btn-primary btn-lg" role="button">Laboratoire VTH</a></p>
		</div>
		<div class="col-md-4">
			<div class="page-header">
				<h2>Suivi Activité Laboratoire</h2>
			</div>
			<p><a href="listDP.php" class="btn btn-primary btn-lg" role="button">Demande de procédure</a></p>
			<p><a href="listDocClient.php" class="btn btn-primary btn-lg" role="button">Document client</a></p>
		</div>
	</div>
	<?php 
		if($_SESSION['infoUser']['categUser']==3)
		{	
			echo'<br/><p><a href="../redacteur/index.php" class="btn btn-success btn-lg" role="button">Retour rédaction</a></p>';
		}
	?>
</div><!-- /.container -->

	

<?php
require('bottom.php');

?>