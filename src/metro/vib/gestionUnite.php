<?php
require('top.php');
require('../conf/connexion_param.php');
if(isset($_GET["idUniteSupp"]))
{
	
	$idUnite=$_GET["idUniteSupp"];
	$str="delete from unite where idUnite='$idUnite';";
	$req=mysqli_query($bdd, $str);
	if(!$req)
		echo '<div class="alert text-center alert-warning"><strong>Suppression impossible, l\'unite est peut être utilisée par un instrument</strong></div>';
	else
		echo '<div class="alert text-center alert-success"><strong>Suppression effectuée</strong></div>';
}
elseif(isset($_GET["idUniteModif"]))
{
	$idUnite=$_GET["idUniteModif"];
	
	$nomUnite=trim(htmlspecialchars(mysqli_real_escape_string($bdd,$_GET["nomUnite"])));
	$str="update unite set nomUnite='$nomUnite' where idUnite='$idUnite';";
	$req=mysqli_query($bdd, $str);
	
}
elseif(isset($_GET["nouvUnite"]))
{
	$nomUnite=trim(htmlspecialchars(mysqli_real_escape_string($bdd,$_GET["nouvUnite"])));

	$str="insert into unite values(null,'$nomUnite');";
	$req=mysqli_query($bdd, $str);
	if(!$req)
		echo "<div class='alert alert-error'><strong>Erreur d'ajout de l'unité</strong></div>";
	else
		echo "<div class='alert text-center alert-success'><strong>Ajout effectuée</strong></div>";
	

	
}

$str="select idUnite, nomUnite from unite;";
$req=mysqli_query($bdd, $str);
if(!$req)
	echo '<div class="alert alert-danger"><strong>Une erreur s\'est produite lors de la récupération des localisations</strong></div>';
else
{
?>
	<div class="container">
		<div class="page-header">
			<h2>Gestion des unités</h2>
		</div>
		<p>
			<input id="btnajUnite" type="button" class="btn btn-primary btn-lg" value="Ajouter une unité" onclick="ajouterUnite()" />
			<div id="ajUnite" style="display:none;" class="container theme-showcase" role="main">
				<h4>Ajouter une unité</h4>
				<div class="jumbotron">
					<form method="GET" action="gestionUnite.php" class="form-user" role="form">
						<p>
							<input id="nouvUnite" type="text" name="nouvUnite" class="form-control" placeholder="Nouvelle unité" required />
						</p>
						<input type="submit" class="btn btn-primary btn-lg" value="Ajouter" />
						<input type="button" class="btn btn-primary btn-lg" value="Annuler" onclick="ajouterUnite()"/>
					</form>
				</div>
			</div>
		</p>
		<div class="container theme-showcase" role="main">
			<h4>Listes des moyens</h4>
			<div class="jumbotron">
				<div class="table-responsive">
					<table class="table table-striped table-tri" id="tri">
						<thead>
							<tr >
								<th>Unité</th>
								<th style="text-align:right">Supprimer</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>Unité</th>
							</tr>
						</tfoot>
						<tbody>
							<?php
								while($lg=mysqli_fetch_object($req))
								{
									$idUnite=$lg->idUnite;
									$nomUnite=$lg->nomUnite;
									
									echo "<tr>";
										echo '<td style="cursor:pointer;" onclick="modifUnite(this,\''.$idUnite.'\');">'.$nomUnite.'</td>';
										echo "<td onclick='confirmSuppr(\"$idUnite\");'><img class='imgSupp' SRC='../img/supr.png'  /></td>";
									echo "</tr>";
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<input type="button" value="Retour" class="btn btn-primary btn-lg" onclick="document.location.href='index.php'"/>
	</div><!-- /.container -->
	<script type="text/javascript" language="javascript" src="../DataTables/jquery.dataTables.js"></script>	
	<script type="text/javascript" language="javascript" src="../DataTables/columnFilter.js"></script>	
	<script type="text/javascript" charset="utf-8">
	var focus=false;
	function ajouterUnite() //fait apparaitre / cache le formulaire d'ajout d'un moyen
	{
		var form =document.getElementById('ajUnite');
		var btn =document.getElementById('btnajUnite');
		if(form.style.display == "none")
		{
			form.style.display = "";
			btn.style.display = "none";
			document.getElementById('nouvUnite').focus();
		}
		else{
			form.style.display = "none";
			btn.style.display = "";
		
		}
	}

	function confirmSuppr(idUnite)
	{
		if(confirm("Voulez vous vraiment supprimer cette unité ?"))
			document.location.href='gestionUnite.php?idUniteSupp='+idUnite;
	}

	function modifUnite(obj,idUnite)
	{
		if(!focus)
		{
			focus=true;
			var nom=$(obj).html();
			$(obj).html('<input type="text" id="modif" value="'+nom+'" class="form-control" placeholder="Nom de la unité" />');
			$(obj).children().first().focus();
			
			$(obj).children().first().on("focusout",function(){
				document.location.href='gestionUnite.php?idUniteModif='+idUnite+'&nomUnite='+document.getElementById("modif").value;
			});
			$(document).keypress(function(e) {
				if(e.which == 13) {
					document.location.href='gestionUnite.php?idUniteModif='+idUnite+'&nomUnite='+document.getElementById("modif").value;
				}
			});
		}
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
require('bottom.php');