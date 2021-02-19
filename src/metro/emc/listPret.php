<?php 
require("top.php");

require('../conf/connexion_param.php');
$labo=$_SESSION['metro']['labo'];
$str="select idPret,nomPret,nomCorresp,nomLocal from pret p LEFT JOIN localisation l ON p.idLocal_localisation=l.idLocal
where p.idLabo_labo='$labo';";
$req=mysqli_query($bdd, $str);
if(!$req) //une erreur dans la requete renvera false
	echo '<div class="alert alert-danger"><strong>Une erreur s\'est produite lors de la récupération des données des prêt</strong></div>';
else
{
?>
	<div class="container">
	
		<div class="page-header">
			<h2>Liste des entrées/sorties</h2>
			<input class='btn btn-lg btn-primary' type='button' value='Retour' onclick='document.location.href="index.php"'/>
		</div>
		
		<div class="container theme-showcase" role="main">
			<div class="jumbotron">
				<div class="table-responsive">
					<table class="table table-striped table-tri" id="tri">
						<thead>
							<tr >
								<th>N°</th>
								<th>Nom</th>
								<th>Correspondant</th>
								<th>Lieu</th>
							</tr>
						</thead>
						<tfoot>
							<tr >
								<th>N°</th>
								<th>Nom</th>
								<th>Correspondant</th>
								<th>Lieu</th>
							</tr>
						</tfoot>
						<tbody>
							<?php
								while($lg=mysqli_fetch_object($req))
								{
									$idPret=$lg->idPret;
									$nomPret=$lg->nomPret;
									$nomCorresp=$lg->nomCorresp;
									$lieu=$lg->nomLocal;
									
					
									echo "<tr style='cursor:pointer;' onclick='document.location.href=\"detailsPret.php?idPret=$idPret\"'>";
										echo "<td>$idPret</td>";
										echo "<td>$nomPret</td>";
										echo "<td>$nomCorresp</td>";
										echo "<td>$lieu</td>";
									echo "</tr>";
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<input class='btn btn-lg btn-primary' type='button' value='Retour' onclick='document.location.href="index.php"'/>
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
require("bottom.php");