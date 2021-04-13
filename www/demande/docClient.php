<?php 
require('top.php');
require('../fonction.php');

require('../conf/connexion_param.php'); //connexion a la bdd

$str="select a.idSpec, a.nomFichier ,a.reference , a.issue, a.rev , a.plateforme, b.nom, b.idTypeDoc from SPEC_CLIENT a, TYPE_DOC b where b.idTypeDoc=a.idTypeDoc_TYPE_DOC;";
$reqSpecClient=mysqli_query($bdd,$str);

?>
<link rel="stylesheet" href="../jquery-ui/css/redmond/jquery-ui.min.css">


<div class="container">
	<div class="page-header">
		<h2>Documents client</h2>
	</div>
	<p>
		<input type="button" id="enrDoc" value="Enregistrer un document" class="btn btn-lg btn-primary" />
	</p>
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
						echo "<td><a style='color:blue' href='download.php?link=$lien&nomOr=$nomFicher'>".tronquer($nomFicher,10)."</a></td>";
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
<div id="dialog" title="Enregistrer un document">
	<form enctype="multipart/form-data" method="post" id="formDoc">
		<select id="tpdoc" name="tpdoc" class="form-control">
			<option value="-1" selected disabled>Choisir le type de document</option>
			<option value="20">Spécifications Electrique et Environnement</option>
			<option value="21">Spécifications Electrique</option>
			<option value="22">Spécifications Environnement</option>
			<option value="23">RFD Electrique</option>	
			<option value="24">RFD Mécanique</option>
			<option value="25">RFD Thermique</option>						
			<option value="26">Autre</option>						
		</select>
		<input placeholder="Référence" class="form-control" type="text" id="ref" name="ref" />
		<input placeholder="Issue" class="form-control" type="text" id="issue" name="issue" />
		<input placeholder="Révision" class="form-control" type="text" id="rev" name="rev" />
		<input placeholder="Plateforme" class="form-control" type="text" id="plateforme" name="plateforme" />
		Document à envoyer:<input title="Document à envoyer"  class="form-control" style="height:auto;" id="file"  type="file" name="monfichier"/>
	</form>
</div>
<script src="../jquery-ui/js/jquery-ui.min.js"></script>
<script src="../js/docClient.js"></script>
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
<script>
//cette variable sert a ajouter ou non l'image pour supprimer un doc (n'ajoute pas la fonction pour le faire, juste un arrangement esthetique en évitant d'avoir a faire un autre fichier pour une ligne de difference)
var admin =false; 
</script>
<?php	
require('bottom.php');
?>