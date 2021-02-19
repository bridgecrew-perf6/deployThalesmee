<?php
require('top.php');
?>
<style>
html, body {
  height: 100%;
 
  }
#part1 {
  min-height: 100%;
  
  }
#part2 {
  min-height: 100%;
  
  }
#sTri{overflow:auto;}
#iAncre{max-width:150px}
</style>
<div id="part1" class="container index" style="min-height:100%;">
	<div class="row" >
		<?php 
		if($_SESSION['infoUser']['categUser']==3)
		{
		?>
		<div class="col-md-6">
			<div class="page-header">
				<h2>Responsable Laboratoire</h2>
			</div>
			<?php
			if($nbProc!=0)
				echo "<p><a href='listProc_affectation.php' class='btn btn-danger btn-lg' role='button'>$nbProc demande(s) en attente d'affectation</a></p>";
			?>
			<p><a href="../suivimee/index.php" class="btn btn-primary btn-lg" role="button">Suivi essai</a></p>
			<p><a href="listProc_affectation.php?rea=0" class="btn btn-primary btn-lg" role="button">Réaffecter une procédure</a></p>
			<p><a href="docClient.php" class="btn btn-primary btn-lg" role="button">Supprimer un document client</a></p>
		</div>
		<?php
		}
		?>
		<div class="col-md-6">
			<div class="page-header">
				<h2>Mes procédures</h2>
			</div>
			<?php
			if($nbNew==0)
				echo "<p><a href='#' class='btn btn-success btn-lg' role='button'>Aucune nouvelle procédure</a></p>";
			else
				echo "<p><a href='listProc.php?idEtat=13' class='btn btn-primary btn-lg' role='button'>$nbNew nouvelle(s) procédure(s)</a></p>";
			if($nbEnCours==0)
				echo "<p><a href='#' class='btn btn-success btn-lg' role='button'>Aucune procédure en cours de rédaction</a></p>";
			else
				echo "<p><a href='listProc.php?idEtat=14' class='btn btn-primary btn-lg' role='button'>$nbEnCours procédure(s) en cours de rédaction</a></p>";
			if($nbEnRelecture==0)
				echo "<p><a href='#' class='btn btn-success btn-lg' role='button'>Aucune procédure en cours de relecture</a></p>";
			else
				echo "<p><a href='listProc.php?idEtat=15' class='btn btn-primary btn-lg' role='button'>$nbEnRelecture procédure(s) en cours de relecture</a></p>";
			if($nbEnSignature==0)
				echo "<p><a href='#' class='btn btn-success btn-lg' role='button'>Aucune procédure en cours de signature</a></p>";
			else
				echo "<p><a href='listProc.php?idEtat=16' class='btn btn-primary btn-lg' role='button'>$nbEnSignature procédure(s) en cours de signature</a></p>";
			?>
		</div>
		<div class="col-md-6">
			<div class="page-header">
				<h2>Recherche</h2>
			</div>
			<p><a href="listDP.php" class="btn btn-primary btn-lg" role="button">Demande de procédure</a></p>
			<p><a href="rechProc.php" class="btn btn-primary btn-lg" role="button">Procédure</a></p>
			<p><a href="listDocClient.php" class="btn btn-primary btn-lg" role="button">Document client</a></p>
			<p><a href="suiviRedacLabo.php" class="btn btn-primary btn-lg" role="button">Suivi rédaction procédure</a></p>	
		</div>
		<div class="col-md-6 tabEssai" >
			<div class="page-header">
				<h2>Suivi rédaction procédure </h2>
			</div>
			<div class="text-center">
				<a id="aSuiv" href="#part2"><img id="iAncre" src="../img/go_down.png" /></a>
			</div>
		</div>
	</div>
</div>
<div id="part2" style="min-height=100%;">
	<?php require('suivi_proc.php');?>
</div>

	
</div><!-- /.container -->

<script type="text/javascript" language="javascript" src="../DataTables/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="../DataTables/columnFilter.js"></script>
<script>
$(document).ready(function() {
		
	/*
	cette page étant deja lourde a charger (Jquery ui + deux datatable), 
	aucun selecteur par classe + contruction de la deuxieme datatble seulement si selection du bouton agrandir
	*/
		
	$('#tri').dataTable({"aaSorting":[[0,"desc"]]}).columnFilter();;
	$('#tri_filter input').attr("placeholder", "Rechercher");
	$('#tri_filter input').attr("class", "form-control");
	$('#tri_filter input').attr("style", "font-weight:normal;");
	$('#tri_length select').attr("class", "form-control");
	$('#agrandir').css("float", "left");
	
	
	
	
	
			// Pour tous les liens commençant par #.
	$("#aSuiv").click(function (e) {
		var yPos;
		var target = ($($(this).attr("href") + ":first"));
		
		// On annule le comportement initial au cas ou la base soit différente de la page courante.
		e.preventDefault();
		

		// On cible manuellement l'ancre pour en extraire sa position.
		// Si c'est un ID on l'obtient.
		target = ($($(this).attr("href") + ":first"));
		
			// Si on a trouvé un name ou un id, on défile.
		if (target.length == 1) {
			yPos = target.offset().top; // Position de l'ancre.
			// On anime le défilement jusqu'à l'ancre.
			$('html,body').animate({ scrollTop: yPos}, 500); // On décale de 40 pixels l'affichage pour ne pas coller le bord haut de l'affichage du navigateur et on défile en 1 seconde jusqu'à l'ancre.
		}
	});
});
</script>
<?php
require('bottom.php');

?>