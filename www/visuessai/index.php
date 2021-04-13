<?php
require('top.php');
?>

<div class="container index">
	<div class="page-header">
		<h2>Suivi Activité Laboratoire</h2>
	</div>
	<div class="row">
		<div class="col-md-4">
			<center><a href="visuLabo.php?idLabo=1" class="btn btn-primary btn-lg" role="button">Laboratoire EMC</a></center>
		</div>
		<div class="col-md-4">
			<center><a href="visuLabo.php?idLabo=2" class="btn btn-primary btn-lg" role="button">Laboratoire VIB</a></center>
		</div>
		<div class="col-md-4">
			<center><a href="visuLabo.php?idLabo=3" class="btn btn-primary btn-lg" role="button">Laboratoire VTH</a></center>
		</div>
	</div>
</div><!-- /.container -->


<?php
require('bottom.php');

?>