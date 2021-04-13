<?php 
require('top.php');
require('../fonction.php');
if($_SESSION['infoUser']['categUser']==3)
{
	require('../conf/connexion_param.php'); //connexion a la bdd
	
	$str="select a.idSpec, a.nomFichier ,a.reference , a.issue, a.rev , a.plateforme, b.nom, b.idTypeDoc from SPEC_CLIENT a, TYPE_DOC b where b.idTypeDoc=a.idTypeDoc_TYPE_DOC;";
	$reqSpecClient=mysqli_query($bdd,$str);
	
	?>
	
	
	
	<div class="container">
		<div class="page-header">
			<h2>Documents client</h2>
		</div>
		<div class="container theme-showcase" role="main">
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
							<th style="text-align:right">Supprimer</th>
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
						echo "<tr>";
							echo "<td>$tpdoc</td>";
							echo "<td>$reference</td>";
							echo "<td>$issue</td>";
							echo "<td>$rev</td>";
							echo "<td>$plateforme</td>";
							echo "<td><a style='color:blue' href='../demande/download.php?link=$nomFicher'>".tronquer($nomFicher,10)."</a></td>";
							echo "<td><IMG style='cursor:pointer;float:right;max-height:20px' SRC='../img/supr.png' onclick='confirmSuppr(\"$idSpec\");' /></td>";
						echo "</tr>";
					}
					?>
					</tbody>
					<tfoot>
						<tr>
							<th>Type de document</th>
							<th>Référence</th>
							<th>Issue</th>
							<th>Révision</th>
							<th>Plateforme</th>
							<th>Voir le doc</th>
							<th style="text-align:right">Supprimer</th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
	<script type="text/javascript" language="javascript" src="../DataTables/jquery.dataTables.js"></script>
	<script type="text/javascript" language="javascript" src="../DataTables/columnFilter.js"></script>
	<script>
	//cette variable sert a ajouter ou non l'image pour supprimer un doc (n'ajoute pas la fonction pour le faire, juste un arrangement esthetique en évitant d'avoir a faire un autre fichier pour une ligne de difference)
	var admin=true;
	function confirmSuppr(idSpec)
	{
		if(confirm("Voulez vous vraiment supprimer ce document ?"))
			document.location.href='supprDoc.php?idSpec='+idSpec+'';
	}
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
else
	echo '<div class="alert alert-danger"><strong>Accés non autorisé</strong></div>';
require('bottom.php');
?>