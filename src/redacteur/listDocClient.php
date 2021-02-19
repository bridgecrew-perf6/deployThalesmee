<?php 
require('top.php');
require('../fonction.php');
require('../conf/connexion_param.php'); //connexion a la bdd
	
$str="select a.idSpec, a.nomFichier ,a.reference , a.issue, a.rev , a.plateforme, b.nom, b.idTypeDoc from SPEC_CLIENT a, TYPE_DOC b where b.idTypeDoc=a.idTypeDoc_TYPE_DOC;";
$reqSpecClient=mysqli_query($bdd,$str);
if(!$reqSpecClient) //si pb dans la requete
	echo '<div class="alert alert-danger"><strong>Erreur de recuperation des documents</strong></div>';
else
{
?>	
	<div class="container">
		<div class="page-header">
			<h2>Documents client</h2>
		</div>
		<div class="container theme-showcase" role="main">
			<h4>Liste des documents</h4>
			<div class="jumbotron">
				<table class="table table-striped table-tri" id="tri">
					<thead >
						<tr>
							<th>Type de document</th>
							<th>Référence</th>
							<th>Issue</th>
							<th>Révision</th>
							<th>Plateforme</th>
							<th>Voir le doc</th>
						</tr>
					</thead>
					<tbody>
					<?php
					while($lg=mysqli_fetch_object($reqSpecClient))
					{	
						$idSpec=$lg->idSpec;
						$nomFicher=$lg->nomFichier;
						$reference=$lg->reference;
						$issue=$lg->issue;
						$rev=$lg->rev;
						$plateforme=$lg->plateforme;
						$tpdoc=$lg->nom;
						$idTypeDoc=$lg->idTypeDoc;
						$lien=$idSpec.$nomFicher;
						echo "<tr>";
							echo "<td>$tpdoc</td>";
							echo "<td>$reference</td>";
							echo "<td>$issue</td>";
							echo "<td>$rev</td>";
							echo "<td>$plateforme</td>";
							echo "<td><a style='color:blue' href='../demande/download.php?link=$lien&nomOr=$nomFicher'>".tronquer($nomFicher,10)."</a></td>";
						echo "</tr>";
					}
					?>
					</tbody>
					<tfoot >
						<tr>
							<th>Type de document</th>
							<th>Référence</th>
							<th>Issue</th>
							<th>Révision</th>
							<th>Plateforme</th>
							<th>Voir le doc</th>
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
	});
	</script>
<?php	
}
require('bottom.php');
?>