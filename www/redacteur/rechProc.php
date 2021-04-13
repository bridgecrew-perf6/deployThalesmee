<?php 
require('top.php');

require('../conf/connexion_param.php'); //connexion a la bdd
	
$str="select p.idProc, dp.idDP, d.reference, d.issue, d.rev from demande_procedure dp, procedures p, document_3it d 
where p.idDP_demande_procedure=dp.idDP
and d.idDoc = p.idDoc_document_3it";
$req=mysqli_query($bdd,$str);
echo mysqli_error($bdd);
if(!$req) //si pb dans la requete
	echo '<div class="alert alert-danger"><strong>Erreur de recuperation des procédures</strong></div>';
else
{
?>	
	<div class="container">
		<div class="page-header">
			<h2>Procédures</h2>
		</div>
		<div class="container theme-showcase" role="main">
			<div class="jumbotron">
				<table class="table table-striped table-tri" id="tri">
					<thead >
						<tr>
							<th>Référence 3it</th>
							<th>Issue 3it</th>
							<th>Révision 3it</th>
							<th>N° procédure</th>
							<th>N° demande</th>
						</tr>
					</thead>
					<tbody>
					<?php
					while($lg=mysqli_fetch_object($req))
					{	
						$idProc=$lg->idProc;
						$idDP=$lg->idDP;
						$reference=$lg->reference;
						$issue=$lg->issue;
						$rev=$lg->rev;
						echo "<tr style='cursor:pointer' onclick='document.location.href=\"detailProc.php?idProc=$idProc\"'>";
							echo "<td>$reference</td>";
							echo "<td>$issue</td>";
							echo "<td>$rev</td>";
							echo "<td>$idProc</td>";
							echo "<td>$idDP</td>";
						echo "</tr>";
					}
					?>
					</tbody>
					<tfoot >
						<tr>
							<th>Référence 3it</th>
							<th>Issue 3it</th>
							<th>Révision 3it</th>
							<th>N° procédure</th>
							<th>N° demande</th>
						</tr>
					</tfoot>
				</table>
			</div>
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
?>