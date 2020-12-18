<?php 
require('top.php');
require('../conf/connexion_param.php');

//on recupere la liste des utilisateurs et quelques informations permettant de les retrouver
$str="select u.idUser,u.logUser, e.nomEmp, e.prenomEmp, e.actif, s.nomService from utilisateur u, employe e, service s
where u.idEmp_Employe=e.idEmp
and e.idService_service=s.idservice";
$req=@mysqli_query($bdd, $str); //le @ est un parametre de gestion d'erreur, il evite un affichage incompréhensible pour un utilisateur (warning / error), à enlever pour tester l'erreur 
if(!$req) //une erreur dans la requete renvera false
	echo '<div class="alert alert-danger"><strong>Une erreur s\'est produite lors de la récupération des données des utilisateurs</strong></div>';
else
{
?>
	<div class="container">
		<div class="page-header">
			<h2>Liste des utilisateurs</h2>
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
								<th>Actif</th>
							</tr>
						</thead>
						<tbody>
							<?php
								while($lg=mysqli_fetch_object($req))
								{
									$idUser=$lg->idUser;
									$logUser=$lg->logUser;
									$nomEmp=ucfirst(mb_strtolower($lg->nomEmp, 'UTF-8'));
									$prenomEmp=ucfirst(mb_strtolower($lg->prenomEmp, 'UTF-8'));
									$nomService=$lg->nomService;
									$actif = $lg->actif;
									
									echo "<tr style='cursor:pointer;' ondblclick='document.location.href=\"detailsUser.php?idUser=$idUser\"'>";
										echo "<td>$logUser</td>";
										echo "<td>$nomEmp</td>";
										echo "<td>$prenomEmp</td>";
										echo "<td>$nomService</td>";
										echo "<td><input onclick='change_actif(\"".$idUser."\", \"".$logUser."\")' type='checkbox' ";
										if ($actif == 1) echo 'checked';
										echo " </td>";
									echo "</tr>";
								}
							?>
						</tbody>
						<tfoot>
							<tr>
								<th>Login</th>
								<th>Nom</th>
								<th>Prénom</th>
								<th>Service</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>		
	<script type="text/javascript" language="javascript" src="../DataTables/jquery.dataTables.js"></script>
	<script src="../js/swal.js"></script>
	<script type="text/javascript" language="javascript" src="../DataTables/columnFilter.js"></script>	
	<script type="text/javascript" charset="utf-8">
		$(document).ready(function() {
			$('#tri').dataTable().columnFilter();
			$('#tri_filter input').attr("placeholder", "Rechercher");
			$('#tri_filter input').attr("class", "form-control");
			$('#tri_filter input').attr("style", "font-weight:normal;");
			$('#tri_length select').attr("class", "form-control");
		} );

		/* Fonction qui permet la redirection lors de la validation
		*/
		function redirection(){
			document.location.href="listUsers.php";
		}

		/* Fonction permettant de changer la status d'un employé
		* @param
		* idUser : l'identifiant de l'utilisateur
		* logUser : le log de l'utilisateur (pour désactiver le meme log)
		*/
		function change_actif(idUser, logUser)
		{
			$.get("user_actif.php?idUser="+idUser+"&logUser="+logUser, function(data) //Requête AJAX
			{
				if (data == "actif") //Si l'employé est désormais actif
				{
					swal({

						title : "Changement éffectué",
						text : "L'employé est désormais actif",
						icon : "success"
						
					});
					setTimeout(redirection, 1000);

				}else //Si l'employé est désormais inactif
				{
					swal({

						title : "Changement éffectué",
						text : "L'employé est désormais inactif",
						icon : "success"
						
					});
					setTimeout(redirection, 1000);
				}
			});
		}
	</script>
<?php
}
require('bottom.php');

?>