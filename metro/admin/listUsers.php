<?php 
require('top.php');
require('../conf/connexion_param.php');

//on recupere la liste des utilisateurs et quelques informations permettant de les retrouver
//Le login admin n'est intentionnellement pas affiché (pas de labo, filtré de la requete)
$str="select idUser, logUser, nomEmp, prenomEmp, l.nomLabo from utilisateur u, labo l
where u.idLabo_labo=l.idLabo";
$req=@mysqli_query($bdd, $str); //le @ est un parametre de gestion d'erreur, il evite un affichage incompréhensible pour un utilisateur (warning / error), à enlever pour tester l'erreur 
if(!$req) //une erreur dans la requete renvera false
	echo '<div class="alert alert-danger"><strong>Une erreur s\'est produite lors de la récupération des données des utilisateurs</strong></div>';
else
{
?>
	<div class="container">
		<div class="page-header">
			<h2>Liste des utilisateurs</h2>
			<input class='btn btn-lg btn-primary' type='button' value='Retour' onclick='document.location.href="index.php"'/>
		</div>
		<div class="container theme-showcase" role="main">
			<div class="jumbotron">
				<div class="table-responsive">
					<table class="table table-striped table-tri" id="tri">
						<thead>
							<tr >
								<th>Login</th>
								<th>Nom</th>
								<th>Prénom</th>
								<th>Service</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>Login</th>
								<th>Nom</th>
								<th>Prénom</th>
								<th>Service</th>
							</tr>
						</tfoot>
						<tbody>
							<?php
								while($lg=mysqli_fetch_object($req))
								{
									$idUser=$lg->idUser;
									$logUser=$lg->logUser;
									$labo=$lg->nomLabo;
									$nomEmp=ucfirst(mb_strtolower($lg->nomEmp, 'UTF-8'));
									$prenomEmp=ucfirst(mb_strtolower($lg->prenomEmp, 'UTF-8'));
					
									echo "<tr style='cursor:pointer;' onclick='document.location.href=\"detailsUser.php?idUser=$idUser\"'>";
										echo "<td>$logUser</td>";
										echo "<td>$nomEmp</td>";
										echo "<td>$prenomEmp</td>";
										echo "<td>$labo</td>";
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
require('bottom.php');

?>