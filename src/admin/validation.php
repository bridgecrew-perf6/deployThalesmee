<?php 
require('top.php');

if (isset($_POST["essais"])){
	$essais = $_POST["essais"];
	foreach ($essais as $key => $value) {
		echo $value;
	}
}

echo "<div class='text-center'>";
	echo "<div class='alert alert-success'><strong>Mise à jour effectuée</strong></div>";
	echo "<input class='btn btn-lg btn-primary' type='button' value='Retour' onclick='document.location.href=\"index.php\"' />";
echo "</div>";

if (isset($_POST["moyens"])){

	$moyens = $_POST["moyens"];
	?>
	<div class="container">
		<h4>Veuillez ajouter ces moyens dans la base de données, ou les ajouter en tant qu'alias de moyens déja existant</h4>
		<div class="jumbotron">
			<table class="table table-striped table-tri" id="tri">
				<thead>
					<tr >
						<th>Nom du moyen inconnu</th>
						
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($moyens as $moyenInconnu)
						echo '<tr><td>'.$moyenInconnu.'</td></tr>';
					?>
				</tbody>
				<tfoot>
					<th>Nom du moyen inconnu</th>
				</tfoot>
			</table>
		</div>
	</div>
	<script type="text/javascript" language="javascript" src="../DataTables/jquery.dataTables.js"></script>
	<script type="text/javascript" language="javascript" src="../DataTables/columnFilter.js"></script>
	<script type="text/javascript" charset="utf-8">
		$(document).ready(function() {
			$('#tri').dataTable().columnFilter();
			$('#tri_filter input').attr("placeholder", "Rechercher");
			$('#tri_filter input').attr("class", "form-control");
			$('#tri_filter input').attr("style", "font-weight:normal;");
			$('#tri_length select').attr("class", "form-control");
		} );
	</script>
	<?php
}


require('bottom.php');
