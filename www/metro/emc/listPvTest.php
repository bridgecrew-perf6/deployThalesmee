<?php
require("top.php"); 
require('../conf/connexion_param.php');
require('../fonction.php');

//on recupere la liste des utilisateurs et quelques informations permettant de les retrouver
$str="select idPv, datePv, LEFT(titrePV,50) as tPv from pv where idlabo_labo=1";
$req=@mysqli_query($bdd, $str); //le @ est un parametre de gestion d'erreur, il evite un affichage incompréhensible pour un utilisateur (warning / error), à enlever pour tester l'erreur 
if(!$req) //une erreur dans la requete renvera false
	echo '<div class="alert alert-danger"><strong>Une erreur s\'est produite lors de la récupération des PV</strong></div>';
else
{
?>
	<div class="container">
	
		<div class="page-header">
			<h2>Liste des PV</h2>
			<input class='btn btn-lg btn-primary' type='button' value='Retour' onclick='document.location.href="index.php"'/>
		</div>
		
		<div class="container theme-showcase" role="main">
			<div class="jumbotron">
				<div class="table-responsive">
					<table class="table table-striped table-tri" id="tri">
						<thead>
							<tr >
								<th>N°</th>
								<th>Titre du PV</th>
								<th>Date</th>
							</tr>
						</thead>
						<tfoot>
							<tr >
								<th>N°</th>
								<th>Titre du PV</th>
								<th>Date</th>
							</tr>
						</tfoot>
						<tbody>
							<?php
								while($lg=mysqli_fetch_object($req))
								{
									$idPv=$lg->idPv;
									$titrePV=$lg->tPv;
									$datePv=dateSQLToFr($lg->datePv);
									echo "<tr style='cursor:pointer;' onclick='document.location.href=\"detailsPv.php?idPV=$idPv\"'>";
										echo "<td>$idPv</td>";
										echo "<td>$titrePV</td>";
										echo "<td>$datePv</td>";
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